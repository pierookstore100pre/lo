<?php include('header.php'); ?>

<!-- CONTENIDO ESPECÍFICO DE ESTA PÁGINA -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Iniciar Sesión</h2>
    <p>Aquí puedes iniciar sesión con tu cuenta.</p>
    
    <!-- Puedes copiar el código de las cards (productos) que ya tienes en el index y pegarlas aquí -->
    <!-- ========================= SECTION CONTENT ========================= -->
<section class="section-conten padding-y" style="min-height:84vh">

<!-- ============================ COMPONENT LOGIN   ================================= -->
	<div class="card mx-auto" style="max-width: 380px; margin-top:100px;">
      <div class="card-body">
      <h4 class="card-title mb-4">Iniciar Sesión</h4>
      <form>
          <div class="form-group">
			 <input type="email" class="form-control" placeholder="Email Address" >
          </div> <!-- form-group// -->
          <div class="form-group">
			<input type="password" class="form-control" placeholder="Password" >
          </div> <!-- form-group// -->
          
          <div class="form-group">
          	<a href="#" class="float-right">Olvido su contraseña?</a> 
           
          </div> <!-- form-group form-check .// -->
          <div class="form-group">
              <button type="submit" class="btn btn-primary btn-block"> Ingresar  </button>
          </div> <!-- form-group// -->    
      </form>
      </div> <!-- card-body.// -->
    </div> <!-- card .// -->

     <p class="text-center mt-4">No tienes cuenta? <a href="./registrar.php">Registrarte</a></p>
     <br><br>
<!-- ============================ COMPONENT LOGIN  END.// ================================= -->


</section>
<!-- ========================= SECTION CONTENT END// ========================= -->










    <div class="row">

    </div>
</div>

<?php include('footer.php'); ?>