<?php 
include('conexion.php'); 
include('header.php'); 

// Obtener ID de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header("Location: tienda.php");
    exit;
}

// Consulta a la base de datos usando Prepared Statements
$sql = "SELECT p.*, c.nombre AS categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.id = ? AND p.activo = 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if (!$resultado || $resultado->num_rows == 0) {
    echo '<div class="container py-5"><div class="alert alert-danger text-center">Producto no encontrado o no disponible.</div></div>';
    include('footer.php');
    exit;
}

$producto = $resultado->fetch_assoc();
$stmt->close();

// Obtener productos relacionados de la misma categoría
$cat_id = $producto['categoria_id'];
$sql_rel = "SELECT * FROM productos WHERE categoria_id = ? AND id != ? AND activo = 1 LIMIT 4";
$stmt_rel = $conexion->prepare($sql_rel);
$stmt_rel->bind_param("ii", $cat_id, $id);
$stmt_rel->execute();
$res_rel = $stmt_rel->get_result();
?>

<!-- CONTENIDO DE DETALLE DE PRODUCTO -->
<div class="container py-5">
    <div class="row align-items-center">
        <!-- Imagen -->
        <div class="col-md-6 mb-4 mb-md-0">
            <div class="card border-0 shadow-sm p-4 text-center rounded" style="background: #ffffff;">
                <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" 
                     class="img-fluid rounded" 
                     alt="<?= htmlspecialchars($producto['nombre']) ?>"
                     style="max-height: 420px; object-fit: contain;">
            </div>
        </div>
        
        <!-- Información -->
        <div class="col-md-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-3">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="tienda.php">Tienda</a></li>
                    <li class="breadcrumb-item active text-truncate" style="max-width: 200px;"><?= htmlspecialchars($producto['nombre']) ?></li>
                </ol>
            </nav>

            <span class="text-primary font-weight-bold d-block mb-1" style="font-size: 0.9rem;">
                Categoría: <?= htmlspecialchars($producto['categoria_nombre'] ?? 'General') ?>
            </span>

            <h1 class="h2 font-weight-bold text-dark mb-2"><?= htmlspecialchars($producto['nombre']) ?></h1>
            
            <div class="my-3">
                <span class="price-tag text-primary display-4 font-weight-bold">$<?= number_format($producto['precio'], 2) ?></span>
            </div>
            
            <div class="my-4">
                <h6 class="font-weight-bold text-muted text-uppercase">Descripción</h6>
                <p class="text-secondary" style="text-align: justify; line-height: 1.6;"><?= nl2br(htmlspecialchars($producto['descripcion'])) ?></p>
            </div>
            
            <div class="mb-4 d-flex align-items-center">
                <span class="text-success font-weight-bold mr-3">
                    <i class="bi bi-check-circle-fill mr-1"></i> Disponible
                </span>
                <span class="text-muted small">Stock disponible: <strong><?= $producto['stock'] ?> unidades</strong></span>
            </div>
            
            <!-- Formulario para agregar al carrito -->
            <form action="agregar-carrito.php" method="POST" class="mb-4">
                <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                <div class="form-row align-items-center">
                    <div class="col-3">
                        <label class="sr-only">Cantidad</label>
                        <input type="number" name="cantidad" value="1" min="1" max="<?= $producto['stock'] ?>" class="form-control form-control-lg font-weight-bold text-center">
                    </div>
                    <div class="col-9">
                        <button type="submit" class="btn btn-success btn-lg btn-block font-weight-bold">
                            Agregar al Carrito
                        </button>
                    </div>
                </div>
            </form>
            
            <a href="tienda.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Seguir Comprando
            </a>
        </div>
    </div>

    <!-- SECCIÓN DE PRODUCTOS RELACIONADOS -->
    <?php if ($res_rel && $res_rel->num_rows > 0): ?>
        <hr class="my-5">
        <h5 class="font-weight-bold mb-4">Productos Relacionados</h5>
        <div class="row">
            <?php while ($p_rel = $res_rel->fetch_assoc()): ?>
                <div class="col-md-3 col-6 mb-4 d-flex align-items-stretch">
                    <div class="card product-card w-100 shadow-sm">
                        <div class="card-img-container text-center">
                            <img src="img/<?= htmlspecialchars($p_rel['imagen']) ?>" class="card-img-top" alt="<?= htmlspecialchars($p_rel['nombre']) ?>">
                        </div>
                        <div class="card-body">
                            <h6 class="card-title" title="<?= htmlspecialchars($p_rel['nombre']) ?>">
                                <?= htmlspecialchars($p_rel['nombre']) ?>
                            </h6>
                            <span class="price-tag mt-auto">$<?= number_format($p_rel['precio'], 2) ?></span>
                            <div class="card-footer-action">
                                <a href="producto.php?id=<?= $p_rel['id'] ?>" class="btn btn-sm btn-outline-primary btn-block">Ver Producto</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<?php 
$stmt_rel->close();
include('footer.php'); 
?>
