<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('conexion.php');

// Si no está logueado, redirigir
if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php");
    exit;
}

$usuario_id = (int) $_SESSION['usuario_id'];
$carrito_id = isset($_POST['carrito_id']) ? (int) $_POST['carrito_id'] : 0;
$cantidad   = isset($_POST['cantidad'])   ? (int) $_POST['cantidad']   : 0;

// Validar rangos razonables
if ($carrito_id > 0 && $cantidad > 0 && $cantidad <= 999) {

    // Verificar que el carrito pertenezca a este usuario Y que el producto tenga stock suficiente
    $stmt = $conexion->prepare(
        "SELECT c.id, c.producto_id, p.stock, p.nombre
         FROM carrito c
         INNER JOIN productos p ON c.producto_id = p.id
         WHERE c.id = ? AND c.usuario_id = ?
         LIMIT 1"
    );
    $stmt->bind_param("ii", $carrito_id, $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $item = $resultado->fetch_assoc();
    $stmt->close();

    if ($item) {
        // No permitir más cantidad que el stock disponible
        $cantidad_final = min($cantidad, (int)$item['stock']);
        if ($cantidad_final < 1) $cantidad_final = 1;

        $stmt = $conexion->prepare(
            "UPDATE carrito SET cantidad = ? WHERE id = ? AND usuario_id = ?"
        );
        $stmt->bind_param("iii", $cantidad_final, $carrito_id, $usuario_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: carrito.php");
exit;
?>