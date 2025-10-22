<?php
# app/controllers/productos/buscar_productos.php

// Conexión a la BD
include __DIR__ . '/../../conexionBD.php'; // ruta corregida

$term = $_GET['term'] ?? '';
$term = trim($term);

$result = [];

try {
    if (!empty($term)) {
        $sql = "SELECT id_producto, nombre_producto, codigo_barras, precio, stock
                FROM producto
                WHERE nombre_producto LIKE :term OR codigo_barras LIKE :term
                ORDER BY nombre_producto ASC
                LIMIT 20";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':term' => "%$term%"]);
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($productos as $p) {
            $result[] = [
                'id' => $p['id_producto'],
                'text' => $p['nombre_producto'],
                'codigo' => $p['codigo_barras'],
                'precio' => $p['precio'],
                'stock' => $p['stock']
            ];
        }
    }
} catch (PDOException $e) {
    // Solo para depuración, no imprimir HTML
    $result = ['error' => $e->getMessage()];
}

// Retornar JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
exit; // siempre mejor terminar aquí
