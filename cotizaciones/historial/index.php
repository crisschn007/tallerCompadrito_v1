<?php
#cotizaciones/historial/index.php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Historial de Cotizaciones</title>
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
                            <h3 class="mb-0"><i class="bi bi-clock-history"></i> Historial de Cotizaciones</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL; ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Cotizaciones</li>
                                <li class="breadcrumb-item active">Historial</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">
                    <div class="card card-outline card-secondary">

                        <!-- 🔹 Formulario de Filtro de Fechas -->
                        <div class="card-header">
                            <form method="GET" class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label for="fecha_inicio" class="form-label">Desde:</label>
                                    <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                                        value="<?= $_GET['fecha_inicio'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <label for="fecha_fin" class="form-label">Hasta:</label>
                                    <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                                        value="<?= $_GET['fecha_fin'] ?? ''; ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Filtrar
                                    </button>
                                    <?php if (!empty($_GET['fecha_inicio']) || !empty($_GET['fecha_fin'])): ?>
                                        <a href="index.php" class="btn btn-secondary w-100 mt-2">
                                            <i class="bi bi-arrow-clockwise"></i> Restablecer
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>

                        <?php
                        // 🔹 Filtro por rango de fechas
                        $fechaInicio = $_GET['fecha_inicio'] ?? '';
                        $fechaFin = $_GET['fecha_fin'] ?? '';

                        $sql = "SELECT c.id_Cotizacion, c.fecha, c.total, c.estado, c.condicion_pago,
                                       cli.nombre_y_apellido AS cliente, u.nombre AS usuario,
                                       (SELECT SUM(dc.cantidad)
                                        FROM Detalle_Cotizacion dc
                                        WHERE dc.id_Cotizacion = c.id_Cotizacion) AS articulos
                                FROM Cotizacion c
                                INNER JOIN Cliente cli ON c.id_cliente = cli.id_cliente
                                INNER JOIN usuarios u ON c.id_Usuarios = u.id_Usuarios";

                        $conditions = [];
                        $params = [];

                        if (!empty($fechaInicio)) {
                            $conditions[] = "DATE(c.fecha) >= :inicio";
                            $params[':inicio'] = $fechaInicio;
                        }
                        if (!empty($fechaFin)) {
                            $conditions[] = "DATE(c.fecha) <= :fin";
                            $params[':fin'] = $fechaFin;
                        }

                        if ($conditions) {
                            $sql .= " WHERE " . implode(" AND ", $conditions);
                        }

                        $sql .= " ORDER BY c.id_Cotizacion DESC";
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                        ?>

                        <div class="card-body table-responsive">

                            <?php if ($fechaInicio || $fechaFin): ?>
                                <div class="alert alert-info py-2 mb-3">
                                    Mostrando cotizaciones desde
                                    <strong><?= $fechaInicio ?: 'el inicio'; ?></strong>
                                    hasta
                                    <strong><?= $fechaFin ?: 'la fecha actual'; ?></strong>.
                                </div>
                            <?php endif; ?>

                            <table id="tablaHistorial" class="table table-bordered table-hover align-middle">
                                <thead class="table-dark text-center">
                                    <tr>
                                        <th>#</th>
                                        <th>Cliente</th>
                                        <th>Usuario</th>
                                        <th>Condición de Pago</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Total (Q)</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $contador = 1;
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                                        <tr class="text-center">
                                            <td><?= $contador++; ?></td>
                                            <td><?= htmlspecialchars($row['cliente']); ?></td>
                                            <td><?= htmlspecialchars($row['usuario']); ?></td>
                                            <td><?= htmlspecialchars($row['condicion_pago']); ?></td>
                                            <td>
                                                <?php
                                                $estado = $row['estado'];
                                                $badge = match ($estado) {
                                                    'Pendiente' => 'warning',
                                                    'Aceptada' => 'success',
                                                    'Rechazada' => 'danger',
                                                    default => 'secondary'
                                                };
                                                ?>
                                                <span class="badge bg-<?= $badge; ?>"><?= $estado; ?></span>
                                            </td>
                                            <td><?= date("d/m/Y H:i", strtotime($row['fecha'])); ?></td>
                                            <td>
                                                Q<?= number_format($row['total'], 2); ?>
                                                <br><small class="text-muted">(<?= $row['articulos'] ?: 0; ?> art.)</small>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm verCotizacion" data-id="<?= $row['id_Cotizacion']; ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>

                                                <a href="../../app/controllers/cotizaciones/pdf_cotizacion.php?id=<?= $row['id_Cotizacion']; ?>"
                                                    target="_blank" class="btn btn-secondary btn-sm">
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>

                                                <button class="btn btn-danger btn-sm eliminarCotizacion" data-id="<?= $row['id_Cotizacion']; ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
        <?php include '../../layouts/notificaciones.php'; ?>
    </div>

    <!-- 🔹 Modal Ver Cotización -->
    <div class="modal fade" id="modalVerCotizacion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-eye"></i> Detalle de Cotización</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="detalleCotizacion" class="p-2 text-center text-muted">Seleccione una cotización para ver su detalle.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔹 Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            new DataTable("#tablaHistorial", {
                responsive: true,
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                }
            });

            // 🔸 Ver cotización
            $(document).on("click", ".verCotizacion", function() {
                const id = $(this).data("id");
                $("#detalleCotizacion").html('<p class="text-center text-muted">Cargando información...</p>');
                $("#modalVerCotizacion").modal("show");

                $.ajax({
                    url: "../../app/controllers/cotizaciones/ver_Cotizacion.php",
                    type: "POST",
                    data: {
                        id_cotizacion: id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status === "success") {
                            const data = response.data;
                            let html = `
                                <table class="table table-bordered">
                                    <tr><th>ID</th><td>${data.id_Cotizacion}</td></tr>
                                    <tr><th>Cliente</th><td>${data.cliente_nombre}</td></tr>
                                    <tr><th>Usuario</th><td>${data.usuario}</td></tr>
                                    <tr><th>Condición de Pago</th><td>${data.condicion_pago}</td></tr>
                                    <tr><th>Estado</th><td>${data.estado}</td></tr>
                                    <tr><th>Fecha</th><td>${data.fecha}</td></tr>
                                    <tr><th>Total</th><td>Q${parseFloat(data.total).toFixed(2)}</td></tr>
                                    <tr><th>Artículos</th><td>${data.total_articulos}</td></tr>
                                </table>
                                <hr>
                                <h6 class="text-start">🛒 Productos:</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead><tr><th>#</th><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead>
                                        <tbody>
                            `;
                            data.detalle.forEach((d, i) => {
                                html += `<tr>
                                    <td>${i + 1}</td>
                                    <td>${d.nombre_producto}</td>
                                    <td>${d.cantidad}</td>
                                    <td>Q${parseFloat(d.precio_unitario).toFixed(2)}</td>
                                    <td>Q${(d.cantidad * d.precio_unitario).toFixed(2)}</td>
                                </tr>`;
                            });
                            html += `</tbody></table></div>`;
                            $("#detalleCotizacion").html(html);
                        } else {
                            $("#detalleCotizacion").html(`<p class="text-danger">${response.message}</p>`);
                        }
                    },
                    error: function() {
                        $("#detalleCotizacion").html('<p class="text-danger">Error al cargar los datos.</p>');
                    }
                });
            });

            // 🔸 Eliminar cotización
            $(document).on("click", ".eliminarCotizacion", function() {
                const id = $(this).data("id");
                Swal.fire({
                    title: "¿Eliminar cotización?",
                    text: "Esta acción no se puede deshacer.",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Sí, eliminar",
                    cancelButtonText: "Cancelar"
                }).then((res) => {
                    if (res.isConfirmed) {
                        $.post("../../app/controllers/cotizaciones/delete_cotizacion.php", {
                            id
                        }, (resp) => {

                            if (resp.status === "success") {
                                Swal.fire("Eliminada", resp.message, "success");
                                $(this).closest("tr").fadeOut(500, function() {
                                    $(this).remove();
                                });

                            } else {
                                Swal.fire("Error", resp.message, "error");
                            }
                        }, "json");
                    }
                });
            });
        });
    </script>
</body>

</html>