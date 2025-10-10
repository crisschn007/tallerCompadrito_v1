<?php
/* app/controllers/usuarios/guardar_usuario.php */

include '../../conexionBD.php';
session_start();

$ruta_imagenes = __DIR__ . '/../../../img/usuarios/'; // no modificar la ruta donde guarda la imagen del usuario

// Comprobamos que la carpeta existe, si no, la creamos
if (!is_dir($ruta_imagenes)) {
    mkdir($ruta_imagenes, 0775, true);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // === SANITIZACIÓN DE DATOS ===
        $nombre           = htmlspecialchars(trim($_POST['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
        $nombre_usuario   = htmlspecialchars(trim($_POST['nombre_usuario'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password         = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        $edad             = filter_var($_POST['edad'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $estado           = htmlspecialchars($_POST['estado'] ?? '', ENT_QUOTES, 'UTF-8');
        $id_roles         = filter_var($_POST['id_roles'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        // Validaciones básicas
        if (
            empty($nombre) || empty($nombre_usuario) || empty($password) ||
            empty($confirm_password) || empty($edad) || empty($estado) || empty($id_roles)
        ) {
            $_SESSION['titulo']  = 'Campos incompletos';
            $_SESSION['mensaje'] = 'Por favor, complete todos los campos obligatorios.';
            $_SESSION['icono']   = 'warning';
            header('Location: ' . $URL . 'usuarios');
            exit;
        }

        // Validar longitud y fortaleza del password
        if (strlen($password) < 6) {
            $_SESSION['titulo']  = 'Contraseña débil';
            $_SESSION['mensaje'] = 'La contraseña debe tener al menos 6 caracteres.';
            $_SESSION['icono']   = 'warning';
            header('Location: ' . $URL . 'usuarios');
            exit;
        }

        // Validar coincidencia de contraseñas
        if (!hash_equals($password, $confirm_password)) { // evita timing attacks
            $_SESSION['titulo']  = 'Contraseñas no coinciden';
            $_SESSION['mensaje'] = 'Las contraseñas ingresadas no son iguales.';
            $_SESSION['icono']   = 'error';
            header('Location: ' . $URL . 'usuarios');
            exit;
        }

        // Verificar si el usuario ya existe
        $sql_verificar = "SELECT COUNT(*) FROM usuarios WHERE nombre_usuario = :nombre_usuario";
        $stmt = $pdo->prepare($sql_verificar);
        $stmt->execute([':nombre_usuario' => $nombre_usuario]);
        $existe_usuario = $stmt->fetchColumn();

        if ($existe_usuario > 0) {
            $_SESSION['titulo']  = 'Usuario existente';
            $_SESSION['mensaje'] = 'El nombre de usuario ya está en uso. Intente con otro.';
            $_SESSION['icono']   = 'warning';
            header('Location: ' . $URL . 'usuarios');
            exit;
        }

        // Encriptar contraseña (ya sanitizada)
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Procesar imagen
        $nombre_foto_bd = null;

        if (!empty($_FILES['foto']['name'])) {
            $nombre_original = basename($_FILES['foto']['name']);
            $tmp_foto        = $_FILES['foto']['tmp_name'];
            $extension       = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

            // Validar extensión
            $ext_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($extension, $ext_permitidas)) {
                $_SESSION['titulo']  = 'Formato no permitido';
                $_SESSION['mensaje'] = 'Solo se permiten imágenes JPG, PNG, GIF o WEBP.';
                $_SESSION['icono']   = 'error';
                header('Location: ' . $URL . 'usuarios');
                exit;
            }

            // Generar nombre seguro
            $nombre_foto_bd = uniqid('user_', true) . '.' . $extension;
            $ruta_destino = $ruta_imagenes . $nombre_foto_bd;

            // Mover la imagen
            if (!move_uploaded_file($tmp_foto, $ruta_destino)) {
                $_SESSION['titulo']  = 'Error al subir imagen';
                $_SESSION['mensaje'] = 'No se pudo guardar la imagen del usuario.';
                $_SESSION['icono']   = 'error';
                header('Location: ' . $URL . 'usuarios');
                exit;
            }
        }

        // Insertar usuario de forma segura
        $sql_insert = "INSERT INTO usuarios
            (nombre, nombre_usuario, foto, password, edad, estado, id_roles)
            VALUES (:nombre, :nombre_usuario, :foto, :password, :edad, :estado, :id_roles)";
        $stmt = $pdo->prepare($sql_insert);
        $stmt->execute([
            ':nombre' => $nombre,
            ':nombre_usuario' => $nombre_usuario,
            ':foto' => $nombre_foto_bd,
            ':password' => $password_hash,
            ':edad' => $edad,
            ':estado' => $estado,
            ':id_roles' => $id_roles
        ]);

        $_SESSION['titulo']  = 'Usuario registrado';
        $_SESSION['mensaje'] = 'El nuevo usuario se guardó correctamente.';
        $_SESSION['icono']   = 'success';
        header('Location: ' . $URL . 'usuarios');
        exit;

    } else {
        $_SESSION['titulo']  = 'Acceso inválido';
        $_SESSION['mensaje'] = 'El formulario no fue enviado correctamente.';
        $_SESSION['icono']   = 'error';
        header('Location: ' . $URL . 'usuarios');
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['titulo']  = '¡Error!';
    $_SESSION['mensaje'] = 'No se pudo registrar el nuevo usuario: ' . $e->getMessage();
    $_SESSION['icono']   = 'error';
    header('Location: ' . $URL . 'usuarios');
    exit;
}
?>
