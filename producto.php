<?php include('conexion.php'); ?>
<?php include('header.php'); ?>

<?php
// Obtener ID de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Validar ID
if ($id <= 0) {
    header("Location: tienda.php");
    exit;
}

// Consulta a la base de datos
$sql = "SELECT p.*, c.nombre AS categoria_nombre FROM productos p INNER JOIN categorias c ON p.categoria_id = c.id WHERE p.id = $id AND p.activo = 1";
$resultado = $conexion->query($sql);

// Verificar si existe el producto (¡BLOQUE CORREGIDO AQUÍ!)
if ($resultado->num_rows == 0) {
    echo '<div class="container py-5"><div class="alert alert-danger">Producto no encontrado.</div></div>';
    include('footer.php');
    exit;
}

// Obtener los datos
$producto = $resultado->fetch_assoc();
?>

<!-- CONTENIDO ESPECÍFICO DE DETALLE DE PRODUCTO -->
<div class="container py-5">
    <div class="row g-4">
        <!-- Imagen -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <img src="img/<?= $producto['imagen'] ?>" 
                     class="card-img-top" 
                     alt="<?= $producto['nombre'] ?>"
                     style="height: 500px; object-fit: contain; background: #f8f9fa; padding: 20px;">
            </div>
        </div>
        
        <!-- Información -->
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="tienda.php">Tienda</a></li>
                    <li class="breadcrumb-item active"><?= $producto['nombre'] ?></li>
                </ol>
            </nav>

            <h1 class="display-6 fw-bold"><?= $producto['nombre'] ?></h1>
            <p class="text-muted"><i class="bi bi-tag"></i> Categoría: <?= $producto['categoria_nombre'] ?></p>
            
            <hr>
            
            <h2 class="text-danger display-5 fw-bold">$<?= number_format($producto['precio'], 2) ?></h2>
            
            <div class="my-4">
                <h5>Descripción</h5>
                <p style="text-align: justify;"><?= nl2br($producto['descripcion']) ?></p>
            </div>
            
            <div class="mb-4">
                <span class="badge bg-success fs-6 p-2"><i class="bi bi-check-circle"></i> Disponible</span>
                <small class="text-muted ms-2">Stock: <?= $producto['stock'] ?> unidades</small>
            </div>
            
            <!-- Formulario para agregar al carrito (lo activaremos después) -->
            <form action="agregar-carrito.php" method="POST">
                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                <div class="row g-2 align-items-center">
                    <div class="col-3">
                        <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" class="form-control">
                    </div>
                    <div class="col-9">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                        </button>
                    </div>
                </div>
            </form>
            
            <div class="mt-4">
                <a href="tienda.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Seguir Comprando</a>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>