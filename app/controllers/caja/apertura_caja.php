<?php
session_start();
include '../../conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST['monto_apertura'], $_POST['id_usuario']) &&
        !empty(trim($_POST['monto_apertura']))
    ) {
        try {
            $monto_apertura = floatval($_POST['monto_apertura']);
            $id_usuario = intval($_POST['id_usuario']);

            // Fecha y hora actual
            $fecha_apertura = date('Y-m-d H:i:s');

            // Insertar en la tabla Caja
            $query = "INSERT INTO caja (fecha_apertura, monto_apertura, monto_actual, estado, id_usuario)
                      VALUES (:fecha_apertura, :monto_apertura, :monto_actual, 'abierta', :id_usuario)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fecha_apertura', $fecha_apertura);
            $stmt->bindParam(':monto_apertura', $monto_apertura);
            $stmt->bindParam(':monto_actual', $monto_apertura); // mismo valor inicial
            $stmt->bindParam(':id_usuario', $id_usuario);

            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo']  = '¡Caja Abierta!';
            $_SESSION['mensaje'] = 'La caja se ha aperturado correctamente.';
            $_SESSION['icono']   = 'success';

            header('Location: ' . $URL . 'caja/administrar');
            exit;

        } catch (PDOException $e) {
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo abrir la caja: ' . $e->getMessage();
            $_SESSION['icono']   = 'error';
            header('Location: ' . $URL . 'caja/administrar');
            exit;
        }

    } else {
        $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'El monto de apertura es obligatorio.';
        $_SESSION['icono']   = 'warning';
        header('Location: ' . $URL . 'caja/administrar');
        exit;
    }

} else {
    $_SESSION['titulo']  = '¡Acceso denegado!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder directamente a esta página.';
    $_SESSION['icono']   = 'error';
    header('Location: ' . $URL . 'caja/administrar');
    exit;
}
?>
