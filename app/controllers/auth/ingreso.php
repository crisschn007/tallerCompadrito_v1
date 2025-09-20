<?php
//app/controllers/auth/ingreso.php
include '../../conexionBD.php';

session_start();

if (isset($_POST['username'], $_POST['password'])) {
    // Limpiar datos
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $password = trim($_POST['password']);

    //buscar usuario Activo
    $sql = "SELECT * FROM Usuarios WHERE nombre_usuario = :username AND estado = 'Activo'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        //verificar la contraseña hasheada
        if (password_verify($password, $usuario['password'])) {

            $_SESSION['sesion_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            $_SESSION['id_usuario'] = $usuario['id_Usuarios'];
            $_SESSION['id_roles'] = $usuario['id_roles'];
            
/*
            $_SESSION['titulo'] = '¡Bienvenido!';
            $_SESSION['mensaje'] = 'Has iniciado sesión correctamente.';
            $_SESSION['icono'] = 'success';*/

            header("Location: " . $URL . "index.php");
            exit();
        } else {
            // Contraseña incorrecta
            $_SESSION['titulo'] = 'Contraseña incorrecta';
            $_SESSION['mensaje'] = 'La contraseña que ingresaste no es válida.';
            $_SESSION['icono'] = 'error';
        }
    } else {
        // Usuario no existe o está inactivo
        $_SESSION['titulo'] = 'Usuario no válido';
        $_SESSION['mensaje'] = 'El usuario no existe o está inactivo.';
        $_SESSION['icono'] = 'warning';
    }
} else {
    // Faltan campos del formulario
    $_SESSION['titulo'] = 'Campos requeridos';
    $_SESSION['mensaje'] = 'Por favor, ingresa el usuario y la contraseña.';
    $_SESSION['icono'] = 'info';
}

// Redirigir de vuelta al login con alerta
header("Location: " . $URL . "auth");
exit();
