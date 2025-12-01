<?php

require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// ======================================================
// 1️⃣ Verificar si se recibieron fechas
// ======================================================
$tieneFechas = (isset($_GET['inicio']) && isset($_GET['fin']) && !empty($_GET['inicio']) && !empty($_GET['fin']));

if ($tieneFechas) {
    $inicio = $_GET['inicio'];
    $fin    = $_GET['fin'];

    // Filtro con rango
    $sql = $pdo->prepare("
        SELECT 
            v.id_venta, v.fecha_y_hora,
            c.nombre_y_apellido AS cliente,
            u.nombre AS usuario,
            v.condicion_pago, v.total, v.numero_comprobante
        FROM venta v
        INNER JOIN cliente c ON v.id_cliente = c.id_cliente
        INNER JOIN usuarios u ON v.id_usuario = u.id_Usuarios
        WHERE DATE(v.fecha_y_hora) BETWEEN ? AND ?
        ORDER BY v.fecha_y_hora ASC
    ");
    $sql->execute([$inicio, $fin]);
    $titulo_rango = "Desde: $inicio  Hasta: $fin";
} else {

    // SIN RANGO → reporte general
    $sql = $pdo->query("
        SELECT 
            v.id_venta, v.fecha_y_hora,
            c.nombre_y_apellido AS cliente,
            u.nombre AS usuario,
            v.condicion_pago, v.total, v.numero_comprobante
        FROM venta v
        INNER JOIN cliente c ON v.id_cliente = c.id_cliente
        INNER JOIN usuarios u ON v.id_usuario = u.id_Usuarios
        ORDER BY v.fecha_y_hora ASC
    ");
    $titulo_rango = "Reporte General (sin filtro de fechas)";
}

$ventas = $sql->fetchAll(PDO::FETCH_ASSOC);

// ======================================================
// 2️⃣ Crear Excel
// ======================================================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("ReporteVentas");

// TÍTULO
$sheet->setCellValue('A1', "REPORTE GENERAL DE VENTAS");
$sheet->mergeCells('A1:G1');
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

// RANGO
$sheet->setCellValue('A2', $titulo_rango);
$sheet->mergeCells('A2:G2');

// ENCABEZADOS
$encabezados = ['ID Venta','Fecha y Hora','Cliente','Usuario','Condición de Pago','Total','Comprobante'];
$col = 'A';
foreach ($encabezados as $enc) {
    $sheet->setCellValue($col . '4', $enc);
    $sheet->getStyle($col . '4')->getFont()->setBold(true);
    $sheet->getStyle($col . '4')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $col++;
}

// CONTENIDO
$fila = 5;
$total_general = 0;

foreach ($ventas as $v) {
    $sheet->setCellValue("A$fila", $v['id_venta']);
    $sheet->setCellValue("B$fila", $v['fecha_y_hora']);
    $sheet->setCellValue("C$fila", $v['cliente']);
    $sheet->setCellValue("D$fila", $v['usuario']);
    $sheet->setCellValue("E$fila", $v['condicion_pago']);
    $sheet->setCellValue("F$fila", $v['total']);
    $sheet->setCellValue("G$fila", $v['numero_comprobante']);

    $total_general += $v['total'];
    $fila++;
}

// TOTAL GENERAL
$sheet->setCellValue("E$fila", "TOTAL GENERAL");
$sheet->setCellValue("F$fila", $total_general);
$sheet->getStyle("E$fila:F$fila")->getFont()->setBold(true);
$sheet->getStyle("E$fila:F$fila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// DESCARGA
$nombreArchivo = $tieneFechas
                ? "Reporte_Ventas_{$inicio}_A_{$fin}.xlsx"
                : "Reporte_Ventas_General.xlsx";

header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment;filename=\"$nombreArchivo\"");
header("Cache-Control: max-age=0");

$writer = new Xlsx($spreadsheet);
$writer->save("php://output");

exit;
?>
