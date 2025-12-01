<?php
// ruta: app/controllers/compras/eliminar_compra.php
include '../../conexionBD.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['titulo'] = 'Error';
    $_SESSION['mensaje'] = 'ID de compra no especificado.';
    $_SESSION['icono'] = 'error';
    header('Location: ' . $URL . 'compras/historial');
    exit;
}

try {
    // Usar transacción
    $pdo->beginTransaction();

    // Eliminar detalles
    $stmt = $pdo->prepare("DELETE FROM detalle_compra WHERE id_compra = :id");
    $stmt->execute([':id' => $id]);

    // Eliminar compra
    $stmt2 = $pdo->prepare("DELETE FROM compra WHERE id_Compra = :id");
    $stmt2->execute([':id' => $id]);

    $pdo->commit();

    $_SESSION['titulo'] = 'Eliminado';
    $_SESSION['mensaje'] = 'Compra eliminada correctamente.';
    $_SESSION['icono'] = 'success';
    header('Location: ' . $URL . 'compras/historial');
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['titulo'] = 'Error';
    $_SESSION['mensaje'] = 'No se pudo eliminar la compra: ' . $e->getMessage();
    $_SESSION['icono'] = 'error';
    header('Location: ' . $URL . 'compras/historial');
    exit;
}
