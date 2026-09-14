<?php
ini_set('default_charset', 'UTF-8');

// ============================================
// 1. CARGAR CONFIGURACIÓN PRIVADA
// ============================================
// Si config.php no existe (por ejemplo, en el hosting olvidaste subirlo),
// usamos valores por defecto para desarrollo local.
if (file_exists(__DIR__ . '/config.php')) {
    include_once(__DIR__ . '/config.php');
} else {
    // Fallback SOLO para desarrollo local
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_NAME')) define('DB_NAME', 'lacompudelolo');
}

// ============================================
// 2. CREAR LA CONEXIÓN
// ============================================
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar si hubo error (sin exponer detalles sensibles)
if ($conexion->connect_error) {
    error_log("Error de conexión MySQL: " . $conexion->connect_error);
    die("Error de conexión a la base de datos. Contacta al administrador.");
}

// Configurar juego de caracteres a utf8mb4
$conexion->set_charset("utf8mb4");
?>
