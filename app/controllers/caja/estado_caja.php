<?php
# app/controllers/caja/estado_caja.php

require_once '../../conexionBD.php';

header('Content-Type: application/json');

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Validar sesión */
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode([
        'estado' => 'ERROR',
        'mensaje' => 'Sesión no válida'
    ]);
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];

try {

    /* Buscar caja abierta del usuario */
    $sql = "
        SELECT 
            c.fecha_apertura,
            c.monto_actual,
            u.nombre AS usuario
        FROM caja c
        INNER JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios
        WHERE c.estado = 'abierta'
          AND c.id_Usuarios = :idUsuario
        ORDER BY c.id_caja DESC
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
    $stmt->execute();

    $caja = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($caja) {
        echo json_encode([
            'estado' => 'ABIERTA',
            'usuario' => $caja['usuario'],
            'fecha_apertura' => $caja['fecha_apertura'],
            'monto_actual' => number_format((float)$caja['monto_actual'], 2, '.', '')
        ]);
    } else {
        echo json_encode([
            'estado' => 'CERRADA'
        ]);
    }

} catch (PDOException $e) {

    echo json_encode([
        'estado' => 'ERROR',
        'mensaje' => 'Error al consultar la caja'
    ]);
}
