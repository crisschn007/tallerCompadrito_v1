<?php
session_start(); // 🔹 Siempre al inicio

// 🔹 Conexión y sesión
include '../../../app/conexionBD.php';
include '../../../layouts/sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $monto = isset($_POST['monto']) ? floatval($_POST['monto']) : 0;
    $descripcion = trim($_POST['descripcion']);
    $id_usuario = $_SESSION['id_usuario'];

    try {
        if ($monto <= 0 || empty($descripcion)) {
            throw new Exception('Debe ingresar un monto válido y una descripción.');
        }

        // 🔹 Verificar caja abierta
        $sqlCaja = "SELECT * FROM Caja WHERE estado = 'abierta' AND id_usuario = :id_usuario LIMIT 1";
        $stmtCaja = $pdo->prepare($sqlCaja);
        $stmtCaja->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmtCaja->execute();
        $caja = $stmtCaja->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            throw new Exception('No tienes una caja abierta. Debes abrir una antes de registrar una devolución.');
        }

        $id_caja = $caja['id_caja'];

        // 🔹 Registrar movimiento
        $sqlInsert = "INSERT INTO Historial_Caja (id_caja, tipo_movimiento, monto, descripcion, fecha_movimiento, id_usuario)
              VALUES (:id_caja, 'devolucion', :monto, :descripcion, NOW(), :id_usuario)";
        $stmtInsert = $pdo->prepare($sqlInsert);
        $stmtInsert->bindParam(':id_caja', $id_caja);
        $stmtInsert->bindParam(':monto', $monto);
        $stmtInsert->bindParam(':descripcion', $descripcion);
        $stmtInsert->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        $stmtInsert->execute();


        // 🔹 Actualizar monto actual (SUMA)
        $sqlUpdate = "UPDATE Caja SET monto_actual = monto_actual + :monto WHERE id_caja = :id_caja";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':monto', $monto);
        $stmtUpdate->bindParam(':id_caja', $id_caja);
        $stmtUpdate->execute();

        // ✅ Almacenar notificación
        $_SESSION['titulo']  = 'Devolución registrada';
        $_SESSION['mensaje'] = 'La devolución se registró correctamente y el monto de la caja fue actualizado.';
        $_SESSION['icono']   = 'success';
    } catch (Exception $e) {
        $_SESSION['titulo']  = 'Error';
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['icono']   = 'error';
    }

    // ✅ Redirección limpia (NO modificar)
    header('Location: ' . $URL . 'caja/administrar');
    exit;
}
