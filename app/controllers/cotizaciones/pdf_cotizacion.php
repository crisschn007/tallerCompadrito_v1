<?php
require_once '../../conexionBD.php';
require_once '../../libraries/tcpdf/tcpdf.php';

if (!isset($_GET['id'])) {
    die("ID de cotización no proporcionado.");
}

$idCotizacion = intval($_GET['id']);

// 🔹 Obtener datos generales
$sql = "SELECT c.id_Cotizacion, c.fecha, c.condicion_pago, c.estado,
               cl.nombre_y_apellido AS cliente, cl.telefono, cl.direccion,
               u.nombre AS usuario
        FROM Cotizacion c
        INNER JOIN Cliente cl ON c.id_cliente = cl.id_cliente
        INNER JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios
        WHERE c.id_Cotizacion = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$idCotizacion]);
$cotizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cotizacion) {
    die("Cotización no encontrada.");
}

// 🔹 Obtener detalle de productos
$sqlDetalle = "SELECT p.nombre_producto, dc.cantidad, dc.precio_unitario
               FROM Detalle_Cotizacion dc
               INNER JOIN Producto p ON dc.id_producto = p.id_producto
               WHERE dc.id_Cotizacion = ?";
$stmtDetalle = $pdo->prepare($sqlDetalle);
$stmtDetalle->execute([$idCotizacion]);
$detalles = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

// ==================== TCPDF ====================
// Tamaño personalizado: ancho 80 mm (ticket térmico)
$ancho = 80; // mm
$alto = 200; // ajustable
$pageLayout = array($ancho, $alto);

$pdf = new TCPDF('P', 'mm', $pageLayout, true, 'UTF-8', false);
$pdf->SetCreator('TallerCompadrito');
$pdf->SetAuthor('Sistema de Cotizaciones');
$pdf->SetTitle('Cotización #' . $idCotizacion);

// 🔹 Márgenes mínimos para ticket
$pdf->SetMargins(5, 5, 5);
$pdf->SetAutoPageBreak(TRUE, 5);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

// 🔸 Encabezado tipo ticket
$html = '
<div style="text-align:center;">
    <h3 style="margin:0;">Taller Compadrito</h3>
    <span style="font-size:10px;">Cotización de Productos</span><br>
    <small>Tel: ' . htmlspecialchars($cotizacion['telefono']) . '</small><br>
    <small>Fecha: ' . date("d/m/Y H:i", strtotime($cotizacion['fecha'])) . '</small>
</div>
<hr>
<b>Cotización N°:</b> ' . $cotizacion['id_Cotizacion'] . '<br>
<b>Cliente:</b> ' . htmlspecialchars($cotizacion['cliente']) . '<br>
<b>Dirección:</b> ' . htmlspecialchars($cotizacion['direccion']) . '<br>
<b>Usuario:</b> ' . htmlspecialchars($cotizacion['usuario']) . '<br>
<b>Condición:</b> ' . htmlspecialchars($cotizacion['condicion_pago']) . '<br>
<b>Estado:</b> ' . htmlspecialchars($cotizacion['estado']) . '<br>
<hr>

<table width="100%" cellpadding="2">
<thead>
<tr style="border-bottom:1px solid #000;">
    <th align="left">Producto</th>
    <th align="center">Cant</th>
    <th align="right">P.U</th>
    <th align="right">Subt</th>
</tr>
</thead>
<tbody>
';

$totalGeneral = 0;
foreach ($detalles as $detalle) {
    $subtotal = $detalle['cantidad'] * $detalle['precio_unitario'];
    $totalGeneral += $subtotal;
    $html .= '
    <tr>
        <td>' . htmlspecialchars(substr($detalle['nombre_producto'], 0, 20)) . '</td>
        <td align="center">' . $detalle['cantidad'] . '</td>
        <td align="right">' . number_format($detalle['precio_unitario'], 2) . '</td>
        <td align="right">' . number_format($subtotal, 2) . '</td>
    </tr>';
}

$html .= '
</tbody>
</table>
<hr>
<h4 style="text-align:right;">TOTAL: Q' . number_format($totalGeneral, 2) . '</h4>
<br>
<p style="text-align:center; font-size:9px;">
*** Cotización válida por 7 días hábiles ***
</p>

<br>
<p style="text-align:center; font-size:9px;">
*** Si algun caso acepta la cotizacion, no olvide traer esta cotizacion para su debido
proceso de venta ***
</p>


';

$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('cotizacion_ticket_' . $idCotizacion . '.pdf', 'I');
