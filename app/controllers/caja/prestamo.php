<?php
session_start(); // 🔹 Siempre al inicio

// 🔹 Conexión y sesión
include '../../../app/conexionBD.php';
include '../../../layouts/sesion.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Método no permitido.');
    }

    // 🔹 Datos del formulario
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $descripcion = trim($_POST['descripcion']);
    $id_usuario = $_SESSION['id_usuario'];

    if ($monto <= 0 || empty($descripcion)) {
        throw new Exception('Debe ingresar un monto válido y una descripción.');
    }

    // 🔹 Verificar caja abierta del usuario actual
    $sqlCaja = "SELECT * FROM Caja WHERE estado = 'abierta' AND id_usuario = :id_usuario LIMIT 1";
    $stmtCaja = $pdo->prepare($sqlCaja);
    $stmtCaja->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmtCaja->execute();
    $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

    if (!$caja) {
        throw new Exception('No tienes una caja abierta. Debes abrir una antes de registrar un préstamo.');
    }

    $id_caja = $caja['id_caja'];
    $monto_actual = (float)$caja['monto_actual'];

    if ($monto > $monto_actual) {
        throw new Exception('El monto del préstamo no puede ser mayor al monto disponible en caja.');
    }

    // 🔹 Registrar movimiento en Historial_Caja
    $sqlInsert = "INSERT INTO Historial_Caja (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, id_usuario)
                  VALUES (:id_caja, 'prestamo', :monto, :descripcion, NOW(), :id_usuario)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(':id_caja', $id_caja);
    $stmtInsert->bindParam(':monto', $monto);
    $stmtInsert->bindParam(':descripcion', $descripcion);
    $stmtInsert->bindParam(':id_usuario', $id_usuario);
    $stmtInsert->execute();

    // 🔹 Actualizar el monto actual en la tabla Caja
    $sqlUpdate = "UPDATE Caja SET monto_actual = monto_actual - :monto WHERE id_caja = :id_caja";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':monto', $monto);
    $stmtUpdate->bindParam(':id_caja', $id_caja);
    $stmtUpdate->execute();

    // ✅ Notificación de éxito
    $_SESSION['titulo']  = 'Préstamo registrado';
    $_SESSION['mensaje'] = 'El préstamo se registró correctamente y el monto de la caja fue actualizado.';
    $_SESSION['icono']   = 'success';

} catch (Exception $e) {
    // ❌ Notificación de error
    $_SESSION['titulo']  = 'Error';
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono']   = 'error';
}

// 🔹 Redirección final
header('Location: ' . $URL . 'caja/administrar');
exit;
?>
