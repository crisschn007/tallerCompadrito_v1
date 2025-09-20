<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
include '../app/controllers/categorias/listadoCate.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Categorias</title>

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
                            <h3 class="mb-0">Categorias</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Categorias
                                </li>
                            </ol>
                        </div>
                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->

                    <div class="col-md-12">
                        <div class="card card-outline card-secondary">
                            <div class="card-header">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarCategoria">
                                    <i class="bi bi-folder-plus"></i> Agregar Categoria
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="agregarCategoria" tabindex="-1" aria-labelledby="agregarCategoriaLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="../app/controllers/categorias/guardarCate.php" method="post">

                                                <div class="modal-header bg-primary text-white">
                                                    <h1 class="modal-title fs-5" id="agregarCategoriaLabel"><i class="bi bi-folder-plus"></i> Agregar Nueva Categoria</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">

                                                    <div class="mb-3">
                                                        <label for="nombre_categoria" class="form-label fw-semibold text-start d-block">Nombre de Categoria</label>
                                                        <input type="text" name="nombre_categoria" id="nombre_categoria" class="form-control" maxlength="100">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="descripcion_cate" class="form-label fw-semibold text-start d-block">Descripcion</label>
                                                        <textarea class="form-control" name="descripcion_cate" id="descripcion_cate" rows="2"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="estadoCategoria" class="form-label fw-semibold text-start d-block">Estado</label>
                                                        <select class="form-control" name="estadoCategoria" id="estadoCategoria">
                                                            <option value="" hidden> -- Seleccionar Estado --</option>
                                                            <option value="Activo">Activo</option>
                                                            <option value="Inactivo">Inactivo</option>
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

                            </div> <!-- /.card-header -->
                            <div class="card-body">

                                <div class="table-responsive overflow-auto">
                                    <table class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 600px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre</th>
                                                <th scope="col">Descripcion</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col">Acciones</th>
                                            </tr>
                                        </thead>

                                        <?php
                                        $contadorCategoria = 0;
                                        foreach ($categoria_datos as $categorias) {
                                            $id_Categoria = $categorias['id_categoria'];
                                            $nombre_Categoria = $categorias['nombre'];
                                            $descripcion_Categoria = $categorias['descripcion'];
                                            $estado_Categoria = $categorias['estado'];


                                        ?>
                                            <tbody class="text-center">
                                                <tr>
                                                    <th scope="row"><?php echo ++$contadorCategoria; ?></th>
                                                    <td> <?php echo $nombre_Categoria ?> </td>
                                                    <td> <?php echo $descripcion_Categoria ?> </td>
                                                    <td><?php
                                                        if ($estado_Categoria == 'Activo') {
                                                            echo '<span class="badge rounded-pill text-bg-success">Activo</span>';
                                                        } else {
                                                            echo '<span class="badge rounded-pill text-bg-danger">Inactivo</span>';
                                                        }

                                                        ?> </td>

                                                    <td>
                                                        <!-- Example single danger button -->
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-info dropdown-toggle text-white" data-bs-toggle="dropdown" aria-expanded="false">
                                                                Ver más
                                                            </button>
                                                            <ul class="dropdown-menu">

                                                                <li>
                                                                    <a class="dropdown-item text-primary" href="#" data-bs-toggle="modal" data-bs-target="#editarCategoria<?= $id_Categoria ?>">
                                                                        <i class="bi bi-pencil-square"></i> Editar
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <hr class="dropdown-divider">
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger btn-eliminar"
                                                                        data-id="<?= $id_Categoria ?>"
                                                                        href="#">
                                                                        <i class="bi bi-trash-fill"></i> Eliminar
                                                                    </a>

                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>

                                                    <!-- Modal para editar Categoria-->
                                                    <div class="modal fade" id="editarCategoria<?= $id_Categoria ?>" tabindex="-1" aria-labelledby="editarCategoriaLabel<?= $id_Categoria ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <form action="../app/controllers/categorias/editarCate.php" method="post">
                                                                    <div class="modal-header bg-primary text-white">
                                                                        <h1 class="modal-title fs-5 bi bi-pencil-square" id="editarCategoriaLabel<?= $id_Categoria ?>"> Editar Datos de la Categoria</h1>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <input type="hidden" name="id_categoria" value="<?= $id_Categoria ?>">
                                                                        <div class="mb-3">
                                                                            <label for="nombre_categoria <?= $id_Categoria ?>" class="form-label fw-semibold text-start d-block">Nombre de Categoria</label>
                                                                            <input type="text" name="nombre_categoria" id="nombre_categoria <?= $id_Categoria ?>" class="form-control" maxlength="100" value="<?= htmlspecialchars($nombre_Categoria) ?>">
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="descripcion_cate <?= $id_Categoria ?>" class="form-label fw-semibold text-start d-block">Descripcion</label>
                                                                            <textarea class="form-control" name="descripcion_cate" id="descripcion_cate <?= $id_Categoria ?>" rows="2"><?= htmlspecialchars($descripcion_Categoria) ?></textarea>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label for="estadoCategoria <?= $id_Categoria ?>" class="form-label fw-semibold text-start d-block">Estado</label>
                                                                            <select class="form-control" name="estadoCategoria" id="estadoCategoria <?= $id_Categoria ?>">
                                                                                <option value="" hidden> -- Seleccionar Estado --</option>
                                                                                <option value="Activo" <?= $estado_Categoria == 'Activo' ? 'selected' : '' ?>>Activo</option>
                                                                                <option value="Inactivo" <?= $estado_Categoria == 'Inactivo' ? 'selected' : '' ?>>Inactivo</option>
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
                                                </tr>


                                            </tbody>
                                        <?php  } ?>
                                    </table>

                                </div>

                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->


                    </div> <!--end::Container-->

                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main-->

        <?php include '../layouts/footer.php'; ?>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const eliminarBtns = document.querySelectorAll(".btn-eliminar");

                eliminarBtns.forEach(btn => {
                    btn.addEventListener("click", function(e) {
                        e.preventDefault();

                        const idCategoria = this.getAttribute("data-id");

                        Swal.fire({
                            title: "¿Estás seguro?",
                            text: "¡No podrás revertir esta acción!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Sí, eliminar",
                            cancelButtonText: "Cancelar"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirige al controlador
                                window.location.href = `../app/controllers/categorias/deleteCate.php?id=${idCategoria}`;
                            }
                        });
                    });
                });
            });
        </script>

        <?php include '../layouts/notificaciones.php'; ?>


</body><!--end::Body-->

</html>