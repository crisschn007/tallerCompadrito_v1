<?php
/* app/controllers/usuarios/editar_usuario.php */

include '../../conexionBD.php';
session_start();

$ruta_imagenes = __DIR__ . '/../../../img/usuarios/'; // Carpeta donde se guardan las fotos

// Si la carpeta no existe, se crea
if (!is_dir($ruta_imagenes)) {
    mkdir($ruta_imagenes, 0775, true);
}

try {
    // Verificamos si viene el ID del usuario
    if (!isset($_POST['id_Usuarios'])) {
        throw new Exception("ID de usuario no proporcionado.");
    }

    $id_usuario = (int) $_POST['id_Usuarios'];

    // Sanitización de campos
    $nombre          = trim(filter_var($_POST['nombre'] ?? '', FILTER_SANITIZE_STRING));
    $nombre_usuario  = trim(filter_var($_POST['nombre_usuario'] ?? '', FILTER_SANITIZE_STRING));
    $password        = $_POST['password'] ?? ''; // puede estar vacío
    $edad            = (int) ($_POST['edad'] ?? 0);
    $estado          = trim(filter_var($_POST['estado'] ?? '', FILTER_SANITIZE_STRING));
    $id_roles        = (int) ($_POST['id_roles'] ?? 0);

    // Validaciones básicas (sin incluir la contraseña)
    if (empty($nombre) || empty($nombre_usuario) || $edad <= 0 || empty($estado) || $id_roles <= 0) {
        throw new Exception("Todos los campos obligatorios deben completarse correctamente.");
    }

    // Obtener la foto actual
    $stmt = $pdo->prepare("SELECT foto FROM usuarios WHERE id_Usuarios = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        throw new Exception("Usuario no encontrado.");
    }

    $foto_actual = $usuario['foto'];
    $nueva_foto = $foto_actual; // mantiene la actual por defecto

    // --- Manejo de la foto ---
    if (!empty($_FILES['foto']['name'])) {
        $foto_nombre = basename($_FILES['foto']['name']);
        $extension   = strtolower(pathinfo($foto_nombre, PATHINFO_EXTENSION));

        // Validar formato
        $formatos_validos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $formatos_validos)) {
            throw new Exception("Formato de imagen no válido. Solo se permiten JPG, PNG, GIF o WEBP.");
        }

        // Nombre único
        $nombre_final = uniqid('user_') . '.' . $extension;
        $ruta_destino = $ruta_imagenes . $nombre_final;

        // Subir imagen
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_destino)) {
            throw new Exception("Error al subir la imagen del usuario.");
        }

        // Eliminar imagen anterior si existe
        if (!empty($foto_actual) && file_exists($ruta_imagenes . $foto_actual)) {
            unlink($ruta_imagenes . $foto_actual);
        }

        $nueva_foto = $nombre_final;
    }

    // --- Construcción del UPDATE dinámico ---
    $sql_update = "UPDATE usuarios
                   SET nombre = :nombre,
                       nombre_usuario = :nombre_usuario,
                       edad = :edad,
                       estado = :estado,
                       id_roles = :id_roles,
                       foto = :foto";

    $params = [
        ':nombre' => $nombre,
        ':nombre_usuario' => $nombre_usuario,
        ':edad' => $edad,
        ':estado' => $estado,
        ':id_roles' => $id_roles,
        ':foto' => $nueva_foto,
        ':id' => $id_usuario
    ];

    // Solo actualizar la contraseña si el usuario la ingresó
    if (!empty(trim($password))) {
        $password_segura = password_hash($password, PASSWORD_DEFAULT);
        $sql_update .= ", password = :password";
        $params[':password'] = $password_segura;
    }

    $sql_update .= " WHERE id_Usuarios = :id";

    // Ejecutar actualización
    $stmt = $pdo->prepare($sql_update);
    $stmt->execute($params);

    // Notificación de éxito
    $_SESSION['titulo']  = '¡Actualización exitosa!';
    $_SESSION['mensaje'] = 'Los datos del usuario se han actualizado correctamente.';
    $_SESSION['icono']   = 'success';

    header('Location: ' . $URL . 'usuarios');
    exit;

} catch (Exception $e) {
    // Notificación de error
    $_SESSION['titulo']  = '¡Error!';
    $_SESSION['mensaje'] = 'No se pudo actualizar el usuario: ' . $e->getMessage();
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'usuarios');
    exit;
}
?>
