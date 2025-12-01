<?php
require_once '../../conexionBD.php';

header('Content-Type: application/json');

if (isset($_POST['id_cotizacion'])) {
    $id_cotizacion = intval($_POST['id_cotizacion']);

    try {
        // 🔹 Consulta principal
        $sql = "SELECT c.id_Cotizacion, c.fecha, c.total, c.estado, c.condicion_pago,
                       cli.nombre_y_apellido AS cliente_nombre,
                       u.nombre_usuario AS usuario
                FROM Cotizacion c
                INNER JOIN Cliente cli ON c.id_cliente = cli.id_cliente
                INNER JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios
                WHERE c.id_Cotizacion = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id_cotizacion]);
        $cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cotizacion) {
            echo json_encode(["status" => "error", "message" => "Cotización no encontrada."]);
            exit;
        }

        // 🔹 Consulta detalle con productos
        $sql_detalle = "SELECT dc.cantidad, dc.precio_unitario, p.nombre_producto
                        FROM Detalle_Cotizacion dc
                        INNER JOIN Producto p ON dc.id_producto = p.id_producto
                        WHERE dc.id_Cotizacion = :id";
        $stmt_det = $pdo->prepare($sql_detalle);
        $stmt_det->execute([':id' => $id_cotizacion]);
        $detalles = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

        // 🔹 Contar productos
        $cotizacion['total_articulos'] = array_sum(array_column($detalles, 'cantidad'));
        $cotizacion['detalle'] = $detalles;

        echo json_encode(["status" => "success", "data" => $cotizacion]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID no recibido."]);
}
?>
