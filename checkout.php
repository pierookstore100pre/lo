<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// Si el usuario no está logueado, redirigir al inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php?mensaje=Por favor inicia sesión para procesar tu compra");
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);

// Consultar los ítems del carrito
$sql = "SELECT c.id as carrito_id, c.cantidad, p.id as producto_id, p.nombre, p.precio, p.imagen, p.stock 
        FROM carrito c 
        INNER JOIN productos p ON c.producto_id = p.id 
        WHERE c.usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

$items = [];
$total = 0;
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $items[] = $row;
        $total += $row['precio'] * $row['cantidad'];
    }
}
$stmt->close();

// Si el carrito está vacío, redirigir a la tienda
if (count($items) === 0) {
    header("Location: tienda.php");
    exit;
}

// Intentar pre-cargar datos del usuario
$stmt_u = $conexion->prepare("SELECT nombre, email, telefono, direccion FROM usuarios WHERE id = ?");
$stmt_u->bind_param("i", $usuario_id);
$stmt_u->execute();
$usuario_datos = $stmt_u->get_result()->fetch_assoc();
$stmt_u->close();

include('header.php');
?>

<div class="container py-5">
    <div class="mb-4">
        <h3 class="font-weight-bold text-dark mb-1">Finalizar Compra</h3>
        <p class="text-muted">Por favor ingresa los datos para la entrega de tu pedido en La Compu de Lolo</p>
    </div>

    <form action="procesar-pedido.php" method="POST">
        <div class="row">
            <!-- Datos de Envío y Pago -->
            <div class="col-lg-7 mb-4">
                <div class="card checkout-card p-4 shadow-sm">
                    <h6 class="font-weight-bold mb-3 border-bottom pb-2 text-dark">
                        1. Información de Envío
                    </h6>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small">Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario_datos['nombre'] ?? $_SESSION['usuario_nombre'] ?? '') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold small">Teléfono de Contacto</label>
                            <input type="tel" name="telefono" class="form-control" placeholder="Ej. 987654321" value="<?= htmlspecialchars($usuario_datos['telefono'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold small">Correo Electrónico</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($usuario_datos['email'] ?? '') ?>" readonly style="background-color: #f8f9fa;">
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold small">Dirección Completa de Entrega</label>
                        <textarea name="direccion" class="form-control" rows="3" placeholder="Av. Principal #123, Distrito, Ciudad..." required><?= htmlspecialchars($usuario_datos['direccion'] ?? '') ?></textarea>
                    </div>

                    <h6 class="font-weight-bold mb-3 border-bottom pb-2 text-dark">
                        2. Método de Pago
                    </h6>

                    <div class="mb-3">
                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" id="pagoYape" name="metodo_pago" value="yape_plin" class="custom-control-input" checked>
                            <label class="custom-control-label font-weight-bold" for="pagoYape">
                                Yape / Plin (Pago Móvil Inmediato)
                            </label>
                            <small class="d-block text-muted">Te mostraremos los datos de transferencia al finalizar.</small>
                        </div>

                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" id="pagoTransf" name="metodo_pago" value="transferencia" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold" for="pagoTransf">
                                Transferencia Bancaria
                            </label>
                            <small class="d-block text-muted">Transferencia a cuenta BCP o Interbank.</small>
                        </div>

                        <div class="custom-control custom-radio mb-3">
                            <input type="radio" id="pagoContraentrega" name="metodo_pago" value="contraentrega" class="custom-control-input">
                            <label class="custom-control-label font-weight-bold" for="pagoContraentrega">
                                Pago Contra Entrega
                            </label>
                            <small class="d-block text-muted">Pago en efectivo al momento de recibir el producto.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen del Pedido -->
            <div class="col-lg-5 mb-4">
                <div class="card checkout-card p-4 shadow-sm bg-light">
                    <h6 class="font-weight-bold mb-3 border-bottom pb-2 text-dark">
                        Resumen de la Compra
                    </h6>

                    <div class="mb-3" style="max-height: 280px; overflow-y: auto;">
                        <?php foreach ($items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="img/<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" style="width: 45px; height: 45px; object-fit: contain; background: #fff; border-radius: 4px;" class="p-1 border mr-2">
                                    <div>
                                        <h6 class="mb-0 text-truncate font-weight-bold" style="max-width: 170px; font-size: 0.85rem;" title="<?= htmlspecialchars($item['nombre']) ?>">
                                            <?= htmlspecialchars($item['nombre']) ?>
                                        </h6>
                                        <small class="text-muted">Cantidad: <?= $item['cantidad'] ?></small>
                                    </div>
                                </div>
                                <span class="font-weight-bold text-dark" style="font-size: 0.9rem;">
                                    $<?= number_format($item['precio'] * $item['cantidad'], 2) ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal:</span>
                            <span class="font-weight-bold">$<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Envío:</span>
                            <span class="text-success font-weight-bold">Gratis</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 font-weight-bold">Total:</span>
                            <span class="h4 font-weight-bold text-primary">$<?= number_format($total, 2) ?></span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold py-3">
                            Confirmar y Realizar Pedido
                        </button>
                        <a href="carrito.php" class="btn btn-outline-secondary btn-block mt-2">
                            <i class="bi bi-arrow-left"></i> Regresar al Carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include('footer.php'); ?>

