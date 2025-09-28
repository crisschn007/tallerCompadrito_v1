<?php
include '../../conexionBD.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (
        isset(
            $_POST['nombre_completo'],
            $_POST['direccion_cliente'],
            $_POST['telefono_cliente'],
            $_POST['email_cliente'],
            $_POST['dpi_nit_cliente'],
            $_POST['genero_cliente'],
            $_POST['estado_cliente']
        ) &&

        !empty(trim($_POST['nombre_completo'])) &&
        !empty(trim($_POST['direccion_cliente'])) &&
        !empty(trim($_POST['telefono_cliente'])) &&
        !empty(trim($_POST['email_cliente'])) &&
        !empty(trim($_POST['dpi_nit_cliente'])) &&
        !empty(trim($_POST['genero_cliente'])) &&
        !empty(trim($_POST['estado_cliente']))
    ) {
        // Sanitizar las entradas
        $nombre_completoC = trim($_POST['nombre_completo']);
        $direccion_C      = trim($_POST['direccion_cliente']);
        $telefono_C       = trim($_POST['telefono_cliente']);
        $email_C          = trim($_POST['email_cliente']);
        $cui_C            = trim($_POST['dpi_nit_cliente']);
        $genero_C         = trim($_POST['genero_cliente']);
        $estado_C         = trim($_POST['estado_cliente']);

        try {
            // Preparar la consulta de inserción
            $sql = "INSERT INTO cliente
                (nombre_y_apellido, direccion, telefono, email, cui, genero, estado)
                VALUES
                (:nombre_completo, :direccion_cliente, :telefono_cliente, :email_cliente, :dpi_nit_cliente, :genero_cliente, :estado_cliente)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nombre_completo', $nombre_completoC);
            $stmt->bindParam(':direccion_cliente', $direccion_C);
            $stmt->bindParam(':telefono_cliente', $telefono_C);
            $stmt->bindParam(':email_cliente', $email_C);
            $stmt->bindParam(':dpi_nit_cliente', $cui_C);
            $stmt->bindParam(':genero_cliente', $genero_C); 
            $stmt->bindParam(':estado_cliente', $estado_C);
            $stmt->execute();

            // Notificación de éxito
            $_SESSION['titulo']  = '¡Bien Hecho!';
            $_SESSION['mensaje'] = 'El cliente ha sido registrado correctamente.';
            $_SESSION['icono']   = 'success';

            header('Location: ' . $URL . 'clientes');
            exit;
        } catch (PDOException $e) {
            // Notificación de error
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo registrar el nuevo cliente: ' . $e->getMessage();
            $_SESSION['icono']   = 'error';

            header('Location: ' . $URL . 'clientes');
            exit;
        }
    } else {
        // Notificación si faltan campos
        /*  $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'Todos los campos son obligatorios.';
        $_SESSION['icono']   = 'warning';*/

        header('Location: ' . $URL . 'clientes');
        exit;
    }
} else {
    // Acceso indebido
    $_SESSION['titulo']  = '¡Acceso no permitido!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder a esta función.';
    $_SESSION['icono']   = 'error';

    header('Location: ' . $URL . 'clientes');
    exit;
}
