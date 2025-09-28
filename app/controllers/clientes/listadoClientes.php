<?php
include __DIR__ . '/../../conexionBD.php';

$sql_clientes = "SELECT
    id_cliente,
    nombre_y_apellido,
    direccion,
    telefono,
    email,
    cui,
    genero,
    estado
FROM cliente";
$query_clientes = $pdo->prepare($sql_clientes);
$query_clientes->execute();
$clientes_datos = $query_clientes->fetchAll(PDO::FETCH_ASSOC);
