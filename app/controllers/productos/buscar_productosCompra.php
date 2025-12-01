<?php
# app/controllers/productos/buscar_productosCompra.php

include __DIR__ . '/../../conexionBD.php';

$term = $_GET['term'] ?? '';
$term = trim($term);

$result = [];

try {
    if (!empty($term)) {

        $sql = "SELECT id_producto, nombre_producto, codigo_barras, stock
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
                'stock' => $p['stock']
            ];
        }
    }

} catch (PDOException $e) {
    $result = ['error' => $e->getMessage()];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
exit;
