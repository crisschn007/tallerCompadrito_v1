<?php
/* app/controllers/productos/listado_productos.php */

try {
    $sql_productos = "SELECT 
        p.id_producto AS id_producto,
        p.codigo_barras,
        p.imagen,
        p.nombre_producto,
        p.descripcion,
        p.stock,
        p.precio,
        p.id_categoria,        
        c.nombre AS categoria
    FROM Producto p
    LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
    ORDER BY p.id_producto ASC";

    $query_producto = $pdo->prepare($sql_productos);
    $query_producto->execute();
    $producto_datos = $query_producto->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al Consultar Productos: " . $e->getMessage();
}
?>
