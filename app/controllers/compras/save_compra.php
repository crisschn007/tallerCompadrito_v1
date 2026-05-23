<?php

header('Content-Type: application/json');
require '../../conexionBD.php';
session_start();

require_once(__DIR__ . '/../../libraries/tcpdf/tcpdf.php');

// DEBUG
$input = file_get_contents("php://input");
$data = json_decode($input, true);


if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'JSON vacío o inválido'
    ]);
    exit;
}

try {

    $pdo->beginTransaction();

    // ==============================
    // DATOS
    // ==============================
    $fecha = $data['fecha'];
    $tipoDocumento = $data['tipoDocumento'];
    $numeroDocumento = $data['numeroDocumento'];
    $id_proveedor = $data['id_proveedor'];

    $tipoCalculo = $data['tipoCompra'];
    $porcentajeDescuento = $data['porcentaje_descuento'] ?? 0;

    $productos = $data['productos'];
    $id_usuario = $_SESSION['id_usuario'];

    // ==============================
    // CALCULOS
    // ==============================
    $subtotal = 0;

    foreach ($productos as $p) {
        $subtotal += $p['precio'] * $p['cantidad'];
    }

    $descuento_total = ($tipoCalculo === "descuento")
        ? $subtotal * ($porcentajeDescuento / 100)
        : 0;

    $total = $subtotal - $descuento_total;

    // ==============================
    // INSERT COMPRA
    // ==============================
    $sqlCompra = "INSERT INTO compra
        (fecha, tipo_documento, numero_documento, tipo_calculo,
         porcentaje_descuento_global, subtotal, descuento_total,
         total, id_proveedor, id_Usuarios)
        VALUES (:fecha, :tipo_documento, :numero_documento, :tipo_calculo,
         :porcentaje_descuento_global, :subtotal, :descuento_total,
         :total, :id_proveedor, :id_Usuarios)";

    $stmt = $pdo->prepare($sqlCompra);

    $stmt->execute([
        ':fecha' => $fecha,
        ':tipo_documento' => $tipoDocumento,
        ':numero_documento' => $numeroDocumento,
        ':tipo_calculo' => $tipoCalculo,
        ':porcentaje_descuento_global' => $porcentajeDescuento,
        ':subtotal' => $subtotal,
        ':descuento_total' => $descuento_total,
        ':total' => $total,
        ':id_proveedor' => $id_proveedor,
        ':id_Usuarios' => $id_usuario
    ]);

    $id_compra = $pdo->lastInsertId();

    // ==============================
    // DETALLE + STOCK + PRECIOS
    // ==============================
    $sqlDetalle = "INSERT INTO detalle_compra
        (id_Compra, id_producto, cantidad, costo_unitario, subtotal)
        VALUES (:id_compra, :id_producto, :cantidad, :costo_unitario, :subtotal)";

    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $p) {

        $subtotal_item = $p['precio'] * $p['cantidad'];

        // INSERT DETALLE
        $stmtDetalle->execute([
            ':id_compra' => $id_compra,
            ':id_producto' => $p['id'],
            ':cantidad' => $p['cantidad'],
            ':costo_unitario' => $p['precio'],
            ':subtotal' => $subtotal_item
        ]);

        // ACTUALIZAR STOCK + PRECIOS
        $sqlUpdateProducto = "UPDATE producto
            SET stock = stock + :cantidad,
                precio = :precio_venta,
                precio_mayorista = :precio_mayorista
            WHERE id_producto = :id_producto";

        $stmtUpdateProducto = $pdo->prepare($sqlUpdateProducto);
        $stmtUpdateProducto->execute([
            ':cantidad' => $p['cantidad'],
            ':precio_venta' => $p['precio_venta'],
            ':precio_mayorista' => $p['precio_mayorista'],
            ':id_producto' => $p['id']
        ]);
    }

    // ==============================
    // CAJA
    // ==============================
    $sqlCaja = "SELECT * FROM caja
                WHERE estado = 'abierta'
                AND id_Usuarios = :id_usuario
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
        throw new Exception("Saldo insuficiente en caja.");
    }

    // UPDATE CAJA
    $pdo->prepare("UPDATE caja SET monto_actual = :monto WHERE id_caja = :id")
        ->execute([
            ':monto' => $nuevo_monto,
            ':id' => $id_caja
        ]);

    // HISTORIAL
    $pdo->prepare("INSERT INTO historial_caja
    (id_caja, tipo_movimiento, monto, descripcion, numero_comprobante, tabla_origen, id_Usuarios)
    VALUES (:id_caja, 'egreso', :monto, :descripcion, :numero, 'Compra', :id_usuario)")
        ->execute([
            ':id_caja' => $id_caja,
            ':monto' => $total,
            ':descripcion' => 'Compra #' . $id_compra,
            ':numero' => $numeroDocumento,
            ':id_usuario' => $id_usuario
        ]);

    $pdo->commit();

    // ==============================
    // PDF
    // ==============================
    $stmtProv = $pdo->prepare("SELECT nombre_empresa FROM proveedor WHERE id_proveedor = :id");
    $stmtProv->execute([':id' => $id_proveedor]);
    $prov = $stmtProv->fetch(PDO::FETCH_ASSOC);

    $pdf = new TCPDF();
    $pdf->AddPage();

    $html = "<h1>Compra #{$id_compra}</h1>
    <p><strong>Fecha:</strong> {$fecha}</p>
    <p><strong>Proveedor:</strong> " . ($prov['nombre_empresa'] ?? 'N/A') . "</p>
    <table border='1' cellpadding='4'>
    <tr><th>Producto</th><th>Cant</th><th>Costo</th><th>Subtotal</th></tr>";

    foreach ($productos as $p) {
        $sub = $p['precio'] * $p['cantidad'];
        $html .= "<tr>
            <td>{$p['nombre']}</td>
            <td>{$p['cantidad']}</td>
            <td>{$p['precio']}</td>
            <td>{$sub}</td>
        </tr>";
    }

    $html .= "</table><h3>Total: Q {$total}</h3>";

    $pdf->writeHTML($html);

    $ruta = __DIR__ . '/../../../reportes/compras/'; //salida del archivo de reportes
    if (!is_dir($ruta)) mkdir($ruta, 0777, true);

    $pdf->Output($ruta . "compra_$id_compra.pdf", 'F');

    echo json_encode([
        'success' => true,
        'message' => 'Compra guardada correctamente'
    ]);
} catch (Exception $e) {

    $pdo->rollBack();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
