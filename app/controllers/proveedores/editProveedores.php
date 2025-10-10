<?php
include '../../conexionBD.php'; // conexión PDO y variable $URL
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (
        isset(
            $_POST['id_proveedor'],
            $_POST['nombre_empresa'],
            $_POST['representante'],
            $_POST['direccion'],
            $_POST['telefono'],
            $_POST['email'],
            $_POST['sitio_web'],
            $_POST['estado'],
            $_POST['condicion_pago']
        )
        &&

        !empty(trim($_POST['nombre_empresa'])) &&
        !empty(trim($_POST['representante'])) &&
        !empty(trim($_POST['direccion'])) &&
        !empty(trim($_POST['telefono'])) &&
        !empty(trim($_POST['email'])) &&
        !empty(trim($_POST['sitio_web'])) &&
        !empty(trim($_POST['estado'])) &&
        !empty(trim($_POST['condicion_pago']))
    ) {
        // Sanitizar datos (datos declarados en el index)
        $id_Proveedor       = (int) $_POST['id_proveedor'];
        $nombre_empresa = trim($_POST['nombre_empresa']);
        $representante  = trim($_POST['representante']);
        $direccion      = trim($_POST['direccion']);
        $telefono       = trim($_POST['telefono']);
        $email          = trim($_POST['email']);
        $sitio_web      = trim($_POST['sitio_web']);
        $estado         = trim($_POST['estado']);
        $condicion_pago = trim($_POST['condicion_pago']);

        try {
            $sql = " UPDATE proveedor SET nombre_empresa= :nombre_empresa , representante= :representante,
            direccion=:direccion , telefono=:telefono, email=:email, sitio_web=:sitio_web,
            estado= :estado, condicion_pago= :condicion_pago WHERE id_proveedor= :id_proveedor ";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_empresa', $nombre_empresa);
            $stmt->bindParam(':representante', $representante);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':sitio_web', $sitio_web);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':condicion_pago', $condicion_pago);
            $stmt->bindParam(':id_proveedor', $id_Proveedor, PDO::PARAM_INT);
            $stmt->execute();


            // Notificación de éxito
            $_SESSION['titulo']  = '¡Bien Hecho!';
            $_SESSION['mensaje'] = 'El proveedor ha sido modificado correctamente.';
            $_SESSION['icono']   = 'success';

            header('Location: ' . $URL . 'proveedores');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo modificar los datos del proveedor: ' . $e->getMessage();
            $_SESSION['icono']   = 'error';

            header('Location: ' . $URL . 'proveedores');
            exit;
        }
    } else {
        // Notificación si faltan campos
        /*$_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'Todos los campos son obligatorios.';
        $_SESSION['icono']   = 'warning';*/

        header('Location: ' . $URL . 'proveedores');
        exit;
    }
} else {
    // Acceso indebido
    $_SESSION['titulo']  = '¡Acceso no permitido!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder a esta función.';
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'proveedores');
    exit;
}
