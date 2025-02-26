<?php

include ('../../config.php');


$Departamento = $_POST['Departamento'];





$sentencia = $pdo->prepare("INSERT INTO tb_clientes
       ( Departamento) 
VALUES (:Departamento)");

$sentencia->bindParam('Departamento',$Departamento);



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






