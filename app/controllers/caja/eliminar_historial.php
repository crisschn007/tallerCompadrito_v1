<?php
session_start();

// Ajustar rutas según la ubicación real
include '../../../app/conexionBD.php';
include '../../../layouts/sesion.php'; // Para validar sesión si es necesario

$URL = "http://localhost/tallerCompadrito_v1/";

// Obtener ID del historial
$id_historial = $_GET['id'] ?? null;

if ($id_historial) {
    // Eliminar fila del historial
    $stmt = $pdo->prepare("DELETE FROM historial_caja WHERE id_historial = :id");
    $stmt->bindParam(":id", $id_historial, PDO::PARAM_INT);
    $stmt->execute();

    // Notificación de éxito
    $_SESSION['titulo']  = '¡Movimiento eliminado!';
    $_SESSION['mensaje'] = 'El registro del historial se eliminó correctamente.';
    $_SESSION['icono']   = 'success';
} else {
    // Notificación de error
    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = 'No se pudo identificar el movimiento.';
    $_SESSION['icono']   = 'error';
}

// Redirigir al historial
header('Location: ' . $URL . 'caja/historial');
exit;
