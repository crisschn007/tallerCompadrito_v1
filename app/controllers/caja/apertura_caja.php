<<<<<<< HEAD
<?php
# app/controllers/caja/apertura_caja.php

require_once '../../conexionBD.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['id_usuario'])) {
        $_SESSION['titulo']  = 'Sesión inválida';
        $_SESSION['mensaje'] = 'Debe iniciar sesión nuevamente.';
        $_SESSION['icono']   = 'error';
        header('Location: ' . $URL . 'auth/');
        exit;
    }

    if (isset($_POST['monto_apertura']) && trim($_POST['monto_apertura']) !== '') {

        try {

            $monto_apertura = floatval($_POST['monto_apertura']);
            $id_usuario     = (int) $_SESSION['id_usuario'];
            $fecha_apertura = date('Y-m-d H:i:s');

            // 🔍 Verificar si ya existe caja abierta
            $verificar = $pdo->prepare("
                SELECT id_caja 
                FROM caja 
                WHERE id_Usuarios = :id_usuario 
                  AND estado = 'abierta'
                LIMIT 1
            ");
            $verificar->execute(['id_usuario' => $id_usuario]);

            if ($verificar->fetch()) {

                $_SESSION['titulo']  = 'Caja ya abierta';
                $_SESSION['mensaje'] = 'Ya tienes una caja abierta actualmente.';
                $_SESSION['icono']   = 'warning';
                header('Location: ' . $URL . 'caja/administrar');
                exit;
            }

            // ✅ Insertar nueva caja
            $query = "
                INSERT INTO caja 
                (fecha_apertura, monto_apertura, monto_actual, estado, id_Usuarios)
                VALUES 
                (:fecha_apertura, :monto_apertura, :monto_actual, 'abierta', :id_usuario)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'fecha_apertura' => $fecha_apertura,
                'monto_apertura' => $monto_apertura,
                'monto_actual'   => $monto_apertura,
                'id_usuario'     => $id_usuario
            ]);

            $_SESSION['titulo']  = '¡Caja Abierta!';
            $_SESSION['mensaje'] = 'La caja se ha aperturado correctamente.';
            $_SESSION['icono']   = 'success';

        } catch (PDOException $e) {

            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo abrir la caja.';
            $_SESSION['icono']   = 'error';
        }

    } else {

        $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'El monto de apertura es obligatorio.';
        $_SESSION['icono']   = 'warning';
    }

} else {

    $_SESSION['titulo']  = '¡Acceso denegado!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder directamente.';
    $_SESSION['icono']   = 'error';
}

header('Location: ' . $URL . 'caja/administrar');
exit;
=======
<?php
# app/controllers/caja/apertura_caja.php

require_once '../../conexionBD.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['id_usuario'])) {
        $_SESSION['titulo']  = 'Sesión inválida';
        $_SESSION['mensaje'] = 'Debe iniciar sesión nuevamente.';
        $_SESSION['icono']   = 'error';
        header('Location: ' . $URL . 'auth/');
        exit;
    }

    if (isset($_POST['monto_apertura']) && trim($_POST['monto_apertura']) !== '') {

        try {

            $monto_apertura = floatval($_POST['monto_apertura']);
            $id_usuario     = (int) $_SESSION['id_usuario'];
            $fecha_apertura = date('Y-m-d H:i:s');

            // 🔍 Verificar si ya existe caja abierta
            $verificar = $pdo->prepare("
                SELECT id_caja 
                FROM caja 
                WHERE id_Usuarios = :id_usuario 
                  AND estado = 'abierta'
                LIMIT 1
            ");
            $verificar->execute(['id_usuario' => $id_usuario]);

            if ($verificar->fetch()) {

                $_SESSION['titulo']  = 'Caja ya abierta';
                $_SESSION['mensaje'] = 'Ya tienes una caja abierta actualmente.';
                $_SESSION['icono']   = 'warning';
                header('Location: ' . $URL . 'caja/administrar');
                exit;
            }

            // ✅ Insertar nueva caja
            $query = "
                INSERT INTO caja 
                (fecha_apertura, monto_apertura, monto_actual, estado, id_Usuarios)
                VALUES 
                (:fecha_apertura, :monto_apertura, :monto_actual, 'abierta', :id_usuario)
            ";

            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'fecha_apertura' => $fecha_apertura,
                'monto_apertura' => $monto_apertura,
                'monto_actual'   => $monto_apertura,
                'id_usuario'     => $id_usuario
            ]);

            $_SESSION['titulo']  = '¡Caja Abierta!';
            $_SESSION['mensaje'] = 'La caja se ha aperturado correctamente.';
            $_SESSION['icono']   = 'success';

        } catch (PDOException $e) {

            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo abrir la caja.';
            $_SESSION['icono']   = 'error';
        }

    } else {

        $_SESSION['titulo']  = '¡Atención!';
        $_SESSION['mensaje'] = 'El monto de apertura es obligatorio.';
        $_SESSION['icono']   = 'warning';
    }

} else {

    $_SESSION['titulo']  = '¡Acceso denegado!';
    $_SESSION['mensaje'] = 'No tienes permiso para acceder directamente.';
    $_SESSION['icono']   = 'error';
}

header('Location: ' . $URL . 'caja/administrar');
exit;
>>>>>>> cfc5285756738da19aa887ceb403c03569566b27
