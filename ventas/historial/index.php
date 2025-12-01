<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Historial de Ventas</title>
    <?php include '../../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">

    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>

        <main class="app-main">

            <!-- Encabezado -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-sm-6">
                            <h3 class="mb-0">Historial de Ventas</h3>
                        </div>

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Ventas</li>
                                <li class="breadcrumb-item active">Historial de Ventas</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <div class="app-content">
                <div class="container-fluid">

                    <?php
                    $sql = "
                        SELECT v.id_venta, v.numero_comprobante, v.fecha_y_hora, v.total,
                               c.nombre_y_apellido AS cliente, v.condicion_pago
                        FROM venta v
                        LEFT JOIN cliente c ON c.id_cliente = v.id_cliente
                        ORDER BY v.id_venta DESC
                    ";

                    $consulta = $pdo->prepare($sql);
                    $consulta->execute();
                    $ventas = $consulta->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <div class="card card-outline card-primary shadow-sm">

                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mt-1">Listado de ventas realizadas</h5>

                            <!-- BOTONES DE REPORTES GENERALES -->
                            <div>


                                <!-- BOTÓN PDF GENERAL -->
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#exportarPDF">
                                    <i class="bi bi-filetype-pdf"></i> PDF General
                                </button>

                                <!-- BOTÓN EXCEL GENERAL -->
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#exportarExcel">
                                    <i class="bi bi-file-earmark-excel"></i> Excel General
                                </button>

                                <style>
                                    #exportarExcel .modal-content,
                                    #exportarPDF .modal-content {
                                        border-radius: .5rem;
                                        overflow: hidden;
                                    }
                                </style>

                                <!-- MODAL EXPORTAR PDF -->
                                <div class="modal fade" id="exportarPDF" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">

                                            <form action="<?php echo $URL; ?>app/controllers/ventas/reporte_ventas_general_PDF.php" method="GET">

                                                <div class="row g-0">

                                                    <!-- LADO ROJO -->
                                                    <div class="col-md-4 bg-danger text-white d-flex flex-column justify-content-center p-4"
                                                        style="border-top-left-radius: .4rem; border-bottom-left-radius: .4rem;">

                                                        <h4 class="mb-3">
                                                            <i class="bi bi-filetype-pdf"></i> Exportar PDF
                                                        </h4>

                                                        <button type="button" class="btn btn-outline-light btn-sm w-50" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-lg"></i> Cerrar
                                                        </button>
                                                    </div>

                                                    <!-- FORMULARIO -->
                                                    <div class="col-md-8 p-4">

                                                        <div class="mb-3">
                                                            <label class="form-label">Fecha de Inicio</label>
                                                            <input type="date" name="desde" class="form-control" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Fecha de Fin</label>
                                                            <input type="date" name="hasta" class="form-control" required>
                                                        </div>

                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="bi bi-filetype-pdf"></i> Exportar a PDF
                                                            </button>
                                                        </div>
                                                    </div>

                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                                <!-- MODAL EXPORTAR EXCEL -->
                                <div class="modal fade" id="exportarExcel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">

                                            <form action="<?php echo $URL; ?>app/controllers/ventas/reporte_ventas_general_EXCEL.php" method="GET">

                                                <div class="row g-0">

                                                    <!-- LADO VERDE -->
                                                    <div class="col-md-4 bg-success text-white d-flex flex-column justify-content-center p-4"
                                                        style="border-top-left-radius: .4rem; border-bottom-left-radius: .4rem;">

                                                        <h4 class="mb-3">
                                                            <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar Excel
                                                        </h4>

                                                        <button type="button" class="btn btn-outline-light btn-sm w-50" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-lg"></i> Cerrar
                                                        </button>
                                                    </div>

                                                    <!-- FORMULARIO -->
                                                    <div class="col-md-8 p-4">

                                                        <div class="mb-3">
                                                            <label class="form-label">Fecha de Inicio</label>
                                                            <input type="date" name="inicio" class="form-control" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Fecha de Fin</label>
                                                            <input type="date" name="fin" class="form-control" required>
                                                        </div>

                                                        <div class="text-end">
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="bi bi-file-earmark-excel"></i> Exportar a Excel
                                                            </button>
                                                        </div>

                                                    </div>

                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="card-body table-responsive">

                            <table id="tablaVentas" class="table table-striped table-bordered table-sm">
                                <thead class="table-dark tex">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Comprobante</th>
                                        <th class="text-center">Cliente</th>
                                        <th class="text-center">Fecha</th>
                                        <th class="text-center">Total</th>
                                        <th class="text-center">Condición</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($ventas as $i => $venta): ?>
                                        <tr class="text-center">
                                            <td><?= $i + 1 ?></td>
                                            <td><b><?= htmlspecialchars($venta['numero_comprobante'] ?: '—') ?></b></td>
                                            <td><?= htmlspecialchars($venta['cliente'] ?: 'Sin cliente') ?></td>
                                            <td><?= date("d/m/Y H:i A", strtotime($venta['fecha_y_hora'])) ?></td>
                                            <td>Q <?= number_format($venta['total'], 2) ?></td>
                                            <td><span class="badge bg-info"><?= htmlspecialchars($venta['condicion_pago']) ?></span></td>

                                            <td>
                                                <button class="btn btn-primary btn-sm btnVerVenta" data-id="<?= $venta['id_venta'] ?>">
                                                    <i class="bi bi-eye-fill"></i>
                                                </button>

                                                <a href="../../app/controllers/ventas/save_Ventas.php?id=<?= (int)$venta['id_venta'] ?>"
                                                    class="btn btn-danger btn-sm" target="_blank">
                                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                                </a>

                                                <a href="../../app/controllers/ventas/export_Ventas_EXCEL.php?id=<?= (int)$venta['id_venta'] ?>"
                                                    class="btn btn-success btn-sm">
                                                    <i class="bi bi-file-earmark-excel-fill"></i>
                                                </a>

                                                <button type="button"
                                                    class="btn btn-warning btn-eliminar"
                                                    data-id="<?= (int)$venta['id_venta'] ?>">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>


                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>

                        </div>
                    </div>

                    <!-- MODAL -->
                    <div class="modal fade" id="modalVenta" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title">
                                        <i class="bi bi-receipt-cutoff"></i> Detalle de venta
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body" id="contenidoDetalle">
                                    <div class="text-center py-5">
                                        <div class="spinner-border text-primary"></div>
                                        <p class="mt-3">Cargando detalles...</p>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="bi bi-x-circle"></i> Cerrar
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>

        <?php include '../../layouts/footer.php'; ?>

        <script>
            $(document).ready(function() {

                $('#tablaVentas').DataTable({
                    responsive: true,
                    language: {
                        url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                    }
                });

                $(document).on("click", ".btnVerVenta", function() {

                    let idVenta = $(this).data("id");

                    $("#contenidoDetalle").html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-3">Cargando detalles...</p>
                        </div>
                    `);

                    $("#modalVenta").modal("show");

                    $.ajax({
                        url: "ajax_detalle_venta.php",
                        type: "POST",
                        data: {
                            id: idVenta
                        },
                        success: function(respuesta) {
                            $("#contenidoDetalle").html(respuesta);
                        },
                        error: function() {
                            $("#contenidoDetalle").html("<p class='text-danger'>Error al cargar el detalle.</p>");
                        }
                    });
                });

            });
        </script>

        <script>
            $(document).on("click", ".btn-eliminar", function() {
                let id = $(this).data("id");
                let url = "../../app/controllers/ventas/eliminar_venta.php?id=" + id;

                Swal.fire({
                    title: "¿Eliminar venta?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        </script>
        <?php include '../../layouts/notificaciones.php'; ?>


</body>

</html>