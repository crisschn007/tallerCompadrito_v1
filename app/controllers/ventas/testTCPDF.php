<?php
require_once __DIR__ . '/../../../app/libraries/tcpdf/tcpdf.php';

// Librería de código de barras
require_once '../../../vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;



// -----------------------------
// Datos de ejemplo
// -----------------------------
$cliente = 'Juan Pérez';
$fecha = date('d/m/Y H:i:s');
$codigo = 'VENTA-006';
$productos = [
    ['cantidad' => 2, 'descripcion' => 'Aceite Motor 5W30', 'precio' => 75.50],
    ['cantidad' => 1, 'descripcion' => 'Filtro de aceite', 'precio' => 25.00],
    ['cantidad' => 3, 'descripcion' => 'Bujías', 'precio' => 15.00],
    ['cantidad' => 5, 'descripcion' => 'Líquido de frenos', 'precio' => 18.00],
];

// -----------------------------
// Generar código de barras
// -----------------------------
$generator = new BarcodeGeneratorPNG();
$barcodeData = $generator->getBarcode($codigo, $generator::TYPE_CODE_128);

$tmpDir = __DIR__ . '/../../../img/barcodes/';
if (!is_dir($tmpDir)) mkdir($tmpDir, 0777, true);
$tmpBarcode = $tmpDir . $codigo . '.png';
file_put_contents($tmpBarcode, $barcodeData);

// -----------------------------
// Crear PDF tamaño ticket ancho fijo 80 mm, altura dinámica
// -----------------------------
$pdf = new TCPDF('P', 'mm', [80, 200], true, 'UTF-8', false); // altura inicial arbitraria
$pdf->SetMargins(3, 3, 3);
$pdf->SetAutoPageBreak(false);
$pdf->AddPage();

// -----------------------------
// Encabezado
// -----------------------------
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 5, 'Taller Compadrito', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 4, 'San Pedro Sacatepéquez, Guatemala', 0, 1, 'C');
$pdf->Ln(2);
$pdf->Cell(0, 4, 'Fecha: ' . $fecha, 0, 1, 'L');
$pdf->Cell(0, 4, 'Cliente: ' . $cliente, 0, 1, 'L');
$pdf->Ln(2);

// -----------------------------
// Tabla minimalista con MultiCell
// -----------------------------
$pdf->SetFont('helvetica', 'B', 8);
$pdf->Cell(15, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(45, 5, 'Descripción', 0, 0, 'L');
$pdf->Cell(20, 5, 'Precio', 0, 1, 'R');

$pdf->SetFont('helvetica', '', 8);
$totalVenta = 0;

foreach ($productos as $p) {
    $total = $p['cantidad'] * $p['precio'];
    $totalVenta += $total;

    $pdf->Cell(15, 5, $p['cantidad'], 0, 0, 'C');
    $pdf->MultiCell(45, 5, $p['descripcion'], 0, 'L', false, 0);
    $pdf->Cell(20, 5, number_format($p['precio'], 2), 0, 1, 'R');
}

// -----------------------------
// Total general
// -----------------------------
$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(60, 5, 'TOTAL Q', 0, 0, 'R');
$pdf->Cell(20, 5, number_format($totalVenta, 2), 0, 1, 'R');

// -----------------------------
// Código de barras al final
// -----------------------------
$pdf->Ln(3);
$pdf->Image($tmpBarcode, 10, '', 60, 15, 'PNG');
$pdf->Ln(7);
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 4, $codigo, 0, 1, 'C');

// -----------------------------
// Mensaje final
// -----------------------------
$pdf->Ln(2);
$pdf->Cell(0, 4, '¡Gracias por su compra!', 0, 1, 'C');

// -----------------------------
// Mostrar PDF
// -----------------------------
$pdf->Output('recibo_termico_mejorado.pdf', 'I');

// -----------------------------
// Limpiar PNG
// -----------------------------
if (file_exists($tmpBarcode)) unlink($tmpBarcode);
