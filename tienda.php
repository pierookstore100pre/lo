<?php 
include('conexion.php'); 
include('header.php'); 

// 1. Obtener parámetros de filtrado y sanitizarlos
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : 0;
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$orden = isset($_GET['orden']) ? $_GET['orden'] : 'recientes';

// 2. Construir la consulta SQL dinámica con Prepared Statements
$conditions = ["activo = 1"];
$params = [];
$types = "";

if ($categoria_id > 0) {
    $conditions[] = "categoria_id = ?";
    $params[] = $categoria_id;
    $types .= "i";
}

if (!empty($buscar)) {
    $conditions[] = "(nombre LIKE ? OR descripcion LIKE ?)";
    $term = "%" . $buscar . "%";
    $params[] = $term;
    $params[] = $term;
    $types .= "ss";
}

$whereClause = implode(" AND ", $conditions);

// Definir ordenamiento
$orderBy = "ORDER BY id DESC";
if ($orden === 'precio_asc') {
    $orderBy = "ORDER BY precio ASC";
} elseif ($orden === 'precio_desc') {
    $orderBy = "ORDER BY precio DESC";
}

$sql = "SELECT p.*, c.nombre as categoria_nombre FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id WHERE $whereClause $orderBy";
$stmt = $conexion->prepare($sql);

if (!empty($types) && count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();

// 3. Obtener la lista de categorías para el sidebar
$res_cats = $conexion->query("SELECT * FROM categorias ORDER BY nombre ASC");
$categorias_lista = [];
if ($res_cats && $res_cats->num_rows > 0) {
    while ($cat = $res_cats->fetch_assoc()) {
        $categorias_lista[] = $cat;
    }
}

// Título descriptivo
$titulo_pagina = "Todos los Productos";
if ($categoria_id > 0) {
    foreach ($categorias_lista as $c) {
        if ($c['id'] == $categoria_id) {
            $titulo_pagina = "Categoría: " . htmlspecialchars($c['nombre']);
            break;
        }
    }
} elseif (!empty($buscar)) {
    $titulo_pagina = "Búsqueda: \"" . htmlspecialchars($buscar) . "\"";
}
?>

<!-- CONTENIDO PRINCIPAL DE LA TIENDA -->
<div class="container py-4">
    <div class="tienda-layout">
        
        <!-- SIDEBAR DE FILTROS (260px) -->
        <aside class="tienda-sidebar">
            <div class="card filter-card p-3 shadow-sm">
                <h6 class="filter-group-title border-bottom pb-2 mb-3">
                    Filtros de Tienda
                </h6>

                <!-- Filtro de Búsqueda -->
                <form action="tienda.php" method="GET" class="mb-4">
                    <?php if ($categoria_id > 0): ?>
                        <input type="hidden" name="categoria" value="<?= $categoria_id ?>">
                    <?php endif; ?>
                    <label class="form-label font-weight-bold text-muted small">BUSCAR PRODUCTO</label>
                    <div class="input-group">
                        <input type="text" name="buscar" class="form-control form-control-sm" placeholder="Ej. Laptop, Mouse..." value="<?= htmlspecialchars($buscar) ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>

                <!-- Filtro por Categorías -->
                <div class="mb-4">
                    <label class="form-label font-weight-bold text-muted small">CATEGORÍAS</label>
                    <div class="list-group list-group-flush">
                        <a href="tienda.php<?= !empty($buscar) ? '?buscar='.urlencode($buscar) : '' ?>" 
                           class="list-group-item list-group-item-action border-0 rounded mb-1 px-3 py-2 <?= $categoria_id == 0 ? 'active font-weight-bold' : '' ?>">
                           Todas las Categorías
                        </a>
                        <?php foreach ($categorias_lista as $cat): ?>
                            <a href="tienda.php?categoria=<?= $cat['id'] ?><?= !empty($buscar) ? '&buscar='.urlencode($buscar) : '' ?>" 
                               class="list-group-item list-group-item-action border-0 rounded mb-1 px-3 py-2 <?= $categoria_id == $cat['id'] ? 'active font-weight-bold' : '' ?>">
                               <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Botón de Limpiar Filtros -->
                <?php if ($categoria_id > 0 || !empty($buscar) || $orden !== 'recientes'): ?>
                    <a href="tienda.php" class="btn btn-sm btn-outline-danger btn-block mt-2">
                        Limpiar Filtros
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <!-- MAIN CATALOG AREA (flexible con min-width:0 para evitar desbordamientos) -->
        <main class="tienda-main">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-3 mb-4">
                <div>
                    <h4 class="font-weight-bold text-dark mb-1" style="font-size: 1.5rem;"><?= $titulo_pagina ?></h4>
                    <span class="text-muted small"><?= $resultado->num_rows ?> productos encontrados</span>
                </div>

                <!-- Ordenamiento -->
                <form action="tienda.php" method="GET" class="form-inline mt-2 mt-sm-0">
                    <?php if ($categoria_id > 0): ?>
                        <input type="hidden" name="categoria" value="<?= $categoria_id ?>">
                    <?php endif; ?>
                    <?php if (!empty($buscar)): ?>
                        <input type="hidden" name="buscar" value="<?= htmlspecialchars($buscar) ?>">
                    <?php endif; ?>
                    <label class="mr-2 text-muted small font-weight-bold">Ordenar por:</label>
                    <select name="orden" class="form-control form-control-sm" onchange="this.form.submit()">
                        <option value="recientes" <?= $orden == 'recientes' ? 'selected' : '' ?>>Más Recientes</option>
                        <option value="precio_asc" <?= $orden == 'precio_asc' ? 'selected' : '' ?>>Precio: Menor a Mayor</option>
                        <option value="precio_desc" <?= $orden == 'precio_desc' ? 'selected' : '' ?>>Precio: Mayor a Menor</option>
                    </select>
                </form>
            </div>

            <!-- Grilla de productos -->
            <div class="row">
                <?php if ($resultado->num_rows > 0): ?>
                    <?php while ($producto = $resultado->fetch_assoc()): ?>
                        <div class="col-xl-4 col-lg-4 col-md-6 col-6 mb-4 d-flex align-items-stretch">
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
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-secondary text-center py-5">
                            <h5>No se encontraron productos</h5>
                            <p class="text-muted mb-3">Intenta cambiar los filtros o buscar con otro término.</p>
                            <a href="tienda.php" class="btn btn-primary btn-sm px-4">Ver Todos los Productos</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<?php 
$stmt->close();
include('footer.php'); 
?>


