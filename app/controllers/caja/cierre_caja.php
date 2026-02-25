<?php
# app/controllers/caja/cierre_caja.php

require_once '../../conexionBD.php';

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

    $id_usuario    = (int) $_SESSION['id_usuario'];
    $id_caja       = isset($_POST['id_caja']) ? (int) $_POST['id_caja'] : 0;
    $monto_contado = isset($_POST['monto_final']) ? floatval($_POST['monto_final']) : 0;
    $observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

    if ($id_caja <= 0 || $monto_contado < 0) {
        throw new Exception('Datos inválidos.');
    }

    $pdo->beginTransaction();

    // 🔒 Buscar caja abierta del usuario y bloquearla
    $sqlCaja = "
        SELECT monto_actual
        FROM caja
        WHERE id_caja = :id_caja
          AND id_Usuarios = :id_usuario
          AND estado = 'abierta'
        LIMIT 1
        FOR UPDATE
    ";

    $stmtCaja = $pdo->prepare($sqlCaja);
    $stmtCaja->execute([
        'id_caja'    => $id_caja,
        'id_usuario' => $id_usuario
    ]);

    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        throw new Exception('No se encontró una caja abierta válida.');
    }

    $monto_sistema = (float) $caja['monto_actual'];
    $diferencia = $monto_contado - $monto_sistema;

    // 🔍 Registrar ajuste si existe diferencia
    if ($diferencia != 0) {

        $tipo = $diferencia > 0 ? 'ingreso' : 'egreso';
        $monto_ajuste = abs($diferencia);

        $sqlAjuste = "
            INSERT INTO historial_caja
            (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, tabla_origen, id_Usuarios)
            VALUES
            (:id_caja, :tipo, :monto, :descripcion, NOW(), 'Otro', :id_usuario)
        ";

        $stmtAjuste = $pdo->prepare($sqlAjuste);
        $stmtAjuste->execute([
            'id_caja'    => $id_caja,
            'tipo'       => $tipo,
            'monto'      => $monto_ajuste,
            'descripcion'=> 'Ajuste por cierre de caja. ' . $observaciones,
            'id_usuario' => $id_usuario
        ]);
    }

    // 🔒 Cerrar caja (NO sobrescribimos monto_actual arbitrariamente)
    $sqlCerrar = "
        UPDATE caja
        SET estado = 'cerrada',
            fecha_cierre = NOW()
        WHERE id_caja = :id_caja
    ";

    $stmtCerrar = $pdo->prepare($sqlCerrar);
    $stmtCerrar->execute([
        'id_caja' => $id_caja
    ]);

    $pdo->commit();

    $_SESSION['titulo']  = '¡Caja Cerrada!';
    $_SESSION['mensaje'] = 'El cierre de caja se realizó correctamente.';
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
