<?php
// Incluir el archivo de configuración y establecer la conexión con la base de datos
include('../app/config.php');

// Verificar si se han enviado las fechas de filtro
$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : '1970-01-01'; // Por defecto muestra desde 1970 si no se selecciona fecha de inicio
$fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-d'); // Si no se selecciona fecha de fin, por defecto muestra hasta la fecha actual

// Consulta SQL para obtener los datos necesarios con un filtro de rango de fechas
$sql_carrito = "SELECT 
                    c.nro_venta, 
                    cl.Departamento, 
                    a.nombre AS nombre_producto, 
                    a.descripcion as Descripcion,
                    c.cantidad, 
                    v.costo_total,
                    DATE(c.fyh_creacion) as fecha_creacion
                FROM tb_carrito AS c
                INNER JOIN tb_almacen AS a ON c.id_producto = a.id_producto
                INNER JOIN tb_ventas AS v ON c.nro_venta = v.nro_venta
                INNER JOIN tb_clientes AS cl ON v.id_cliente = cl.id_cliente
                WHERE DATE(c.fyh_creacion) >= :fecha_inicio
                AND DATE(c.fyh_creacion) <= :fecha_fin
                ORDER BY c.nro_venta ASC";

// Preparar la consulta SQL con los parámetros
$query_carrito = $pdo->prepare($sql_carrito);
$query_carrito->bindParam(':fecha_inicio', $fecha_inicio);
$query_carrito->bindParam(':fecha_fin', $fecha_fin);
$query_carrito->execute();

// Datos obtenidos de la consulta
$carrito_datos = $query_carrito->fetchAll(PDO::FETCH_ASSOC);
?>
