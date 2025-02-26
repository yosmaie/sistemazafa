<?php
include('../../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_carrito = $_POST['id_carrito'];

    $sentencia = $pdo->prepare("DELETE FROM tb_carrito WHERE id_carrito=:id_carrito");
    $sentencia->bindParam('id_carrito', $id_carrito);

    if($sentencia->execute()){

        ?>
        <script>
            location.href = "<?php echo $URL;?>/ventas/create.php";
        </script>
        <?php
    }else{
    
    
    
        session_start();
        $_SESSION['mensaje'] = "Error no se pudo registrar en la base de datos";
        $_SESSION['icono'] = "error";
        //  header('Location: '.$URL.'/categorias');
        ?>
        <script>
            location.href = "<?php echo $URL;?>/ventas/create.php";
        </script>
        <?php
    }
}
?>
