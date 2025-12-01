<?php
require __DIR__ . '/../../conexionBD.php';
require __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

// ==========================
// VALIDAR ID
// ==========================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de venta no válido.");
}

$idVenta = intval($_GET['id']);

// ==========================
// OBTENER DATOS DE LA VENTA
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
    WHERE v.id_venta = :id
";

$stmt = $pdo->prepare($sqlVenta);
$stmt->execute(['id' => $idVenta]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$venta) {
    die("Venta no encontrada.");
}

// ==========================
// DETALLES DE VENTA
// ==========================
$sqlDetalles = "
    SELECT
        p.nombre_producto,
        dv.cantidad,
        dv.precio_unitario,
        dv.descuento,
        dv.total_linea
    FROM detalle_venta dv
    INNER JOIN producto p ON p.id_producto = dv.id_producto
    WHERE dv.id_venta = :id
";

$stmtDetalles = $pdo->prepare($sqlDetalles);
$stmtDetalles->execute(['id' => $idVenta]);
$detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

// ==========================
// CREAR EXCEL
// ==========================
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// ==========================
// ESTILOS
// ==========================
$bold = [
    'font' => [ 'bold' => true ]
];

$borderAll = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN
        ]
    ]
];

$center = [
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
];

// ==========================
// ENCABEZADO GENERAL
// ==========================
$sheet->setCellValue('A1', 'TALLER EL COMPADRITO');
$sheet->mergeCells('A1:E1');
$sheet->getStyle('A1')->applyFromArray($bold)->getAlignment()->setHorizontal('center');

$sheet->setCellValue('A3', "Comprobante: " . $venta['numero_comprobante']);
$sheet->setCellValue('A4', "Cliente: " . ($venta['cliente'] ?: "Sin cliente"));
$sheet->setCellValue('A5', "Fecha: " . date("d/m/Y h:i A", strtotime($venta['fecha_y_hora'])));
$sheet->setCellValue('A6', "Condición: " . $venta['condicion_pago']);

// ==========================
// ENCABEZADOS DE TABLA
// ==========================
$sheet->setCellValue('A8', 'Producto');
$sheet->setCellValue('B8', 'Cantidad');
$sheet->setCellValue('C8', 'Precio');
$sheet->setCellValue('D8', 'Descuento');
$sheet->setCellValue('E8', 'Total');

$sheet->getStyle('A8:E8')->applyFromArray($bold);
$sheet->getStyle('A8:E8')->applyFromArray($center);
$sheet->getStyle('A8:E8')->applyFromArray($borderAll);

// ==========================
// RELLENAR DETALLES
// ==========================
$fila = 9;

foreach ($detalles as $d) {
    $sheet->setCellValue("A$fila", $d['nombre_producto']);
    $sheet->setCellValue("B$fila", $d['cantidad']);
    $sheet->setCellValue("C$fila", $d['precio_unitario']);
    $sheet->setCellValue("D$fila", $d['descuento']);
    $sheet->setCellValue("E$fila", $d['total_linea']);

    $sheet->getStyle("A$fila:E$fila")->applyFromArray($borderAll);

    $fila++;
}

// ==========================
// TOTAL GENERAL
// ==========================
$sheet->setCellValue("D$fila", 'TOTAL:');
$sheet->setCellValue("E$fila", $venta['total']);

$sheet->getStyle("D$fila:E$fila")->applyFromArray($bold);
$sheet->getStyle("D$fila:E$fila")->applyFromArray($borderAll);

// Ajustar ancho automático
foreach (range('A', 'E') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// ==========================
// DESCARGAR ARCHIVO
// ==========================
$filename = "Venta_$idVenta.xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
