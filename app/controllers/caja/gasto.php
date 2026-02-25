<?php
# app/controllers/caja/gasto.php

require_once '../../../app/conexionBD.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception('Sesión inválida.');
    }

    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
    $id_usuario = (int) $_SESSION['id_usuario'];

    if ($monto <= 0 || empty($descripcion)) {
        throw new Exception('Debe ingresar un monto válido y una descripción.');
    }

    // 🔒 Iniciar transacción
    $pdo->beginTransaction();

    // 🔍 Buscar caja abierta del usuario (bloqueo de fila)
    $sqlCaja = "
        SELECT id_caja, monto_actual
        FROM caja
        WHERE estado = 'abierta'
          AND id_Usuarios = :id_usuario
        LIMIT 1
        FOR UPDATE
    ";

    $stmtCaja = $pdo->prepare($sqlCaja);
    $stmtCaja->execute(['id_usuario' => $id_usuario]);
    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        throw new Exception('No tienes una caja abierta.');
    }

    $id_caja = (int) $caja['id_caja'];
    $monto_actual = (float) $caja['monto_actual'];

    if ($monto > $monto_actual) {
        throw new Exception('El monto del gasto no puede ser mayor al monto disponible.');
    }

    // 📝 Insertar en historial
    $sqlInsert = "
        INSERT INTO historial_caja
        (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, id_Usuarios)
        VALUES
        (:id_caja, 'gasto', :monto, :descripcion, NOW(), :id_usuario)
    ";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        'id_caja'    => $id_caja,
        'monto'      => $monto,
        'descripcion'=> $descripcion,
        'id_usuario' => $id_usuario
    ]);

    // 💰 Actualizar monto actual (RESTA)
    $sqlUpdate = "
        UPDATE caja
        SET monto_actual = monto_actual - :monto
        WHERE id_caja = :id_caja
    ";

    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([
        'monto'   => $monto,
        'id_caja' => $id_caja
    ]);

    // ✅ Confirmar cambios
    $pdo->commit();

    $_SESSION['titulo']  = 'Gasto registrado';
    $_SESSION['mensaje'] = 'El gasto se registró correctamente.';
    $_SESSION['icono']   = 'success';

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono']   = 'error';
}

header('Location: ' . $URL . 'caja/administrar');
exit;
