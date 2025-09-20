<?php
/*app/controllers/usuarios/guardar_usuario.php*/

include '../../conexionBD.php';

session_start();


$ruta_imagenes = __DIR__ . '/../../../img/usuarios/'; //no modificar la ruta donde guarda la imagen del usuario


// Comprobamos que la carpeta existe, si no, la creamos
if (!is_dir($ruta_imagenes)) {
    mkdir($ruta_imagenes, 0775, true);
}

?>