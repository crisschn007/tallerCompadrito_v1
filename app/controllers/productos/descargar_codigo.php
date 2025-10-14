<?php
// descargar_codigo_termica.php
require_once '../../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_GET['descargar_codigo'], $_GET['codigo'])) {
    $codigo = $_GET['codigo'];

    // Generar código de barras en PNG
    $generator = new BarcodeGeneratorPNG();
    $barcodeData = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

    // Crear imagen desde el PNG generado
    $barcodeImage = imagecreatefromstring($barcodeData);
    if (!$barcodeImage) {
        exit("Error al generar el código de barras.");
    }

    $barcodeWidth  = imagesx($barcodeImage);
    $barcodeHeight = imagesy($barcodeImage);

    // Escalar el código de barras
    $scale = 2; // Ajusta según el ancho físico deseado
    $scaledWidth  = $barcodeWidth * $scale;
    $scaledHeight = $barcodeHeight * $scale;

    // Altura extra para el texto (opcional)
    $textHeight = 20;
    $finalImage = imagecreatetruecolor($scaledWidth, $scaledHeight + $textHeight);

    // Colores
    $white = imagecolorallocate($finalImage, 255, 255, 255);
    $black = imagecolorallocate($finalImage, 0, 0, 0);

    // Fondo blanco
    imagefilledrectangle($finalImage, 0, 0, $scaledWidth, $scaledHeight + $textHeight, $white);

    // Redimensionar y copiar código de barras
    imagecopyresampled(
        $finalImage,
        $barcodeImage,
        0,
        0,
        0,
        0,
        $scaledWidth,
        $scaledHeight,
        $barcodeWidth,
        $barcodeHeight
    );

    // Convertir a blanco y negro puro (opcional para térmica)
    for ($x = 0; $x < $scaledWidth; $x++) {
        for ($y = 0; $y < $scaledHeight + $textHeight; $y++) {
            $rgb = imagecolorat($finalImage, $x, $y);
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;
            $gray = ($r + $g + $b) / 3;
            $color = ($gray > 127) ? $white : $black;
            imagesetpixel($finalImage, $x, $y, $color);
        }
    }

    // Escribir código debajo, centrado (opcional)
    $showText = true; // Cambiar a false para omitir texto
    if ($showText) {
        $fontHeight = 5;
        $textWidth  = imagefontwidth($fontHeight) * strlen($codigo);
        $x = ($scaledWidth - $textWidth) / 2;
        $y = $scaledHeight + 2; // margen
        imagestring($finalImage, $fontHeight, $x, $y, $codigo, $black);
    }

    // Forzar descarga
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="barcode_' . $codigo . '.png"');
    imagepng($finalImage);

    // Liberar memoria
    imagedestroy($barcodeImage);
    imagedestroy($finalImage);
    exit;
} else {
    echo "Parámetros inválidos.";
    exit;
}
