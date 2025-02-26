<?php
include (__DIR__ . '/../../config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_rol = $_POST['id_rol'] ?? 0;
    $permisos = $_POST['permisos'] ?? [];

    if ($id_rol == 0) {
        header("Location: ../../../roles/permisos.php?id_rol=$id_rol&error=Datos inválidos");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Eliminar permisos existentes del rol
        $sql_delete = "DELETE FROM tb_permisos WHERE id_rol = ?";
        $query_delete = $pdo->prepare($sql_delete);
        $query_delete->execute([$id_rol]);

        // Insertar nuevos permisos
        $sql_insert = "INSERT INTO tb_permisos (id_rol, id_modulo, accion, permitido, fyh_creacion, fyh_actualizacion) 
                       VALUES (?, ?, ?, ?, NOW(), NOW())";
        $query_insert = $pdo->prepare($sql_insert);

        foreach ($permisos as $id_modulo => $acciones) {
            foreach ($acciones as $accion => $permitido) {
                if (!empty($permitido)) { // Solo guardar si el checkbox estaba marcado
                    $query_insert->execute([$id_rol, $id_modulo, $accion, 1]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../../../roles/permisos.php?id_rol=$id_rol&mensaje=" . urlencode("Permisos actualizados correctamente"));
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        header("Location: ../../../roles/permisos.php?id_rol=$id_rol&error=" . urlencode("Error al guardar los permisos: " . $e->getMessage()));
        exit();
    }
} else {
    echo "Acceso denegado";
}
