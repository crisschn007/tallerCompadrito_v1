<?php
session_start();

/*
|--------------------------------------------------------------------------
| Verificar datos
|--------------------------------------------------------------------------
*/
if (
    isset($_GET['archivo']) &&
    isset($_GET['tipo'])
) {

    $archivo = basename($_GET['archivo']);
    $tipo    = $_GET['tipo'];

    /*
    |--------------------------------------------------------------------------
    | Definir carpeta según tipo
    |--------------------------------------------------------------------------
    */
    if ($tipo == 'Completo') {

        $rutaBase =
            '../../../backups_storage/completos/';

    } else {

        $rutaBase =
            '../../../backups_storage/base_datos/';
    }

    /*
    |--------------------------------------------------------------------------
    | Ruta final
    |--------------------------------------------------------------------------
    */
    $rutaArchivo = $rutaBase . $archivo;

    /*
    |--------------------------------------------------------------------------
    | Eliminar archivo
    |--------------------------------------------------------------------------
    */
    if (file_exists($rutaArchivo)) {

        unlink($rutaArchivo);

        $_SESSION['titulo']  = 'Backup eliminado';
        $_SESSION['mensaje'] = 'El backup fue eliminado correctamente.';
        $_SESSION['icono']   = 'success';

    } else {

        $_SESSION['titulo']  = 'Error';
        $_SESSION['mensaje'] = 'El archivo no existe.';
        $_SESSION['icono']   = 'error';
    }

} else {

    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = 'Datos inválidos.';
    $_SESSION['icono']   = 'error';
}

/*
|--------------------------------------------------------------------------
| Redirección
|--------------------------------------------------------------------------
*/
header('Location: ../../../backups/historial/');
exit;
?>