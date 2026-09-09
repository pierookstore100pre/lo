<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php");
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);
$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($pedido_id <= 0) {
    header("Location: tienda.php");
    exit;
}

// Obtener datos del pedido
$sql_p = "SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?";
$stmt_p = $conexion->prepare($sql_p);
$stmt_p->bind_param("ii", $pedido_id, $usuario_id);
$stmt_p->execute();
$res_p = $stmt_p->get_result();

if (!$res_p || $res_p->num_rows === 0) {
    echo "<div class='container py-5'><div class='alert alert-danger'>Pedido no encontrado.</div></div>";
    include('footer.php');
    exit;
}

$pedido = $res_p->fetch_assoc();
$stmt_p->close();

// Obtener los detalles del pedido
$sql_d = "SELECT d.*, p.nombre, p.imagen 
          FROM pedido_detalles d 
          INNER JOIN productos p ON d.producto_id = p.id 
          WHERE d.pedido_id = ?";
$stmt_d = $conexion->prepare($sql_d);
$stmt_d->bind_param("i", $pedido_id);
$stmt_d->execute();
$res_d = $stmt_d->get_result();

$detalles = [];
if ($res_d && $res_d->num_rows > 0) {
    while ($row = $res_d->fetch_assoc()) {
        $detalles[] = $row;
    }
}
$stmt_d->close();

include('header.php');
?>

<div class="container py-5">
    <div class="card shadow-sm border-0 rounded p-4 max-w-75 mx-auto">
        <div class="text-center mb-4">
            <div class="display-4 text-success mb-2">
                <i class="bi bi-check-circle"></i>
            </div>
            <h3 class="font-weight-bold text-dark">Gracias por tu Compra en La Compu de Lolo</h3>
            <p class="text-muted">Tu pedido <strong>#<?= sprintf("%05d", $pedido['id']) ?></strong> ha sido registrado con éxito.</p>
            <span class="badge badge-secondary px-3 py-2 font-weight-bold" style="font-size: 0.85rem;">
                Estado: <?= strtoupper(htmlspecialchars($pedido['estado'])) ?>
            </span>
        </div>

        <div class="row my-4">
            <!-- Detalles del Pedido -->
            <div class="col-md-6 mb-3">
                <div class="p-3 bg-light rounded border">
                    <h6 class="font-weight-bold text-dark mb-3"><i class="bi bi-geo-alt"></i> Datos de Entrega</h6>
                    <p class="mb-1"><strong>Cliente:</strong> <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></p>
                    <p class="mb-1"><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono']) ?></p>
                    <p class="mb-1"><strong>Dirección:</strong> <?= htmlspecialchars($pedido['direccion_envio']) ?></p>
                    <p class="mb-0"><strong>Fecha:</strong> <?= date("d/m/Y H:i", strtotime($pedido['fecha_pedido'])) ?></p>
                </div>
            </div>

            <!-- Instrucciones de Pago -->
            <div class="col-md-6 mb-3">
                <div class="p-3 bg-light rounded border">
                    <h6 class="font-weight-bold text-dark mb-3"><i class="bi bi-credit-card"></i> Confirmación de Pago</h6>
                    <p class="small text-muted mb-2">Para coordinar la entrega o envío del pedido:</p>
                    <div class="alert alert-info p-2 mb-2 font-weight-bold small">
                        Yape / Plin: 987-654-321 (La Compu de Lolo)
                    </div>
                    <a href="https://api.whatsapp.com/send?phone=51987654321&text=Hola,%20acabo%20de%20realizar%20el%20pedido%20%23<?= sprintf('%05d', $pedido['id']) ?>%20por%20un%20total%20de%20$<?= number_format($pedido['total'], 2) ?>" 
                       target="_blank" class="btn btn-sm btn-success btn-block font-weight-bold">
                        <i class="bi bi-whatsapp"></i> Confirmar Pedido por WhatsApp
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabla de Productos Comprados -->
        <h6 class="font-weight-bold mb-3 border-bottom pb-2">Productos en tu Pedido</h6>
        <div class="table-responsive mb-4">
            <table class="table align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $det): ?>
                        <tr>
                            <td class="d-flex align-items-center">
                                <img src="img/<?= htmlspecialchars($det['imagen']) ?>" alt="<?= htmlspecialchars($det['nombre']) ?>" style="width: 40px; height: 40px; object-fit: contain;" class="mr-3 border rounded p-1">
                                <span class="font-weight-bold text-dark"><?= htmlspecialchars($det['nombre']) ?></span>
                            </td>
                            <td>$<?= number_format($det['precio_unitario'], 2) ?></td>
                            <td><?= $det['cantidad'] ?></td>
                            <td class="text-right font-weight-bold text-dark">$<?= number_format($det['precio_unitario'] * $det['cantidad'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-right font-weight-bold h6">Total Pagado:</th>
                        <th class="text-right font-weight-bold text-primary h5">$<?= number_format($pedido['total'], 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="text-center">
            <a href="tienda.php" class="btn btn-primary font-weight-bold mr-2">
                Volver a la Tienda
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer"></i> Imprimir Comprobante
            </button>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

