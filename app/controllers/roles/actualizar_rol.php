<?php
include '../../conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['id_roles'], $_POST['nombre_rol'], $_POST['descripcion'], $_POST['estado']) &&
        !empty(trim($_POST['nombre_rol'])) &&
        !empty(trim($_POST['descripcion'])) &&
        !empty(trim($_POST['estado']))
    ) {
        // Sanitizar y asignar variables
        $id_roles     = (int) $_POST['id_roles']; // debe coincidir con el name del input hidden
        $nombre_roles = trim($_POST['nombre_rol']);
        $descripcion  = trim($_POST['descripcion']);
        $estado       = trim($_POST['estado']);

        try {
            // Consulta SQL segura
            $sql = "UPDATE roles
                    SET nombre_roles = :nombre_roles,
                        descripcion = :descripcion,
                        estado = :estado
                    WHERE id_roles = :id_roles";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_roles', $nombre_roles, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindParam(':id_roles', $id_roles, PDO::PARAM_INT);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo'] = '¡Bien Hecho!';
            $_SESSION['mensaje'] = "Datos actualizados correctamente";
            $_SESSION['icono'] = "success";
            header('Location: ' . $URL . 'roles');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo'] = '¡Error!';
            $_SESSION['mensaje'] = 'Error al actualizar el rol: ' . $e->getMessage();
            $_SESSION['icono'] = 'error';
            header('Location: ' . $URL . 'roles');
            exit;
        }
    } else {
        // Notificación por campos vacíos
        $_SESSION['titulo'] = '¡Atención!';
        $_SESSION['mensaje'] = 'Por favor, completa todos los campos obligatorios.';
        $_SESSION['icono'] = 'warning';
        header('Location: ' . $URL . 'roles');
        exit;
    }
} else {
    // Acceso no permitido
    $_SESSION['titulo'] = '¡Error!';
    $_SESSION['mensaje'] = 'Acceso no permitido';
    $_SESSION['icono'] = 'error';
    header('Location: ' . $URL . 'roles');
    exit;
}
