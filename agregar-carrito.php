<?php
// ============================================
// AGREGAR AL CARRITO
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php?mensaje=" . urlencode("Debes iniciar sesión para agregar al carrito"));
    exit;
}

$usuario_id  = (int) $_SESSION['usuario_id'];
$producto_id = isset($_POST['producto_id']) ? (int) $_POST['producto_id'] : 0;
$cantidad    = isset($_POST['cantidad'])    ? (int) $_POST['cantidad']    : 1;

// Validaciones básicas
if ($producto_id <= 0 || $cantidad <= 0) {
    header("Location: tienda.php");
    exit;
}
// Límite razonable por operación
if ($cantidad > 99) {
    $cantidad = 99;
}

// ---- Verificar que el producto exista y esté activo ----
$stmt = $conexion->prepare(
    "SELECT id, nombre, stock FROM productos WHERE id = ? AND activo = 1 LIMIT 1"
);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    // Producto inexistente o inactivo
    header("Location: tienda.php");
    exit;
}

$stock_disponible = (int) $producto['stock'];

// ---- Ver si ya está en el carrito ----
$stmt = $conexion->prepare(
    "SELECT id, cantidad FROM carrito WHERE usuario_id = ? AND producto_id = ? LIMIT 1"
);
$stmt->bind_param("ii", $usuario_id, $producto_id);
$stmt->execute();
$item = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($item) {
    // ---- Ya existe: sumar cantidad respetando stock ----
    $nueva_cantidad = (int)$item['cantidad'] + $cantidad;
    if ($nueva_cantidad > $stock_disponible) {
        $nueva_cantidad = $stock_disponible;
    }
    if ($nueva_cantidad < 1) {
        $nueva_cantidad = 1;
    }

    $stmt = $conexion->prepare(
        "UPDATE carrito SET cantidad = ? WHERE id = ? AND usuario_id = ?"
    );
    $stmt->bind_param("iii", $nueva_cantidad, $item['id'], $usuario_id);
    $stmt->execute();
    $stmt->close();
} else {
    // ---- No existe: insertar respetando stock ----
    if ($cantidad > $stock_disponible) {
        $cantidad = $stock_disponible;
    }
    if ($cantidad < 1) {
        // No hay stock, redirigir sin agregar
        header("Location: producto.php?id=" . $producto_id . "&mensaje=" . urlencode("Producto sin stock"));
        exit;
    }

    $stmt = $conexion->prepare(
        "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("iii", $usuario_id, $producto_id, $cantidad);
    $stmt->execute();
    $stmt->close();
}

// Redirigir al carrito
header("Location: carrito.php");
exit;
?>
