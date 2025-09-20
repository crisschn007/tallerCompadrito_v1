<?php
// app/controllers/roles/delete_roles.php

session_start();



include './../../conexionBD.php'; // Conexión con $pdo y $URL

if (isset($_GET['id'])) {
    $roleId = $_GET['id'];

    try {
        // Verificar si el rol existe antes de eliminar
        $stmt = $pdo->prepare("SELECT * FROM roles WHERE id_roles = :id");
        $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
        $stmt->execute();
        $rol = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rol) {
            // Eliminar el rol de la base de datos
            $stmt = $pdo->prepare("DELETE FROM roles WHERE id_roles = :id");
            $stmt->bindParam(':id', $roleId, PDO::PARAM_INT);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo']  = '¡Rol eliminado!';
            $_SESSION['mensaje'] = 'El rol se eliminó correctamente.';
            $_SESSION['icono']   = 'success';
        } else {
            // El rol no existe
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'El rol que intentas eliminar no existe.';
            $_SESSION['icono']   = 'error';
        }

        // Redirigir a la vista de roles
        header('Location: ' . $URL . 'roles');
        exit();

    } catch (Exception $e) {
        // Captura de error en la consulta
        $_SESSION['titulo']  = '¡Error!';
        $_SESSION['mensaje'] = 'No se pudo eliminar el rol: ' . $e->getMessage();
        $_SESSION['icono']   = 'error';
        header('Location: ' . $URL . 'roles');
        exit();
    }
} else {
    // Si no viene un ID válido
    $_SESSION['titulo']  = '¡Advertencia!';
    $_SESSION['mensaje'] = 'No se especificó el rol a eliminar.';
    $_SESSION['icono']   = 'warning';
    header('Location: ' . $URL . 'roles');
    exit();
}
