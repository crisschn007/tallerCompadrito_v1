<?php
// app/controllers/roles/listado_activo.php

require_once __DIR__ . '/../../conexionBD.php';

// Consulta para roles activos
$sql_listProveedoresActivo = "SELECT id_proveedor, nombre_empresa , condicion_pago FROM proveedor WHERE estado ='Activo';";

$proveedores_activos = []; // Inicializa variable para evitar errores si falla la consulta

try {
    $stmt = $pdo->prepare($sql_listProveedoresActivo);
    $stmt->execute();
    $proveedores_activos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción evita mostrar errores directamente al usuario
    error_log("Error al consultar roles: " . $e->getMessage());
}
