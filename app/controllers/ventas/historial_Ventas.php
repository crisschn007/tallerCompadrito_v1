<?php
include '../../conexionBD.php';
session_start();

$stmt = $pdo->query("
    SELECT 
        v.id_venta,
        v.fecha_y_hora,
        v.numero_comprobante,
        v.total,
        v.condicion_pago,
        v.efectivo_recibido,
        v.cambio,
        c.id_caja,
        c.monto_actual,
        c.estado AS estado_caja,
        cli.nombre_y_apellido AS cliente,
        u.nombre AS usuario
    FROM venta v
    INNER JOIN cliente cli ON cli.id_cliente = v.id_cliente
    INNER JOIN usuarios u ON u.id_Usuarios = v.id_usuario
    LEFT JOIN caja c ON c.id_caja = v.id_caja
    ORDER BY v.id_venta DESC
");

$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../../../ventas/historial/index.php';
