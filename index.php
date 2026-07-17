<?php
include('header.php');
?>

<!-- ========================= SECTION MAIN ========================= BANNER CON CARRUSEL -->
<section class="section-intro padding-y-sm">
   <div class="container">
      <div class="intro-banner-wrap" style="padding: 0; overflow: hidden; border-radius: 12px;">
         <!-- CARRUSEL BOOTSTRAP 4 (SIN BOTONES) -->
         <div id="bannerCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
            <div class="carousel-inner">
               <div class="carousel-item active">
                  <img src="img/2.png" class="d-block w-100" alt="Banner 1" style="width: 100%; height: 460px; object-fit: cover;">
               </div>
               <div class="carousel-item">
                  <img src="img/3.png" class="d-block w-100" alt="Banner 2" style="width: 100%; height: 460px; object-fit: cover;">
               </div>
            </div>
            <!-- SIN CONTROLES (botones eliminados) -->
         </div>
      </div>
   </div>
</section>

<!-- ========================= SECTION PRODUCTOS POPULARES ========================= -->
<section class="section-name padding-y-sm">
   <div class="container">
      <header class="section-heading">
         <a href="tienda.php" class="btn btn-outline-primary float-right">Todos</a>
         <h3 class="section-title">Productos populares</h3>
      </header>

      <!-- ============================  PRODUCTOS POPULARES  WEB DINAMICA ========================= -->
      <div class="row">
         <?php
         // Consultar productos
         $sql = "SELECT * FROM productos WHERE activo = 1 ORDER BY id DESC LIMIT 8";
         $resultado = $conexion->query($sql);

         if ($resultado->num_rows > 0) {
            while ($producto = $resultado->fetch_assoc()) {
         ?>
               <div class="col-md-3 col-6 mb-4">
                  <div class="card h-100 shadow-sm">
                     <img src="img/<?= $producto['imagen'] ?>"
                        class="card-img-top"
                        alt="<?= $producto['nombre'] ?>"
                        style="height: 200px; object-fit: cover; background: #f8f9fa;">
                     <div class="card-body d-flex flex-column">
                        <h6 class="card-title text-truncate" title="<?= $producto['nombre'] ?>"><?= $producto['nombre'] ?></h6>
                        <p class="text-danger fw-bold fs-5 mt-auto">$<?= number_format($producto['precio'], 2) ?></p>
                        <a href="producto.php?id=<?= $producto['id'] ?>" class="btn btn-sm btn-primary w-100">Ver Detalle</a>
                     </div>
                  </div>
               </div>
         <?php
            }
         } else {
            echo '<div class="col-12"><p class="text-center">No hay productos disponibles en la tienda.</p></div>';
         }
         ?>
      </div>
   </div>
</section>

<?php include('footer.php'); ?>