<?php
/*app/controllers/users/Listado_Usuarios */

try {
    $sql_user = "SELECT us.id_Usuarios, us.nombre, us.nombre_usuario as usuario,
                us.edad,  us.estado,  rol.nombre_roles
                FROM usuarios AS us INNER JOIN roles as rol ON us.id_roles= rol.id_roles;";

    $query_user = $pdo->prepare($sql_user);
    $query_user->execute();
    $usuario_datos = $query_user->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al Consultar Usuarios: " . $e->getMessage();
}
