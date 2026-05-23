<?php
require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Filtros
$desde = $_GET['desde'] ?? null;
$hasta = $_GET['hasta'] ?? null;

$where = "";
$params = [];

if ($desde && $hasta) {
    $where = "WHERE c.fecha BETWEEN :desde AND :hasta";
    $params = ['desde' => $desde, 'hasta' => $hasta];
} elseif ($desde) {
    $where = "WHERE c.fecha >= :desde";
    $params = ['desde' => $desde];
} elseif ($hasta) {
    $where = "WHERE c.fecha <= :hasta";
    $params = ['hasta' => $hasta];
}

// CONSULTA CORREGIDA
$sql = "SELECT
    c.id_Compra,
    c.fecha,
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
ORDER BY c.fecha DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// CREAR EXCEL
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Historial Compras");

// Encabezados
$encabezado = [
    'A1' => 'ID',
    'B1' => 'Fecha',
    'C1' => 'Proveedor',
    'D1' => 'Documento',
    'E1' => 'Método',
    'F1' => 'Artículos',
    'G1' => 'Productos',
    'H1' => 'Total (Q)'
];

foreach ($encabezado as $col => $titulo) {
    $sheet->setCellValue($col, $titulo);
}

// Estilo encabezado
$sheet->getStyle('A1:H1')->getFont()->setBold(true);
$sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A1:H1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

// Llenar datos
$fila = 2;

foreach ($compras as $c) {
    $sheet->setCellValue("A$fila", $c['id_Compra']);
    $sheet->setCellValue("B$fila", $c['fecha']);
    $sheet->setCellValue("C$fila", $c['proveedor']);
    $sheet->setCellValue("D$fila", $c['tipo_documento'] . ' #' . $c['numero_documento']);
    $sheet->setCellValue("E$fila", ucfirst($c['tipo_calculo']));
    $sheet->setCellValue("F$fila", $c['total_articulos']);
    $sheet->setCellValue("G$fila", $c['lista_productos']);
    $sheet->setCellValue("H$fila", number_format($c['total_compra'], 2, '.', ''));

    $sheet->getStyle("A$fila:H$fila")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

    $fila++;
}

$ultimaFila = $fila - 1;

$sheet->getStyle("H2:H$ultimaFila")
    ->getNumberFormat()
    ->setFormatCode('"Q" #,##0.00');

// Auto tamaño columnas
foreach (range('A', 'H') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Nombre archivo
$nombreArchivo = "Historial_Compras";

if ($desde && $hasta) {
    $nombreArchivo .= "_{$desde}_al_{$hasta}";
} else {
    $nombreArchivo .= "_" . date('m_Y');
}

$nombreArchivo .= ".xlsx";

// Headers descarga
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$nombreArchivo\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
