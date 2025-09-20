<?php
// app/controllers/roles/listado_activo.php

require_once __DIR__ . '/../../conexionBD.php';

// Consulta para roles activos
$sql_listRolesActivo = "SELECT id_roles, nombre_roles FROM roles WHERE estado = 'Activo';";

$roles_activos = []; // Inicializa variable para evitar errores si falla la consulta

try {
    $stmt = $pdo->prepare($sql_listRolesActivo);
    $stmt->execute();
    $roles_activos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción evita mostrar errores directamente al usuario
    error_log("Error al consultar roles: " . $e->getMessage());
}
