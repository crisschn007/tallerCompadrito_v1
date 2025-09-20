<?php
/* layouts/sesion.php */

session_start();

// Evita el caché del navegador para impedir volver con el botón atrás
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');

// Verifica si hay sesión activa
if (isset($_SESSION['sesion_usuario'])) {
    $usuario_sesion = $_SESSION['sesion_usuario'];

    // Asegúrate de que $pdo y $URL estén definidos antes de incluir este archivo
    $sql = "SELECT * FROM usuarios WHERE nombre_usuario = :username AND estado = 'Activo'";
    $query = $pdo->prepare($sql);
    $query->bindParam(':username', $usuario_sesion, PDO::PARAM_STR);
    $query->execute();
    $usuario = $query->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Variables útiles en la sesión
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['id_usuario'] = $usuario['id_Usuarios'];
        $_SESSION['id_rol'] = $usuario['id_roles'];
    } else {
        // Usuario no encontrado o inactivo → Redirigir siempre a auth
        header('Location: ' . $URL . 'auth/');
        exit();
    }
} else {
    // No hay sesión activa → Redirigir siempre a auth
    header('Location: ' . $URL . 'auth/');
    exit();
}
