<?php
include '../app/conexionBD.php';
include '../layouts/sesion.php';

$id_usuario = $_SESSION['id_usuario'] ?? null;

$usuario = [];
if ($id_usuario) {
    $sql = "SELECT u.nombre,
                   u.nombre_usuario AS usuarioLogin,
                   u.edad,
                   u.estado,
                   u.foto,
                   r.nombre_roles
            FROM usuarios u
            INNER JOIN roles r ON u.id_roles = r.id_roles
            WHERE u.id_Usuarios = :id;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $id_usuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

// Foto de perfil
$fotoPerfil = (!empty($usuario['foto']))
    ? $URL . "img/usuarios/" . $usuario['foto']
    : $URL . "img/usuarios/default.png";

// Valores seguros con fallback
$nombre        = $usuario['nombre']        ?? "Usuario desconocido";
$usuario_login = $usuario['usuarioLogin']  ?? "N/A";   // <- CORREGIDO
$edad          = $usuario['edad']          ?? "N/A";
$estado        = $usuario['estado']        ?? "Inactivo";
$rol           = $usuario['nombre_roles']  ?? "Sin rol";
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <title>Perfil del Usuario</title>
    <?php include '../layouts/head.php'; ?>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <div class="app-wrapper">
        <?php include '../layouts/navAside.php'; ?>

        <main class="app-main">

            <br><br><br>

            <div class="app-content">
                <div class="container-fluid">

                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <div class="card card-outline card-secondary shadow-lg rounded-4">

                                <!-- Card Header con título y botón de colapsar -->
                                <div class="card-header">
                                    <h3 class="card-title">Perfil de Usuario</h3>
                                </div>

                                <!-- Card Body (contenido colapsable) -->
                                <div class="card-body text-center">
                                    <!-- Imagen -->
                                    <img src="<?= $fotoPerfil; ?>" alt="foto de perfil" class="rounded-circle mb-3 shadow" width="150" height="150" style="object-fit: cover;">

                                    <hr>

                                    <!-- Info -->
                                    <div class="text-start px-4">
                                        <p><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></p>
                                        <p><strong>Usuario:</strong> <?= htmlspecialchars($usuario_login) ?></p>
                                        <p><strong>Rol Designado:</strong> <?= htmlspecialchars($rol) ?></p>
                                        <p><strong>Edad:</strong> <?= htmlspecialchars($edad) ?> <?= $edad !== "N/A" ? "años" : "" ?></p>
                                        <p><strong>Estado:</strong>
                                            <?php if ($estado === 'Activo'): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>


                                    <hr>
                                    <!-- Botones -->
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Button trigger modal -->
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editarPerfil">
                                            <i class="bi bi-pencil-square"></i> Editar Perfil
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal -->
                    <div class="modal fade" id="editarPerfil" tabindex="-1" aria-labelledby="editarPerfilLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="../app/controllers/usuarios/editarPerfil.php" method="POST" enctype="multipart/form-data">
                                    <!-- Header -->
                                    <div class="modal-header bg-primary text-white">
                                        <h1 class="modal-title fs-5" id="editarPerfilLabel">
                                            <i class="bi bi-pencil-square"></i> Editar Perfil
                                        </h1>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body">
                                        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">
                                        <!-- Foto -->
                                        <div class="mb-3">
                                            <div class="mt-2 text-center">
                                                <img src="<?= $fotoPerfil ?>" alt="Foto actual" class="rounded-circle shadow" width="100" height="100" style="object-fit: cover;">
                                                <p class="small text-muted mt-1">Foto actual</p>
                                            </div>
                                            <label for="foto" class="form-label">Foto de perfil</label>
                                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                                        </div>

                                        <!-- Nombre -->
                                        <div class="mb-3">
                                            <label for="nombre" class="form-label">Nombre completo</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre"
                                                value="<?= htmlspecialchars($nombre) ?>" required>
                                        </div>

                                        <!-- Usuario -->
                                        <div class="mb-3">
                                            <label for="nombre_usuario" class="form-label">Usuario</label>
                                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario"
                                                value="<?= htmlspecialchars($usuario_login) ?>" required>

                                        </div>

                                        <!-- Edad -->
                                        <div class="mb-3">
                                            <label for="edad" class="form-label">Edad</label>
                                            <input type="number" class="form-control" id="edad" name="edad"
                                                value="<?= $edad !== "N/A" ? htmlspecialchars($edad) : "" ?>" min="1">
                                        </div>

                                        <!-- Estado -->
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado</label>
                                            <select class="form-select" id="estado" name="estado" required>
                                                <option value="Activo" <?= $estado === "Activo" ? "selected" : "" ?>>Activo</option>
                                                <option value="Inactivo" <?= $estado === "Inactivo" ? "selected" : "" ?>>Inactivo</option>
                                            </select>
                                        </div>

                                        <!-- Rol (solo mostrar, no editable si lo decides) -->
                                        <div class="mb-3">
                                            <label class="form-label">Rol</label>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($rol) ?>" disabled>
                                        </div>


                                    </div>

                                    <!-- Footer -->
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                        <button type="submit" class="btn btn-outline-success">
                                            <i class="bi bi-person-check"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </main>

        <?php include '../layouts/footer.php'; ?>
    </div>
</body>

</html>