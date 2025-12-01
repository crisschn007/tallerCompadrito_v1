<?php
// ruta: app/controllers/ventas/reporte_ventas_general_PDF.php
require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../libraries/tcpdf/tcpdf.php';

// -------------------------------
// 1. Captura de Fechas
// -------------------------------
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$where = "";
$params = [];

if ($desde && $hasta) {
    $where = "WHERE DATE(v.fecha_y_hora) BETWEEN :desde AND :hasta";
    $params = [
        ":desde" => $desde,
        ":hasta" => $hasta
    ];
}

// -------------------------------
// 2. Consulta BD
// -------------------------------
$sql = "
    SELECT 
        v.id_venta,
        v.numero_comprobante,
        v.fecha_y_hora,
        v.total,
        c.nombre_y_apellido AS cliente,
        v.condicion_pago
    FROM venta v
    LEFT JOIN cliente c ON c.id_cliente = v.id_cliente
    $where
    ORDER BY v.id_venta DESC
";

$stm = $pdo->prepare($sql);
$stm->execute($params);
$ventas = $stm->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------
// 3. Crear clase extendida para tabla coloreada
// -------------------------------
class PDFColorTable extends TCPDF {

    public function FancyTable($header, $data) {

        // Colores cabecera
        $this->SetFillColor(220, 53, 69); // Rojo bootstrap
        $this->SetTextColor(255);
        $this->SetDrawColor(130, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');

        // Anchos de columna (ajustados a hoja carta)
        $w = array(12, 35, 55, 35, 28, 25);

        // Imprimir cabecera
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();

        // Restaurar colores
        $this->SetFillColor(240, 240, 240);
        $this->SetTextColor(0);
        $this->SetFont('');

        // Filas
        $fill = 0;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'C', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill);
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 'LR', 0, 'R', $fill);
            $this->Cell($w[5], 6, $row[5], 'LR', 0, 'C', $fill);

            $this->Ln();
            $fill = !$fill;
        }

        // Línea final inferior
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// -------------------------------
// 4. Crear PDF
// -------------------------------
$pdf = new PDFColorTable('P', 'mm', 'LETTER', true, 'UTF-8', false);

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->SetMargins(12, 12, 12);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// -------------------------------
// 5. Título
// -------------------------------
$html = "
<h2 style='text-align:center;'>Taller Compadrito</h2>
<h4 style='text-align:center;'>Historial General de Ventas</h4>";

if ($desde && $hasta) {
    $html .= "<p style='text-align:center;'>Desde: <b>$desde</b> — Hasta: <b>$hasta</b></p>";
}

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Ln(4);

// -------------------------------
// 6. Preparar datos para tabla
// -------------------------------
$header = ['#', 'Comprobante', 'Cliente', 'Fecha', 'Total', 'Cond.'];

$data = [];
$i = 1;

foreach ($ventas as $v) {
    $data[] = [
        $i,
        $v['numero_comprobante'],
        $v['cliente'] ?: 'Sin cliente',
        date("d/m/Y H:i A", strtotime($v['fecha_y_hora'])),
        "Q " . number_format($v['total'], 2),
        $v['condicion_pago']
    ];

    $i++;
}

// Si no hay datos
if (empty($data)) {
    $pdf->writeHTML("<p style='color:red;text-align:center;'>No hay registros para mostrar</p>");
} else {
    // Imprimir tabla coloreada
    $pdf->FancyTable($header, $data);
}

$pdf->Output('ventas_general.pdf', 'I');
