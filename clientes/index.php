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
                            <h3 class="mb-0">Clientes</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Clientes
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
                                

                            </div> <!-- /.card-header -->

                            
                            <div class="card-body">

                                <div class="table-responsive overflow-auto">
                                    <table class="table table-bordered table-hover align-middle text-nowrap w-100" style="min-width: 600px;">
                                        <thead class="text-center">
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Nombre Completo</th>
                                                <th scope="col">Direccion</th>
                                                <th scope="col">Telefono</th>
                                                <th scope="col">E-Mail</th>
                                                <th scope="col">DPI / NIT</th>
                                                <th scope="col">Genero</th>
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