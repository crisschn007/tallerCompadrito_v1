<?php
// ==========================================
// Controlador: Editar producto
// ==========================================
include '../../conexionBD.php';
require_once '../../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();

try {
    // 🟦 1. Obtener datos del formulario
    $id_producto     = $_POST['id_producto'] ?? null;
    $nombre_producto = trim($_POST['nombre_producto'] ?? '');
    $descripcion     = trim($_POST['descripcion'] ?? '');
    $stock           = $_POST['stock'] ?? null;
    $precio          = $_POST['precio'] ?? 0.00;
    $id_categoria    = $_POST['id_categoria'] ?? null;
    $codigo_barras   = trim($_POST['codigo_barras'] ?? '');

    if (!$id_producto || empty($nombre_producto) || empty($descripcion) || empty($stock) || empty($id_categoria)) {
        throw new Exception("Por favor completa todos los campos obligatorios.");
    }

    // 🟦 2. Obtener los datos actuales del producto
    $query = $pdo->prepare("SELECT codigo_barras, imagen FROM producto WHERE id_producto = :id");
    $query->execute([':id' => $id_producto]);
    $productoActual = $query->fetch(PDO::FETCH_ASSOC);

    if (!$productoActual) {
        throw new Exception("El producto no existe.");
    }

    $codigoActual = $productoActual['codigo_barras'];
    $imagenActual = $productoActual['imagen'];
    $nombre_imagen = $imagenActual;

    // 🟦 3. Manejo de imagen (⚠️ el name debe ser 'imagen', no 'foto')
    if (!empty($_FILES['imagen']['name'])) {
        $carpeta = __DIR__ . '/../../../img/productos/';  //ruta de las imagenes guardadas de los productos
        $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        $nombreOriginal = basename($_FILES['imagen']['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $tamanoArchivo = $_FILES['imagen']['size'];
        $tmpArchivo = $_FILES['imagen']['tmp_name'];

        if (!in_array($extension, $extPermitidas)) {
            throw new Exception("Formato de imagen no permitido. Solo se permiten JPG, PNG o WEBP.");
        }

        if ($tamanoArchivo > $maxSize) {
            throw new Exception("La imagen excede el tamaño máximo permitido (2MB).");
        }

        // Crear nombre único para evitar conflictos
        $nombre_imagen = uniqid('IMG_') . '.' . $extension;

        // Subir imagen
        if (!move_uploaded_file($tmpArchivo, $carpeta . $nombre_imagen)) {
            throw new Exception("Error al subir la imagen.");
        }

        // Eliminar imagen anterior (excepto la default)
        if ($imagenActual && file_exists($carpeta . $imagenActual) && $imagenActual != 'default.png') {
            unlink($carpeta . $imagenActual);
        }
    } else {
        // Si no se sube nueva imagen, mantener la actual
        $nombre_imagen = $imagenActual;
    }


    // 🟦 4. Generar código de barras (si cambió o no existía)
    $carpeta_barcode = __DIR__ . '/../../../img/barcodes/';     //ruta de las imagenes guardadas de los codigos de barras
    if (!file_exists($carpeta_barcode)) mkdir($carpeta_barcode, 0777, true);

    if (empty($codigo_barras)) {
        $codigo_barras = uniqid("P-");
    }

    // Generar nueva imagen de código de barras si cambió
    if ($codigo_barras !== $codigoActual) {
        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);
        file_put_contents($carpeta_barcode . $codigo_barras . '.png', $barcodeData);

        // Eliminar el código anterior si existía
        if ($codigoActual && file_exists($carpeta_barcode . $codigoActual . '.png')) {
            unlink($carpeta_barcode . $codigoActual . '.png');
        }
    }

    // 🟦 5. Actualizar la base de datos
    $sql = "UPDATE producto SET 
                codigo_barras = :codigo_barras,
                nombre_producto = :nombre_producto,
                descripcion = :descripcion,
                stock = :stock,
                imagen = :imagen,
                id_categoria = :id_categoria,
                precio = :precio
            WHERE id_producto = :id_producto";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo_barras' => $codigo_barras,
        ':nombre_producto' => $nombre_producto,
        ':descripcion' => $descripcion,
        ':stock' => $stock,
        ':imagen' => $nombre_imagen,
        ':id_categoria' => $id_categoria,
        ':precio' => $precio,
        ':id_producto' => $id_producto
    ]);

    // 🟦 6. Mensajes de sesión para SweetAlert
    $_SESSION['titulo'] = "Producto actualizado";
    $_SESSION['mensaje'] = "El producto se editó correctamente.";
    $_SESSION['icono'] = "success";
} catch (Exception $e) {
    $_SESSION['titulo'] = "Error";
    $_SESSION['mensaje'] = "No se pudo actualizar el producto: " . $e->getMessage();
    $_SESSION['icono'] = "error";
}

// 🟦 7. Redirección al módulo de productos
header('Location: ' . $URL . 'productos/');
exit;
