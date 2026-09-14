<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

echo "<h2>Debug activo</h2>";

// Probar conexión
require 'conexion.php';

if (isset($conexion) && $conexion instanceof mysqli) {
    echo "<p style='color:green;'>✅ Conexión a la base de datos OK</p>";
    echo "<p>Servidor MySQL: " . $conexion->server_info . "</p>";
    echo "<p>Base de datos: " . DB_NAME . "</p>";
} else {
    echo "<p style='color:red;'>❌ No se pudo conectar a la BD</p>";
}