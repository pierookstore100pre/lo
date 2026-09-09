<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// Si no está logueado, redirigir
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php?mensaje=Inicia sesión para ver tu carrito");
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);

// Consultar el carrito con información del producto usando Prepared Statements
$sql = "SELECT c.id as carrito_id, c.cantidad, p.id as producto_id, p.nombre, p.precio, p.imagen 
        FROM carrito c 
        INNER JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

// Calcular total
$total = 0;
$items = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $items[] = $row;
        $total += $row['precio'] * $row['cantidad'];
    }
}
$stmt->close();

include('header.php');
?>

<div class="container py-5">
    <h2 class="section-title mb-4">Mi Carrito</h2>

    <?php if (isset($_GET['mensaje'])): ?>
        <div class="alert alert-info"><?= htmlspecialchars($_GET['mensaje']) ?></div>
    <?php endif; ?>

    <?php if (count($items) == 0): ?>
        <div class="alert alert-info text-center">
            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
            <h4>Tu carrito está vacío</h4>
            <a href="tienda.php" class="btn btn-primary mt-3">Ir a la tienda</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Producto</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="img/<?= $item['imagen'] ?>" alt="<?= $item['nombre'] ?>" style="height: 60px; object-fit: contain;">
                            </td>
                            <td><?= $item['nombre'] ?></td>
                            <td>$<?= number_format($item['precio'], 2) ?></td>
                            <td>
                                <form action="actualizar-carrito.php" method="POST" class="d-flex align-items-center gap-2">
                                    <input type="hidden" name="carrito_id" value="<?= $item['carrito_id'] ?>">
                                    <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                                    <button type="submit" class="btn btn-sm btn-warning">Actualizar</button>
                                </form>
                            </td>
                            <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                            <td>
                                <a href="eliminar-carrito.php?id=<?= $item['carrito_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este producto del carrito?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end fw-bold">Total:</td>
                        <td colspan="2" class="fw-bold fs-5 text-danger">$<?= number_format($total, 2) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="tienda.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Seguir comprando</a>
            <a href="checkout.php" class="btn btn-success btn-lg">Proceder al pago <i class="bi bi-arrow-right"></i></a>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>