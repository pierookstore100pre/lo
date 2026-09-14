<?php
include_once('conexion.php');
include('header.php');
?>

<!-- ========================= BANNER PRINCIPAL ========================= -->
<section class="section-intro py-3">
   <div class="container">
      <div class="banner-wrapper shadow-sm">
         <div id="bannerCarousel" class="carousel slide" data-ride="carousel" data-interval="3500">
            <ol class="carousel-indicators">
               <li data-target="#bannerCarousel" data-slide-to="0" class="active"></li>
               <li data-target="#bannerCarousel" data-slide-to="1"></li>
            </ol>
            <div class="carousel-inner">
               <div class="carousel-item active">
                  <img src="img/2.png" class="d-block banner-img" alt="Banner La Compu de Lolo 1">
               </div>
               <div class="carousel-item">
                  <img src="img/3.png" class="d-block banner-img" alt="Banner La Compu de Lolo 2">
               </div>
            </div>
            <a class="carousel-control-prev" href="#bannerCarousel" role="button" data-slide="prev">
               <span class="carousel-control-prev-icon" aria-hidden="true"></span>
               <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#bannerCarousel" role="button" data-slide="next">
               <span class="carousel-control-next-icon" aria-hidden="true"></span>
               <span class="sr-only">Siguiente</span>
            </a>
         </div>
      </div>
   </div>
</section>

<!-- ========================= SECCIÓN PRODUCTOS DESTACADOS ========================= -->
<section class="section-name py-4">
   <div class="container">
      <header class="section-heading mb-4 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center">
         <div>
            <h3 class="font-weight-bold text-dark mb-1" style="font-size: 1.65rem;">Productos Destacados</h3>
            <p class="text-muted mb-0" style="font-size: 0.95rem;">Lo último en tecnología y cómputo de alta gama</p>
         </div>
         <a href="tienda.php" class="btn btn-outline-primary font-weight-bold mt-2 mt-sm-0" style="border-radius: 6px; padding: 0.5rem 1.2rem;">
            Ver Todo en Tienda <i class="bi bi-arrow-right ml-1"></i>
         </a>
      </header>

      <div class="row">
         <?php
         $sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE p.activo = 1 ORDER BY p.id DESC LIMIT 8";
         $resultado = $conexion->query($sql);

         if ($resultado && $resultado->num_rows > 0) {
            while ($producto = $resultado->fetch_assoc()) {
         ?>
               <div class="col-xl-3 col-lg-3 col-md-4 col-6 mb-4 d-flex align-items-stretch">
                  <div class="card product-card w-100 shadow-sm">
                     <div class="card-img-container text-center">
                        <img src="img/<?= htmlspecialchars($producto['imagen']) ?>"
                           class="card-img-top"
                           alt="<?= htmlspecialchars($producto['nombre']) ?>">
                     </div>
                     <div class="card-body">
                        <span class="card-category-text"><?= htmlspecialchars($producto['categoria_nombre'] ?? 'Producto') ?></span>
                        <h6 class="card-title" title="<?= htmlspecialchars($producto['nombre']) ?>">
                           <?= htmlspecialchars($producto['nombre']) ?>
                        </h6>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                           <span class="price-tag">S/ <?= number_format($producto['precio'], 2) ?></span>
                           <span class="stock-text">Stock: <?= $producto['stock'] ?></span>
                        </div>
                        <div class="card-footer-action">
                           <a href="producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-primary btn-block font-weight-bold py-2 mb-2">
                              Ver Detalle
                           </a>
                           <form action="agregar-carrito.php" method="POST">
                              <input type="hidden" name="producto_id" value="<?= $producto['id'] ?>">
                              <input type="hidden" name="cantidad" value="1">
                              <button type="submit" class="btn btn-sm btn-outline-success btn-block py-2 font-weight-bold">
                                 Agregar al Carrito
                              </button>
                           </form>
                        </div>
                     </div>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<div class="col-12"><div class="alert alert-info text-center py-4">No hay productos disponibles actualmente.</div></div>';
         }
         ?>
      </div>
   </div>
</section>

<?php include('footer.php'); ?>