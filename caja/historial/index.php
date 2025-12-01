<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';

// 1️⃣ Detectar caja
// Intentamos tomar la caja abierta más reciente
$stmtCaja = $pdo->prepare("SELECT id_caja, estado FROM caja ORDER BY fecha_apertura DESC LIMIT 1");
$stmtCaja->execute();
$caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

// Si hay caja abierta, usamos su id y estado, si no, mostramos null
$id_caja = $caja['id_caja'] ?? null;
$estado_caja = $caja['estado'] ?? 'cerrada';

// 2️⃣ Filtro por tipo
$tipo = $_GET['tipo'] ?? 'todos';

// 3️⃣ Consulta historial de caja
if ($id_caja) {
    $sql = "SELECT
        h.id_historial,
        h.tipo_movimiento,
        h.monto,
        h.descripcion,
        h.fecha_movimiento,
        h.numero_comprobante,
        h.tabla_origen,
        u.nombre AS usuario
    FROM historial_caja h
    INNER JOIN usuarios u ON h.id_usuario = u.id_Usuarios
    WHERE h.id_caja = :id_caja";

    if ($tipo === 'gastos_egresos') {
        $sql .= " AND (h.tipo_movimiento = 'gasto' OR h.tipo_movimiento = 'egreso')";
    } elseif ($tipo !== 'todos') {
        $sql .= " AND h.tipo_movimiento = :tipo";
    }

    $sql .= " ORDER BY h.fecha_movimiento DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id_caja", $id_caja, PDO::PARAM_INT);

    if ($tipo !== 'todos' && $tipo !== 'gastos_egresos') {
        $stmt->bindParam(":tipo", $tipo);
    }

    $stmt->execute();
    $historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $historial = [];
}

// 4️⃣ Función para colores de badges
function badgeColor($tipo_movimiento)
{
    return match ($tipo_movimiento) {
        'ingreso' => 'bg-success',
        'devolucion' => 'bg-info',
        'prestamo' => 'bg-warning',
        'gasto', 'egreso' => 'bg-danger',
        default => 'bg-secondary'
    };
}

// 5️⃣ Tabs de filtro
$tipos_tab = [
    'ingreso' => 'INGRESOS',
    'devolucion' => 'DEVOLUCIONES',
    'prestamo' => 'PRÉSTAMOS',
    'gastos_egresos' => 'GASTOS/EGRESOS',
    'todos' => 'TODOS'
];

// 6️⃣ Resumen de montos
$resumen = [
    'ingreso' => 0,
    'devolucion' => 0,
    'prestamo' => 0,
    'gasto_egreso' => 0,
    'total' => 0
];

foreach ($historial as $h) {
    switch ($h['tipo_movimiento']) {
        case 'ingreso':
            $resumen['ingreso'] += $h['monto'];
            break;
        case 'devolucion':
            $resumen['devolucion'] += $h['monto'];
            break;
        case 'prestamo':
            $resumen['prestamo'] += $h['monto'];
            break;
        case 'gasto':
        case 'egreso':
            $resumen['gasto_egreso'] += $h['monto'];
            break;
    }
    $resumen['total'] += $h['monto'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Historial de Caja</title>
    <?php include '../../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Historial de Caja</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Caja</li>
                                <li class="breadcrumb-item active">Historial de Caja</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Movimientos de Caja</h4>
                        </div>
                        <div class="card-body">

                            <!-- Estado de la caja -->
                            <p><strong>Estado de la caja:</strong>
                                <span class="badge <?= $estado_caja == 'cerrada' ? 'bg-danger' : 'bg-success' ?>">
                                    <?= strtoupper($estado_caja) ?>
                                </span>
                                <?php if (!$id_caja): ?>
                                    <span class="text-danger ms-2">No hay cajas registradas</span>
                                <?php endif; ?>
                            </p>

                            <!-- Resumen -->
                            <div class="mb-3">
                                <span class="badge bg-success">Ingresos: Q <?= number_format($resumen['ingreso'], 2) ?></span>
                                <span class="badge bg-info">Devoluciones: Q <?= number_format($resumen['devolucion'], 2) ?></span>
                                <span class="badge bg-warning">Préstamos: Q <?= number_format($resumen['prestamo'], 2) ?></span>
                                <span class="badge bg-danger">Gastos/Egresos: Q <?= number_format($resumen['gasto_egreso'], 2) ?></span>
                                <span class="badge bg-primary">Total: Q <?= number_format($resumen['total'], 2) ?></span>
                            </div>

                            <!-- Tabs -->
                            <ul class="nav nav-tabs">
                                <?php foreach ($tipos_tab as $t => $label): ?>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($tipo == $t ? 'active' : '') ?>" href="?tipo=<?= $t ?>">
                                            <?= $label ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <br>

                            <!-- Tabla -->
                            <div class="table-responsive">
                                <table id="tablaHistorial" class="table table-bordered table-striped">
                                    <thead class="text-center">
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha</th>
                                            <th>Movimiento</th>
                                            <th>Monto</th>
                                            <th>Comprobante</th>
                                            <th>Origen</th>
                                            <th>Descripción</th>
                                            <th>Usuario</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php foreach ($historial as $index => $h): ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= $h['fecha_movimiento'] ?></td>
                                                <td>
                                                    <span class="badge <?= badgeColor($h['tipo_movimiento']) ?>">
                                                        <?= strtoupper($h['tipo_movimiento']) ?>
                                                    </span>
                                                </td>
                                                <td>Q <?= number_format($h['monto'], 2) ?></td>
                                                <td><?= $h['numero_comprobante'] ?></td>
                                                <td><?= $h['tabla_origen'] ?></td>
                                                <td><?= $h['descripcion'] ?></td>
                                                <td><?= $h['usuario'] ?></td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm btnEliminar"
                                                        data-id="<?= $h['id_historial'] ?>"
                                                        <?= $estado_caja == 'cerrada' ? 'disabled' : '' ?>>
                                                        <i class="bi bi-trash"></i>
                                                    </button>

                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (!$historial): ?>
                                            <tr>
                                                <td colspan="9">No hay movimientos registrados en esta caja.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
        <?php include '../../layouts/notificaciones.php'; ?>
    </div>
</body>

<script>
document.querySelectorAll('.btnEliminar').forEach(button => {
    button.addEventListener('click', () => {
        const id = button.dataset.id;

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir al controlador para eliminar
                window.location.href = `<?=  $URL ?>app/controllers/caja/eliminar_historial.php?id=${id}`;
            }
        });
    });
});
</script>

</html>