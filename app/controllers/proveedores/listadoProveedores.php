<?php
include __DIR__ . '/../../conexionBD.php';

$sql_proveedoir = " SELECT id_proveedor,nombre_empresa, representante, direccion, telefono, email, sitio_web, estado, condicion_pago FROM proveedor;";
$query_proveedor = $pdo->prepare($sql_proveedoir);
$query_proveedor->execute();
$proveedor_datos= $query_proveedor->fetchAll(PDO::FETCH_ASSOC);
