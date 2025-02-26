<?php
include('../app/config.php');
include('../layout/sesion.php');

include('../layout/parte1.php');


include('../app/controllers/almacen/listado_de_productos.php');


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->

    <!-- /.content-header -->


    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-outline card-hotink">
                        <div class="card-header">
                            <h7 class="card-title">Productos registrados</h7>
                        </div>

                        <div class="card-body" style="display: block;">
                            <?php
                            // Verificar si hay productos con stock menor al mínimo
                            $hay_alertas = false;
                            $productos_bajo_stock = []; // Para almacenar los productos que están por debajo del stock mínimo

                            foreach ($productos_datos as $productos_dato) {
                                if ($productos_dato['stock'] < $productos_dato['stock_minimo']) {
                                    $hay_alertas = true;
                                    $productos_bajo_stock[] = $productos_dato['nombre']; // Guardar el nombre del producto
                                }
                            }

                            // Contar la cantidad de productos por debajo del stock mínimo
                            $cantidad_bajo_stock = count($productos_bajo_stock);
                            ?>

                            <?php if ($hay_alertas): ?>
                                <div class="alert alert-danger" role="alert">
                                    <strong>¡Atención!</strong> Hay <?php echo $cantidad_bajo_stock; ?> producto<?php echo $cantidad_bajo_stock > 1 ? 's' : ''; ?> con stock por debajo del mínimo:
                                    <ul>
                                        <?php foreach ($productos_bajo_stock as $producto): ?>
                                            <li><?php echo $producto; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="table table-responsive">
                                <table id="example1" class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                                <center>Código</center>
                                            </th>
                                            <th>
                                                <center>Categoría</center>
                                            </th>
                                            <th>
                                                <center>Imagen</center>
                                            </th>
                                            <th>
                                                <center>Nombre</center>
                                            </th>
                                            <th>
                                                <center>Descripción</center>
                                            </th>
                                            <th>
                                                <center>Stock</center>
                                            </th>
                                            <th>
                                                <center>Precio compra</center>
                                            </th>
                                            <th>
                                                <center>Precio venta</center>
                                            </th>
                                            <th>
                                                <center>Acciones</center>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($productos_datos as $productos_dato) {
                                            $id_producto = $productos_dato['id_producto'];
                                            $stock_actual = $productos_dato['stock'];
                                            $stock_maximo = $productos_dato['stock_maximo'];
                                            $stock_minimo = $productos_dato['stock_minimo'];

                                            // Determinar el color de la fila
                                            $fila_color = "";
                                            if ($stock_actual < $stock_minimo) {
                                                $fila_color = "style='background-color: #ee868b;'";
                                            }
                                        ?>
                                            <tr <?php echo $fila_color; ?>>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['codigo']; ?></td>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['categoria']; ?></td>
                                                <td>
                                                    <img src="<?php echo $URL . "/almacen/img_productos/" . $productos_dato['imagen']; ?>" width="50px" alt="Producto">
                                                </td>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['nombre']; ?></td>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['descripcion']; ?></td>
                                                <td style="font-weight: 500;">
                                                    <center><?php echo number_format($productos_dato['stock'], 2, '.', ','); ?></center>
                                                </td>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['precio_compra']; ?></td>
                                                <td style="font-weight: 500;"><?php echo $productos_dato['precio_venta']; ?></td>
                                                <td style="font-weight: 500;">
                                                    <center>
                                                        <div class="btn-group">
                                                            <a href="show.php?id=<?php echo $id_producto; ?>" type="button" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> Ver</a>
                                                            <a href="update.php?id=<?php echo $id_producto; ?>" type="button" class="btn btn-success btn-sm"><i class="fa fa-pencil-alt"></i> Editar</a>
                                                            <a href="delete.php?id=<?php echo $id_producto; ?>" type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Borrar</a>
                                                        </div>
                                                    </center>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.content-wrapper -->


<?php include('../layout/mensajes.php'); ?>
<?php include('../layout/parte2.php'); ?>


<script>
    $(function() {
        $("#example1").DataTable({
            "pageLength": 5,
            "language": {
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Productos",
                "infoEmpty": "Mostrando 0 a 0 de 0 Roles",
                "infoFiltered": "(Filtrado de _MAX_ total Roles)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_Productos",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscador:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            buttons: [{
                    extend: 'collection',
                    text: 'Reportes',
                    orientation: 'landscape',
                    buttons: [{
                        text: 'Copiar',
                        extend: 'copy',
                    }, {
                        extend: 'pdf'
                    }, {
                        extend: 'csv'
                    }, {
                        extend: 'excel'
                    }, {
                        text: 'Imprimir',
                        extend: 'print'
                    }]
                },
                {
                    extend: 'colvis',
                    text: 'Visor de columnas',
                    collectionLayout: 'fixed three-column'
                }
            ],
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });
</script>