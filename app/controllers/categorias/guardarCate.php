<?php

include '../../conexionBD.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (
        isset($_POST['nombre_categoria'], $_POST['descripcion_cate'], $_POST['estadoCategoria']) &&
        !empty(trim($_POST['nombre_categoria'])) &&
        !empty(trim($_POST['descripcion_cate'])) &&
        !empty(trim($_POST['estadoCategoria']))
    ) {


        // Sanitizar entradas
        $nombre_Categoria = trim($_POST['nombre_categoria']);
        $descripcion_Categoria  = trim($_POST['descripcion_cate']);
        $estado_Categoria       = trim($_POST['estadoCategoria']);
        try {
            // Preparar la consulta de insercióntrim(
            $sql = "INSERT INTO categoria (nombre, descripcion, estado)
            VALUES (:nombre_categoria, :descripcion_cate, :estadoCategoria);";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_categoria', $nombre_Categoria);
            $stmt->bindParam(':descripcion_cate', $descripcion_Categoria);
            $stmt->bindParam(':estadoCategoria', $estado_Categoria);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo']  = '¡Categoria Agregado!';
            $_SESSION['mensaje'] = 'La nueva categoria ha sido registrado correctamente.';
            $_SESSION['icono']   = 'success';

            header('Location: ' . $URL . 'categorias');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo registrar el nuevo rol: ' . $e->getMessage();
            $_SESSION['icono']   = 'error';

            header('Location: ' . $URL . 'categorias');
            exit;
        }
    } else {
        // Notificación si faltan campos
        $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'Todos los campos son obligatorios.';
        $_SESSION['icono']   = 'warning';

        header('Location: ' . $URL . 'categorias');
        exit;
    }
} else {
    // Acceso indebido
    $_SESSION['titulo']  = '¡Acceso no permitido!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder a esta función.';
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'categorias');
    exit;
}
