<?php


$sql_ventas = "SELECT *,client.Departamento as Departamanto FROM tb_ventas as ve inner join tb_clientes as client on client.id_cliente = ve.id_cliente ";
         
$query_ventas = $pdo->prepare($sql_ventas);
$query_ventas->execute();
$ventas_datos = $query_ventas->fetchAll(PDO::FETCH_ASSOC);