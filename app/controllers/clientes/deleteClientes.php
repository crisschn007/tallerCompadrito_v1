<?php
include '../../conexionBD.php';

if (isset($_GET['id'])) {
    $id_cliente = (int) $_GET['id'];

    $sql = "DELETE FROM cliente WHERE id_cliente = :id";
    $query = $pdo->prepare($sql);
    $query->bindParam(':id', $id_cliente, PDO::PARAM_INT);

    if ($query->execute()) {
        session_start();
        $_SESSION['titulo']  = '¡Bien Hecho!';
        $_SESSION['mensaje'] = "Cliente eliminado correctamente";
        $_SESSION['icono'] = "success";
    } else {
        session_start();
        $_SESSION['titulo']  = '¡Error!';
        $_SESSION['mensaje'] = "Error al eliminar cliente";
        $_SESSION['icono'] = "error";
    }
}

header('Location: ' . $URL . 'clientes');
exit;
