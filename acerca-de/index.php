<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Acerca de</title>
    <?php include '../layouts/head.php'; ?>
</head>

<!--begin::Body-->

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <?php include '../layouts/navAside.php'; ?>

        <!--begin::App Main-->
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content-header">
                <!--begin::Container-->
                <div class="container-fluid">
                    <!--begin::Row-->
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">Acerca de</h3>
                        </div>
                    </div>
                    <!--end::Row-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content Header-->

            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
    <div class="card-body">
        

        <!-- Descripción -->
                    <p class="card-text text-justify">
                        El sistema de gestión de <strong>Taller El Compadrito</strong> fue desarrollado con el objetivo
                        de mejorar la organización y el control de las operaciones internas del taller.
                        Permite administrar de manera eficiente las ventas, compras e inventario,
                        ofreciendo a los usuarios una plataforma sencilla y práctica.
                    </p>
                    <p class="card-text text-justify">
                        Su propósito principal es agilizar los procesos diarios, reducir errores manuales
                        y brindar información clara y accesible para la toma de decisiones.
                    </p>

                    <!-- Tecnologías utilizadas -->
                    <h6 class="mt-4">Tecnologías utilizadas</h6>
                    <ul>
                        <li><strong>Lenguajes:</strong> PHP, HTML, CSS y JavaScript</li>
                        <li><strong>Base de datos:</strong> MySQL</li>
                        <li><strong>Frameworks/Plantillas:</strong> AdminLTE, Bootstrap, SweetAlert2</li>
                        <li><strong>Plugins:</strong> DataTables</li>
                        <li><strong>Entorno de desarrollo:</strong> XAMPP en entorno local</li>
                    </ul>

                    <!-- Contacto -->
                    <h6 class="mt-4 fw-bold">Contacto:</h6>

                    <p class="mb-0">
                        Para más información o soporte técnico puede comunicarse con el equipo desarrollador:
                    </p>
                    <ul class="mb-0">
                        <!--  <li>Email: <a href="mailto:soporte@compadrito.com">soporte@compadrito.com</a></li> -->
                        <!-- Botón WhatsApp con ícono -->
                        <a href="https://wa.me/50240720981" target="_blank" class="btn btn-success">
                            <i class="bi bi-whatsapp"></i> Contactanos
                        </a>

                    </ul>
                </div>
            </div>


    </div>
    </div>
    <!--end::Row-->
    </div>
    <!--end::Container-->
    </div>
    <!--end::App Content-->
    </main>
    <!--end::App Main-->

    <?php include '../layouts/footer.php'; ?>
    </div>
</body>
<!--end::Body-->

</html>