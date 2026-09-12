<?php
// ============================================
// ELIMINAR PRODUCTO DEL CARRITO
// ============================================
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
$carrito_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($carrito_id > 0) {
    // El WHERE con usuario_id asegura que solo se borre si pertenece al usuario
    $stmt = $conexion->prepare(
        "DELETE FROM carrito WHERE id = ? AND usuario_id = ?"
    );
    $stmt->bind_param("ii", $carrito_id, $usuario_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: carrito.php");
exit;
?>
