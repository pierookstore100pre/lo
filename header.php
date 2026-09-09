<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/html; charset=UTF-8');
include('conexion.php');

// Calcular total de items del carrito
$total_items = 0;
if (isset($_SESSION['usuario_id'])) {
	$usuario_id = intval($_SESSION['usuario_id']);
	$stmt_c = $conexion->prepare("SELECT SUM(cantidad) as total FROM carrito WHERE usuario_id = ?");
	if ($stmt_c) {
		$stmt_c->bind_param("i", $usuario_id);
		$stmt_c->execute();
		$res_count = $stmt_c->get_result();
		if ($res_count && $row_count = $res_count->fetch_assoc()) {
			$total_items = $row_count['total'] ?? 0;
		}
		$stmt_c->close();
	}
}
?>

<!DOCTYPE HTML>
<html lang="es">

<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="cache-control" content="max-age=604800" />
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>La Compu de Lolo | Tienda de Cómputo</title>

	<link href="images/favicon.ico" rel="shortcut icon" type="image/x-icon">

	<!-- Google Fonts: Inter -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

	<!-- jQuery -->
	<script src="js/jquery-2.0.0.min.js" type="text/javascript"></script>

	<!-- Bootstrap4 files-->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
	<link href="css/bootstrap.css" rel="stylesheet" type="text/css" />

	<!-- Font awesome 5 & Bootstrap Icons -->
	<link href="fonts/fontawesome/css/all.min.css" type="text/css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

	<!-- custom style -->
	<link href="css/ui.css" rel="stylesheet" type="text/css" />
	<link href="css/responsive.css" rel="stylesheet" media="only screen and (max-width: 1200px)" />
	<link href="css/custom-theme.css" rel="stylesheet" type="text/css" />

	<!-- custom javascript -->
	<script src="js/script.js" type="text/javascript"></script>
</head>

<body>

	<!-- ENCABEZADO GLOBAL PEGADO Y FLUIDO -->
	<div class="header-sticky-wrapper">
		<header class="section-header">

			<!-- BARRA PRINCIPAL -->
			<div class="header-main-bar border-bottom">
				<div class="container">
					<div class="header-main-flex py-3">
						
						<!-- 1. Logo -->
						<a href="index.php" class="header-logo-anchor" title="Ir al inicio de La Compu de Lolo">
							<img class="header-logo-img" src="./img/logo-clean.png" alt="La Compu de Lolo">
						</a>

						<!-- 2. Botón Categorías -->
						<div class="dropdown flex-shrink-0">
							<button type="button" class="btn btn-primary btn-header-categories dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="fa fa-bars mr-1"></i>
								<span>Categorías</span>
							</button>
							<div class="dropdown-menu header-categories-dropdown">
								<div class="row">
									<div class="col-sm-4">
										<h6 class="dropdown-header text-primary font-weight-bold px-0">Computadoras</h6>
										<a class="dropdown-item px-0" href="tienda.php?categoria=1">Laptops Gamer</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=2">PC Armadas</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=3">Todo en Uno</a>
									</div>
									<div class="col-sm-4">
										<h6 class="dropdown-header text-primary font-weight-bold px-0">Periféricos</h6>
										<a class="dropdown-item px-0" href="tienda.php?categoria=4">Teclados Mecánicos</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=5">Mouses RGB</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=6">Audífonos 7.1</a>
									</div>
									<div class="col-sm-4">
										<h6 class="dropdown-header text-primary font-weight-bold px-0">Componentes</h6>
										<a class="dropdown-item px-0" href="tienda.php?categoria=7">Procesadores</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=8">Tarjetas Gráficas</a>
										<a class="dropdown-item px-0" href="tienda.php?categoria=9">Memorias RAM</a>
									</div>
								</div>
							</div>
						</div>

						<!-- 3. Buscador Flexible (Centro) -->
						<form action="tienda.php" method="GET" class="header-search-form">
							<div class="header-search-group">
								<input type="text" name="buscar" class="form-control header-search-input" placeholder="Buscar productos, componentes o categorías..." value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>" aria-label="Buscar productos">
								<button class="header-search-btn" type="submit" aria-label="Buscar">
									<i class="bi bi-search"></i>
								</button>
							</div>
						</form>

						<!-- 4. Cuenta y Carrito (Derecha) -->
						<div class="header-actions-wrap">
							<div class="header-account-widget">
								<div class="header-account-icon">
									<i class="bi bi-person"></i>
								</div>
								<div class="header-account-details">
									<span class="header-account-label">MI CUENTA</span>
									<div class="header-account-actions">
										<?php if (isset($_SESSION['usuario_nombre'])): ?>
											<div class="dropdown d-inline-block">
												<a href="#" class="dropdown-toggle action-primary" data-toggle="dropdown">
													<?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
												</a>
												<div class="dropdown-menu dropdown-menu-right shadow-sm border-0">
													<a href="cerrar-sesion.php" class="dropdown-item text-danger small">
														<i class="bi bi-box-arrow-right mr-1"></i> Cerrar Sesión
													</a>
												</div>
											</div>
										<?php else: ?>
											<a href="iniciar-sesion.php" class="action-primary">Ingresar</a>
											<span class="sep">|</span>
											<a href="registrar.php">Registrarse</a>
										<?php endif; ?>
									</div>
								</div>
							</div>

							<a href="carrito.php" class="header-cart-link" aria-label="Carrito de compras" title="Ver carrito de compras">
								<i class="bi bi-cart3"></i>
								<span class="cart-count-badge"><?= $total_items ?></span>
							</a>
						</div>

					</div>
				</div>
			</div>

			<!-- BARRA DE NAVEGACIÓN -->
			<nav class="header-navbar">
				<div class="container">
					<ul class="header-nav-list">
						<li><a href="index.php" class="header-nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="bi bi-house-door"></i> Inicio</a></li>
						<li><a href="tienda.php" class="header-nav-link <?= basename($_SERVER['PHP_SELF']) == 'tienda.php' ? 'active' : '' ?>">Tienda</a></li>
						<li><a href="nosotros.php" class="header-nav-link <?= basename($_SERVER['PHP_SELF']) == 'nosotros.php' ? 'active' : '' ?>">Nosotros</a></li>
					</ul>
				</div>
			</nav>
		</header>
	</div>


