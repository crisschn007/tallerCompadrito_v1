<?php
# app/controllers/caja/editar_monto.php

require_once '../../conexionBD.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        if (!isset($_SESSION['id_usuario'])) {
            throw new Exception('Sesión inválida.');
        }

        $id_usuario  = (int) $_SESSION['id_usuario'];
        $id_caja     = isset($_POST['id_caja']) ? (int) $_POST['id_caja'] : 0;
        $nuevo_monto = isset($_POST['nuevo_monto']) ? floatval($_POST['nuevo_monto']) : 0;

        if ($id_caja <= 0 || $nuevo_monto <= 0) {
            throw new Exception('Datos inválidos para actualizar el monto.');
        }

        // 🔍 Verificar que la caja exista, esté abierta y pertenezca al usuario
        $sqlCheck = "
            SELECT monto_apertura, monto_actual
            FROM caja
            WHERE id_caja = :id_caja
              AND id_Usuarios = :id_usuario
              AND estado = 'abierta'
            LIMIT 1
        ";

        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            'id_caja'   => $id_caja,
            'id_usuario'=> $id_usuario
        ]);

        $caja = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            throw new Exception('No se puede editar el monto. La caja no está abierta o no te pertenece.');
        }

        $monto_anterior = (float) $caja['monto_apertura'];
        $monto_actual   = (float) $caja['monto_actual'];

        // 🧮 Calcular diferencia
        $diferencia = $nuevo_monto - $monto_anterior;
        $nuevo_monto_actual = $monto_actual + $diferencia;

        // 🔄 Actualizar correctamente
        $sqlUpdate = "
            UPDATE caja
            SET monto_apertura = :nuevo_monto,
                monto_actual   = :nuevo_monto_actual
            WHERE id_caja = :id_caja
        ";

        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            'nuevo_monto'        => $nuevo_monto,
            'nuevo_monto_actual' => $nuevo_monto_actual,
            'id_caja'            => $id_caja
        ]);

        $_SESSION['titulo']  = 'Monto actualizado';
        $_SESSION['mensaje'] = 'El monto inicial fue actualizado correctamente.';
        $_SESSION['icono']   = 'success';

    } catch (Exception $e) {

        $_SESSION['titulo']  = 'Error';
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['icono']   = 'error';
    }

    header('Location: ' . $URL . 'caja/administrar');
    exit;
}
