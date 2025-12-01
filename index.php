<?php
include 'app/conexionBD.php';
include 'layouts/sesion.php';
include 'app/controllers/productos/listado_productos.php';
include __DIR__ . '/app/controllers/clientes/listadoClientes.php';

/* === 🔍 Consultar estado actual de la caja === */
$idUsuario = $_SESSION['id_usuario'];
$sqlCaja = "SELECT * FROM Caja WHERE id_usuario = :id_usuario ORDER BY id_caja DESC LIMIT 1";
$stmtCaja = $pdo->prepare($sqlCaja);
$stmtCaja->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
$stmtCaja->execute();
$caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

// Variables para mostrar
$estadoCaja = $caja ? strtoupper($caja['estado']) : 'SIN REGISTRO';
$montoCaja = $caja ? number_format($caja['monto_actual'], 2) : '0.00';
$fechaApertura = $caja ? date('d/m/Y H:i', strtotime($caja['fecha_apertura'])) : '—';
$fechaCierre = ($caja && $caja['fecha_cierre']) ? date('d/m/Y H:i', strtotime($caja['fecha_cierre'])) : '—';
$id_caja = $caja ? $caja['id_caja'] : null;

// Color dinámico de tarjeta
$colorEstado = match ($estadoCaja) {
    'ABIERTA' => 'success',
    'CERRADA' => 'danger',
    default => 'secondary',
};

// Movimientos de caja
$ingresos = $egresos = $devoluciones = $prestamos = $gastos = 0.00;
if ($id_caja) {
    $qMovimientos = $pdo->prepare("
        SELECT tipo_movimiento, SUM(monto) AS total
        FROM Historial_Caja
        WHERE id_caja = :id_caja
        GROUP BY tipo_movimiento
    ");
    $qMovimientos->execute(['id_caja' => $id_caja]);
    while ($row = $qMovimientos->fetch(PDO::FETCH_ASSOC)) {
        $total = (float)$row['total'];
        switch ($row['tipo_movimiento']) {
            case 'ingreso':
                $ingresos = $total;
                break;
            case 'egreso':
                $egresos = $total;
                break;
            case 'devolucion':
                $devoluciones = $total;
                break;
            case 'prestamo':
                $prestamos = $total;
                break;
            case 'gasto':
                $gastos = $total;
                break;
        }
    }
}

// Contar registros
$total_usuarios = $pdo->query("SELECT COUNT(*) AS total FROM usuarios")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
$totalProveedor = $pdo->query("SELECT COUNT(*) AS totalProveedor FROM proveedor")->fetch(PDO::FETCH_ASSOC)['totalProveedor'] ?? 0;
$totalVentasDia = $pdo->query("SELECT SUM(total) AS totalVentasDia FROM venta WHERE DATE(fecha_y_hora) = CURDATE()")->fetch(PDO::FETCH_ASSOC)['totalVentasDia'] ?? 0;
$totalComprasMes = $pdo->query("SELECT SUM(total) AS totalComprasMes FROM compra WHERE MONTH(fecha_y_hora) = MONTH(CURDATE()) AND YEAR(fecha_y_hora) = YEAR(CURDATE())")->fetch(PDO::FETCH_ASSOC)['totalComprasMes'] ?? 0;

// Ventas y Compras últimos 7 días
$labels7dias = $ventas7dias = $compras7dias = [];
for ($i = 6; $i >= 0; $i--) {
    $fecha = date('Y-m-d', strtotime("-$i days"));
    $labels7dias[] = date('D', strtotime($fecha));

    // Ventas
    $stmtV = $pdo->prepare("SELECT SUM(total) AS total FROM venta WHERE DATE(fecha_y_hora) = :fecha");
    $stmtV->execute(['fecha' => $fecha]);
    $ventas7dias[] = (float)($stmtV->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Compras
    $stmtC = $pdo->prepare("SELECT SUM(total) AS total FROM compra WHERE DATE(fecha_y_hora) = :fecha");
    $stmtC->execute(['fecha' => $fecha]);
    $compras7dias[] = (float)($stmtC->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Inicio</title>
    <?php include 'layouts/head.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include 'layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Panel de Control</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="row g-4">
                        <!-- Usuarios -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-primary p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold"><?= $total_usuarios ?></h3>
                                    <p class="mb-0">Usuarios Registrados</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-people-fill text-white"></i>
                                </div>
                                <a href="<?= $URL ?>usuarios" class="small-box-footer link-light mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Productos -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-success p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold"><?= $total_productos ?></h3>
                                    <p class="mb-0">Productos</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-box-seam text-white"></i>
                                </div>
                                <a href="<?= $URL ?>productos" class="small-box-footer link-light mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Clientes -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-warning p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold"><?= $total_clientes ?? 0 ?></h3>
                                    <p class="mb-0">Clientes</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-person-lines-fill text-dark"></i>
                                </div>
                                <a href="<?= $URL ?>clientes" class="small-box-footer link-dark mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>

                        <!-- Proveedores -->
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-danger p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold"><?= $totalProveedor ?></h3>
                                    <p class="mb-0">Proveedores</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-truck text-white"></i>
                                </div>
                                <a href="<?= $URL ?>proveedores" class="small-box-footer link-light mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>

                    <!-- Cards resumen -->
                    <div class="row g-4 mt-3">
                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-success p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold">Q<?= number_format($totalVentasDia, 2) ?></h3>
                                    <p class="mb-0">Ventas del Día</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-cash-coin text-white"></i>
                                </div>
                                <a href="<?= $URL ?>ventas" class="small-box-footer link-light mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-info p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold">Q<?= number_format($totalComprasMes, 2) ?></h3>
                                    <p class="mb-0">Compras del Mes</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-bag text-white"></i>
                                </div>
                                <a href="<?= $URL ?>compras" class="small-box-footer link-light mt-3 d-block">Ver más <i class="bi bi-arrow-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-warning p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner">
                                    <h3 class="fw-bold">Q<?= number_format($ingresos + $egresos + $devoluciones + $prestamos + $gastos, 2) ?></h3>
                                    <p class="mb-0">Movimientos de Caja</p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-cash-stack text-dark"></i>
                                </div>
                                <div class="mt-3 px-1 small text-start">
                                    <p class="mb-1"><strong>Ingresos:</strong> Q<?= number_format($ingresos, 2) ?></p>
                                    <p class="mb-1"><strong>Egresos:</strong> Q<?= number_format($egresos, 2) ?></p>
                                    <p class="mb-1"><strong>Devoluciones:</strong> Q<?= number_format($devoluciones, 2) ?></p>
                                    <p class="mb-1"><strong>Préstamos:</strong> Q<?= number_format($prestamos, 2) ?></p>
                                    <p class="mb-0"><strong>Gastos:</strong> Q<?= number_format($gastos, 2) ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-6">
                            <div class="small-box text-bg-<?= $colorEstado ?> p-3 rounded-4 shadow-sm hover-card">
                                <div class="inner text-center">
                                    <h3 class="fw-bold"><?= htmlspecialchars($estadoCaja) ?></h3>
                                    <p class="mb-1">Estado de Caja</p>
                                    <p class="mb-0 fw-bold">Q<?= $montoCaja ?></p>
                                </div>
                                <div class="position-absolute top-0 end-0 opacity-25 p-3" style="font-size:60px;">
                                    <i class="bi bi-cash-coin text-white"></i>
                                </div>
                                <div class="mt-3 px-1 small text-center">
                                    <p class="mb-1"><strong>Apertura:</strong> <?= $fechaApertura ?></p>
                                    <p class="mb-0"><strong>Cierre:</strong> <?= $fechaCierre ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row g-4 mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Ventas vs Compras (últimos 7 días)</div>
                                <div class="card-body">
                                    <canvas id="chartVentasCompras"></canvas>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Movimientos de Caja</div>
                                <div class="card-body" style="position: relative; height: 300px;">
                                    <canvas id="chartCaja"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include 'layouts/footer.php'; ?>

        <script>
            // Ventas vs Compras
            new Chart(document.getElementById('chartVentasCompras'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($labels7dias) ?>,
                    datasets: [{
                            label: 'Ventas',
                            data: <?= json_encode($ventas7dias) ?>,
                            backgroundColor: '#28a745'
                        },
                        {
                            label: 'Compras',
                            data: <?= json_encode($compras7dias) ?>,
                            backgroundColor: '#007bff'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Ventas vs Compras (últimos 7 días)'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Movimientos de Caja
            new Chart(document.getElementById('chartCaja'), {
                type: 'doughnut',
                data: {
                    labels: ['Ingresos', 'Egresos', 'Devoluciones', 'Préstamos', 'Gastos'],
                    datasets: [{
                        data: [<?= $ingresos ?>, <?= $egresos ?>, <?= $devoluciones ?>, <?= $prestamos ?>, <?= $gastos ?>],
                        backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8', '#6f42c1'],
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        title: {
                            display: true,
                            text: 'Distribución de Movimientos (Q)'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Q' + context.raw.toLocaleString('es-GT', {
                                        minimumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        </script>
</body>

</html>