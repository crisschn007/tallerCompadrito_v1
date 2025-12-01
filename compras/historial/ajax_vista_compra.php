<?php
// ruta: compras/historial/ajax_vista_compra.php
include '../../app/conexionBD.php';

// Recibe por POST id_compra (porque el frontend hace POST)
$id = $_POST['id_compra'] ?? null;
if (!$id) {
    echo "<div class='alert alert-danger'>Error: No se recibió el ID de la compra.</div>";
    exit;
}

// Encabezado compra
$sql = "SELECT 
    c.*,
    pr.nombre_empresa AS proveedor,
    u.nombre AS usuario
FROM compra c
LEFT JOIN proveedor pr ON c.id_proveedor = pr.id_proveedor
LEFT JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios
WHERE c.id_Compra = :id LIMIT 1";
$stm = $pdo->prepare($sql);
$stm->execute([':id' => $id]);
$compra = $stm->fetch(PDO::FETCH_ASSOC);

if (!$compra) {
    echo "<div class='alert alert-warning'>No se encontró la compra con ID " . htmlspecialchars($id) . ".</div>";
    exit;
}

// Detalles
$sqlDetalle = "SELECT dc.*, p.nombre_producto
FROM detalle_compra dc
LEFT JOIN producto p ON dc.id_producto = p.id_producto
WHERE dc.id_compra = :id";
$stm2 = $pdo->prepare($sqlDetalle);
$stm2->execute([':id' => $id]);
$detalles = $stm2->fetchAll(PDO::FETCH_ASSOC);

// Formateo de la salida (estilo factura)
?>
<div class="mb-2">
    <div class="d-flex justify-content-between">
        <div>
            <h5>Compra #<?= htmlspecialchars($compra['id_Compra']) ?></h5>
            <p class="mb-0"><strong>Proveedor:</strong> <?= htmlspecialchars($compra['proveedor'] ?: '---') ?></p>
            <p class="mb-0"><strong>Registrado por:</strong> <?= htmlspecialchars($compra['usuario'] ?: '---') ?></p>
        </div>
        <div class="text-end">
            <p class="mb-0"><strong>Fecha:</strong> <?= htmlspecialchars($compra['fecha_y_hora']) ?></p>
            <p class="mb-0"><strong>Documento:</strong> <?= htmlspecialchars($compra['tipo_documento']) ?> #<?= htmlspecialchars($compra['numero_documento']) ?></p>
        </div>
    </div>
</div>

<hr />

<div class="table-responsive">
<table class="table table-sm table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Producto</th>
            <th class="text-center">Cant.</th>
            <th class="text-end">Precio</th>
            <th class="text-end">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $d): ?>
            <tr>
                <td><?= htmlspecialchars($d['nombre_producto'] ?: 'Producto eliminado') ?></td>
                <td class="text-center"><?= (int)$d['cantidad'] ?></td>
                <td class="text-end">Q <?= number_format($d['costo_unitario'], 2) ?></td>
                <td class="text-end">Q <?= number_format($d['subtotal'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" class="text-end"><strong>Subtotal</strong></td>
            <td class="text-end">Q <?= number_format($compra['subtotal'], 2) ?></td>
        </tr>
        <?php if ($compra['tipo_calculo'] === 'descuento'): ?>
        <tr>
            <td colspan="3" class="text-end"><strong>Descuento</strong></td>
            <td class="text-end">Q <?= number_format($compra['descuento_total'], 2) ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($compra['tipo_calculo'] === 'impuesto'): ?>
        <tr>
            <td colspan="3" class="text-end"><strong>Impuesto (<?= htmlspecialchars($compra['tipo_impuesto']) ?>)</strong></td>
            <td class="text-end">Q <?= number_format($compra['impuesto_total'], 2) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="3" class="text-end"><strong>Total</strong></td>
            <td class="text-end"><strong>Q <?= number_format($compra['total'], 2) ?></strong></td>
        </tr>
    </tfoot>
</table>
</div>
