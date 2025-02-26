<?php
include('../../config.php');

$nro_venta = $_GET['nro_venta'];
$id_cliente = $_GET['id_cliente'];
$total_a_cancelar = $_GET['total_a_cancelar'];

$fechaHora = date('Y-m-d H:i:s');

$pdo->beginTransaction();

$sentencia = $pdo->prepare("INSERT INTO tb_ventas (nro_venta, id_cliente, costo_total, fyh_creacion) 
VALUES (:nro_venta, :id_cliente, :costo_total, :fyh_creacion)");

$sentencia->bindParam('nro_venta', $nro_venta);
$sentencia->bindParam('id_cliente', $id_cliente);
$sentencia->bindParam('costo_total', $total_a_cancelar);
$sentencia->bindParam('fyh_creacion', $fechaHora);

if ($sentencia->execute()) {
    $id_venta = $pdo->lastInsertId(); // Obtener el ID de la venta recién insertada

    $pdo->commit();
    
    session_start();
    $_SESSION['mensaje'] = "Se registró el despacho correctamente";
    $_SESSION['icono'] = "success";

    // Generar la factura en PDF y redirigir automáticamente
    ?>
    <script>
        window.open("<?php echo $URL; ?>/ventas/generar_factura.php?id_venta=<?php echo $id_venta; ?>", "_blank");
        location.href = "<?php echo $URL; ?>/ventas/";
    </script>
    <?php
} else {
    $pdo->rollBack();

    session_start();
    $_SESSION['mensaje'] = "Error: no se pudo registrar la venta";
    $_SESSION['icono'] = "error";
    ?>
    <script>
        location.href = "<?php echo $URL; ?>/ventas/create.php";
    </script>
    <?php
}
?>
