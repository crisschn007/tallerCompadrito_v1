<?php
// RUTA CORRECTA
include '../../app/conexionBD.php';

if (!isset($_POST['id'])) {
    echo "<p class='text-danger'>Error: no se recibió el ID de venta.</p>";
    exit;
}

$idVenta = intval($_POST['id']);

// ==============================
// OBTENER DATOS DE LA VENTA
// ==============================
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
    echo "<p class='text-danger'>Error: la venta no existe.</p>";
    exit;
}

// ==============================
// OBTENER DETALLE
// ==============================
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

?>

<!-- DATOS GENERALES -->
<div class="row mb-2">
    <div class="col-md-6">
        <b>Comprobante:</b> <?= htmlspecialchars($venta['numero_comprobante']) ?>
    </div>
    <div class="col-md-6 text-end">
        <b>Fecha:</b> <?= date("d/m/Y H:i A", strtotime($venta['fecha_y_hora'])) ?>
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-8">
        <b>Cliente:</b> <?= htmlspecialchars($venta['cliente'] ?: "Sin cliente") ?>
    </div>
    <div class="col-md-4 text-end">
        <b>Total general:</b> <span class="text-success fw-bold">Q <?= number_format($venta['total'], 2) ?></span>
    </div>
</div>

<b>Condición de pago:</b> <?= htmlspecialchars($venta['condicion_pago']) ?>

<hr>

<!-- TABLA DETALLE -->
<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="table-light">
            <tr class="text-center">
                <th>Producto</th>
                <th>Cant.</th>
                <th>Precio</th>
                <th>Desc.</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($detalles)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay detalles.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($detalles as $d): ?>
                    <tr class="text-center">
                        <td class="text-start"><?= htmlspecialchars($d['nombre_producto']) ?></td>
                        <td><?= (int)$d['cantidad'] ?></td>
                        <td>Q <?= number_format($d['precio_unitario'], 2) ?></td>
                        <td>Q <?= number_format($d['descuento'], 2) ?></td>
                        <td><b>Q <?= number_format($d['total_linea'], 2) ?></b></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
