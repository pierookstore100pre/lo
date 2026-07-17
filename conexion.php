<?php
// ============================================
// 1. CONFIGURACIÓN DE LA BASE DE DATOS
// ============================================
$servidor = "localhost";
$usuario = "root";        // En WAMP por defecto es 'root'
$clave = "";              // En WAMP por defecto está VACÍA (sin contraseña)
$basedatos = "lacompudelolo"; // El nombre exacto de tu BD

// Crear la conexión
$conexion = new mysqli($servidor, $usuario, $clave, $basedatos);

// Verificar si hubo error
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
// (Opcional) Para ver que todo va bien, descomenta la línea de abajo:
// echo "Conexión exitosa a la BD";

$conexion->set_charset("utf8");

?>
