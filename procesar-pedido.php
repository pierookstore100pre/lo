<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tienda.php");
    exit;
}

$usuario_id = intval($_SESSION['usuario_id']);
$direccion = trim($_POST['direccion'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$metodo_pago = trim($_POST['metodo_pago'] ?? 'yape_plin');

if (empty($direccion) || empty($telefono)) {
    header("Location: checkout.php?error=Por favor completa todos los campos requeridos");
    exit;
}

// 1. Obtener los ítems del carrito del usuario
$sql_cart = "SELECT c.id as carrito_id, c.cantidad, p.id as producto_id, p.precio, p.stock 
             FROM carrito c 
             INNER JOIN productos p ON c.producto_id = p.id 
             WHERE c.usuario_id = ?";
$stmt_cart = $conexion->prepare($sql_cart);
$stmt_cart->bind_param("i", $usuario_id);
$stmt_cart->execute();
$res_cart = $stmt_cart->get_result();

if (!$res_cart || $res_cart->num_rows === 0) {
    header("Location: tienda.php");
    exit;
}

$items = [];
$total_pedido = 0;
while ($row = $res_cart->fetch_assoc()) {
    $items[] = $row;
    $total_pedido += $row['precio'] * $row['cantidad'];
}
$stmt_cart->close();

// 2. Insertar el pedido en la tabla 'pedidos'
$sql_pedido = "INSERT INTO pedidos (usuario_id, total, estado, direccion_envio, telefono) VALUES (?, ?, 'pendiente', ?, ?)";
$stmt_pedido = $conexion->prepare($sql_pedido);
$stmt_pedido->bind_param("idss", $usuario_id, $total_pedido, $direccion, $telefono);

if ($stmt_pedido->execute()) {
    $pedido_id = $conexion->insert_id;
    $stmt_pedido->close();

    // 3. Insertar el detalle del pedido en 'pedido_detalles'
    $sql_det = "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_det = $conexion->prepare($sql_det);

    foreach ($items as $item) {
        $prod_id = $item['producto_id'];
        $cant = $item['cantidad'];
        $precio_u = $item['precio'];
        $stmt_det->bind_param("iiid", $pedido_id, $prod_id, $cant, $precio_u);
        $stmt_det->execute();
    }
    $stmt_det->close();

    // 4. Vaciar el carrito del usuario
    $stmt_del = $conexion->prepare("DELETE FROM carrito WHERE usuario_id = ?");
    $stmt_del->bind_param("i", $usuario_id);
    $stmt_del->execute();
    $stmt_del->close();

    // 5. Actualizar teléfono y dirección en la cuenta del usuario si aplica
    $stmt_upd = $conexion->prepare("UPDATE usuarios SET telefono = ?, direccion = ? WHERE id = ?");
    $stmt_upd->bind_param("ssi", $telefono, $direccion, $usuario_id);
    $stmt_upd->execute();
    $stmt_upd->close();

    // Redirigir a la página de confirmación de pedido
    header("Location: confirmacion-pedido.php?id=" . $pedido_id);
    exit;
} else {
    die("Error al procesar el pedido: " . $conexion->error);
}
?>
