<?php
include '../../conexionBD.php';
header('Content-Type: application/json');

if (!isset($_POST['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID no proporcionado']);
    exit;
}

$id = $_POST['id'];

try {
    // Primero eliminamos los detalles de la cotización
    $stmt1 = $pdo->prepare("DELETE FROM Detalle_Cotizacion WHERE id_Cotizacion = ?");
    $stmt1->execute([$id]);

    // Luego eliminamos la cotización principal
    $stmt2 = $pdo->prepare("DELETE FROM Cotizacion WHERE id_Cotizacion = ?");
    $stmt2->execute([$id]);

    if ($stmt2->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Cotización eliminada correctamente.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se encontró la cotización.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar: ' . $e->getMessage()]);
}
