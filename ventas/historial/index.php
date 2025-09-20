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

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <?php include '../../layouts/navAside.php'; ?>


        <!--begin::App Main-->
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="app-content-header"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Historial de Ventas</h3>
                        </div>
                          <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?php echo $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Historial de Ventas
                                </li>
                            </ol>
                        </div>

                    </div> <!--end::Row-->
                </div> <!--end::Container-->
            </div> <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->

                 

                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main-->

        <?php include '../../layouts/footer.php'; ?>


</body><!--end::Body-->

</html>