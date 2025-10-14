<?php
// ==========================================
// Controlador: Agregar nuevo producto
// ==========================================

include '../../conexionBD.php';
require_once '../../../vendor/autoload.php'; // Librería Picqer
use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();

try {
    // ==========================================
    // 1️⃣ Validar y obtener datos del formulario
    // ==========================================
    $nombre_producto = trim($_POST['nombre_producto'] ?? '');
    $descripcion     = trim($_POST['descripcion'] ?? '');
    $stock           = intval($_POST['stock'] ?? 0);
    $precio          = floatval($_POST['precio'] ?? 0.00);
    $id_categoria    = intval($_POST['id_categoria'] ?? 0);
    $codigo_barras   = trim($_POST['codigo_barras'] ?? '');

    // Validación de campos obligatorios
    if ($nombre_producto === '' || $descripcion === '' || $stock <= 0 || $id_categoria <= 0) {
        throw new Exception("Por favor completa todos los campos obligatorios correctamente.");
    }

    // Generar código de barras automático si no se ingresó
    if (empty($codigo_barras)) {
        $codigo_barras = uniqid("P-");
    }

    // Validar que el código de barras no exista ya
    $verificar = $pdo->prepare("SELECT COUNT(*) FROM Producto WHERE codigo_barras = ?");
    $verificar->execute([$codigo_barras]);
    if ($verificar->fetchColumn() > 0) {
        throw new Exception("El código de barras '$codigo_barras' ya existe. Intenta con otro.");
    }

    // ==========================================
    // 2️⃣ Manejo de la imagen del producto
    // ==========================================
    $nombre_archivo = null;

    if (!empty($_FILES['imagen']['name'])) {
        $carpeta = __DIR__ . '/../../../img/productos/';
        $maxSize = 2 * 1024 * 1024; // 2MB
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        $nombreOriginal = basename($_FILES['imagen']['name']);
        $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        $tamanoArchivo = $_FILES['imagen']['size'];
        $tmpArchivo = $_FILES['imagen']['tmp_name'];

        if (!in_array($extension, $extensionesPermitidas)) {
            throw new Exception("Formato de imagen no permitido. Solo JPG, PNG o WEBP.");
        }

        if ($tamanoArchivo > $maxSize) {
            throw new Exception("La imagen excede el tamaño máximo permitido (2MB).");
        }

        $nombre_archivo = uniqid('IMG_') . "." . $extension;
        $ruta_fisica = $carpeta . $nombre_archivo;

        if (!move_uploaded_file($tmpArchivo, $ruta_fisica)) {
            throw new Exception("Error al subir la imagen del producto.");
        }
    }

    // ==========================================
    // 3️⃣ Insertar los datos en la base de datos
    // ==========================================
    $sql = "INSERT INTO Producto (codigo_barras, nombre_producto, descripcion, stock, imagen, id_categoria, precio)
            VALUES (:codigo_barras, :nombre_producto, :descripcion, :stock, :imagen, :id_categoria, :precio)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':codigo_barras', $codigo_barras);
    $stmt->bindParam(':nombre_producto', $nombre_producto);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindParam(':imagen', $nombre_archivo);
    $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
    $stmt->bindParam(':precio', $precio);
    $stmt->execute();

    // ==========================================
    // 4️⃣ Generar código de barras con Picqer
    // ==========================================
    $carpeta_barcode = __DIR__ . '/../../../img/barcodes/';
    if (!file_exists($carpeta_barcode)) {
        mkdir($carpeta_barcode, 0777, true);
    }

    $generator = new BarcodeGeneratorPNG();
    $barcodeData = $generator->getBarcode($codigo_barras, $generator::TYPE_CODE_128);
    file_put_contents($carpeta_barcode . $codigo_barras . '.png', $barcodeData);

    // ==========================================
    // ✅ Notificación de éxito
    // ==========================================
    $_SESSION['titulo'] = "Producto agregado";
    $_SESSION['mensaje'] = "El producto se registró correctamente con su código de barras.";
    $_SESSION['icono']   = "success";

} catch (Exception $e) {
    // ==========================================
    // ⚠️ Manejo de errores
    // ==========================================
    $_SESSION['titulo'] = "Error";
    $_SESSION['mensaje'] = "No se pudo registrar el producto: " . $e->getMessage();
    $_SESSION['icono']   = "error";
}

// ==========================================
// 🔁 Redirigir al listado de productos
// ==========================================
header('Location: ' . $URL . 'productos/');
exit;
?>
