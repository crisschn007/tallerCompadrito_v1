<?php
// ruta: compras/historial/index.php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';

// Consulta para mostrar historial (resumida)
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
GROUP BY c.id_Compra
ORDER BY c.fecha_y_hora DESC, c.id_Compra DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Historial de Compras</title>
    <?php include '../../layouts/head.php'; ?>
    <style>
        /* pequeños ajustes estéticos */
        .table td,
        .table th {
            vertical-align: middle;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Historial de Compras</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= htmlspecialchars($URL) ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Compras</li>
                                <li class="breadcrumb-item active">Historial de Compras</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Listado de Compras</h5>

                            <div class="d-flex gap-2">

                                <a href="<?php echo $URL ?>app/controllers/compras/export_pdf.php" class="btn btn-danger" id="exportPdfAll">
                                    <i class="bi bi-file-earmark-pdf"></i> PDF general
                                </a>

                                <a href="<?php echo $URL ?>app/controllers/compras/export_excel.php"
                                    class="btn btn-success" target="_blank">
                                    <i class="bi bi-file-earmark-spreadsheet-fill"></i> Excel general
                                </a>


                            </div>

                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="tablaHistorial" class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Fecha y Hora</th>
                                            <th>Proveedor</th>
                                            <th>Documento</th>
                                            <th>Método</th>
                                            <th>Artículos</th>
                                            <th>Productos</th>
                                            <th>Total (Q)</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($compras as $fila): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($fila['id_Compra']) ?></td>
                                                <td><?= htmlspecialchars($fila['fecha_y_hora']) ?></td>
                                                <td><?= htmlspecialchars($fila['proveedor']) ?></td>
                                                <td><?= htmlspecialchars($fila['tipo_documento']) ?> #<?= htmlspecialchars($fila['numero_documento']) ?></td>
                                                <td><?= htmlspecialchars(ucfirst($fila['tipo_calculo'])) ?></td>
                                                <td><?= htmlspecialchars($fila['total_articulos']) ?> artículos</td>
                                                <td style="max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                                    <?= htmlspecialchars($fila['lista_productos']) ?>
                                                </td>
                                                <td><strong>Q <?= number_format($fila['total_compra'], 2) ?></strong></td>
                                                <td class="text-center">
                                                    <!-- Vista previa (modal via AJAX) -->
                                                    <button class="btn btn-info btn-sm btnVista" data-id="<?= htmlspecialchars($fila['id_Compra']) ?>" title="Ver detalle">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </button>

                                                    <!-- PDF (controlador) -->
                                                    <a class="btn btn-danger btn-sm" title="Descargar PDF"
                                                        href="<?= htmlspecialchars($URL) ?>app/controllers/compras/historial_compra.php?id=<?= urlencode($fila['id_Compra']) ?>" target="_blank">
                                                        <i class="bi bi-filetype-pdf"></i>
                                                    </a>

                                                    <!-- Eliminar -->
                                                    <button class="btn btn-danger btn-sm btnEliminar" data-id="<?= htmlspecialchars($fila['id_Compra']) ?>" title="Eliminar">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
    </div>

    <!-- Modal Vista Previa -->
    <div class="modal fade" id="modalVistaCompra" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de Compra</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body" id="contenidoVista">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p>Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // inicializar DataTables
            if ($.fn.DataTable) {
                $('#tablaHistorial').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json"
                    },
                    "order": [
                        [1, "desc"]
                    ],
                    "pageLength": 25
                });
            }

            // Abrir vista previa por AJAX (POST)
            $(document).on('click', '.btnVista', function() {
                const id = $(this).data('id');
                $('#contenidoVista').html('<div class="text-center p-4"><div class="spinner-border text-primary"></div><p>Cargando...</p></div>');
                $.ajax({
                    url: 'ajax_vista_compra.php',
                    type: 'POST',
                    data: {
                        id_compra: id
                    },
                    success: function(resp) {
                        $('#contenidoVista').html(resp);
                        $('#modalVistaCompra').modal('show');
                    },
                    error: function(xhr) {
                        $('#contenidoVista').html('<p class="text-danger">Error cargando la vista.</p>');
                        $('#modalVistaCompra').modal('show');
                        console.error(xhr);
                    }
                });
            });

            // Eliminar compra (confirm)
            $(document).on('click', '.btnEliminar', function() {
                const id = $(this).data('id');
                Swal.fire({
                    title: '¿Eliminar la compra?',
                    text: 'Se eliminarán también sus detalles. Esta acción es irreversible.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // redirige al controlador de eliminación
                        window.location.href = '<?= htmlspecialchars($URL) ?>app/controllers/compras/eliminar_compra.php?id=' + encodeURIComponent(id);
                    }
                });
            });

            // Exportar (placeholder) — puedes apuntarlo a controlador que haga todo el PDF general
            $('#exportPdfAll').on('click', function(e) {
                e.preventDefault();
                window.open('<?= htmlspecialchars($URL) ?>app/controllers/compras/export_pdf.php?all=1', '_blank');
            });
        });
    </script>
<?php include '../../layouts/notificaciones.php'; ?>

</body>

</html>