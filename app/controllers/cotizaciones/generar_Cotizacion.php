<?php
require_once '../../conexionBD.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    // 🔹 Campos obligatorios
    $campos_obligatorios = [
        'id_cliente',
        'id_usuario',
        'condicion_pago',
        'estado',
        'total',
        'productos',
        'tipo_precio'
    ];

    foreach ($campos_obligatorios as $campo) {
        if (!isset($_POST[$campo]) || $_POST[$campo] === '') {
            throw new Exception("Falta el campo: $campo");
        }
    }

    // 🔹 Variables
    $id_cliente     = (int) $_POST['id_cliente'];
    $id_usuario     = (int) $_POST['id_usuario'];
    $condicion_pago = trim($_POST['condicion_pago']);
    $estado         = trim($_POST['estado']);
    $total          = (float) $_POST['total'];
    $tipo_precio    = trim($_POST['tipo_precio']);
    $productos      = json_decode($_POST['productos'], true);

    // 🔹 Validaciones
    if (!is_array($productos) || count($productos) === 0) {
        throw new Exception('Debe incluir al menos un producto.');
    }

    $estados_permitidos = ['Pendiente', 'Aceptada', 'Rechazada'];
    if (!in_array($estado, $estados_permitidos)) {
        throw new Exception('Estado inválido.');
    }

    $tipos_precio_permitidos = ['Normal', 'Mayorista'];
    if (!in_array($tipo_precio, $tipos_precio_permitidos)) {
        throw new Exception('Tipo de precio inválido.');
    }

    // 🔹 Iniciar transacción
    $pdo->beginTransaction();

    // 🔹 1. Insertar cotización
    $sqlCotizacion = "
        INSERT INTO cotizacion
        (total, estado, condicion_pago, id_cliente, id_Usuarios)
        VALUES (:total, :estado, :condicion_pago, :id_cliente, :id_Usuarios)
    ";

    $stmt = $pdo->prepare($sqlCotizacion);
    $stmt->execute([
        ':total'          => $total,
        ':estado'         => $estado,
        ':condicion_pago' => $condicion_pago,
        ':id_cliente'     => $id_cliente,
        ':id_Usuarios'    => $id_usuario
    ]);

    $id_cotizacion = $pdo->lastInsertId();

    // 🔹 2. Insertar detalle de cotización
    $sqlDetalle = "
        INSERT INTO detalle_cotizacion
        (cantidad, precio_unitario, tipo_precio, id_producto, id_Cotizacion)
        VALUES (:cantidad, :precio_unitario, :tipo_precio, :id_producto, :id_Cotizacion)
    ";

    $stmtDetalle = $pdo->prepare($sqlDetalle);

    foreach ($productos as $p) {

        if (
            !isset($p['id_producto'], $p['cantidad'], $p['precio_unitario']) ||
            $p['cantidad'] <= 0 ||
            $p['precio_unitario'] < 0
        ) {
            throw new Exception('Datos de producto inválidos.');
        }

        $stmtDetalle->execute([
            ':cantidad'        => (int) $p['cantidad'],
            ':precio_unitario' => (float) $p['precio_unitario'],
            ':tipo_precio'     => $tipo_precio,
            ':id_producto'     => (int) $p['id_producto'],
            ':id_Cotizacion'   => $id_cotizacion
        ]);
    }

    // 🔹 Confirmar transacción
    $pdo->commit();

    // ✅ MENSAJE DE ÉXITO
    $_SESSION['titulo']  = "Listo";
    $_SESSION['mensaje'] = "La cotización se guardó correctamente.";
    $_SESSION['icono']   = "success";

    header('Location: ' . $URL . 'cotizaciones/nueva');
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // ❌ MENSAJE DE ERROR
    $_SESSION['titulo']  = "Error";
    $_SESSION['mensaje'] = $e->getMessage();
    $_SESSION['icono']   = "error";

    header('Location: ' . $URL . 'cotizaciones/nueva');
    exit;
}
