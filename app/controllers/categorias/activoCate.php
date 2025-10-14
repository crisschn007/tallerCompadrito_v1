<?php
// app/controllers/categorias/activoCate.php

require_once __DIR__ . '/../../conexionBD.php';

// Consulta para roles activos
$sql_listCategoriaActivo = "SELECT id_categoria, nombre FROM categoria WHERE estado='Activo';";

$categoria_Activos = []; // Inicializa variable para evitar errores si falla la consulta

try {
    $stmt = $pdo->prepare($sql_listCategoriaActivo);
    $stmt->execute();
    $categoria_Activos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En producción evita mostrar errores directamente al usuario
    error_log("Error al consultar roles: " . $e->getMessage());
}
