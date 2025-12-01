<?php
header('Content-Type: application/json');
require '../../conexionBD.php';
session_start();

// ------------- TCPDF -------------
require_once(__DIR__ . '/../../libraries/tcpdf/tcpdf.php');

// 1. Leer JSON crudo
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibió información válida'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    // =======================================
    //  DATOS DE COMPRA
    // =======================================
    $fecha = $data['fecha'];
    $tipoDocumento = $data['tipoDocumento'];
    $numeroDocumento = $data['numeroDocumento'];
    $id_proveedor = $data['id_proveedor'];
    $tipoCalculo = $data['tipoCalculo'];
    $porcentajeDescuento = $data['porcentajeDescuentoGlobal'];
    $tipoImpuesto = $data['tipoImpuesto'];
    $productos = $data['productos'];
    $id_usuario = $_SESSION['id_usuario'];

    // SUBTOTAL
    $subtotal = array_sum(array_column($productos, 'subtotal'));

    // DESCUENTO GLOBAL
    $descuento_total = ($tipoCalculo === "descuento")
        ? $subtotal * ($porcentajeDescuento / 100)
        : 0;

    // IMPUESTO GLOBAL
    $impuesto_total = ($tipoCalculo === "impuesto")
        ? $subtotal * $tipoImpuesto
        : 0;

    // TOTAL A PAGAR
    $total = ($subtotal - $descuento_total) + $impuesto_total;

    // =======================================
    // INSERT EN TABLA COMPRA
    // =======================================
    $sqlCompra = "INSERT INTO compra
        (fecha_y_hora, tipo_documento, numero_documento, tipo_calculo,
         porcentaje_descuento_global, tipo_impuesto, subtotal, descuento_total,
         impuesto_total, total, id_proveedor, id_Usuarios)
        VALUES (:fecha_y_hora, :tipo_documento, :numero_documento, :tipo_calculo,
         :porcentaje_descuento_global, :tipo_impuesto, :subtotal, :descuento_total,
         :impuesto_total, :total, :id_proveedor, :id_Usuarios)";

    $stmt = $pdo->prepare($sqlCompra);

    $stmt->execute([
        ':fecha_y_hora' => $fecha . " " . date("H:i:s"),
        ':tipo_documento' => $tipoDocumento,
        ':numero_documento' => $numeroDocumento,
        ':tipo_calculo' => $tipoCalculo,
        ':porcentaje_descuento_global' => $porcentajeDescuento,
        ':tipo_impuesto' => ($tipoImpuesto == 0 ? null : "IVA12"),
        ':subtotal' => $subtotal,
        ':descuento_total' => $descuento_total,
        ':impuesto_total' => $impuesto_total,
        ':total' => $total,
        ':id_proveedor' => $id_proveedor,
        ':id_Usuarios' => $id_usuario
    ]);

    $id_compra = $pdo->lastInsertId();

    // =======================================
    // DETALLE COMPRA Y STOCK
    // =======================================
    $sqlDetalle = "INSERT INTO detalle_compra
        (id_compra, id_producto, cantidad, costo_unitario, subtotal)
        VALUES (:id_compra, :id_producto, :cantidad, :costo_unitario, :subtotal)";

    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $p) {

        // Insertar detalle
        $stmtDetalle->execute([
            ':id_compra' => $id_compra,
            ':id_producto' => $p['id'],
            ':cantidad' => $p['cantidad'],
            ':costo_unitario' => $p['precio'],
            ':subtotal' => $p['subtotal']
        ]);

        // Actualizar stock
        $sqlStock = "UPDATE producto
                     SET stock = stock + :cantidad
                     WHERE id_producto = :id_producto";

        $stmtStock = $pdo->prepare($sqlStock);
        $stmtStock->execute([
            ':cantidad' => $p['cantidad'],
            ':id_producto' => $p['id']
        ]);
    }

    // =======================================
    // AFECTAR CAJA (EGRESO)
    // =======================================
    $sqlCaja = "SELECT * FROM caja
                WHERE estado = 'abierta'
                AND id_usuario = :id_usuario
                LIMIT 1";

    $stmtCaja = $pdo->prepare($sqlCaja);
    $stmtCaja->execute([':id_usuario' => $id_usuario]);
    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        throw new Exception("Debe abrir una caja antes de registrar compras.");
    }

    $id_caja = $caja['id_caja'];
    $nuevo_monto = $caja['monto_actual'] - $total;

    if ($nuevo_monto < 0) {
        throw new Exception("Saldo insuficiente en caja para registrar esta compra.");
    }

    // Actualizar monto en caja
    $sqlUpdateCaja = "UPDATE caja
                      SET monto_actual = :monto_actual
                      WHERE id_caja = :id_caja";

    $stmtUpdateCaja = $pdo->prepare($sqlUpdateCaja);
    $stmtUpdateCaja->execute([
        ':monto_actual' => $nuevo_monto,
        ':id_caja' => $id_caja
    ]);

    // Historial de caja
    $sqlHistorial = "INSERT INTO historial_caja
        (id_caja, tipo_movimiento, monto, descripcion, numero_comprobante,
         tabla_origen, id_usuario)
        VALUES
        (:id_caja, 'egreso', :monto, :descripcion, :numero_comprobante,
         'Compra', :id_usuario)";

    $stmtHistorial = $pdo->prepare($sqlHistorial);
    $stmtHistorial->execute([
        ':id_caja' => $id_caja,
        ':monto' => $total,
        ':descripcion' => 'Compra #' . $id_compra,
        ':numero_comprobante' => $numeroDocumento,
        ':id_usuario' => $id_usuario
    ]);

    // Finalizar SQL
    $pdo->commit();

    // =======================================
    // GENERAR PDF
    // =======================================
    // Obtener nombre del proveedor
    $sqlProv = "SELECT nombre_empresa FROM proveedor WHERE id_proveedor = :id_proveedor LIMIT 1";
    $stmtProv = $pdo->prepare($sqlProv);
    $stmtProv->execute([':id_proveedor' => $id_proveedor]);
    $proveedor = $stmtProv->fetch(PDO::FETCH_ASSOC);

    $nombre_empresa = $proveedor ? $proveedor['nombre_empresa'] : 'Proveedor desconocido';

    $pdf = new TCPDF();
    $pdf->AddPage();

   $html = "
    <h1>Compra #{$id_compra}</h1>
    <p><strong>Fecha:</strong> {$fecha}</p>
    <p><strong>Documento:</strong> {$tipoDocumento} - {$numeroDocumento}</p>
    <p><strong>Proveedor:</strong> {$nombre_empresa}</p>

    <br>
    <table border='1' cellspacing='0' cellpadding='4'>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Costo</th>
            <th>Subtotal</th>
        </tr>";

    foreach ($productos as $p) {
        $html .= "
            <tr>
                <td>{$p['nombre']}</td>
                <td>{$p['cantidad']}</td>
                <td>{$p['precio']}</td>
                <td>{$p['subtotal']}</td>
            </tr>";
    }

    $html .= "
        </table>
        <br><br>
        <h3>Total: Q {$total}</h3>
    ";

    $pdf->writeHTML($html, true, false, true, false, '');

    // =======================================
    // RUTA DE PDF (CORREGIDA)
    // =======================================
    $carpeta = __DIR__ . '/../../../reportes/compras/';

    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0777, true);
    }

    $rutaPDF = $carpeta . "compra_" . $id_compra . ".pdf";

    $pdf->Output($rutaPDF, 'F');

    echo json_encode([
        'success' => true,
        'message' => 'Compra guardada correctamente',
        'id_compra' => $id_compra,
        'pdf_url' => "reportes/compras/compra_{$id_compra}.pdf"
    ]);
    exit;
} catch (Exception $e) {

    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
    exit;
}
