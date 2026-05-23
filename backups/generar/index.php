<?php
include '../../app/conexionBD.php';
include '../../layouts/sesion.php';
?>



<!DOCTYPE html>
<html lang="es">

<head>
    <title>Genera Backup</title>
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
                            <h3 class="mb-0">Generar Backup</h3>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-end">
                                <li class="breadcrumb-item"><a href="<?= $URL ?>">Inicio</a></li>
                                <li class="breadcrumb-item active">Backups</li>
                                <li class="breadcrumb-item active">Generar Backup</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row">

                        <!-- Card principal -->
                        <div class="col-md-8">

                            <div class="card card-outline card-primary shadow-sm">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="bi bi-database-fill-down"></i>
                                        Generar Backup del Sistema
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        Se recomienda generar un respaldo antes de realizar cambios importantes.
                                    </div>

                                    <form action="../../app/controllers/backups/generar_backup.php" method="POST">

                                        <div class="mb-3">
                                            <label class="form-label">Nombre del Backup</label>
                                            <input type="text" name="nombre_backup" class="form-control" placeholder="Ejemplo: backup_17_05_2026" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tipo de Respaldo</label>

                                            <select name="tipo_backup" class="form-select">

                                                <option value="completo">
                                                    Backup Completo
                                                </option>

                                                <option value="solo_bd">
                                                    Solo Base de Datos
                                                </option>

                                            </select>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-download"></i>
                                            Generar Backup
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>


                        <!-- Card informativa -->
                        <div class="col-md-4">

                            <div class="card card-outline card-secondary">

                                <div class="card-header">
                                    <h3 class="card-title">
                                        Información
                                    </h3>
                                </div>

                                <div class="card-body">

                                    <p>
                                        El sistema generará un archivo comprimido
                                        con la base de datos del sistema.
                                    </p>

                                    <hr>

                                    <p>
                                        Los backups ayudan a recuperar información
                                        en caso de errores o pérdidas de datos.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
        <?php include '../../layouts/notificaciones.php'; ?>



</body>

</html>