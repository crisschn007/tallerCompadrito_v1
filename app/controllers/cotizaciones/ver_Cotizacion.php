<?php
require_once '../../conexionBD.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['id_cotizacion'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID no recibido.'
    ]);
    exit;
}

$id_cotizacion = (int) $_POST['id_cotizacion'];

try {

    // 🔹 Cabecera de la cotización
    $sql = "
        SELECT c.id_Cotizacion,
               c.fecha,
               c.total,
               c.estado,
               c.condicion_pago,
               cli.nombre_y_apellido AS cliente_nombre,
               u.nombre AS usuario
        FROM cotizacion c
        INNER JOIN cliente cli ON c.id_cliente = cli.id_cliente
        INNER JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios
        WHERE c.id_Cotizacion = :id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_cotizacion]);
    $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cotizacion) {
        throw new Exception('Cotización no encontrada.');
    }

    // 🔹 Detalle (incluye tipo_precio)
    $sqlDetalle = "
        SELECT dc.cantidad,
               dc.precio_unitario,
               dc.tipo_precio,
               p.nombre_producto
        FROM detalle_cotizacion dc
        INNER JOIN producto p ON dc.id_producto = p.id_producto
        WHERE dc.id_Cotizacion = :id
    ";

    $stmtDet = $pdo->prepare($sqlDetalle);
    $stmtDet->execute([':id' => $id_cotizacion]);
    $detalle = $stmtDet->fetchAll(PDO::FETCH_ASSOC);

    if (!$detalle) {
        throw new Exception('La cotización no tiene productos.');
    }

    // 🔹 Totales
    $total_articulos = 0;
    foreach ($detalle as $d) {
        $total_articulos += (int) $d['cantidad'];
    }

    // 🔹 Tipo de precio (todos los detalles comparten el mismo)
    $tipo_precio = $detalle[0]['tipo_precio'];

    echo json_encode([
        'status' => 'success',
        'data' => [
            'id_Cotizacion'    => $cotizacion['id_Cotizacion'],
            'cliente_nombre'   => $cotizacion['cliente_nombre'],
            'usuario'          => $cotizacion['usuario'],
            'condicion_pago'   => $cotizacion['condicion_pago'],
            'estado'           => $cotizacion['estado'],
            'fecha'            => $cotizacion['fecha'],
            'total'            => $cotizacion['total'],
            'tipo_precio'      => $tipo_precio,
            'total_articulos'  => $total_articulos,
            'detalle'          => $detalle
        ]
    ]);

} catch (Exception $e) {

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
