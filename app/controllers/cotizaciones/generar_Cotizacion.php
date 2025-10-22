<?php
require_once '../../conexionBD.php';
session_start();

// 🔹 Mostrar errores para depuración (puedes quitar esto en producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 🔹 Aseguramos que no haya salida antes del JSON
ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    // ✅ Verificar datos requeridos
    $campos_obligatorios = ['id_cliente', 'id_usuario', 'condicion_pago', 'estado', 'total', 'productos'];
    foreach ($campos_obligatorios as $campo) {
        if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
            echo json_encode(['status' => 'error', 'message' => "Falta el campo: $campo"]);
            exit;
        }
    }

    // ✅ Asignar variables
    $id_cliente = (int) $_POST['id_cliente'];
    $id_usuario = (int) $_POST['id_usuario'];
    $condicion_pago = trim($_POST['condicion_pago']);
    $estado = trim($_POST['estado']);
    $total = (float) $_POST['total'];
    $productos = json_decode($_POST['productos'], true);

    // ✅ Validar productos
    if (!is_array($productos) || count($productos) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Debe incluir al menos un producto.']);
        exit;
    }

    // ✅ Validar estado permitido
    $estados_permitidos = ['Pendiente', 'Aprobada', 'Rechazada', 'Cancelada'];
    if (!in_array($estado, $estados_permitidos)) {
        echo json_encode(['status' => 'error', 'message' => 'Estado inválido.']);
        exit;
    }

    // ✅ Iniciar transacción
    $pdo->beginTransaction();

    // 🔹 1. Insertar en Cotizacion
    $sqlCotizacion = "INSERT INTO Cotizacion (total, estado, condicion_pago, id_cliente, id_Usuarios)
                      VALUES (:total, :estado, :condicion_pago, :id_cliente, :id_Usuarios)";
    $stmt = $pdo->prepare($sqlCotizacion);
    $stmt->execute([
        ':total' => $total,
        ':estado' => $estado,
        ':condicion_pago' => $condicion_pago,
        ':id_cliente' => $id_cliente,
        ':id_Usuarios' => $id_usuario
    ]);

    // 🔹 Obtener ID de la cotización recién creada
    $id_cotizacion = $pdo->lastInsertId();

    // 🔹 2. Insertar detalles
    $sqlDetalle = "INSERT INTO Detalle_Cotizacion (cantidad, precio_unitario, id_producto, id_Cotizacion)
                   VALUES (:cantidad, :precio_unitario, :id_producto, :id_Cotizacion)";
    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $p) {
        // ✅ Validar datos del producto
        if (
            !isset($p['id_producto'], $p['cantidad'], $p['precio_unitario']) ||
            $p['cantidad'] <= 0 || $p['precio_unitario'] < 0
        ) {
            throw new Exception('Datos de producto inválidos o incompletos.');
        }

        $stmtDetalle->execute([
            ':cantidad' => (int) $p['cantidad'],
            ':precio_unitario' => (float) $p['precio_unitario'],
            ':id_producto' => (int) $p['id_producto'],
            ':id_Cotizacion' => $id_cotizacion
        ]);
    }

    // ✅ Confirmar transacción
    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'La cotización se guardó correctamente.',
        'id_cotizacion' => $id_cotizacion
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // 🔹 Limpiar buffers para evitar HTML mezclado con JSON
    ob_clean();
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar la cotización: ' . $e->getMessage()
    ]);
}
