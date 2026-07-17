<?php include('conexion.php'); ?>
<?php include('header.php'); ?>

<?php
// 1. ¿Qué categoría nos pide el usuario?
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;

// 2. Construir la consulta SQL
if ($categoria_id > 0) {
    // Si hay categoría, filtramos
    $sql = "SELECT * FROM productos WHERE categoria_id = $categoria_id AND activo = 1 ORDER BY id DESC";
    $titulo_pagina = "Categoría: " . obtenerNombreCategoria($conexion, $categoria_id);
} else {
    // Si NO hay categoría, mostramos TODOS
    $sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC";
    $titulo_pagina = "Todos los Productos";
}

$resultado = $conexion->query($sql);
?>

<!-- CONTENIDO PRINCIPAL -->
<div class="container py-4">
    <h2 class="section-title mb-4"><?= $titulo_pagina ?></h2>

    <div class="row">
        <?php
        if ($resultado->num_rows > 0) {
            while ($producto = $resultado->fetch_assoc()) {
        ?>
                <div class="col-md-3 col-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="img/<?= $producto['imagen'] ?>"
                            class="card-img-top"
                            alt="<?= $producto['nombre'] ?>"
                            style="height: 200px; object-fit: contain; background: #f8f9fa; padding: 10px;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-truncate" title="<?= $producto['nombre'] ?>"><?= $producto['nombre'] ?></h6>
                            <p class="text-danger fw-bold fs-5 mt-auto">$<?= number_format($producto['precio'], 2) ?></p>
                            <a href="producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-primary w-100">Ver Detalle</a>

                            <form action="agregar-carrito.php" method="POST" class="mt-2">
                                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                                <input type="hidden" name="cantidad" value="1">
                                <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                    <i class="bi bi-cart-plus"></i> Agregar
                                </button>
                            </form>


                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<div class="col-12"><div class="alert alert-info">No hay productos disponibles en esta categoría.</div></div>';
        }
        ?>
    </div>
</div>

<?php
// Función auxiliar para obtener el nombre de la categoría (la ponemos aquí para no complicar)
function obtenerNombreCategoria($conexion, $id)
{
    $sql = "SELECT nombre FROM categorias WHERE id = $id";
    $res = $conexion->query($sql);
    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        return $row['nombre'];
    }
    return "Sin categoría";
}
?>

<?php include('footer.php'); ?>