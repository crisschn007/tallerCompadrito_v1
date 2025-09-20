<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/usuarios/Listado_Usuarios.php';



if (file_exists('../app/controllers/roles/listado_activo.php')) {
    require_once '../app/controllers/roles/listado_activo.php';
} else {
    echo "<div class='alert alert-danger'>Archivo de roles no encontrado</div>";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Usuarios</title>

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
                            <h3 class="mb-0">Usuarios</h3>
                        </div>

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Usuarios
                                </li>
                            </ol>
                        </div>

                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->





                    <br><br>

                    <div class="col-md-12">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUser">
                                    <i class="bi bi-person-plus-fill"></i> Agregar Usuario
                                </button>
                                <!-- Modal -->
                                <div class="modal fade" id="addUser" tabindex="-1" aria-labelledby="addUserLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title" id="addUserLabel">
                                                    <i class="bi bi-person-plus"></i> Agregar Nuevo Usuario
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="../app/controllers/usuarios/guardar_usuario.php" method="post" enctype="multipart/form-data">
                                                    <!-- Nombre completo -->
                                                    <div class="mb-3">
                                                        <label for="nombre" class="form-label fw-semibold text-start d-block">Nombre completo: </label>
                                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre" required>
                                                    </div>

                                                    <!-- Nombre de usuario -->
                                                    <div class="mb-3">
                                                        <label for="nombre_usuario" class="form-label fw-semibold text-start d-block">Nombre de usuario: </label>
                                                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" placeholder="Ingrese al usuario" required>
                                                    </div>

                                                    <!-- Foto -->
                                                    <div class="mb-3">
                                                        <label for="foto" class="form-label fw-semibold text-start d-block">Foto de perfil</label>
                                                        <input class="form-control" type="file" id="foto" name="foto" accept="image/*">
                                                    </div>

                                                    <!-- Password -->
                                                    <div class="mb-3">
                                                        <label for="password" class="form-label fw-semibold text-start d-block">Contraseña: </label>
                                                        <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                                    </div>

                                                    <!-- Confirmación de Password -->
                                                    <div class="mb-3">
                                                        <label for="confirm_password" class="form-label fw-semibold text-start d-block">Confirmar Contraseña: </label>
                                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Repita la contraseña" required>
                                                    </div>

                                                    <!-- Edad -->
                                                    <div class="mb-3">
                                                        <label for="edad" class="form-label fw-semibold text-start d-block">Edad: </label>
                                                        <input type="number" class="form-control" id="edad" name="edad" placeholder="Edad" min="1" required>
                                                    </div>

                                                    <!-- Estado -->
                                                    <div class="mb-3">
                                                        <label for="estado" class="form-label fw-semibold text-start d-block">Estado: </label>
                                                        <select class="form-select" id="estado" name="estado" required>
                                                            <option value="" disabled selected hidden>--Seleccione estado--</option>
                                                            <option value="Activo">Activo</option>
                                                            <option value="Inactivo">Inactivo</option>
                                                        </select>
                                                    </div>

                                                    <!-- Rol del usuario (ya lo tienes dinámico) -->
                                                    <div class="mb-3">
                                                        <label for="id_roles" class="form-label fw-semibold text-start d-block">Rol a desempeñar: </label>
                                                        <select class="form-select" id="id_roles" name="id_roles" required>
                                                            <option value="" disabled selected hidden>--Seleccione un rol--</option>
                                                            <?php if (!empty($roles_activos)): ?>
                                                                <?php foreach ($roles_activos as $rol): ?>
                                                                    <option value="<?= htmlspecialchars($rol['id_roles']) ?>">
                                                                        <?= htmlspecialchars($rol['nombre_roles']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <option value="" disabled>No hay roles activos</option>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-circle"></i> Cancelar
                                                        </button>
                                                        <button type="submit" class="btn btn-outline-success">
                                                            <i class="bi bi-person-check"></i> Guardar Nuevo Usuario
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                             
                            </div> <!-- /.card-header -->
                            <div class="card-body">


                                <div class="table-responsive overflow-auto">
                                    <table class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 600px;" id="tablaUsuarios">
                                        <thead class="text-center">
                                            <tr>
                                                <th scope="col" class="text-center">#</th>
                                                <th scope="col" class="text-center">Nombre Completo</th>
                                                <th scope="col" class="text-center">Nombre del Usuario</th>
                                                <th scope="col" class="text-center">Edad</th>
                                                <th scope="col" class="text-center">Estado</th>
                                                <th scope="col" class="text-center">Rol Designado</th>
                                                <th scope="col" class="text-center">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <?php
                                            $contadorUsuarios = 0;
                                            foreach ($usuario_datos as $usuarios) {
                                                $id_usuario = $usuarios['id_Usuarios'];
                                                $nombre_usuario = $usuarios['nombre'];       // nombre real
                                                $usuario_login  = $usuarios['usuario'];      // alias en SELECT
                                                $edadUsuario    = $usuarios['edad'];
                                                $estadoUsuario  = $usuarios['estado'];
                                                $nombre_roles   = $usuarios['nombre_roles'];
                                            ?>
                                                <tr>
                                                    <th scope="row"><?php echo ++$contadorUsuarios; ?></th>
                                                    <td><?php echo $nombre_usuario; ?></td>
                                                    <td><?php echo $usuario_login; ?></td>
                                                    <td><?php echo $edadUsuario; ?></td>
                                                    <td>
                                                        <?php if ($estadoUsuario == 'Activo') { ?>
                                                            <span class="badge rounded-pill text-bg-success">Activo</span>
                                                        <?php } else { ?>
                                                            <span class="badge rounded-pill text-bg-danger">Inactivo</span>
                                                        <?php } ?>
                                                    </td>
                                                    <td><?php echo $nombre_roles; ?></td>

                                                    <td>
                                                        <!-- Example single danger button -->
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Ver más
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editUser<?= $id_usuario ?>">
                                                                        <i class="bi bi-pencil-square"></i> Editar
                                                                    </a></li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li><a class="dropdown-item text-danger btn-eliminar" href="#">
                                                                        <i class="bi bi-trash-fill"></i> Eliminar
                                                                    </a></li>
                                                            </ul>
                                                        </div>



                                                        <!-- Modal de editar usuarios-->
                                                        <div class="modal fade" id="editUser<?= $id_usuario ?>" tabindex="-1" aria-labelledby="editUserLabel<?= $id_usuario ?>" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h1 class="modal-title fs-5 bi bi-pencil-square" id="editUserLabel<?= $id_usuario ?>"> Editar Datos del Usuario</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <form action="#" method="post">
                                                                            <input type="hidden" name="id_Usuarios" value="<?= $id_usuario ?>">

                                                                            <!-- Nombre completo-->
                                                                            <div class="mb-3">
                                                                                <label for="nombre<?= $id_usuario ?>" class="form-label fw-semibold text-start d-block">Nombre completo: </label>
                                                                                <input type="text" class="form-control" id="nombre<?= $id_usuario ?>" name="nombre" placeholder="Ingrese el nombre" value="<?= htmlspecialchars($nombre_usuario) ?>">
                                                                            </div>

                                                                            <!-- Nombre de usuario -->
                                                                            <div class="mb-3">
                                                                                <label for="nombre_usuario<?= $id_usuario ?>" class="form-label fw-semibold text-start d-block">Nombre de usuario: </label>
                                                                                <input type="text" class="form-control" id="nombre_usuario<?= $id_usuario ?>" name="nombre_usuario" placeholder="Ingrese al usuario" value="<?= htmlspecialchars($usuario_login) ?>">
                                                                            </div>

                                                                            <!-- Foto -->
                                                                            <div class="mb-3">
                                                                                <label for="foto<?= $id_usuario ?>" class="form-label">Foto de perfil</label>
                                                                                <input type="file" class="form-control" id="foto<?= $id_usuario ?>" name="foto" accept="image/*">
                                                                                <div class="mt-2 text-center">
                                                                                    <img src="<?= $fotoPerfil ?>" alt="Foto actual" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;">
                                                                                    <p class="small text-muted mt-1">Foto actual</p>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Password -->
                                                                            <div class="mb-3">
                                                                                <label for="password" class="form-label fw-semibold text-start d-block">Contraseña: </label>
                                                                                <input type="password" class="form-control" id="password<?= $id_usuario ?>" name="password" placeholder="Si desea conservar su contraseña deje en blanco">
                                                                            </div>
                                                                            <!-- Edad -->
                                                                            <div class="mb-3">
                                                                                <label for="edad<?= $id_usuario ?>" class="form-label fw-semibold text-start d-block">Edad: </label>
                                                                                <input type="number" class="form-control" id="edad<?= $id_usuario ?>" name="edad" value="<?= htmlspecialchars($edadUsuario) ?>">
                                                                            </div>

                                                                            <!-- Estado -->
                                                                            <div class="mb-3">
                                                                                <label for="estado<?= $id_usuario ?>" class="form-label fw-semibold text-start d-block">Estado: </label>
                                                                                <select class="form-select" id="estado" name="estado<?= $id_usuario ?>" required>
                                                                                    <option value="" disabled selected hidden>--Seleccione estado--</option>
                                                                                    <option value="Activo" <?= $estadoUsuario == 'Activo' ? 'selected' : ''  ?>>Activo</option>
                                                                                    <option value="Inactivo" <?= $estadoUsuario == 'Inactivo' ? 'selected' : ''  ?>>Inactivo</option>
                                                                                </select>
                                                                            </div>

                                                                            <!-- Rol del usuario (ya lo tienes dinámico) -->
                                                                            <div class="mb-3">
                                                                                <label for="id_roles<?= $id_usuario ?>" class="form-label fw-semibold text-start d-block">Rol a desempeñar: </label>
                                                                                <select class="form-select" id="id_roles<?= $id_usuario ?>" name="id_roles" required>
                                                                                    <option value="" disabled hidden>--Seleccione un rol--</option>
                                                                                    <?php if (!empty($roles_activos)): ?>
                                                                                        <?php foreach ($roles_activos as $rol): ?>
                                                                                            <option value="<?= htmlspecialchars($rol['id_roles']) ?>"
                                                                                                <?= $rol['nombre_roles'] == $nombre_roles ? 'selected' : '' ?>>
                                                                                                <?= htmlspecialchars($rol['nombre_roles']) ?>
                                                                                            </option>
                                                                                        <?php endforeach; ?>
                                                                                    <?php else: ?>
                                                                                        <option value="" disabled>No hay roles activos</option>
                                                                                    <?php endif; ?>
                                                                                </select>
                                                                            </div>


                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                                                                    <i class="bi bi-x-circle"></i> Cancelar
                                                                                </button>
                                                                                <button type="submit" class="btn btn-outline-success">
                                                                                    <i class="bi bi-person-check"></i> Guardar Cambios
                                                                                </button>
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
            $(document).ready(function() {
                $('#tablaUsuarios').DataTable({
                    lengthMenu: [
                        [5, 10, 25, 50, 100],
                        [5, 10, 25, 50, 100] // Estos son los textos que se muestran en el menú
                    ],
                    responsive: true,
                    autoWidth: false,
                    pageLength: 5,
                    language: {
                        processing: "Procesando...",
                        lengthMenu: "Mostrar _MENU_ registros",
                        zeroRecords: "No se encontraron resultados",
                        emptyTable: "Ningún dato disponible en esta tabla",
                        info: "Mostrando usuarios registrados del _START_ al _END_ de un total de _TOTAL_ Usuarios",
                        infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 Usuarios",
                        infoFiltered: "(filtrado de un total de _MAX_ registros)",
                        search: "Buscar:",
                        infoThousands: ",",
                        loadingRecords: "Cargando...",
                        paginate: {
                            first: "Primero",
                            last: "Último",
                            next: "Siguiente",
                            previous: "Anterior"
                        },
                        aria: {
                            sortAscending: ": Activar para ordenar la columna de manera ascendente",
                            sortDescending: ": Activar para ordenar la columna de manera descendente"
                        },
                        buttons: {
                            copy: "Copiar",
                            colvis: "Visibilidad",
                            collection: "Colección",
                            colvisRestore: "Restaurar visibilidad",
                            copyKeys: "Presiona ctrl o ⌘ + C para copiar la tabla<br>al portapapeles.<br><br>Para cancelar, haz clic en este mensaje o presiona Esc.",
                            copySuccess: {
                                "1": "Copiada 1 fila al portapapeles",
                                "_": "Copiadas %d filas al portapapeles"
                            },
                            copyTitle: "Copiar al portapapeles",
                            csv: "CSV",
                            excel: "Excel",
                            pageLength: {
                                "-1": "Mostrar todas las filas",
                                "_": "Mostrar %d filas"
                            },
                            pdf: "PDF",
                            print: "Imprimir"
                        }
                    }
                });
            });
        </script>



</body><!--end::Body-->

</html>