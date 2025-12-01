<?php
session_start();
include '../../conexionBD.php';
include '../../layouts/sesion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_caja = $_POST['id_caja'];
    $nuevo_monto = $_POST['nuevo_monto'];
    $id_usuario = $_POST['id_usuario'];

    try {
        // Validar datos obligatorios
        if (empty($id_caja) || empty($nuevo_monto) || empty($id_usuario)) {
            throw new Exception('Faltan datos para actualizar el monto.');
        }

        // Verificar si la caja existe y está abierta
        $sqlCheck = "SELECT * FROM caja WHERE id_caja = :id_caja AND estado = 'abierta'";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
        $stmtCheck->execute();
        $caja = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            throw new Exception('No se puede editar el monto. La caja no está abierta o no existe.');
        }

        // Actualizar el monto de apertura y monto actual
        $sqlUpdate = "UPDATE caja 
                      SET monto_apertura = :nuevo_monto, 
                          monto_actual = :nuevo_monto
                      WHERE id_caja = :id_caja";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':nuevo_monto', $nuevo_monto);
        $stmtUpdate->bindParam(':id_caja', $id_caja, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // No se registra en historial, porque es una corrección del monto inicial,
        // no un movimiento financiero.

        // Mostrar notificación de éxito
        $_SESSION['titulo']  = 'Monto actualizado';
        $_SESSION['mensaje'] = 'El monto inicial fue actualizado correctamente.';
        $_SESSION['icono']   = 'success';

        header('Location: ' . $URL . 'caja/administrar');
        exit;

    } catch (Exception $e) {
        $_SESSION['titulo']  = 'Error';
        $_SESSION['mensaje'] = $e->getMessage();
        $_SESSION['icono']   = 'error';
        header('Location: ' . $URL . 'caja/administrar');
        exit;
    }
}
?>
