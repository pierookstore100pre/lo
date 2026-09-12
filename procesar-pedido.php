<?php
// ============================================
// PROCESAR PEDIDO
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// Solo POST y usuario logueado
if (!isset($_SESSION['usuario_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: tienda.php");
    exit;
}

$usuario_id  = (int) $_SESSION['usuario_id'];
$nombre      = trim($_POST['nombre'] ?? '');
$direccion   = trim($_POST['direccion'] ?? '');
$telefono    = trim($_POST['telefono'] ?? '');
$metodo_pago = trim($_POST['metodo_pago'] ?? 'yape_plin');

// ---- Validaciones ----
if (empty($direccion) || empty($telefono)) {
    header("Location: checkout.php?error=" . urlencode("Por favor completa todos los campos requeridos"));
    exit;
}

// Lista blanca de métodos de pago
$metodos_validos = ['yape_plin', 'transferencia', 'contra_entrega', 'tarjeta'];
if (!in_array($metodo_pago, $metodos_validos, true)) {
    $metodo_pago = 'yape_plin';
}

// Validación básica del teléfono (solo dígitos, +, espacios, guiones)
if (!preg_match('/^[0-9+\-\s]{6,20}$/', $telefono)) {
    header("Location: checkout.php?error=" . urlencode("Teléfono inválido"));
    exit;
}

// ---- 1. Obtener los ítems del carrito ----
$stmt = $conexion->prepare(
    "SELECT c.id AS carrito_id, c.cantidad,
            p.id AS producto_id, p.nombre, p.precio, p.stock
     FROM carrito c
     INNER JOIN productos p ON c.producto_id = p.id
     WHERE c.usuario_id = ? AND p.activo = 1"
);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$res = $stmt->get_result();

$items = [];
$total_pedido = 0;
while ($row = $res->fetch_assoc()) {
    // Validar stock suficiente
    if ((int)$row['cantidad'] > (int)$row['stock']) {
        $stmt->close();
        header("Location: carrito.php?mensaje=" . urlencode("Stock insuficiente para: " . $row['nombre']));
        exit;
    }
    $items[] = $row;
    $total_pedido += (float)$row['precio'] * (int)$row['cantidad'];
}
$stmt->close();

if (empty($items)) {
    header("Location: tienda.php");
    exit;
}

// ---- 2. Iniciar transacción ----
$conexion->begin_transaction();

try {
    // ---- Insertar el pedido ----
    $stmt = $conexion->prepare(
        "INSERT INTO pedidos (usuario_id, total, estado, direccion_envio, telefono, metodo_pago)
         VALUES (?, ?, 'pendiente', ?, ?, ?)"
    );
    $stmt->bind_param("idsss", $usuario_id, $total_pedido, $direccion, $telefono, $metodo_pago);
    $stmt->execute();
    $pedido_id = $conexion->insert_id;
    $stmt->close();

    // ---- Insertar los detalles del pedido ----
    $stmt_det = $conexion->prepare(
        "INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio_unitario)
         VALUES (?, ?, ?, ?)"
    );

    // ---- Preparar también el UPDATE de stock (reutilizable) ----
    $stmt_stock = $conexion->prepare(
        "UPDATE productos SET stock = stock - ? WHERE id = ? AND stock >= ?"
    );

    foreach ($items as $item) {
        $prod_id  = (int) $item['producto_id'];
        $cant     = (int) $item['cantidad'];
        $precio_u = (float) $item['precio'];

        // Detalle
        $stmt_det->bind_param("iiid", $pedido_id, $prod_id, $cant, $precio_u);
        $stmt_det->execute();

        // Descontar stock (el WHERE con stock >= ? previene ventas en paralelo)
        $stmt_stock->bind_param("iii", $cant, $prod_id, $cant);
        $stmt_stock->execute();

        // Si no se afectó ninguna fila, alguien más compró el último stock
        if ($stmt_stock->affected_rows === 0) {
            throw new Exception("Stock insuficiente para el producto #" . $prod_id);
        }
    }
    $stmt_det->close();
    $stmt_stock->close();

    // ---- Vaciar el carrito ----
    $stmt = $conexion->prepare("DELETE FROM carrito WHERE usuario_id = ?");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->close();

    // ---- Actualizar datos del usuario ----
    $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, telefono = ?, direccion = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nombre, $telefono, $direccion, $usuario_id);
    $stmt->execute();
    $stmt->close();

    // ---- Confirmar transacción ----
    $conexion->commit();

    header("Location: confirmacion-pedido.php?id=" . $pedido_id);
    exit;

} catch (Exception $e) {
    // ---- Deshacer todos los cambios ----
    $conexion->rollback();

    // Registrar el error en el log del servidor (no mostrarlo al usuario)
    error_log("Error al procesar pedido (usuario $usuario_id): " . $e->getMessage());

    header("Location: checkout.php?error=" . urlencode("Hubo un problema al procesar tu pedido. Intenta nuevamente."));
    exit;
}
?>
