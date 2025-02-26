<?php
include ('../../config.php');

session_start();

if (!isset($_GET['id_compra'], $_GET['id_producto'], $_GET['cantidad_compra'], $_GET['stock_actual'])) {
    echo json_encode(["status" => "error", "message" => "Parámetros inválidos"]);
    exit();
}

$id_compra = (int) $_GET['id_compra'];
$id_producto = (int) $_GET['id_producto'];
$cantidad_compra = (int) $_GET['cantidad_compra'];
$stock_actual = (int) $_GET['stock_actual'];

if ($id_compra <= 0 || $id_producto <= 0 || $cantidad_compra < 0 || $stock_actual < 0) {
    echo json_encode(["status" => "error", "message" => "Datos inválidos"]);
    exit();
}

try {
    $pdo->beginTransaction();

    // Eliminar compra
    $sentencia = $pdo->prepare("DELETE FROM tb_compras WHERE id_compra = :id_compra");
    $sentencia->bindParam(':id_compra', $id_compra);
    
    if (!$sentencia->execute() || $sentencia->rowCount() === 0) {
        throw new Exception("No se encontró la compra a eliminar");
    }

    // Actualizar stock
    $stock_nuevo = max(0, $stock_actual - $cantidad_compra);
    $sentencia = $pdo->prepare("UPDATE tb_almacen SET stock = :stock WHERE id_producto = :id_producto");
    $sentencia->bindParam(':stock', $stock_nuevo);
    $sentencia->bindParam(':id_producto', $id_producto);

    if (!$sentencia->execute()) {
        throw new Exception("Error al actualizar el stock");
    }

    $pdo->commit();
    
    $_SESSION['mensaje'] = "Compra eliminada correctamente";
    $_SESSION['icono'] = "success";
    
    echo json_encode(["status" => "success", "message" => "Compra eliminada correctamente"]);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    
    $_SESSION['mensaje'] = "Error: " . $e->getMessage();
    $_SESSION['icono'] = "error";
    
    echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    exit();
}
