<?php include('header.php'); ?>

<!-- CONTENIDO ESPECÍFICO DE ESTA PÁGINA -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Registrar</h2>
    <p>Aquí puedes registrarte para crear una cuenta.</p>
    
    <!-- Puedes copiar el código de las cards (productos) que ya tienes en el index y pegarlas aquí -->
<!-- ============================ COMPONENT REGISTER   ================================= -->
	<div class="card mx-auto" style="max-width:520px; margin-top:40px;">
      <article class="card-body">
		<header class="mb-4"><h4 class="card-title"></h4>Llene los campos</header>
		<form>
				<div class="form-row">
					<div class="col form-group">
						<label>Primer nombre</label>
					  	<input type="text" class="form-control" placeholder="">
					</div> <!-- form-group end.// -->
					<div class="col form-group">
						<label>Primer apellido</label>
					  	<input type="text" class="form-control" placeholder="">
					</div> <!-- form-group end.// -->
				</div> <!-- form-row end.// -->
				<div class="form-group">
					<label>Email</label>
					<input type="email" class="form-control" placeholder="">
					<small class="form-text text-muted">Nunca compartiremos tu correo electrónico con nadie más.</small>
				</div> <!-- form-group end.// -->
				<div class="form-group">
					<label class="custom-control custom-radio custom-control-inline">
					  <input class="custom-control-input" checked="" type="radio" name="gender" value="option1">
					  <span class="custom-control-label"> Masculino </span>
					</label>
					<label class="custom-control custom-radio custom-control-inline">
					  <input class="custom-control-input" type="radio" name="gender" value="option2">
					  <span class="custom-control-label"> Femenino </span>
					</label>
				</div> <!-- form-group end.// -->
				<div class="form-row">
					<div class="form-group col-md-6">
					  <label>Ciudad</label>
					  <input type="text" class="form-control">
					</div> <!-- form-group end.// -->
					<div class="form-group col-md-6">
					  <label>Región</label>
					  <select id="inputState" class="form-control">
					    <option>Amazonas</option>
					    <option>Ancash</option>
					    <option>Apurimac</option>
                        <option>Arequipa</option>
                        <option>Ayacucho</option>
                        <option>Cajamarca</option>
                        <option>Cusco</option>
                        <option>Huancavelica</option>
                        <option>Huanuco</option>
                        <option>Ica</option>
                        <option>Junin</option>
                        <option>La Libertad</option>
                        <option>Lima</option>
                        <option>Loreto</option>
                        <option>Madre de Dios</option>
                        <option>Moquegua</option>
                        <option>Pasco</option>
                        <option>Piura</option>
                        <option>Puno</option>
                        <option>San Martin</option>
                        <option>Tacna</option>
                        <option>Tumbes</option>
                        <option>Ucayali</option>
                        <option>P.C. Callao</option>
			     
					  </select>
					</div> <!-- form-group end.// -->
				</div> <!-- form-row.// -->
				<div class="form-row">
					<div class="form-group col-md-6">
						<label>Crear contraseña</label>
					    <input class="form-control" type="password">
					</div> <!-- form-group end.// --> 
					<div class="form-group col-md-6">
						<label>Repetir contraseña</label>
					    <input class="form-control" type="password">
					</div> <!-- form-group end.// -->  
				</div>
			    <div class="form-group">
			        <button type="submit" class="btn btn-primary btn-block"> Registrar  </button>
			    </div> <!-- form-group// -->      
			        
			</form>
		</article><!-- card-body.// -->
    </div> <!-- card .// -->
    <p class="text-center mt-4">Tienes una cuenta? <a href="">Ingresar</a></p>
    <br><br>
<!-- ============================ COMPONENT REGISTER  END.// ================================= -->


    </section>
    <!-- ========================= SECTION CONTENT END// ========================= -->


   <div class="row">
        <div class="col-md-3">
            <div class="card"></div>
        </div>
        <!-- Más productos -->
    </div>
</div>

<?php include('footer.php'); ?>