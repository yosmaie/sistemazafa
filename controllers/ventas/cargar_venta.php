<?php
// Verificar que 'id_venta' esté presente en la URL (GET)
if (isset($_GET['id_venta'])) {
    $id_venta_get = $_GET['id_venta'];  // Obtener el valor de 'id_venta' de la URL

    // Consulta SQL para obtener los datos de la venta y el cliente
    $sql_ventas = "SELECT *, client.Departamento as Departamento 
                   FROM tb_ventas as ve 
                   INNER JOIN tb_clientes as client 
                   ON client.id_cliente = ve.id_cliente 
                   WHERE ve.id_venta = :id_venta_get";  // Usando un marcador para el parámetro

    // Preparar la consulta SQL
    $query_ventas = $pdo->prepare($sql_ventas);

    // Vincular el parámetro de la consulta con el valor de la variable $id_venta_get
    $query_ventas->bindParam(':id_venta_get', $id_venta_get, PDO::PARAM_INT);

    // Ejecutar la consulta
    $query_ventas->execute();

    // Obtener todos los resultados
    $ventas_datos = $query_ventas->fetchAll(PDO::FETCH_ASSOC);

    // Si hay resultados, procesarlos
    if ($ventas_datos) {
        foreach ($ventas_datos as $venta_dato) {
            // Asignar los valores obtenidos de la consulta a las variables
            $nro_venta = $venta_dato['nro_venta'];
            // Aquí puedes agregar más variables según lo que necesites usar de la consulta
        }
    } else {
        // En caso de no encontrar la venta, puedes mostrar un mensaje de error o redirigir
        echo "No se encontró la venta con ese ID.";
    }
} else {
    // Si no se ha pasado el parámetro 'id_venta' en la URL, manejarlo adecuadamente
    echo "Falta el parámetro 'id_venta' en la URL.";
}
?>
