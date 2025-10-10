<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/proveedores/listadoProveedores.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Proveedores</title>
    <?php include '../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../layouts/navAside.php'; ?>

        <main class="app-main">
            <!-- Encabezado -->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Proveedores</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Proveedores</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <div class="app-content">
                <div class="container-fluid">
                    <div class="col-md-12">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <!-- Botón agregar -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarProveedor">
                                    <i class="bi bi-person-plus"></i> Agregar Proveedor
                                </button>

                                <!-- Modal Agregar Proveedor -->
                                <div class="modal fade" id="agregarProveedor" tabindex="-1" aria-labelledby="agregarProveedorLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-person-plus"></i> Agregar Nuevo Proveedor
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                            </div>
                                            <form action="../app/controllers/proveedores/addProveedores.php" method="POST">
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label for="nombre_empresa" class="form-label">Nombre Empresa</label>
                                                            <input type="text" class="form-control" name="nombre_empresa" id="nombre_empresa" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="representante" class="form-label">Representante</label>
                                                            <input type="text" class="form-control" name="representante" id="representante" required>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label for="direccion" class="form-label">Dirección</label>
                                                            <textarea class="form-control" name="direccion" id="direccion" rows="2" required></textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="telefono" class="form-label">Teléfono</label>
                                                            <input type="text" class="form-control" name="telefono" id="telefono" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="email" class="form-label">Email</label>
                                                            <input type="email" class="form-control" name="email" id="email" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label for="sitio_web" class="form-label">Sitio Web</label>
                                                            <input type="url" class="form-control" name="sitio_web" id="sitio_web">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="estado" class="form-label">Estado</label>
                                                            <select class="form-select" name="estado" id="estado" required>
                                                                <option value="" hidden>-- Seleccione Estado --</option>
                                                                <option value="Activo">Activo</option>
                                                                <option value="Inactivo">Inactivo</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="condicion_pago" class="form-label">Condición de Pago</label>
                                                            <select class="form-select" name="condicion_pago" id="condicion_pago" required>
                                                                <option value="" hidden>-- Seleccione una Opción --</option>
                                                                <option value="Contado">Contado</option>
                                                                <option value="Credito 30 dias">Crédito 30 días</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-circle"></i> Cancelar
                                                    </button>
                                                    <button type="submit" class="btn btn-outline-success">
                                                        <i class="bi bi-save"></i> Guardar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla -->
                            <div class="card-body">
                                <div class="table-responsive overflow-auto">
                                    <table id="tablaProveedores" class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 900px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th class="text-center">#</th>
                                                <th class="text-center">Nombre Empresa</th>
                                                <th class="text-center">Representante</th>
                                                <th class="text-center">Dirección</th>
                                                <th class="text-center">Teléfono</th>
                                                <th class="text-center">E-mail</th>
                                                <th class="text-center">Sitio Web</th>
                                                <th class="text-center">Estado</th>
                                                <th class="text-center">Condición de Pago</th>
                                                <th class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php if (!empty($proveedor_datos)): ?>
                                                <?php $contador = 0; // inicializamos el contador ?>
                                                <?php foreach ($proveedor_datos as $proveedores): ?>
                                                    <tr>
                                                        <!-- Aquí usamos el contador en lugar del id_proveedor -->
                                                        <th scope="row"><?= ++$contador; ?></th>

                                                        <td><?= htmlspecialchars($proveedores['nombre_empresa']); ?></td>
                                                        <td><?= htmlspecialchars($proveedores['representante']); ?></td>
                                                        <td><?= htmlspecialchars($proveedores['direccion']); ?></td>
                                                        <td><?= htmlspecialchars($proveedores['telefono']); ?></td>
                                                        <td><?= htmlspecialchars($proveedores['email']); ?></td>
                                                        <td><?= htmlspecialchars($proveedores['sitio_web']); ?></td>
                                                        <td>
                                                            <span class="badge <?= $proveedores['estado'] === "Activo" ? "bg-success" : "bg-danger"; ?>">
                                                                <?= htmlspecialchars($proveedores['estado']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $proveedores['condicion_pago'] === "Contado" ? "bg-primary" : "bg-warning text-dark"; ?>">
                                                                <?= $proveedores['condicion_pago'] === "Contado" ? "Contado" : "Crédito 30 días"; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-info text-white dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Ver más
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editarProveedor<?= $proveedores['id_proveedor']; ?>">
                                                                            <i class="bi bi-pencil-square"></i> Editar
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item text-danger btn-eliminar" href="#" data-id="<?= $proveedores['id_proveedor']; ?>">
                                                                            <i class="bi bi-trash-fill"></i> Eliminar
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                            <!-- Modal Editar -->
                                                            <div class="modal fade" id="editarProveedor<?= $proveedores['id_proveedor']; ?>" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-xl">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-primary text-white">
                                                                            <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Editar Proveedor</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <form action="../app/controllers/proveedores/editProveedores.php" method="POST">
                                                                                <input type="hidden" name="id_proveedor" value="<?= (int) $proveedores['id_proveedor']; ?>">
                                                                                <div class="row g-3">
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label">Nombre Empresa</label>
                                                                                        <input type="text" class="form-control" name="nombre_empresa" value="<?= htmlspecialchars($proveedores['nombre_empresa']); ?>" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label">Representante</label>
                                                                                        <input type="text" class="form-control" name="representante" value="<?= htmlspecialchars($proveedores['representante']); ?>" required>
                                                                                    </div>
                                                                                    <div class="col-md-12">
                                                                                        <label class="form-label">Dirección</label>
                                                                                        <textarea class="form-control" name="direccion" required><?= htmlspecialchars($proveedores['direccion']); ?></textarea>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label">Teléfono</label>
                                                                                        <input type="text" class="form-control" name="telefono" value="<?= htmlspecialchars($proveedores['telefono']); ?>" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label">Email</label>
                                                                                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($proveedores['email']); ?>" required>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <label class="form-label">Sitio Web</label>
                                                                                        <input type="text" class="form-control" name="sitio_web" value="<?= htmlspecialchars($proveedores['sitio_web']); ?>">
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <label class="form-label">Estado</label>
                                                                                        <select class="form-select" name="estado" required>
                                                                                            <option value="Activo" <?= $proveedores['estado'] === "Activo" ? "selected" : ""; ?>>Activo</option>
                                                                                            <option value="Inactivo" <?= $proveedores['estado'] === "Inactivo" ? "selected" : ""; ?>>Inactivo</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="col-md-3">
                                                                                        <label class="form-label">Condición de Pago</label>
                                                                                        <select class="form-select" name="condicion_pago" required>
                                                                                            <option value="Contado" <?= $proveedores['condicion_pago'] === "Contado" ? "selected" : ""; ?>>Contado</option>
                                                                                            <option value="Credito 30 dias" <?= $proveedores['condicion_pago'] === "Credito 30 dias" ? "selected" : ""; ?>>Crédito 30 días</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="mt-3 text-end">
                                                                                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">No hay proveedores registrados</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include '../layouts/footer.php'; ?>

        <!-- Scripts -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // SweetAlert eliminar
                const botonesEliminar = document.querySelectorAll(".btn-eliminar");
                botonesEliminar.forEach(boton => {
                    boton.addEventListener("click", function(e) {
                        e.preventDefault();
                        let idProveedor = this.getAttribute("data-id");
                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "No podrás revertir esta acción",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Sí, eliminar",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "<?php echo $URL; ?>app/controllers/proveedores/deleteProveedores.php?id=" + idProveedor;
                            }
                        });
                    });
                });

                $(document).ready(function() {
                    $('#tablaProveedores').DataTable({
                        lengthMenu: [
                            [5, 10, 25, 50, 100],
                            [5, 10, 25, 50, 100] // Estos son los textos que se muestran en el menú
                        ],
                        language: {
                            processing: "Procesando...",
                            search: "Buscar:",
                            lengthMenu: "Mostrar _MENU_ registros",
                            info: "Mostrando _START_ a _END_ de _TOTAL_ de Proveedores Registrados",
                            infoEmpty: "Mostrando 0 de 0 registros",
                            infoFiltered: "(filtrado de _MAX_ registros en total)",
                            loadingRecords: "Cargando...",
                            zeroRecords: "No se encontraron registros coincidentes",
                            emptyTable: "No hay datos disponibles en la tabla",
                            paginate: {
                                first: "Primero",
                                previous: "Anterior",
                                next: "Siguiente",
                                last: "Último"
                            },
                            aria: {
                                sortAscending: ": activar para ordenar la columna ascendente",
                                sortDescending: ": activar para ordenar la columna descendente"
                            }
                        }
                    });
                });

            });
        </script>

        <?php include '../layouts/notificaciones.php'; ?>
    </div>
</body>

</html>