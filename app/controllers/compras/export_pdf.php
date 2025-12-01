<?php
// ruta: app/controllers/compras/export_pdf.php
require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../libraries/tcpdf/tcpdf.php';

// Recibir filtros opcionales
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$where = "";
$params = [];
if ($desde && $hasta) {
    $where = "WHERE DATE(c.fecha_y_hora) BETWEEN :desde AND :hasta";
    $params = ['desde' => $desde, 'hasta' => $hasta];
} elseif ($desde) {
    $where = "WHERE DATE(c.fecha_y_hora) >= :desde";
    $params = ['desde' => $desde];
} elseif ($hasta) {
    $where = "WHERE DATE(c.fecha_y_hora) <= :hasta";
    $params = ['hasta' => $hasta];
}

// Consulta principal
$sql = "SELECT
    c.id_Compra,
    c.fecha_y_hora,
    c.tipo_documento,
    c.numero_documento,
    c.tipo_calculo,
    c.total AS total_compra,
    pr.nombre_empresa AS proveedor,
    SUM(dc.cantidad) AS total_articulos,
    GROUP_CONCAT(DISTINCT p.nombre_producto SEPARATOR ', ') AS lista_productos
FROM compra c
INNER JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
INNER JOIN detalle_compra dc ON c.id_Compra = dc.id_compra
INNER JOIN producto p ON dc.id_producto = p.id_producto
{$where}
GROUP BY c.id_Compra
ORDER BY c.fecha_y_hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Extender TCPDF
class MYPDF extends TCPDF {
    public function ColoredTable($header, $data) {
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetDrawColor(128, 128, 128);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B', 9);

        // Ancho de columnas ajustado para Carta
        $w = [8, 25, 30, 30, 20, 15, 50, 18];

        // Encabezado
        foreach ($header as $i => $col) {
            $this->Cell($w[$i], 7, $col, 1, 0, 'C', 1);
        }
        $this->Ln();

        $this->SetFont('', '', 8);
        $fill = 0;

        foreach ($data as $row) {
            // Calcular altura máxima de la fila usando TCPDF::getNumLines()
            $nb = max(
                $this->getNumLines($row['#'], $w[0]),
                $this->getNumLines($row['fecha'], $w[1]),
                $this->getNumLines($row['proveedor'], $w[2]),
                $this->getNumLines($row['documento'], $w[3]),
                $this->getNumLines($row['metodo'], $w[4]),
                $this->getNumLines($row['articulos'], $w[5]),
                $this->getNumLines($row['productos'], $w[6]),
                $this->getNumLines(number_format($row['total'],2), $w[7])
            );
            $h = 5 * $nb;

            // Imprimir cada celda con altura uniforme
            $this->MultiCell($w[0], $h, $row['#'], 1, 'C', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[1], $h, $row['fecha'], 1, 'C', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[2], $h, $row['proveedor'], 1, 'L', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[3], $h, $row['documento'], 1, 'L', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[4], $h, $row['metodo'], 1, 'C', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[5], $h, $row['articulos'], 1, 'C', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[6], $h, $row['productos'], 1, 'L', $fill, 0, '', '', true, 0, false, true, $h, 'M');
            $this->MultiCell($w[7], $h, number_format($row['total'],2), 1, 'R', $fill, 1, '', '', true, 0, false, true, $h, 'M');

            $fill = !$fill;
        }
    }
}

// Crear PDF
$pdf = new MYPDF('P', 'mm', 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Taller Compadrito');
$pdf->SetTitle('Historial General de Compras');
$pdf->SetMargins(10, 15, 10);
$pdf->SetAutoPageBreak(TRUE, 15);
$pdf->SetFont('dejavusans', '', 9);
$pdf->AddPage();

// Encabezado
$html = '<h2>Taller Compadrito</h2>
<p style="margin-bottom:5px;">Historial General de Compras</p>';
if ($desde || $hasta) {
    $html .= "<p>Rango de fechas: " . ($desde ?? 'Inicio') . " - " . ($hasta ?? 'Fin') . "</p>";
}
$pdf->writeHTML($html, true, false, true, false, '');

// Preparar datos para la tabla
$header = ['#', 'Fecha', 'Proveedor', 'Documento', 'Método', 'Artículos', 'Productos', 'Total (Q)'];
$data = [];
foreach ($compras as $i => $c) {
    $data[] = [
        '#' => $i + 1,
        'fecha' => $c['fecha_y_hora'],
        'proveedor' => $c['proveedor'],
        'documento' => $c['tipo_documento'] . ' #' . $c['numero_documento'],
        'metodo' => ucfirst($c['tipo_calculo']),
        'articulos' => $c['total_articulos'],
        'productos' => $c['lista_productos'],
        'total' => $c['total_compra']
    ];
}

// Imprimir tabla
$pdf->ColoredTable($header, $data);

// Total general
$totalGeneral = array_sum(array_column($compras, 'total_compra'));
$pdf->Ln(5);
$pdf->SetFont('', 'B');
$pdf->Cell(160, 6, 'TOTAL GENERAL', 0, 0, 'R');
$pdf->Cell(36, 6, 'Q' . number_format($totalGeneral, 2), 0, 1, 'R');

// Salida PDF
$nombreArchivo = "Historial_Compras_General.pdf";
$pdf->Output($nombreArchivo, 'I');
