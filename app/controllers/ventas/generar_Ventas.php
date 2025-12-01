<?php
// app/controllers/ventas/generar_Ventas.php
// Reconstruido por ChatGPT - ajustado a tus tablas reales

include '../../conexionBD.php';
session_start();

// Asegúrate que $URL y $pdo vienen desde conexionBD.php
// $URL => url base del proyecto
// $pdo => instancia PDO

// Helpers de redirección con mensaje
function redirect_with($url, $title, $message, $icon = 'info') {
    $_SESSION['titulo']  = $title;
    $_SESSION['mensaje'] = $message;
    $_SESSION['icono']   = $icon;
    header('Location: ' . $url);
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with($URL . 'ventas/nueva', 'Acceso no permitido', 'La petición debe ser POST.', 'warning');
}

// Obtener y sanitizar entradas
$id_cliente = isset($_POST['id_cliente']) ? intval($_POST['id_cliente']) : 0;
$condicion_pago = isset($_POST['condicion_pago']) ? trim($_POST['condicion_pago']) : '';
$total = isset($_POST['total']) ? floatval(str_replace(',', '', $_POST['total'])) : 0.00;
$efectivo_recibido = isset($_POST['efectivo_recibido']) ? floatval($_POST['efectivo_recibido']) : 0.00;
$cambio = isset($_POST['cambio']) ? floatval(str_replace(',', '', $_POST['cambio'])) : 0.00;
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : (isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 0);
$id_caja = isset($_POST['id_caja']) ? intval($_POST['id_caja']) : 0;
$productos_json = isset($_POST['productos']) ? $_POST['productos'] : '';

if ($id_cliente <= 0 || $id_usuario <= 0 || $id_caja <= 0 || empty($productos_json)) {
    redirect_with($URL . 'ventas/nueva', 'Campos incompletos', 'Por favor, complete todos los campos obligatorios.', 'warning');
}

// Decodificar productos
$productos = json_decode($productos_json, true);
if (!is_array($productos) || count($productos) === 0) {
    redirect_with($URL . 'ventas/nueva', 'Productos inválidos', 'No se detectaron productos en la venta.', 'warning');
}

try {
    // 1) Verificar que la caja indicada está abierta y pertenece al usuario (o está abierta cualquiera)
    $stmtCaja = $pdo->prepare("SELECT * FROM caja WHERE id_caja = :id_caja AND estado = 'abierta' LIMIT 1");
    $stmtCaja->execute([':id_caja' => $id_caja]);
    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        redirect_with($URL . 'ventas/nueva', 'Caja cerrada', 'La caja seleccionada no está abierta. Abra una caja antes de registrar la venta.', 'warning');
    }

    // 2) Validar stock para cada producto (y obtener precio actual si quieres validar)
    // Recolectar comprobaciones
    $productos_check = [];
    $stmtProd = $pdo->prepare("SELECT id_producto, nombre_producto, stock, precio FROM producto WHERE id_producto = :id_producto LIMIT 1");

    foreach ($productos as $p) {
        $id_producto = intval($p['id_producto']);
        $cantidad = intval($p['cantidad']);
        if ($id_producto <= 0 || $cantidad <= 0) {
            redirect_with($URL . 'ventas/nueva', 'Producto inválido', 'Se encontró un producto con datos inválidos.', 'warning');
        }

        $stmtProd->execute([':id_producto' => $id_producto]);
        $row = $stmtProd->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            redirect_with($URL . 'ventas/nueva', 'Producto no existe', "El producto (ID: {$id_producto}) no existe.", 'warning');
        }

        if ($row['stock'] < $cantidad) {
            redirect_with($URL . 'ventas/nueva', 'Stock insuficiente', "Stock insuficiente para el producto: {$row['nombre_producto']} (Disponible: {$row['stock']}).", 'warning');
        }

        // Guardar info útil
        $productos_check[] = [
            'id_producto' => $id_producto,
            'cantidad' => $cantidad,
            'precio_unitario' => isset($p['precio_unitario']) ? floatval($p['precio_unitario']) : floatval($row['precio']),
            'descuento' => isset($p['descuento']) ? floatval($p['descuento']) : 0.00,
            'stock_actual' => intval($row['stock'])
        ];
    }

    // 3) Todo validado -> Iniciar transacción
    $pdo->beginTransaction();

    // Generar numero_comprobante único si es necesario
    // Si en el formulario se hubiera enviado numero_comprobante usarlo (no lo tenías), aquí genero uno:
    $numero_comprobante = 'V' . date('YmdHis') . rand(100, 999);

    // 4) Insertar en tabla venta
    $sqlVenta = "INSERT INTO venta (fecha_y_hora, total, id_cliente, id_usuario, condicion_pago, efectivo_recibido, cambio, numero_comprobante, id_caja)
                 VALUES (NOW(), :total, :id_cliente, :id_usuario, :condicion_pago, :efectivo_recibido, :cambio, :numero_comprobante, :id_caja)";
    $stmtVenta = $pdo->prepare($sqlVenta);
    $stmtVenta->execute([
        ':total' => $total,
        ':id_cliente' => $id_cliente,
        ':id_usuario' => $id_usuario,
        ':condicion_pago' => $condicion_pago,
        ':efectivo_recibido' => $efectivo_recibido,
        ':cambio' => $cambio,
        ':numero_comprobante' => $numero_comprobante,
        ':id_caja' => $id_caja
    ]);
    $id_venta = $pdo->lastInsertId();

    if (!$id_venta) {
        // Algo salió mal
        $pdo->rollBack();
        redirect_with($URL . 'ventas/nueva', 'Error', 'No se pudo guardar la venta (error en insert venta).', 'error');
    }

    // 5) Insertar cada detalle y actualizar stock
    $sqlDetalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, descuento, total_linea)
                   VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario, :descuento, :total_linea)";
    $stmtDetalle = $pdo->prepare($sqlDetalle);

    $sqlUpdateStock = "UPDATE producto SET stock = stock - :cantidad WHERE id_producto = :id_producto";
    $stmtUpdateStock = $pdo->prepare($sqlUpdateStock);

    foreach ($productos_check as $item) {
        $total_linea = ($item['precio_unitario'] - $item['descuento']) * $item['cantidad'];
        if ($total_linea < 0) $total_linea = 0;

        $stmtDetalle->execute([
            ':id_venta' => $id_venta,
            ':id_producto' => $item['id_producto'],
            ':cantidad' => $item['cantidad'],
            ':precio_unitario' => $item['precio_unitario'],
            ':descuento' => $item['descuento'],
            ':total_linea' => $total_linea
        ]);

        // Reducir stock
        $stmtUpdateStock->execute([
            ':cantidad' => $item['cantidad'],
            ':id_producto' => $item['id_producto']
        ]);

        // Verificar que stock no quedó negativo (consulta rápida)
        $stmtCheck = $pdo->prepare("SELECT stock FROM producto WHERE id_producto = :id_producto LIMIT 1");
        $stmtCheck->execute([':id_producto' => $item['id_producto']]);
        $rowAfter = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        if ($rowAfter === false || intval($rowAfter['stock']) < 0) {
            $pdo->rollBack();
            redirect_with($URL . 'ventas/nueva', 'Stock inválido', 'Error al actualizar stock para un producto. Operación cancelada.', 'error');
        }
    }

    // 6) Actualizar caja e insertar historial_caja si procede
    // Definimos monto_ingreso: para 'Contado' sumamos el efectivo recibido; para 'Crédito' no sumamos
    $monto_ingreso = 0.00;
    if (strtolower($condicion_pago) === 'contado' || $condicion_pago === 'Contado') {
        // Si efectivo_recibido es 0 pero total > 0, podríamos decidir sumar total; aquí sumamos efectivo_recibido
        $monto_ingreso = round($efectivo_recibido, 2);
    }

    if ($monto_ingreso > 0) {
        // Actualizar monto_actual en tabla caja
        $stmtUpdCaja = $pdo->prepare("UPDATE caja SET monto_actual = monto_actual + :monto WHERE id_caja = :id_caja");
        $stmtUpdCaja->execute([
            ':monto' => $monto_ingreso,
            ':id_caja' => $id_caja
        ]);

        // Insertar historial_caja
        $sqlHist = "INSERT INTO historial_caja (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, numero_comprobante, tabla_origen, id_usuario)
                    VALUES (:id_caja, 'ingreso', :monto, :descripcion, NOW(), :numero_comprobante, 'Venta', :id_usuario)";
        $stmtHist = $pdo->prepare($sqlHist);
        $desc = "Ingreso por venta ID: {$id_venta}";
        $stmtHist->execute([
            ':id_caja' => $id_caja,
            ':monto' => $monto_ingreso,
            ':descripcion' => $desc,
            ':numero_comprobante' => $numero_comprobante,
            ':id_usuario' => $id_usuario
        ]);
    }

    // 7) Commit
    $pdo->commit();

    // Mensaje éxito
    $_SESSION['titulo']  = 'Venta registrada';
    $_SESSION['mensaje'] = "Venta guardada correctamente. Comprobante: {$numero_comprobante}";
    $_SESSION['icono']   = 'success';

    // Aquí puedes redirigir a la vista de historial o imprimir comprobante
    header('Location: ' . $URL . 'ventas/nueva');
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Loguear error si quieres (error_log o tabla de logs)
    error_log("Error generar_Ventas.php: " . $e->getMessage());

    redirect_with($URL . 'ventas/nueva', 'Error al procesar', 'Ocurrió un error al procesar la venta: ' . $e->getMessage(), 'error');
}
