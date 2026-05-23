<?php
# app/controllers/backups/generar_backup.php

session_start();

include '../../conexionBD.php';
include '../../../layouts/sesion.php';

/* =========================================================
   VALIDAR MÉTODO POST
========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['titulo']  = 'Acceso denegado';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder directamente.';
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'backups/generar');
    exit;
}

/* =========================================================
   OBTENER DATOS DEL FORMULARIO
========================================================= */

$nombre_backup = trim($_POST['nombre_backup']);
$tipo_backup   = trim($_POST['tipo_backup']);

/* =========================================================
   VALIDACIONES
========================================================= */

if (empty($nombre_backup) || empty($tipo_backup)) {

    $_SESSION['titulo']  = 'Campos incompletos';
    $_SESSION['mensaje'] = 'Debe completar todos los campos.';
    $_SESSION['icono']   = 'warning';

    header('Location: ' . $URL . 'backups/generar');
    exit;
}

try {

    /* =========================================================
       CONFIGURACIÓN
    ========================================================= */

    $host = 'localhost';
    $usuario = 'root';
    $password = '';
    $base_datos = 'tallercompadrito';

    $fecha = date('Y-m-d_H-i-s');

    $nombre_archivo = $nombre_backup . '_' . $fecha;

    /* =========================================================
       RUTAS
    ========================================================= */

    $rutaBD = "../../../backups_storage/base_datos/";
    $rutaCompleto = "../../../backups_storage/completos/";

    /* =========================================================
       GENERAR BACKUP SQL
    ========================================================= */

    $archivoSQL = $rutaBD . $nombre_archivo . '.sql';

    $comando = "C:\\xampp\\mysql\\bin\\mysqldump.exe "
        . "--user={$usuario} "
        . "--password={$password} "
        . "--host={$host} "
        . "{$base_datos} > \"{$archivoSQL}\"";

    system($comando, $resultado);

    if ($resultado !== 0) {
        throw new Exception('No se pudo generar el backup SQL.');
    }

    /* =========================================================
       BACKUP COMPLETO
    ========================================================= */

    if ($tipo_backup === 'completo') {

        $archivoZIP = $rutaCompleto . $nombre_archivo . '.zip';

        $zip = new ZipArchive();

        if ($zip->open($archivoZIP, ZipArchive::CREATE) === TRUE) {

            /*
             * Agregar archivo SQL
             */
            $zip->addFile($archivoSQL, basename($archivoSQL));

            /*
             * Agregar proyecto completo
             */
            $rutaProyecto = realpath('../../../');

           /*
 * Carpetas excluidas
 */
$excluir = [
    'vendor',
    'node_modules',
    '.git',
    'backups_storage'
];

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(
        $rutaProyecto,
        RecursiveDirectoryIterator::SKIP_DOTS
    ),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $file) {

    if ($file->isDir()) {
        continue;
    }

    $filePath = $file->getRealPath();

    /*
     * Verificar exclusiones
     */
    $omitido = false;

    foreach ($excluir as $carpeta) {

        if (strpos($filePath, DIRECTORY_SEPARATOR . $carpeta . DIRECTORY_SEPARATOR) !== false) {
            $omitido = true;
            break;
        }
    }

    if ($omitido) {
        continue;
    }

    /*
     * Ruta relativa
     */
    $relativePath = substr($filePath, strlen($rutaProyecto) + 1);

    /*
     * Agregar archivo
     */
    $zip->addFile($filePath, $relativePath);
}

            foreach ($files as $file) {

                if (!$file->isDir()) {

                    $filePath = $file->getRealPath();

                    /*
                     * Evitar incluir backups dentro del ZIP
                     */
                    if (strpos($filePath, 'backups_storage') !== false) {
                        continue;
                    }

                    $relativePath = substr($filePath, strlen($rutaProyecto) + 1);

                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

        } else {
            throw new Exception('No se pudo generar el archivo ZIP.');
        }
    }

    /* =========================================================
       NOTIFICACIÓN ÉXITO
    ========================================================= */

    $_SESSION['titulo']  = 'Backup generado';
    $_SESSION['mensaje'] = 'El respaldo fue generado correctamente.';
    $_SESSION['icono']   = 'success';

} catch (Exception $e) {

    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono']   = 'error';
}

/* =========================================================
   REDIRECCIÓN
========================================================= */

header('Location: ' . $URL . 'backups/generar');
exit;
?>