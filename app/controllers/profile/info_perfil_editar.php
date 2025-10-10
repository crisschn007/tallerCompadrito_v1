<?php
session_start();
include '../../conexionBD.php';

// Verifica que el formulario haya sido enviado correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    $nombre = trim($_POST['nombre']);
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $edad = !empty($_POST['edad']) ? (int) $_POST['edad'] : null;
    $estado = $_POST['estado'];

    // Manejo de la foto
    $nombre_archivo = null;
    if (!empty($_FILES['foto']['name'])) {
        // Carpeta donde se guardarán las imágenes
        $carpeta = __DIR__ . '/../../../img/usuarios/';

        // Crear carpeta si no existe
        if (!file_exists($carpeta)) {
            mkdir($carpeta, 0777, true);
        }

        // Nombre único
        $nombre_archivo = uniqid() . "_" . basename($_FILES['foto']['name']);
        $ruta_fisica = $carpeta . $nombre_archivo;

        // Mover archivo subido
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_fisica)) {
            // Foto guardada correctamente
        } else {
            $nombre_archivo = null; // Error al subir
        }
    }

    try {
        // Obtener foto actual
        $sql_foto = "SELECT foto FROM usuarios WHERE id_Usuarios = :id";
        $stmt_foto = $pdo->prepare($sql_foto);
        $stmt_foto->execute([':id' => $id_usuario]);
        $foto_actual = $stmt_foto->fetchColumn();

        // Si se subió una nueva imagen, eliminar la anterior (si no es la default)
        if ($nombre_archivo && $foto_actual && $foto_actual !== 'default.png') {
            $foto_path = __DIR__ . '/../../../img/usuarios/' . $foto_actual;
            if (file_exists($foto_path)) {
                unlink($foto_path);
            }
        }

        // Si no se subió nueva foto, mantener la actual
        $foto_final = $nombre_archivo ? $nombre_archivo : $foto_actual;

        // Actualizar datos del usuario
        $sql_update = "UPDATE usuarios
                       SET nombre = :nombre,
                           nombre_usuario = :nombre_usuario,
                           edad = :edad,
                           estado = :estado,
                           foto = :foto
                       WHERE id_Usuarios = :id";
        $stmt = $pdo->prepare($sql_update);
        $stmt->execute([
            ':nombre' => $nombre,
            ':nombre_usuario' => $nombre_usuario,
            ':edad' => $edad,
            ':estado' => $estado,
            ':foto' => $foto_final,
            ':id' => $id_usuario
        ]);

        // Notificación de éxito
        $_SESSION['titulo'] = "Perfil actualizado";
        $_SESSION['mensaje'] = "Tus datos se guardaron correctamente.";
        $_SESSION['icono'] = "success";
    } catch (PDOException $e) {
        // En caso de error en la BD
        $_SESSION['titulo'] = "Error";
        $_SESSION['mensaje'] = "Ocurrió un error al actualizar el perfil.";
        $_SESSION['icono'] = "error";
    }

    // Redirección al perfil
    header('Location: ' . $URL . 'profile/');
    exit;
} else {
    // Si se accede sin POST válido
    $_SESSION['titulo'] = "Acceso inválido";
    $_SESSION['mensaje'] = "No se pudo procesar la solicitud.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . $URL . 'profile/');
    exit;
}
