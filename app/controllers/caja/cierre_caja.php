<?php
session_start();
include '../../conexionBD.php';

// Recibir datos del formulario
$id_caja       = $_POST['id_caja'] ?? null;
$monto_contado = $_POST['monto_final'] ?? 0; // El input del modal sigue llamándose 'monto_final'
$observaciones = $_POST['observaciones'] ?? '';

if ($id_caja) {
    // Actualizar la caja como cerrada usando monto_actual
    $stmt = $pdo->prepare("
        UPDATE Caja
        SET monto_actual   = :monto_contado,
            estado         = 'cerrada',
            fecha_cierre   = NOW()
        WHERE id_caja = :id_caja
    ");
    $stmt->execute([
        ':monto_contado' => $monto_contado,
        ':id_caja'       => $id_caja
    ]);

    // Guardar observaciones en historial_caja como movimiento de cierre (opcional)
    $stmtHistorial = $pdo->prepare("
        INSERT INTO historial_caja (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, tabla_origen, id_usuario)
        VALUES (:id_caja, 'egreso', :monto, :descripcion, NOW(), 'Cierre', :id_usuario)
    ");
    $stmtHistorial->execute([
        ':id_caja'     => $id_caja,
        ':monto'       => $monto_contado,
        ':descripcion' => $observaciones,
        ':id_usuario'  => $_SESSION['id_usuario']
    ]);

    // Notificación de éxito
    $_SESSION['titulo']  = '¡Caja Cerrada!';
    $_SESSION['mensaje'] = 'El cierre de caja se realizó correctamente.';
    $_SESSION['icono']   = 'success';
} else {
    // Notificación de error
    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = 'No se pudo identificar la caja a cerrar.';
    $_SESSION['icono']   = 'error';
}

// Redirigir a la administración de caja
header('Location: ' . $URL . 'caja/administrar');
exit;
