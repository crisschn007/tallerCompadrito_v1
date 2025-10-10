<?php
session_start();
include __DIR__ . '/../../conexionBD.php'; // ✅ Ruta absoluta

if (isset($_GET['id'])) {
    $id_proveedor = intval($_GET['id']); // ✅ Aseguramos que sea un entero

    try {
        // Nombre de tabla consistente con addProveedores.php
        $sql = "DELETE FROM Proveedor WHERE id_proveedor = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id_proveedor, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['titulo']  = '¡Bien Hecho!';
            $_SESSION['mensaje'] = 'El proveedor ha sido eliminado correctamente.';
            $_SESSION['icono']   = 'success';
        } else {
            $_SESSION['titulo']  = '¡Error!';
            $_SESSION['mensaje'] = 'No se pudo eliminar el proveedor.';
            $_SESSION['icono']   = 'error';
        }
    } catch (PDOException $e) {
        $_SESSION['titulo']  = '¡Error!';
        $_SESSION['mensaje'] = 'Error al intentar eliminar el proveedor: ' . $e->getMessage();
        $_SESSION['icono']   = 'error';
    }
} else {
    $_SESSION['titulo']  = '¡Advertencia!';
    $_SESSION['mensaje'] = 'No se recibió el identificador del proveedor.';
    $_SESSION['icono']   = 'warning';
}

// Redirigir siempre
header('Location: ' . $URL . 'proveedores');
exit;
