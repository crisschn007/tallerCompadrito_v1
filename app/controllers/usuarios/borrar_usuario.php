<?php
session_start();
include '../../conexionBD.php'; // Ya contiene la ruta del proyecto

if (isset($_GET['id'])) {
    $id_usuario = (int)$_GET['id'];

    try {
        // Preparar y ejecutar DELETE
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id_Usuarios = :id");
        $stmt->bindParam(':id', $id_usuario);
        $stmt->execute();

        // Notificación de éxito
        $_SESSION['titulo']  = '¡Eliminado!';
        $_SESSION['mensaje'] = 'El usuario ha sido eliminado correctamente.';
        $_SESSION['icono']   = 'success';

    } catch (PDOException $e) {
        // Notificación de error
        $_SESSION['titulo']  = 'Error';
        $_SESSION['mensaje'] = 'No se pudo eliminar el usuario.';
        $_SESSION['icono']   = 'error';
    }

} else {
    // Notificación de error por ID no proporcionado
    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = 'ID de usuario no proporcionado.';
    $_SESSION['icono']   = 'error';
}

// Redirigir a la página de usuarios usando la ruta que ya maneja conexionBD.php
header('Location: ' . $URL . 'usuarios');
exit;
