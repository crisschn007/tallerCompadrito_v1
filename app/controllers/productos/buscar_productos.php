<?php
include __DIR__ . '/../../conexionBD.php';

$term = trim($_GET['term'] ?? '');
$result = [];

try {
    if ($term !== '') {

        $sql = "SELECT 
                    id_producto,
                    nombre_producto,
                    codigo_barras,
                    precio,
                    precio_mayorista,
                    stock
                FROM producto
                WHERE nombre_producto LIKE :term 
                   OR codigo_barras LIKE :term
                ORDER BY nombre_producto ASC
                LIMIT 20";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':term' => "%$term%"]);

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $result[] = [
                'id' => $p['id_producto'],
                'text' => $p['nombre_producto'],
                'codigo' => $p['codigo_barras'],
                'precio' => (float)$p['precio'],
                'precio_mayorista' => (float)$p['precio_mayorista'],
                'stock' => (int)$p['stock']
            ];
        }
    }
} catch (PDOException $e) {
    $result = [];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result);
exit;
