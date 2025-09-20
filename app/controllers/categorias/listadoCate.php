<?php
// Incluye el archivo de conexión con ruta absoluta
include __DIR__ . '/../../conexionBD.php';

$sql_Categoria = "SELECT * FROM Categoria";
$query_categoria = $pdo->prepare($sql_Categoria);
$query_categoria->execute();
$categoria_datos = $query_categoria->fetchAll(PDO::FETCH_ASSOC);
?>
