<?php
include '../../conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST['id_categoria'], $_POST['nombre_categoria'], $_POST['descripcion_cate'], $_POST['estadoCategoria']) &&
        !empty(trim($_POST['nombre_categoria'])) &&
        !empty(trim($_POST['descripcion_cate'])) &&
        !empty(trim($_POST['estadoCategoria']))
    ) {
        // Sanitizar y asignar variables
        $id_Categoria         = (int) $_POST['id_categoria'];
        $nombre_Categoria     = trim($_POST['nombre_categoria']);
        $descripcion_Categoria = trim($_POST['descripcion_cate']);
        $estado_Categoria     = trim($_POST['estadoCategoria']);

        try {
            // Consulta SQL segura
            $sql = "UPDATE categoria
                    SET nombre = :nombre,
                        descripcion = :descripcion,
                        estado = :estado
                    WHERE id_categoria = :id_categoria";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre', $nombre_Categoria, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion_Categoria, PDO::PARAM_STR);
            $stmt->bindParam(':estado', $estado_Categoria, PDO::PARAM_STR);
            $stmt->bindParam(':id_categoria', $id_Categoria, PDO::PARAM_INT);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo'] = '¡Bien Hecho!';
            $_SESSION['mensaje'] = "Datos actualizados correctamente";
            $_SESSION['icono'] = "success";
            header('Location: ' . $URL . 'categorias');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo'] = '¡Error!';
            $_SESSION['mensaje'] = 'Error al actualizar la categoría: ' . $e->getMessage();
            $_SESSION['icono'] = 'error';
            header('Location: ' . $URL . 'categorias');
            exit;
        }
    } else {
        // Notificación por campos vacíos
        $_SESSION['titulo'] = '¡Atención!';
        $_SESSION['mensaje'] = 'Por favor, completa todos los campos obligatorios.';
        $_SESSION['icono'] = 'warning';
        header('Location: ' . $URL . 'categorias');
        exit;
    }
} else {
    // Acceso no permitido
    $_SESSION['titulo'] = '¡Error!';
    $_SESSION['mensaje'] = 'Acceso no permitido';
    $_SESSION['icono'] = 'error';
    header('Location: ' . $URL . 'categorias');
    exit;
}
