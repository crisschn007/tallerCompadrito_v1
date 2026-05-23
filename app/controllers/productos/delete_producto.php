<?php
session_start();
require_once '../../conexionBD.php'; // Ruta correcta a tu conexión PDO

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // 🔹 Obtener datos del producto antes de eliminarlo (para saber qué archivos borrar)
        $stmt = $pdo->prepare("SELECT imagen, codigo_barras FROM producto WHERE id_producto = ?");
        $stmt->execute([$id]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($producto) {
            // 🔹 Rutas de carpetas
            $carpeta_img = __DIR__ . '/../../../img/productos/';
            $carpeta_barcode = __DIR__ . '/../../../img/barcodes/';

            // 🔹 Eliminar imagen del producto (si existe)
            if (!empty($producto['imagen'])) {
                $ruta_imagen = $carpeta_img . $producto['imagen'];
                if (file_exists($ruta_imagen)) {
                    unlink($ruta_imagen);
                }
            }

            // 🔹 Eliminar código de barras (si existe)
            if (!empty($producto['codigo_barras'])) {
                $ruta_barcode = $carpeta_barcode . 'barcode_' . $id . '.png';
                if (file_exists($ruta_barcode)) {
                    unlink($ruta_barcode);
                }
            }

            // 🔹 Eliminar el producto de la base de datos
            $query = $pdo->prepare("DELETE FROM producto WHERE id_producto = ?");
            $query->execute([$id]);

            // 🔹 Mensaje de éxito
            $_SESSION['mensaje_titulo'] = "Producto eliminado";
            $_SESSION['mensaje_texto'] = "El producto y sus archivos asociados fueron eliminados correctamente.";
            $_SESSION['mensaje_icono'] = "success";
        } else {
            // 🔹 Producto no encontrado
            $_SESSION['mensaje_titulo'] = "Error";
            $_SESSION['mensaje_texto'] = "El producto no existe o ya fue eliminado.";
            $_SESSION['mensaje_icono'] = "warning";
        }
    } catch (PDOException $e) {
        // 🔹 Error en la base de datos
        $_SESSION['mensaje_titulo'] = "Error en la base de datos";
        $_SESSION['mensaje_texto'] = "No se pudo eliminar el producto: " . $e->getMessage();
        $_SESSION['mensaje_icono'] = "error";
    }
} else {
    // 🔹 Si no se recibe el ID
    $_SESSION['mensaje_titulo'] = "Error";
    $_SESSION['mensaje_texto'] = "No se recibió un ID válido para eliminar.";
    $_SESSION['mensaje_icono'] = "error";
}

// 🔹 Redirigir al módulo de productos
header('Location: ' . $URL . 'productos/');
exit;
