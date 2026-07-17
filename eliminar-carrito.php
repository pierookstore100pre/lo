<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php");
    exit;
}

$carrito_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($carrito_id > 0) {
    $sql = "DELETE FROM carrito WHERE id = $carrito_id AND usuario_id = " . $_SESSION['usuario_id'];
    $conexion->query($sql);
}

header("Location: carrito.php");
exit;
?>