<?php
include '../../conexionBD.php';
session_start();

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
  echo json_encode(['estado' => 'ERROR', 'mensaje' => 'Usuario no autenticado']);
  exit;
}

$sql = "SELECT c.*, u.nombre AS usuario
        FROM Caja c
        INNER JOIN Usuarios u ON c.id_usuario = u.id_Usuarios
        WHERE c.estado = 'abierta' AND c.id_usuario = :id_usuario
        ORDER BY c.id_caja DESC LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

$caja = $stmt->fetch(PDO::FETCH_ASSOC);

if ($caja) {
  echo json_encode([
    'estado' => 'ABIERTA',
    'usuario' => $caja['usuario'],
    'fecha_apertura' => $caja['fecha_apertura'],
    'monto_actual' => number_format($caja['monto_actual'], 2)
  ]);
} else {
  echo json_encode(['estado' => 'CERRADA']);
}
?>
