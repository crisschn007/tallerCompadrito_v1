<?php
include '../../conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    if (
        isset($_POST['nombre_rol'], $_POST['descripcion'], $_POST['estado']) &&
        !empty(trim($_POST['nombre_rol'])) &&
        !empty(trim($_POST['descripcion'])) &&
        !empty(trim($_POST['estado']))
    ) {

        // Sanitizar entradas
        $nombre_roles = trim($_POST['nombre_rol']);
        $descripcion  = trim($_POST['descripcion']);
        $estado       = trim($_POST['estado']);

        try {
            // Preparar la consulta de insercióntrim(
            $sql = "INSERT INTO roles (nombre_roles, descripcion, estado)
                    VALUES (:nombre_rol, :descripcion, :estado)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_rol', $nombre_roles);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':estado', $estado);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo']  = '¡Rol Agregado!';
            $_SESSION['mensaje'] = 'El nuevo rol ha sido registrado correctamente.';
            $_SESSION['icono']   = 'success';

            header('Location: ' . $URL . 'roles');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo registrar el nuevo rol: ' . $e->getMessage();
            $_SESSION['icono']   = 'error';

            header('Location: ' . $URL . 'roles');
            exit;
        }
    } else {
        // Notificación si faltan campos
        $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'Todos los campos son obligatorios.';
        $_SESSION['icono']   = 'warning';

        header('Location: ' . $URL . 'roles');
        exit;
    }
} else {
    // Acceso indebido
    $_SESSION['titulo']  = '¡Acceso no permitido!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder a esta función.';
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'roles');
    exit;
}
