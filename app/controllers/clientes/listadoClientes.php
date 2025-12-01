<?php
// app/controllers/clientes/listadoClientes.php

include __DIR__ . '/../../conexionBD.php';

$total_clientes = 0; // evitar warnings si algo falla

try {
    // Obtener total de clientes
    $sql_count_clientes = "SELECT COUNT(*) AS totalCliente FROM cliente";
    $query_count = $pdo->prepare($sql_count_clientes);
    $query_count->execute();
    $res = $query_count->fetch(PDO::FETCH_ASSOC);
    if ($res && isset($res['totalCliente'])) {
        $total_clientes = (int)$res['totalCliente'];
    }

    // Obtener listado completo (si lo usas en otra vista)
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

} catch (PDOException $e) {
    error_log("Error al consultar clientes: " . $e->getMessage());
}
