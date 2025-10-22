<?php

// 🔹 Establecer zona horaria global (Guatemala)
date_default_timezone_set('America/Guatemala');

if (!defined('SERVIDOR')) {
    define('SERVIDOR', 'localhost');
}
if (!defined('USUARIO')) {
    define('USUARIO', 'root');
}
if (!defined('PASSWORD')) {
    define('PASSWORD', '');
}
if (!defined('BD')) {
    define('BD', 'tallercompadrito');
}

$servidor = "mysql:dbname=" . BD . ";host=" . SERVIDOR;

try {
    $pdo = new PDO(
        $servidor,
        USUARIO,
        PASSWORD,
        array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION // ✅ Modo excepciones
        )
    );
    // echo "Conexión exitosa a la base de datos.";
} catch (PDOException $e) {
    die("Error al conectar a la base de datos: " . $e->getMessage());
}

// 🔹 Ruta base global del proyecto
$URL = "http://localhost/tallerCompadrito_v1/";
