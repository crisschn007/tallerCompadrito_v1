<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/roles/listado_Roles.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Roles</title>

    <?php include '../layouts/head.php'; ?>

</head>

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <?php include '../layouts/navAside.php'; ?>


        <!--begin::App Main-->
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Roles</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Roles
                                </li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->

                    <!-- Modal -->
                    <div class="modal fade" id="addRoles" tabindex="-1" aria-labelledby="addRolesLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-primary text-white ">
                                    <h1 class="modal-title fs-5" id="addRolesLabel">Agregar Nuevo Rol</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="../app/controllers/roles/guardar_rol.php" method="post">

                                        <div class="mb-3">
                                            <label for="nombre_rol" class="form-label fw-semibold text-start d-block">Nombre del Rol</label>
                                            <input type="text" name="nombre_rol" id="nombre_rol" class="form-control" maxlength="50">
                                        </div>
                                        <div class="mb-3">
                                            <label for="descripcion" class="form-label fw-semibold text-start d-block">Descripcion</label>
                                            <textarea class="form-control" name="descripcion" id="descripcion" rows="2"></textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="estado" class="form-label fw-semibold text-start d-block">Estado</label>
                                            <select class="form-control" name="estado" id="estado">
                                                <option value="" hidden> -- Seleccionar Estado --</option>
                                                <option value="Activo">Activo</option>
                                                <option value="Inactivo">Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                                            <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoles">
                                    <i class="bi bi-plus-circle"></i> Agregar Rol
                                </button>
                            </div> <!-- /.card-header -->
                            <div class="card-body">

                                <div class="table-responsive overflow-auto">
                                    <table class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 600px;" id="Roles">
                                        <thead class="text-center">
                                            <tr>
                                                <th scope="col" class="text-center">#</th>
                                                <th scope="col" class="text-center">Nombre del Rol</th>
                                                <th scope="col" class="text-center">Descripcion</th>
                                                <th scope="col" class="text-center">Estado</th>
                                                <th scope="col" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>

                                        <?php
                                        $roles_contador = 0;
                                        foreach ($roles_datos as $roles) {
                                            $id_roles = $roles['id_roles'];
                                            $nombre_roles = $roles['nombre_roles'];
                                            $descripcion = $roles['descripcion'];
                                            $estado = $roles['estado'];
                                        ?>
                                            <tbody class="text-center">
                                                <tr>
                                                    <th scope="row"><?php echo ++$roles_contador; ?></th>
                                                    <td><?php echo $nombre_roles; ?></td>
                                                    <td><?php echo $descripcion; ?></td>
                                                    <td> <?php
                                                            if ($estado == 'Activo') {
                                                                echo '<span class="badge rounded-pill text-bg-success">Activo</span>';
                                                            } else {
                                                                echo '<span class="badge rounded-pill text-bg-danger">Inactivo</span>';
                                                            }

                                                            ?>

                                                    </td>

                                                    <td>
                                                        <!-- Dropdown para ver detalles de otras acciones -->
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-info dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Ver más
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <!-- Editar Datos de los roles-->
                                                                <li>
                                                                    <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editRole<?= $id_roles ?>">
                                                                        <i class="bi bi-pencil-square"></i> Editar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <!-- Eliminar la fila de los datos de los roles con SweetAlert2-->
                                                                <li>
                                                                    <a class="dropdown-item text-danger btn-eliminar" href="#" data-id="<?= $id_roles ?>">
                                                                        <i class="bi bi-trash-fill"></i> Eliminar
                                                                    </a>
                                                                </li>
                                                            </ul>
                                                        </div>



                                                        <!-- Modal de Edición -->
                                                        <div class="modal fade" id="editRole<?= $id_roles ?>" tabindex="-1" aria-labelledby="editRoleLabel<?= $id_roles ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h1 class="modal-title fs-5" id="editRoleLabel<?= $id_roles ?>">Editar Información</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="../app/controllers/roles/actualizar_rol.php" method="post">
                                                                            <input type="hidden" name="id_roles" value="<?= $id_roles ?>">

                                                                            <div class="mb-3">
                                                                                <label for="nombre_rol<?= $id_roles ?>" class="form-label fw-semibold text-start d-block">Nombre del Rol</label>
                                                                                <input type="text" class="form-control" name="nombre_rol" id="nombre_rol<?= $id_roles ?>" maxlength="100" value="<?= htmlspecialchars($nombre_roles) ?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="descripcion<?= $id_roles ?>" class="form-label fw-semibold text-start d-block">Descripcion</label>
                                                                                <textarea class="form-control" name="descripcion" id="descripcion<?= $id_roles ?>" rows="2"> <?= htmlspecialchars($descripcion) ?></textarea>
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <label for="estado<?= $id_roles ?>" class="form-label fw-semibold text-start d-block">Estado</label>
                                                                                <select class="form-select" name="estado" id="estado<?= $id_roles ?>">
                                                                                    <option value="" hidden> -- Seleccionar Estado -- </option>
                                                                                    <option value="Activo" <?= $estado == 'Activo' ? 'selected' : '' ?>>Activo</option>
                                                                                    <option value="Inactivo" <?= $estado == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal"><i class="bi bi-x-circle"></i> Cancelar</button>
                                                                                <button type="submit" class="btn btn-outline-success"><i class="bi bi-check-circle"></i> Guardar Cambios</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>


                                            <?php } ?>
                                            </tbody>
                                    </table>

                                </div>


                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->

                    </div> <!--end::Container-->
                </div> <!--end::App Content-->
        </main> <!--end::App Main-->

        <?php include '../layouts/footer.php'; ?>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const botonesEliminar = document.querySelectorAll('.btn-eliminar');

                botonesEliminar.forEach(boton => {
                    boton.addEventListener('click', function() {
                        const idRol = this.getAttribute('data-id');

                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "¡Esta acción no se puede deshacer!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Sí, eliminarlo",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirigir dinámicamente con PHP
                                const url = "<?php echo $URL; ?>app/controllers/roles/borrar_rol.php?id=" + idRol;
                                window.location.href = url; // Redirigir al controlador
                            }
                        });
                    });
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                $('#Roles').DataTable({
                    lengthMenu: [
                        [5, 10, 25, 50, 100],
                        [5, 10, 25, 50, 100] // Estos son los textos que se muestran en el menú
                    ],
                    language: {
                        processing: "Procesando...",
                        search: "Buscar:",
                        lengthMenu: "Mostrar _MENU_ registros",
                        info: "Mostrando _START_ a _END_ de _TOTAL_ de Roles Registrados",
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




</body><!--end::Body-->

</html>