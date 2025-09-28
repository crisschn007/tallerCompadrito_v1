<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/clientes/listadoClientes.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Clientes</title>
    <?php include '../layouts/head.php'; ?>
    <style>
        /* Nuevo color rosado para Femenino */
        .text-bg-pink {
            color: #fff !important;
            background-color: #e83e8c !important;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../layouts/navAside.php'; ?>

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Clientes</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Clientes</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <div class="container-fluid">

                    <div class="col-md-12">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#agregarCliente">
                                    Agregar Cliente
                                </button>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive overflow-auto">
                                    <table id="tablaClientes" class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 600px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre y Apellido</th>
                                                <th>Dirección</th>
                                                <th>Teléfono</th>
                                                <th>Email</th>
                                                <th>CUI</th>
                                                <th>Género</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php if (!empty($clientes_datos)): ?>
                                                <?php foreach ($clientes_datos as $cliente): ?>
                                                    <tr>
                                                        <td><?= (int) $cliente['id_cliente'] ?></td>
                                                        <td><?= htmlspecialchars($cliente['nombre_y_apellido']) ?></td>
                                                        <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                                                        <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                                                        <td><?= htmlspecialchars($cliente['email']) ?></td>
                                                        <td><?= htmlspecialchars($cliente['cui']) ?></td>
                                                        <td>
                                                            <?php
                                                            switch ($cliente['genero']) {
                                                                case "Masculino":
                                                                    echo '<span class="badge rounded-pill text-bg-primary">Masculino</span>';
                                                                    break;
                                                                case "Femenino":
                                                                    echo '<span class="badge rounded-pill text-bg-pink">Femenino</span>';
                                                                    break;
                                                                case "Otro":
                                                                    echo '<span class="badge rounded-pill text-bg-secondary">Otro</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="text-muted">No definido</span>';
                                                                    break;
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($cliente['estado'] === "Activo"): ?>
                                                                <span class="badge bg-success">Activo</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-danger">Inactivo</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    Ver más
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li>
                                                                        <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#editarCliente<?= $cliente['id_cliente'] ?>">
                                                                            <i class="bi bi-pencil-square"></i> Editar
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <hr class="dropdown-divider">
                                                                    </li>
                                                                    <li>
                                                                        <a class="dropdown-item text-danger btn-eliminar" data-id="<?= $cliente['id_cliente'] ?>" href="#">
                                                                            <i class="bi bi-trash-fill"></i> Eliminar
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Para editar informacion del Cliente -->
                                                    <div class="modal fade" id="editarCliente<?= $cliente['id_cliente'] ?>" tabindex="-1" aria-labelledby="editarClienteLabel<?= $cliente['id_cliente'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/clientes/editClientes.php" method="post">
                                                                    <div class="modal-header">
                                                                        <h1 class="modal-title fs-5" id="editarClienteLabel<?= $cliente['id_cliente'] ?>">Editar Cliente</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id_cliente" value="<?= (int) $cliente['id_cliente'] ?>">

                                                                        <div class="mb-3">
                                                                            <label for="nombre_completo<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">Nombre y Apellido</label>
                                                                            <input type="text" name="nombre_completo" id="nombre_completo<?= $cliente['id_cliente'] ?>" class="form-control" maxlength="200" value="<?= htmlspecialchars($cliente['nombre_y_apellido']) ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="direccion_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">Dirección</label>
                                                                            <textarea class="form-control" name="direccion_cliente" id="direccion_cliente<?= $cliente['id_cliente'] ?>" rows="2"><?= htmlspecialchars($cliente['direccion']) ?></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="telefono_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">Teléfono</label>
                                                                            <input type="text" name="telefono_cliente" id="telefono_cliente<?= $cliente['id_cliente'] ?>" class="form-control" maxlength="20" value="<?= htmlspecialchars($cliente['telefono']) ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="email_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">E-Mail</label>
                                                                            <input type="email" name="email_cliente" id="email_cliente<?= $cliente['id_cliente'] ?>" class="form-control" maxlength="50" value="<?= htmlspecialchars($cliente['email']) ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="dpi_nit_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">DPI / NIT</label>
                                                                            <input type="text" name="dpi_nit_cliente" id="dpi_nit_cliente<?= $cliente['id_cliente'] ?>" class="form-control" maxlength="20" value="<?= htmlspecialchars($cliente['cui']) ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="genero_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">Género</label>
                                                                            <select class="form-control" name="genero_cliente" id="genero_cliente<?= $cliente['id_cliente'] ?>">
                                                                                <option value="" hidden> -- Seleccionar Género --</option>
                                                                                <option value="Masculino" <?= $cliente['genero'] === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                                                                <option value="Femenino" <?= $cliente['genero'] === 'Femenino' ? 'selected' : '' ?>>Femenino</option>
                                                                                <option value="Otro" <?= $cliente['genero'] === 'Otro' ? 'selected' : '' ?>>Otro</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="estado_cliente<?= $cliente['id_cliente'] ?>" class="form-label fw-semibold">Estado</label>
                                                                            <select class="form-control" name="estado_cliente" id="estado_cliente<?= $cliente['id_cliente'] ?>">
                                                                                <option value="" hidden> -- Seleccionar Estado --</option>
                                                                                <option value="Activo" <?= $cliente['estado'] === 'Activo' ? 'selected' : '' ?>>Activo</option>
                                                                                <option value="Inactivo" <?= $cliente['estado'] === 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                                                                        <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="9" class="text-center text-muted">No hay clientes registrados</td>
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

        <!-- Modal para agregar cliente -->
        <div class="modal fade" id="agregarCliente" tabindex="-1" aria-labelledby="agregarClienteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="../app/controllers/clientes/addClientes.php" method="post">
                        <div class="modal-header bg-primary text-white">
                            <h1 class="modal-title fs-5" id="agregarClienteLabel">Agregar Nuevo Cliente</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="nombre_completo" class="form-label fw-semibold">Nombre y Apellido</label>
                                <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" maxlength="200" required>
                            </div>
                            <div class="mb-3">
                                <label for="direccion_cliente" class="form-label fw-semibold">Dirección</label>
                                <textarea class="form-control" name="direccion_cliente" id="direccion_cliente" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="telefono_cliente" class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono_cliente" id="telefono_cliente" class="form-control" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label for="email_cliente" class="form-label fw-semibold">E-Mail</label>
                                <input type="email" name="email_cliente" id="email_cliente" class="form-control" maxlength="50">
                            </div>
                            <div class="mb-3">
                                <label for="dpi_nit_cliente" class="form-label fw-semibold">DPI / NIT</label>
                                <input type="text" name="dpi_nit_cliente" id="dpi_nit_cliente" class="form-control" maxlength="20">
                            </div>
                            <div class="mb-3">
                                <label for="genero_cliente" class="form-label fw-semibold">Género</label>
                                <select class="form-control" name="genero_cliente" id="genero_cliente">
                                    <option value="" hidden> -- Seleccionar Género --</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="estado_cliente" class="form-label fw-semibold">Estado</label>
                                <select class="form-control" name="estado_cliente" id="estado_cliente">
                                    <option value="" hidden> -- Seleccionar Estado --</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                            <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-circle"></i> Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include '../layouts/footer.php'; ?>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // Selecciona todos los botones con clase .btn-eliminar
                const botonesEliminar = document.querySelectorAll(".btn-eliminar");

                botonesEliminar.forEach(boton => {
                    boton.addEventListener("click", function(e) {
                        e.preventDefault(); // Evita que recargue la página

                        const clienteId = this.getAttribute("data-id");

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
                                // Redirige al controlador de eliminación
                                window.location.href = "../app/controllers/clientes/deleteClientes.php?id=" + clienteId;
                            }
                        });
                    });
                });
            });
        </script>


<script>
            $(document).ready(function() {
                $('#tablaClientes').DataTable({
                    lengthMenu: [
                        [5, 10, 25, 50, 100],
                        [5, 10, 25, 50, 100] // Estos son los textos que se muestran en el menú
                    ],
                    language: {
                        processing: "Procesando...",
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ de Clientes Registrados",
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
        </script>


        <?php include '../layouts/notificaciones.php'; ?>

    </div>
</body>

</html>