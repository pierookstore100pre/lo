<?php
//para estudiar lo desactivamos el script de sembrado, para que no se ejecute accidentalmente
exit("Este script está desactivado por seguridad.");

// INCLUIMOS LA CONEXIÓN QUE ACABAMOS DE REPARAR
include 'conexion.php';

// Verificamos que la conexión exista
if (!$conexion) {
    die("No hay conexión a la BD. Revisa conexion.php");
}

// IDs de tus categorías (ajusta estos números según lo que viste en DBeaver)
// Si tu tabla categorías tiene IDs: 1,2,3,4,5,6,7,8,9,10,11, déjalos así:
$categorias_ids = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];

// Lista de nombres chéveres para los productos
$nombres_base = ['Pro Gamer', 'Ultra Slim', 'Xtreme', 'Eco', 'Turbo', 'Master', 'Nova', 'Quantum', 'Apex', 'Zen', 'Thor', 'Atlas'];
$descripcion = 'Producto de alta gama con tecnología de punta y rendimiento excepcional.';

// Bucle para crear 30 productos
for ($i = 1; $i <= 30; $i++) {
    // Elegir una categoría aleatoria de las que tenemos
    $cat_id = $categorias_ids[array_rand($categorias_ids)];
    
    // Crear un nombre variado (ej: "Pro Gamer X-452")
    $nombre_random = $nombres_base[array_rand($nombres_base)] . ' ' . chr(65 + rand(0, 25)) . '-' . rand(100, 999);
    
    // Precio entre 150 y 2500 dólares
    $precio = rand(150, 2500) . '.99';
    
    // Stock entre 5 y 50 unidades
    $stock = rand(5, 50);
    
    // IMAGEN: como tienes varias imágenes (producto1.jpg a producto8.jpg según tu carpeta)
    $imagen = 'producto' . rand(1, 8) . '.jpg';
    
    // Insertar en la base de datos
    $sql = "INSERT INTO productos (categoria_id, nombre, descripcion, precio, stock, imagen, activo) 
            VALUES ($cat_id, '$nombre_random', '$descripcion', $precio, $stock, '$imagen', 1)";
    
    if ($conexion->query($sql)) {
        echo "✅ Producto $i insertado: $nombre_random <br>";
    } else {
        echo "❌ Error en producto $i: " . $conexion->error . "<br>";
    }
}

echo "<hr><h2>🎉 ¡Sembrado completado! Revisa tu tabla 'productos' en DBeaver.</h2>";
echo "<p><strong>IMPORTANTE:</strong> ELIMINA ESTE ARCHIVO (sembrar.php) AHORA MISMO por seguridad.</p>";
?>