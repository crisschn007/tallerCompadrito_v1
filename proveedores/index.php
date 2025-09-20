<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <title>Proveedores</title>

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
                            <h3 class="mb-0">Proveedores</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Proveedores
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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Launch demo modal
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
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
                                                <th scope="col">Nombre Empresa</th>
                                                <th scope="col">Representante</th>
                                                <th scope="col">Direccion</th>
                                                <th scope="col">Telefono</th>
                                                <th scope="col">E-mail</th>
                                                <th scope="col">Nit</th>
                                                <th scope="col">Sitio Web</th>
                                                <th scope="col">Manejo de Categorias</th>
                                                <th scope="col">Estado</th>
                                                <th scope="col">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-center">
                                            <tr>
                                                <th scope="row">1</th>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                                <td>Mark</td>
                                                <td>Otto</td>
                                                <td>@mdo</td>
                                                <td>@mdo</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                </div>

                            </div> <!-- /.card-body -->
                        </div> <!-- /.card -->


                    </div> <!--end::Container-->

                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main-->

        <?php include '../layouts/footer.php'; ?>


</body><!--end::Body-->

</html>