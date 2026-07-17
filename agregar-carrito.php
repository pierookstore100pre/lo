<?php
// Iniciar sesión para verificar al usuario
session_start();
include('conexion.php');

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id'])) {
    // Si no está logueado, lo mandamos a login con un mensaje
    header("Location: iniciar-sesion.php?mensaje=Debes iniciar sesión para agregar al carrito");
    exit;
}

// Obtener datos del formulario
$producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 1;

if ($producto_id <= 0) {
    header("Location: tienda.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

// Verificar si el producto ya está en el carrito del usuario
$sql_check = "SELECT id, cantidad FROM carrito WHERE usuario_id = $usuario_id AND producto_id = $producto_id";
$resultado_check = $conexion->query($sql_check);

if ($resultado_check->num_rows > 0) {
    // Si ya existe, actualizar cantidad sumando la nueva
    $row = $resultado_check->fetch_assoc();
    $nueva_cantidad = $row['cantidad'] + $cantidad;
    $sql_update = "UPDATE carrito SET cantidad = $nueva_cantidad WHERE id = " . $row['id'];
    $conexion->query($sql_update);
} else {
    // Si no existe, insertar nuevo registro
    $sql_insert = "INSERT INTO carrito (usuario_id, producto_id, cantidad) VALUES ($usuario_id, $producto_id, $cantidad)";
    $conexion->query($sql_insert);
}

// Redirigir al carrito para ver los cambios
header("Location: carrito.php");
exit;
?>