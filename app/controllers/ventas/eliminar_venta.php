<?php
require __DIR__ . '/../../conexionBD.php';
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['mensaje'] = "ID de venta no válido.";
    $_SESSION['icono'] = "error";
    header('Location: ' . $URL . '/ventas/historial');
    exit;
}

try {

    // INICIAR TRANSACCIÓN
    $pdo->beginTransaction();

    // 1. ELIMINAR DETALLES DE LA VENTA
    $sqlDetalle = "DELETE FROM detalle_venta WHERE id_venta = :id";
    $stmtDetalle = $pdo->prepare($sqlDetalle);
    $stmtDetalle->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtDetalle->execute();

    // 2. ELIMINAR LA VENTA
    $sqlVenta = "DELETE FROM venta WHERE id_venta = :id";
    $stmtVenta = $pdo->prepare($sqlVenta);
    $stmtVenta->bindParam(':id', $id, PDO::PARAM_INT);
    $stmtVenta->execute();

    // CONFIRMAR CAMBIOS
    $pdo->commit();

    $_SESSION['titulo']  = '¡Bien Hecho!';
    $_SESSION['mensaje'] = "Cliente eliminado correctamente";
    $_SESSION['icono'] = "success";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['titulo']  = '¡Error!';
    $_SESSION['mensaje'] = "Error al eliminar cliente";
    $_SESSION['icono'] = "error";
}


header('Location: ' . $URL . '/ventas/historial');
exit;
