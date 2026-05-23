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

                        <div class="col-12">

                            <div class="card card-outline card-primary shadow-sm">

                                <div class="card-header">

                                    <h3 class="card-title">
                                        <i class="bi bi-clock-history"></i>
                                        Historial de Backups
                                    </h3>

                                </div>

                                <div class="card-body table-responsive">

                                    <table class="table table-bordered table-hover align-middle">

                                        <thead class="table-dark">

                                            <tr>

                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th>Fecha</th>
                                                <th>Tamaño</th>
                                                <th>Tipo</th>
                                                <th>Acciones</th>

                                            </tr>

                                        </thead>

                                        <tbody>

                                            <?php

                                            /*
|--------------------------------------------------------------------------
| Rutas de backups
|--------------------------------------------------------------------------
*/
                                            $rutas = [
                                                'Base de Datos' => '../../backups_storage/base_datos/',
                                                'Completo'      => '../../backups_storage/completos/'
                                            ];

                                            $contador = 1;

                                            /*
|--------------------------------------------------------------------------
| Recorrer carpetas
|--------------------------------------------------------------------------
*/
                                            foreach ($rutas as $tipo => $rutaBackups) {

                                                if (is_dir($rutaBackups)) {

                                                    $archivos = scandir($rutaBackups);

                                                    foreach ($archivos as $archivo) {

                                                        /*
             * Ignorar . y ..
             */
                                                        if ($archivo == '.' || $archivo == '..') {
                                                            continue;
                                                        }

                                                        $rutaArchivo = $rutaBackups . $archivo;

                                                        /*
             * Verificar que sea archivo
             */
                                                        if (is_file($rutaArchivo)) {

                                                            /*
                 * Tamaño
                 */
                                                            $tamano = filesize($rutaArchivo);

                                                            if ($tamano >= 1048576) {

                                                                $tamanoLegible =
                                                                    round($tamano / 1048576, 2) . ' MB';
                                                            } else {

                                                                $tamanoLegible =
                                                                    round($tamano / 1024, 2) . ' KB';
                                                            }

                                                            /*
                 * Fecha
                 */
                                                            $fecha = date(
                                                                'd/m/Y h:i A',
                                                                filemtime($rutaArchivo)
                                                            );

                                            ?>

                                                            <tr>

                                                                <td><?= $contador++; ?></td>

                                                                <td>

                                                                    <i class="bi bi-file-earmark-zip text-primary"></i>

                                                                    <?= htmlspecialchars($archivo); ?>

                                                                </td>

                                                                <td><?= $fecha; ?></td>

                                                                <td><?= $tamanoLegible; ?></td>

                                                                <td>

                                                                    <?php if ($tipo == 'Completo'): ?>

                                                                        <span class="badge bg-success">
                                                                            Completo
                                                                        </span>

                                                                    <?php else: ?>

                                                                        <span class="badge bg-primary">
                                                                            Base de Datos
                                                                        </span>

                                                                    <?php endif; ?>

                                                                </td>

                                                                <td>

                                                                    <!-- Descargar -->
                                                                    <a href="<?= $rutaArchivo; ?>"
                                                                        download
                                                                        class="btn btn-sm btn-success">

                                                                        <i class="bi bi-download"></i>

                                                                    </a>

                                                                    <!-- Eliminar -->
                                                                    <a href="#"
                                                                        class="btn btn-sm btn-danger btnEliminarBackup"

                                                                        data-url="../../app/controllers/backups/eliminar_backup.php?archivo=<?= urlencode($archivo); ?>&tipo=<?= urlencode($tipo); ?>">

                                                                        <i class="bi bi-trash"></i>

                                                                    </a>
                                                                </td>

                                                            </tr>

                                            <?php
                                                        }
                                                    }
                                                }
                                            }
                                            ?>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </main>

        <?php include '../../layouts/footer.php'; ?>
        <?php include '../../layouts/notificaciones.php'; ?>

<script>
    /*
    |--------------------------------------------------------------------------
    | Eliminar backup con SweetAlert2
    |--------------------------------------------------------------------------
    */
    document.querySelectorAll('.btnEliminarBackup')
        .forEach(btn => {

            btn.addEventListener('click', function(e) {

                e.preventDefault();

                const url = this.dataset.url;

                Swal.fire({

                    title: '¿Eliminar backup?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',

                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'

                }).then((result) => {

                    if (result.isConfirmed) {

                        window.location.href = url;
                    }

                });

            });

        });
</script>

</body>

</html>