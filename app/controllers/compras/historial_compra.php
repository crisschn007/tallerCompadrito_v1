<?php
require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../libraries/tcpdf/tcpdf.php';

$idCompra = $_GET['id'] ?? null;

if (!$idCompra) {
    echo "ID de compra no proporcionado.";
    exit;
}

// ==============================
// COMPRA
// ==============================
$sqlCompra = "SELECT c.id_Compra, c.fecha, c.tipo_documento, c.numero_documento,
                     c.subtotal, c.descuento_total AS descuento, c.total, 
                     p.nombre_empresa AS proveedor, p.direccion
              FROM compra c
              INNER JOIN proveedor p ON c.id_proveedor = p.id_proveedor
              WHERE c.id_Compra = :id_compra";

$stmt = $pdo->prepare($sqlCompra);
$stmt->execute([':id_compra' => $idCompra]);
$compra = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    echo "Compra no encontrada.";
    exit;
}

// ==============================
// DETALLE
// ==============================
$sqlDetalle = "SELECT dc.id_detalleCompra, pr.nombre_producto AS producto, 
                      dc.cantidad, dc.costo_unitario AS precio, dc.subtotal
               FROM detalle_compra dc
               INNER JOIN producto pr ON dc.id_producto = pr.id_producto
               WHERE dc.id_compra = :id_compra";

$stmtDetalle = $pdo->prepare($sqlDetalle);
$stmtDetalle->execute([':id_compra' => $idCompra]);
$detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

// ==============================
// PDF PERSONALIZADO
// ==============================
class MYPDF extends TCPDF
{
    public function ColoredTable($header, $data)
    {
        $this->SetFillColor(224, 235, 255);
        $this->SetDrawColor(128, 128, 128);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');

        $w = array(10, 80, 25, 25, 25);

        foreach ($header as $i => $col) {
            $this->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
        }
        $this->Ln();

        $this->SetFont('');
        $fill = 0;

        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row['#'], 'LR', 0, 'C', $fill);
            $this->Cell($w[1], 6, $row['producto'], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row['cantidad'], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, number_format($row['precio'], 2), 'LR', 0, 'R', $fill);
            $this->Cell($w[4], 6, number_format($row['total'], 2), 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }

        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// ==============================
// CREAR PDF
// ==============================
$pdf = new MYPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Accesorios R & R');
$pdf->SetTitle('Compra ' . $compra['id_Compra']);
$pdf->SetMargins(15, 20, 15);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('dejavusans', '', 10);
$pdf->AddPage();

// ==============================
// ENCABEZADO
// ==============================
$html = '
<table width="100%" cellpadding="4">
<tr>
<td width="20%"><img src="../../img/logo.png" width="80"></td>
<td width="80%" style="text-align:center;">
<h2>Accesorios R & R</h2>
<p>Aldea San Sebastián, San Marcos</p>
<hr>
</td>
</tr>
</table>

<h4>Compra N°: ' . $compra['id_Compra'] . ' | Documento: ' . $compra['tipo_documento'] . ' ' . $compra['numero_documento'] . '</h4>

<p>
<strong>Proveedor:</strong> ' . $compra['proveedor'] . '<br><br>
<strong>Dirección:</strong> ' . $compra['direccion'] . '<br><br>
<strong>Fecha:</strong> ' . $compra['fecha'] . '
</p>
<br><br>
';

// ==============================
// TABLA
// ==============================
$header = ['#', 'Producto', 'Cantidad', 'Precio', 'Total'];
$data = [];

foreach ($detalles as $i => $d) {
    $data[] = [
        '#' => $i + 1,
        'producto' => $d['producto'],
        'cantidad' => $d['cantidad'],
        'precio' => $d['precio'],
        'total' => $d['subtotal']
    ];
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->ColoredTable($header, $data);

// ==============================
// TOTALES
// ==============================
$pdf->Ln(5);
$pdf->SetFont('', 'B');

$pdf->Cell(140, 6, 'Subtotal', 0, 0, 'R');
$pdf->Cell(30, 6, 'Q ' . number_format($compra['subtotal'], 2), 0, 1, 'R');

$pdf->Cell(140, 6, 'Descuento', 0, 0, 'R');
$pdf->Cell(30, 6, 'Q ' . number_format($compra['descuento'], 2), 0, 1, 'R');

$pdf->Cell(140, 6, 'Total', 0, 0, 'R');
$pdf->Cell(30, 6, 'Q ' . number_format($compra['total'], 2), 0, 1, 'R');

// ==============================
// SALIDA
// ==============================
$pdf->Output('historial_compra_' . $compra['id_Compra'] . '.pdf', 'I');
