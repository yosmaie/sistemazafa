<?php

include ('../../config.php');

// Obtener el id_venta pasado por GET
$id_venta = $_GET['id_venta'];

// Iniciar transacción
$pdo->beginTransaction();

try {
    // 1. Obtener el nro_venta relacionado con el id_venta
    $sentencia1 = $pdo->prepare("SELECT nro_venta FROM tb_ventas WHERE id_venta = :id_venta");
    $sentencia1->bindParam(':id_venta', $id_venta);
    $sentencia1->execute();
    $venta = $sentencia1->fetch(PDO::FETCH_ASSOC);
    
    if (!$venta) {
        throw new Exception("Venta no encontrada");
    }
    
    $nro_venta = $venta['nro_venta'];  // Este es el nro_venta que debemos usar para eliminar los productos del carrito
    
    // 2. Eliminar la venta de la tabla tb_ventas
    $sentencia2 = $pdo->prepare("DELETE FROM tb_ventas WHERE id_venta = :id_venta");
    $sentencia2->bindParam(':id_venta', $id_venta);
    
    if ($sentencia2->execute()) {
        
        // 3. Eliminar los productos del carrito usando el nro_venta
        $sentencia3 = $pdo->prepare("DELETE FROM tb_carrito WHERE nro_venta = :nro_venta");
        $sentencia3->bindParam(':nro_venta', $nro_venta);
        $sentencia3->execute();

        // Si todo fue correcto, hacemos commit
        $pdo->commit();

        // Respuesta de éxito
        echo "success";  // Esto será procesado en la vista de JavaScript

    } else {
        // Si hubo un problema con la eliminación de la venta, lanzamos una excepción
        throw new Exception("Error al eliminar la venta");
    }
} catch (Exception $e) {
    // Si ocurre un error, revertimos la transacción
    $pdo->rollBack();
    echo "error"; // Esto se enviará a la vista en caso de error
}

?>
