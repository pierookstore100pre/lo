<?php
session_start();
include('conexion.php');
?>


<!DOCTYPE HTML>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="cache-control" content="max-age=604800" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>bilalDevX | One of the Biggest Online Shopping Platform</title>

	<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">

	<!-- jQuery -->
	<script src="js/jquery-2.0.0.min.js" type="text/javascript"></script>

	<!-- Bootstrap4 files-->
	 <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
	<!-- <script src="js/bootstrap.bundle.min.js" type="text/javascript"></script> -->
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />

	<!-- Font awesome 5 -->
	<link href="fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet">

	<!-- custom style -->
	<link href="css/ui.css" rel="stylesheet" type="text/css" />
	<link href="css/responsive.css" rel="stylesheet" media="only screen and (max-width: 1200px)" />

	<!-- custom javascript -->
	<script src="js/script.js" type="text/javascript"></script>

	<script type="text/javascript">
		/// some script

		// jquery ready start
		$(document).ready(function() {
			// jQuery code

		});
		// jquery end
	</script>

</head>

<body>

	<!--aqui añadimos para que sea barra fija-->

	<div class="sticky-top bg-white" style="z-index: 1020; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
		<!--<section class="header-main border-bottom"> ANULAMOS ESTO PARA VER EL C-->

		<!-- hasta aqui añadimos al header -->


		<header class="section-header">
			<nav class="navbar p-md-0 navbar-expand-sm navbar-light border-bottom">
				<div class="container">
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarTop4" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarTop4">
						<ul class="navbar-nav mr-auto">
							<li class="nav-item dropdown">
								<a href="#" class="nav-link"> La Compu de Lolo </a>

							</li>
							<li class="nav-item">
								<a href="nosotros.php" class="nav-link"> Nosotros </a>
							</li>
						</ul>
						<ul class="navbar-nav">
							<li><a href="#" class="nav-link"> <i class="fa fa-envelope"></i> Correo </a></li>
							<li><a href="#" class="nav-link"> <i class="fa fa-phone"></i> Celular </a></li>
						</ul> <!-- list-inline //  -->
					</div> <!-- navbar-collapse .// -->
				</div> <!-- container //  -->
			</nav>



			<section class="header-main border-bottom">
				<div class="container">
					<div class="row align-items-center">
						<div class="col-lg-2 col-md-3 col-6">
							<a href="./" class="brand-wrap" style="font-size: 20px;">
								<!-- <img class="logo" src="./images/logo.png"> -->
								Asesoría <span style="color: blue; font-weight: bold;">Gratuita</span>
							</a> <!-- brand-wrap.// -->
						</div>
						<div class="col-lg col-sm col-md col-6 flex-grow-0">
							<div class="category-wrap dropdown d-inline-block float-right">
								<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
									<i class="fa fa-bars"></i> Categorías
								</button>


								<div class="dropdown-menu" style="padding: 15px; min-width: 600px;">
									<div class="row">
										<div class="col-sm-4">
											<h6 class="dropdown-header">💻 Computadoras</h6>

											<!--
     		<a class="dropdown-item" href="laptops-gamer.php">Laptops Gamer</a>
	        <a class="dropdown-item" href="pc-armadas.php">PC Armadas</a>
      		<a class="dropdown-item" href="todo-en-uno.php">Todo en Uno</a>
		-->

											<li><a class="dropdown-item" href="tienda.php?categoria=1">Laptops Gamer</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=2">PC Armadas</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=3">Todo en Uno</a></li>



										</div>
										<!-- note que se le agrego contenido al michi en cada drodpown(ademas se uso minuscula y extension html), lo hare manualmente-->
										<div class="col-sm-4">
											<h6 class="dropdown-header">🎮 Periféricos</h6>

											<li><a class="dropdown-item" href="tienda.php?categoria=4">Teclados Mecánicos</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=5">Mouses RGB</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=6">Audífonos 7.1</a></li>

										</div>


										<div class="col-sm-4">
											<h6 class="dropdown-header">⚙️ Componentes</h6>


											<li><a class="dropdown-item" href="tienda.php?categoria=7">Procesadores</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=8">Tarjetas Gráficas</a></li>
											<li><a class="dropdown-item" href="tienda.php?categoria=9">Memorias RAM</a></li>

										</div>
									</div>
								</div>




							</div> <!-- category-wrap.// -->
						</div> <!-- col.// -->
						<a href="./tienda.php" class="btn btn-outline-primary">Tienda</a>
						<div class="col-lg  col-md-6 col-sm-12 col">
							<form action="#" class="search">
								<div class="input-group w-100">
									<input type="text" class="form-control" style="width:60%;" placeholder="Search">

									<div class="input-group-append">
										<button class="btn btn-primary" type="submit">
											<i class="fa fa-search"></i>
										</button>
									</div>
								</div>
							</form> <!-- search-wrap .end// -->
						</div> <!-- col.// -->

						<!--  este bloque de codigo se agrego para que aparezca el login y registrar, ademas se le agrego un span para separar los links-->
						<div class="col-lg-3 col-sm-6 col-8 order-2 order-lg-3">
							<div class="d-flex justify-content-end mb-3 mb-lg-0">
								<div class="widget-header">
									<small class="title text-muted">Usuarios</small>
									
									

									<?php if (isset($_SESSION['usuario_nombre'])): ?>
    								<!-- Usuario logueado -->
   		 	<div>
      	 	 <span class="text-dark"><i class="fa fa-user-circle"></i> <?= $_SESSION['usuario_nombre'] ?></span>
       	 	<a href="cerrar-sesion.php" class="btn btn-sm btn-outline-danger ml-2">
       	    	 <i class="fa fa-sign-out-alt"></i> Salir
       			 </a>
    			</div>
				<?php else: ?>
   					 <!-- Usuario NO logueado -->
    				<div>
      					  <a href="iniciar-sesion.php">Ingresar</a> <span class="dark-transp"> | </span>
      		  <a href="registrar.php"> Registrar</a>
    			</div>
				<?php endif; ?>
									

								</div>
								<a href="carrito.php" class="widget-header pl-3 ml-3">
									<div class="icon icon-sm rounded-circle border"><i class="fa fa-shopping-cart"></i></div>

									<?php
									// Obtener el número de items del carrito si el usuario está logueado
									$total_items = 0;
									if (isset($_SESSION['usuario_id'])) {
										$usuario_id = $_SESSION['usuario_id'];
										$sql_count = "SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = $usuario_id";
										$res_count = $conexion->query($sql_count);
										if ($res_count && $res_count->num_rows > 0) {
											$row_count = $res_count->fetch_assoc();
											$total_items = $row_count['total'] ?? 0;
										}
									}
									?>


									<span class="badge badge-pill badge-danger notify"><?= $total_items ?></span>
								</a>



							</div> <!-- widgets-wrap.// -->
						</div> <!-- col.// -->

						<!--  este bloque de codigo se agrego para que aparezca el login y registrar, ademas se le agrego un span para separar los links-->


					</div> <!-- row.// -->
				</div> <!-- container.// -->
			</section> <!-- header-main .// -->



		</header> <!-- section-header.// -->

		<!--las dos lineas abajo se añadieron para que sea una barra fija-->
		</section>
	</div>