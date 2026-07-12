<?php
// Incluir la conexión a la base de datos
include('conexion.php');

// CONSULTA CON JOIN para traer el nombre de la categoría
$sql = "SELECT p.*, c.nombre as nombre_categoria 
        FROM productos p 
        INNER JOIN categorias c ON p.categoria_id = c.id";
$resultado = $conexion->query($sql);
?>
<!--se agrego recien para la conexion de la tienda.php, para que pueda mostrar los productos de la base de datos-->

<?php include('header.php'); ?>

<!-- ========================= SECCIÓN DE LA TIENDA ========================= -->
<section class="section-name padding-y-sm">
    <div class="container">
        <div class="row">
            <!-- === BARRA LATERAL IZQUIERDA (SIDEBAR) === -->
            <aside class="col-md-3">
                <div class="card">
                    <article class="card-group-item">
                        <header class="card-header">
                            <h6 class="title">Categorías</h6>
                        </header>
                        <div class="filter-content">
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item">Laptops Gamer</a>
                                <a href="#" class="list-group-item">PC Armadas</a>
                                <a href="#" class="list-group-item">Mouses RGB</a>
                                <!-- Agrega más categorías aquí -->
                            </div>
                        </div>
                    </article>
                    <!-- Puedes agregar más filtros (por precio, tamaño, etc.) aquí -->
                </div>
            </aside>
            <!-- === FIN BARRA LATERAL === -->

            <!-- === LISTA DE PRODUCTOS (PARTE PRINCIPAL) === -->
            <main class="col-md-9">
                <header class="border-bottom mb-4 pb-3">
                    <div class="form-inline">
                        <span class="mr-md-auto"><strong><?php echo $resultado->num_rows; ?></strong> Productos encontrados</span>
                    </div>
                </header>

                <div class="row">
                    <?php
                    // Verificar si hay productos
                    if ($resultado->num_rows > 0) {
                        // Recorrer cada producto y mostrarlo
                        while($producto = $resultado->fetch_assoc()) {
                            // Calcular el descuento si existe precio de oferta
                            $descuento = "";
                            if ($producto['precio_oferta'] && $producto['precio_oferta'] > 0) {
                                $descuento = round(100 - ($producto['precio_oferta'] / $producto['precio']) * 100) . "%";
                            }
                    ?>
                            <div class="col-md-4">
                                <div class="card card-product-grid shadow-sm" style="transition: transform 0.2s; border-radius: 12px; margin-bottom: 20px;">
                                    <a href="./product-detail.php?id=<?php echo $producto['id']; ?>" class="img-wrap" style="background: #f5f7fa; border-radius: 12px 12px 0 0; text-align: center;">
                                        <img src="images/items/<?php echo $producto['imagen']; ?>" class="img-fluid" style="max-height: 200px; padding: 10px;">
                                        <?php if ($descuento): ?>
                                            <span class="badge bg-dark text-white" style="position: absolute; top: 10px; left: 10px; font-weight: 700; padding: 6px 8px; border-radius: 30px;">-<?php echo $descuento; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <figcaption class="info-wrap p-3">
                                        <a href="./product-detail.php?id=<?php echo $producto['id']; ?>" class="title text-dark fw-bold" style="font-size: 0.9rem;"><?php echo $producto['nombre']; ?></a>
                                        <div class="rating-wrap mb-1">
                                            <!-- Aquí puedes agregar estrellas estáticas o dinámicas -->
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star text-warning"></i>
                                            <i class="fas fa-star-half-alt text-warning"></i>
                                            <span class="text-muted small">(45)</span>
                                        </div>
                                        <div class="price mt-1">
                                            <?php if ($producto['precio_oferta'] && $producto['precio_oferta'] > 0): ?>
                                                <span class="text-muted" style="text-decoration: line-through; font-size: 0.9rem;">S/ <?php echo number_format($producto['precio'], 2); ?></span>
                                                <span class="fw-bold text-primary" style="font-size: 1.3rem;">S/ <?php echo number_format($producto['precio_oferta'], 2); ?></span>
                                            <?php else: ?>
                                                <span class="fw-bold text-primary" style="font-size: 1.3rem;">S/ <?php echo number_format($producto['precio'], 2); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-outline-primary btn-sm mt-2 w-100" onclick="alert('Añadido al carrito')">
                                            <i class="fas fa-cart-plus"></i> Añadir
                                        </button>
                                    </figcaption>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<p>No se encontraron productos.</p>";
                    }
                    ?>
                </div> <!-- row.// -->

                <!-- Paginación (opcional) -->
                <nav class="mt-4" aria-label="Page navigation sample">
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">Siguiente</a></li>
                    </ul>
                </nav>
            </main>
            <!-- === FIN LISTA DE PRODUCTOS === -->
        </div> <!-- row.// -->
    </div> <!-- container.// -->
</section>
<!-- ========================= FIN DE LA TIENDA ========================= -->

<?php include('footer.php'); ?>