<?php
ini_set('default_charset', 'UTF-8');

// ============================================
// 1. CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================
$servidor = "localhost";
$usuario = "root";
$clave = "";
$basedatos = "lacompudelolo";

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $clave, $basedatos);

// Verificar si hubo error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Configurar juego de caracteres a utf8mb4
$conexion->set_charset("utf8mb4");

?>

