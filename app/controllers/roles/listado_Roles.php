<?php
// Incluye el archivo de conexión con ruta absoluta
include __DIR__ . '/../../conexionBD.php';

$sql_roles = "SELECT * FROM roles";
$query_roles = $pdo->prepare($sql_roles);
$query_roles->execute();
$roles_datos = $query_roles->fetchAll(PDO::FETCH_ASSOC);
?>
