<?php
include('../../config.php');

$nro_venta = $_GET['nro_venta'];
$id_producto = $_GET['id_producto'];
$cantidad = $_GET['cantidad'];
$fechaHora = date('Y-m-d H:i:s');  // Asegúrate de definir el valor para $fechaHora

// 1. Obtener el stock disponible del producto
$stockProducto = $pdo->prepare("SELECT stock FROM tb_almacen WHERE id_producto = :id_producto");
$stockProducto->bindParam('id_producto', $id_producto);
$stockProducto->execute();
$producto = $stockProducto->fetch(PDO::FETCH_ASSOC);

// Verificamos si el producto existe y obtenemos su stock
if ($producto) {
    $stockDisponible = $producto['stock'];

    // 2. Verificar cuántas unidades ya están en el carrito
    $verificarProducto = $pdo->prepare("SELECT cantidad FROM tb_carrito WHERE nro_venta = :nro_venta AND id_producto = :id_producto");
    $verificarProducto->bindParam('nro_venta', $nro_venta);
    $verificarProducto->bindParam('id_producto', $id_producto);
    $verificarProducto->execute();
    $resultado = $verificarProducto->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        // Si el producto ya está en el carrito, sumamos la cantidad existente con la nueva
        $cantidadEnCarrito = $resultado['cantidad'];
        $cantidadDisponible = $stockDisponible - $cantidadEnCarrito;
    } else {
        // Si no está en el carrito, la cantidad disponible es el total del stock
        $cantidadDisponible = $stockDisponible;
    }

    // 3. Verificar si la cantidad a agregar supera la cantidad disponible
    if ($cantidad > $cantidadDisponible) {
        // Si la cantidad es mayor que la cantidad disponible, mostramos el mensaje con la cantidad exacta disponible
?>
        <script>
            Swal.fire({
                icon: 'error',
                title: '¡Stock insuficiente!',
                text: 'Solo hay <?php echo $cantidadDisponible; ?> unidades disponibles.',
                showConfirmButton: false, // No muestra el botón de confirmar
                timer: 600 // Se cierra automáticamente después de 1.5 segundos
            }).then((result) => {
                location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
            });
        </script>
        <?php
        exit;  // Detenemos la ejecución si el stock no es suficiente
    }

    // 4. Si el producto ya está en el carrito, actualizamos la cantidad
    if ($resultado) {
        $nuevaCantidad = $cantidadEnCarrito + $cantidad;  // Aumentamos la cantidad
        if ($nuevaCantidad > $stockDisponible) {
            // Verificamos si la cantidad total supera el stock disponible
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: '¡No puedes agregar más unidades!',
                    text: 'El stock disponible es solo <?php echo $stockDisponible; ?>.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer: 2000// Se cierra automáticamente después de 1.5 segundos
                }).then((result) => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
        <?php
            exit;  // Detenemos la ejecución si la nueva cantidad supera el stock
        }

        // Actualizamos la cantidad en el carrito
        $actualizarCarrito = $pdo->prepare("UPDATE tb_carrito SET cantidad = :cantidad, fyh_creacion = :fyh_creacion WHERE nro_venta = :nro_venta AND id_producto = :id_producto");
        $actualizarCarrito->bindParam('cantidad', $nuevaCantidad);
        $actualizarCarrito->bindParam('fyh_creacion', $fechaHora);
        $actualizarCarrito->bindParam('nro_venta', $nro_venta);
        $actualizarCarrito->bindParam('id_producto', $id_producto);

        if ($actualizarCarrito->execute()) {
        ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Carrito actualizado',
                    text: 'La cantidad de producto se ha actualizado exitosamente.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer: 600 // Se cierra automáticamente después de 1.5 segundos
                }).then((result) => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
        <?php
        } else {
            // Error al actualizar la cantidad
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo actualizar la cantidad en el carrito.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer: 1500 // Se cierra automáticamente después de 1.5 segundos
                }).then((result) => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
        <?php
        }
    } else {
        // 5. Si el producto no está en el carrito, lo insertamos
        if ($cantidad > $stockDisponible) {
            // Si la cantidad que queremos agregar supera el stock disponible, mostramos una alerta
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: '¡Stock insuficiente!',
                    text: 'Solo hay <?php echo $cantidadDisponible; ?> unidades disponibles.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer: 1500 // Se cierra automáticamente después de 1.5 segundos
                }).then((result) => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
        <?php
            exit;  // Detenemos la ejecución si el stock no es suficiente
        }

        $sentencia = $pdo->prepare("INSERT INTO tb_carrito (nro_venta, id_producto, cantidad, fyh_creacion) VALUES (:nro_venta, :id_producto, :cantidad, :fyh_creacion)");
        $sentencia->bindParam('nro_venta', $nro_venta);
        $sentencia->bindParam('id_producto', $id_producto);
        $sentencia->bindParam('cantidad', $cantidad);
        $sentencia->bindParam('fyh_creacion', $fechaHora);

        if ($sentencia->execute()) {
        ?>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Producto agregado',
                    text: 'El producto se ha agregado al carrito.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer:1500 // Se cierra automáticamente después de 1.5 segundos
                }).then(() => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
        <?php
        } else {
            // Error al insertar en el carrito
        ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo registrar en el carrito.',
                    showConfirmButton: false, // No muestra el botón de confirmar
                    timer: 1500 // Se cierra automáticamente después de 1.5 segundos
                }).then((result) => {
                    location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
                });
            </script>
    <?php
        }
    }
} else {
    // Si no se encontró el producto
    ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Producto no encontrado.',
            showConfirmButton: false, // No muestra el botón de confirmar
            timer: 1500 // Se cierra automáticamente después de 1.5 segundos
        }).then((result) => {
            location.href = "<?php echo $URL; ?>/ventas/create.php"; // Redirige a la página de ventas
        });
    </script>
<?php
}
?>
