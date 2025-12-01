<?php
require '../../conexionBD.php';
session_start();

// ==========================
// VALIDAR ID
// ==========================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<h3>ID no válido</h3>");
}

$idVenta = intval($_GET['id']);

// Tamaño 58mm o 80mm
$tamanoSolicitado = isset($_GET['size']) ? intval($_GET['size']) : 80;

// ==========================
// CONSULTA VENTA
// ==========================
$sqlVenta = "
    SELECT
        v.numero_comprobante,
        v.fecha_y_hora,
        v.total,
        v.condicion_pago,
        c.nombre_y_apellido AS cliente
    FROM venta v
    LEFT JOIN cliente c ON c.id_cliente = v.id_cliente
    WHERE v.id_venta = :idVenta
";

$stmtVenta = $pdo->prepare($sqlVenta);
$stmtVenta->execute(['idVenta' => $idVenta]);
$venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("<h3>La venta no existe</h3>");
}

// ==========================
// DETALLES
// ==========================
$sqlDetalles = "
    SELECT
        dv.cantidad,
        dv.precio_unitario,
        dv.descuento,
        dv.total_linea,
        p.nombre_producto
    FROM detalle_venta dv
    INNER JOIN producto p ON p.id_producto = dv.id_producto
    WHERE dv.id_venta = :idVenta
";

$stmtDetalles = $pdo->prepare($sqlDetalles);
$stmtDetalles->execute(['idVenta' => $idVenta]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);


// ==========================
// CONFIGURACIÓN
// ==========================

if ($tamanoSolicitado == 58) {
    $ANCHO_DOC = 54;
    $colProd   = 24;   // reducido de 28 a 24
    $colCant   = 6;
    $colPrecio = 10;
    $colDesc   = 5;
    $colTotal  = 9;    // ligero ajuste
    $separador = 38;
} else {
    $ANCHO_DOC = 72;
    $colProd   = 32;   // reducido de 36 a 32
    $colCant   = 8;
    $colPrecio = 12;
    $colDesc   = 6;
    $colTotal  = 10;
    $separador = 58;
}


// ==========================
// TCPDF
// ==========================
require_once(__DIR__ . '/../../libraries/tcpdf/tcpdf.php');

class TicketPDF extends TCPDF {}

$pdf = new TicketPDF('P', 'mm', array($ANCHO_DOC, 250), true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(2, 2, 2);
$pdf->SetAutoPageBreak(true, 5);

$pdf->AddPage();

// ==========================
// ENCABEZADO
// ==========================
$pdf->SetFont('dejavusans', 'B', 11);
$pdf->Cell(0, 6, "TALLER EL COMPADRITO", 0, 1, 'C');

$pdf->SetFont('dejavusans', '', 9);
$pdf->Cell(0, 5, "Ticket de Venta", 0, 1, 'C');

$pdf->Ln(2);

// Datos generales
$pdf->SetFont('dejavusans', '', 8);
$pdf->Cell(0, 4, "Comprobante: " . $venta['numero_comprobante'], 0, 1);
$pdf->Cell(0, 4, "Cliente: " . ($venta['cliente'] ?: "Sin cliente"), 0, 1);
$pdf->Cell(0, 4, "Fecha: " . date("d/m/Y h:i A", strtotime($venta['fecha_y_hora'])), 0, 1);
$pdf->Cell(0, 4, "Condición: " . $venta['condicion_pago'], 0, 1);

$pdf->Ln(1);
$pdf->Cell(0, 0, str_repeat("-", $separador), 0, 1);
$pdf->Ln(2);


// ==========================
// TABLA (adaptable con MultiCell)
// ==========================

$pdf->SetFont('dejavusans', 'B', 7.5);
$pdf->SetFillColor(230, 230, 230);

$pdf->Cell($colProd, 6, "Producto", 1, 0, 'C', 1);
$pdf->Cell($colCant, 6, "Cant", 1, 0, 'C', 1);
$pdf->Cell($colPrecio, 6, "Precio", 1, 0, 'C', 1);
$pdf->Cell($colDesc, 6, "D", 1, 0, 'C', 1);
$pdf->Cell($colTotal, 6, "Total", 1, 1, 'C', 1);

$pdf->SetFont('dejavusans', '', 7.2);

// filas
foreach ($detalles as $d) {

    // Ajuste automático de texto en Producto
    $producto = $d['nombre_producto'];

  // filas compactas
$lineHeight = 3.5;
$maxChars = ($tamanoSolicitado == 58) ? 22 : 30;

$producto = $d['nombre_producto'];
$lines = ceil(strlen($producto) / $maxChars);
$rowHeight = $lines * $lineHeight;

// Producto
$x = $pdf->GetX();
$y = $pdf->GetY();

$pdf->MultiCell($colProd, $rowHeight, $producto, 1, 'L', false, 0);

// Cantidad
$pdf->SetXY($x + $colProd, $y);
$pdf->Cell($colCant, $rowHeight, $d['cantidad'], 1, 0, 'C');

// Precio
$pdf->Cell($colPrecio, $rowHeight, number_format($d['precio_unitario'], 2), 1, 0, 'R');

// Desc
$pdf->Cell($colDesc, $rowHeight, number_format($d['descuento'], 2), 1, 0, 'R');

// Total
$pdf->Cell($colTotal, $rowHeight, number_format($d['total_linea'], 2), 1, 1, 'R');

}

$pdf->Ln(2);
$pdf->Cell(0, 0, str_repeat("-", $separador), 0, 1);
$pdf->Ln(2);


// ==========================
// TOTAL GENERAL
// ==========================
$pdf->SetFont('dejavusans', 'B', 10);
$pdf->Cell(0, 6, "TOTAL: Q " . number_format($venta['total'], 2), 0, 1, 'R');

$pdf->Ln(4);

// ==========================
// PIE
// ==========================
$pdf->SetFont('dejavusans', '', 7.5);
$pdf->Cell(0, 4, "Gracias por su compra", 0, 1, 'C');


// ==========================
// SALIDA
// ==========================
$sizeTag = ($tamanoSolicitado == 58) ? "58mm" : "80mm";
$pdf->Output("ticket_venta_{$idVenta}_{$sizeTag}.pdf", 'I');

