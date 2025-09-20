<?php
// Incluye el archivo de conexión con ruta absoluta
include __DIR__ . '/../../conexionBD.php';

$sql_clientes = "SELECT * FROM cliente;";
$query_clientes = $pdo->prepare($sql_clientes);
$query_clientes->execute();
$clientes_datos = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
?>
