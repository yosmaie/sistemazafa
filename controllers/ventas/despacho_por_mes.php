<?php
include 'app/config.php'; // Asegúrate de que la ruta sea correcta

try {
    $sql_ventas_ultimos_cuatro_meses = "SELECT *, client.Departamento as Departamento 
                                        FROM tb_ventas as ve 
                                        INNER JOIN tb_clientes as client ON client.id_cliente = ve.id_cliente 
                                        WHERE ve.fyh_creacion >= DATE_SUB(CURRENT_DATE(), INTERVAL 4 MONTH)";

    $query_ventas_ultimos_cuatro_meses = $pdo->prepare($sql_ventas_ultimos_cuatro_meses);
    $query_ventas_ultimos_cuatro_meses->execute();
    $ventas_ultimos_cuatro_meses = $query_ventas_ultimos_cuatro_meses->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $ventas_ultimos_cuatro_meses = []; // Si hay error, devolvemos un array vacío
    echo "Error: " . $e->getMessage();
}

// Puedes imprimir para verificar los resultados
// print_r($ventas_ultimos_cuatro_meses);
?>
