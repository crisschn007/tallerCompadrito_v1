<?php
// ==========================================
// Controlador: Editar producto
// ==========================================

include '../../conexionBD.php';
require_once '../../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();

try {
    // ==========================================
    // 1️⃣ Obtener datos del formulario
    // ==========================================
    $id_producto       = intval($_POST['id_producto'] ?? 0);
    $nombre_producto   = trim($_POST['nombre_producto'] ?? '');
    $descripcion       = trim($_POST['descripcion'] ?? '');
    $stock             = intval($_POST['stock'] ?? 0);
    $precio_compra     = floatval($_POST['precio_compra'] ?? 0.00);
    $precio            = floatval($_POST['precio'] ?? 0.00);
    $precio_mayorista  = floatval($_POST['precio_mayorista'] ?? 0.00);
    $id_categoria      = intval($_POST['id_categoria'] ?? 0);
    $codigo_barras     = trim($_POST['codigo_barras'] ?? '');

    if (
        $id_producto <= 0 ||
        $nombre_producto === '' ||
        $descripcion === '' ||
        $stock <= 0 ||
        $id_categoria <= 0 ||
        $precio_compra < 0 ||
        $precio < 0 ||
        $precio_mayorista < 0
    ) {
        throw new Exception("Por favor completa todos los campos obligatorios correctamente.");
    }

    // ==========================================
    // 2️⃣ Obtener datos actuales del producto
    // ==========================================
    $query = $pdo->prepare("SELECT codigo_barras, imagen FROM producto WHERE id_producto = :id");
    $query->execute([':id' => $id_producto]);
    $productoActual = $query->fetch(PDO::FETCH_ASSOC);

    if (!$productoActual) {
        throw new Exception("El producto no existe.");
    }

    $codigoActual = $productoActual['codigo_barras'];
    $imagenActual = $productoActual['imagen'];
    $nombre_imagen = $imagenActual;

    // ==========================================
    // 3️⃣ Manejo de imagen
    // ==========================================
    if (!empty($_FILES['imagen']['name'])) {
        $carpeta = __DIR__ . '/../../../img/productos/';
        $extPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $maxSize = 2 * 1024 * 1024;

        if (!file_exists($carpeta)) mkdir($carpeta, 0777, true);

        $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $tamanoArchivo = $_FILES['imagen']['size'];
        $tmpArchivo = $_FILES['imagen']['tmp_name'];

        if (!in_array($extension, $extPermitidas)) {
            throw new Exception("Formato de imagen no permitido.");
        }

        if ($tamanoArchivo > $maxSize) {
            throw new Exception("La imagen excede 2MB.");
        }

        $nombre_imagen = uniqid('IMG_') . '.' . $extension;

        if (!move_uploaded_file($tmpArchivo, $carpeta . $nombre_imagen)) {
            throw new Exception("Error al subir la imagen.");
        }

        if ($imagenActual && file_exists($carpeta . $imagenActual) && $imagenActual !== 'default.png') {
            unlink($carpeta . $imagenActual);
        }
    }

    // ==========================================
    // 4️⃣ Código de barras
    // ==========================================
    if ($codigo_barras === '') {
        $codigo_barras = uniqid('P-');
    }

    if ($codigo_barras !== $codigoActual) {
        $carpeta_barcode = __DIR__ . '/../../../img/barcodes/';
        if (!file_exists($carpeta_barcode)) mkdir($carpeta_barcode, 0777, true);

        $generator = new BarcodeGeneratorPNG();
        $barcodeData = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);
        file_put_contents($carpeta_barcode . $codigo_barras . '.png', $barcodeData);

        if ($codigoActual && file_exists($carpeta_barcode . $codigoActual . '.png')) {
            unlink($carpeta_barcode . $codigoActual . '.png');
        }
    }

    // ==========================================
    // 5️⃣ Actualizar producto
    // ==========================================
    $sql = "UPDATE producto SET
                codigo_barras = :codigo_barras,
                nombre_producto = :nombre_producto,
                descripcion = :descripcion,
                stock = :stock,
                precio_compra = :precio_compra,
                precio = :precio,
                precio_mayorista = :precio_mayorista,
                imagen = :imagen,
                id_categoria = :id_categoria
            WHERE id_producto = :id_producto";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':codigo_barras'    => $codigo_barras,
        ':nombre_producto'  => $nombre_producto,
        ':descripcion'      => $descripcion,
        ':stock'            => $stock,
        ':precio_compra'    => $precio_compra,
        ':precio'           => $precio,
        ':precio_mayorista' => $precio_mayorista,
        ':imagen'           => $nombre_imagen,
        ':id_categoria'     => $id_categoria,
        ':id_producto'      => $id_producto
    ]);

    // ==========================================
    // ✅ Mensaje éxito
    // ==========================================
    $_SESSION['titulo']  = "Producto actualizado";
    $_SESSION['mensaje'] = "El producto se editó correctamente.";
    $_SESSION['icono']   = "success";
} catch (Exception $e) {

    $_SESSION['titulo']  = "Error";
    $_SESSION['mensaje'] = "No se pudo actualizar el producto: " . $e->getMessage();
    $_SESSION['icono']   = "error";
}

header('Location: ' . $URL . 'productos/');
exit;
