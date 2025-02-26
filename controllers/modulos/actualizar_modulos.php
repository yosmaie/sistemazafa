<?php
include ('../../config.php'); // Ajusta la ruta según la estructura de tu proyecto


// Definir la ruta base de los módulos
$carpeta_base = __DIR__ . '/../';

// Lista de carpetas que representan módulos
$modulos_encontrados = ['usuarios', 'roles', 'compras']; // Puedes ampliar esta lista dinámicamente si lo deseas

foreach ($modulos_encontrados as $modulo) {
    $ruta_modulo = $modulo . "/index.php"; // Ruta relativa del módulo

    // Verificar si el módulo ya está registrado en la base de datos
    $sql_verificar = "SELECT COUNT(*) as total FROM tb_modulos WHERE nombre = ?";
    $query_verificar = $pdo->prepare($sql_verificar);
    $query_verificar->execute([$modulo]);
    $resultado = $query_verificar->fetch(PDO::FETCH_ASSOC);

    if ($resultado['total'] == 0) {
        // Insertar el módulo si no está registrado
        $sql_insert = "INSERT INTO tb_modulos (nombre, ruta, fyh_creacion, fyh_actualizacion) VALUES (?, ?, NOW(), NOW())";
        $query_insert = $pdo->prepare($sql_insert);
        $query_insert->execute([$modulo, $ruta_modulo]);

        echo "✅ Módulo '$modulo' agregado a la base de datos.\n";
    } else {
        echo "⚠️ Módulo '$modulo' ya existe en la base de datos.\n";
    }
}

echo "Proceso finalizado.\n";
?>
