<?php
// ============================================
// CERRAR SESIÓN
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerar el ID antes de destruir (evita reutilización)
session_regenerate_id(true);

// Destruir todas las variables de sesión
$_SESSION = [];

// Destruir la cookie de sesión si existe
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destruir la sesión
session_destroy();

// Redirigir al inicio
header("Location: index.php");
exit;
?>
