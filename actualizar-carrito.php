<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['usuario_id'])) {
    header("Location: iniciar-sesion.php");
    exit;
}

$carrito_id = isset($_POST['carrito_id']) ? intval($_POST['carrito_id']) : 0;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : 0;

if ($carrito_id > 0 && $cantidad > 0) {
    $sql = "UPDATE carrito SET cantidad = $cantidad WHERE id = $carrito_id AND usuario_id = " . $_SESSION['usuario_id'];
    $conexion->query($sql);
}

header("Location: carrito.php");
exit;
?>