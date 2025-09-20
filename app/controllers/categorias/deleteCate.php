<?php
include '../../conexionBD.php';
session_start();

if (isset($_GET['id'])) {
    $id_Categoria = (int) $_GET['id'];

    try {
        $sql = "DELETE FROM categoria WHERE id_categoria = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_Categoria, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['titulo'] = "¡Eliminado!";
        $_SESSION['mensaje'] = "La categoría se eliminó correctamente.";
        $_SESSION['icono'] = "success";
        header('Location: ' . $URL . 'categorias');
        exit;
    } catch (PDOException $e) {
        $_SESSION['titulo'] = "¡Error!";
        $_SESSION['mensaje'] = "No se pudo eliminar la categoría: " . $e->getMessage();
        $_SESSION['icono'] = "error";
        header('Location: ' . $URL . 'categorias');
        exit;
    }
} else {
    $_SESSION['titulo'] = "¡Atención!";
    $_SESSION['mensaje'] = "ID de categoría no válido.";
    $_SESSION['icono'] = "warning";
    header('Location: ' . $URL . 'categorias');
    exit;
}
