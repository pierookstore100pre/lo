<?php
// ============================================
// ENVIAR ASESORÍA - Procesar formulario de contacto
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: asesoria.php');
    exit;
}

// ---- Recibir y limpiar datos ----
$nombre   = trim($_POST['nombre']   ?? '');
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$consulta = trim($_POST['consulta'] ?? '');

// ---- Validaciones ----
$errores = [];

if (empty($nombre) || mb_strlen($nombre) > 100) {
    $errores[] = 'El nombre es inválido.';
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
    $errores[] = 'El correo electrónico es inválido.';
}
if (empty($consulta) || mb_strlen($consulta) > 1000) {
    $errores[] = 'La consulta es inválida.';
}
if (!empty($telefono) && !preg_match('/^[0-9+\-\s]{6,20}$/', $telefono)) {
    $errores[] = 'El teléfono es inválido.';
}

if (!empty($errores)) {
    header('Location: asesoria.php?error=' . urlencode(implode(' ', $errores)));
    exit;
}

// ---- Sanitizar contra Email Header Injection ----
// Elimina saltos de línea y sus codificaciones URL
$limpiar_header = function ($valor) {
    return str_replace(["\r", "\n", "%0a", "%0d", "%0A", "%0D"], '', $valor);
};

$nombre_seguro   = $limpiar_header($nombre);
$email_seguro    = $limpiar_header($email);
$telefono_seguro = $limpiar_header($telefono);

// ---- Construir el correo ----
$para    = 'ventas@lacompudelolo.com';
$asunto  = 'Nueva solicitud de asesoría';

$mensaje = "Nombre: {$nombre}\n"
         . "Email: {$email}\n"
         . "Teléfono: {$telefono}\n\n"
         . "Consulta:\n{$consulta}";

$headers = "From: no-reply@lacompudelolo.com\r\n"
         . "Reply-To: {$email_seguro}\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n"
         . "X-Mailer: PHP/" . phpversion();

// ---- Enviar (silencioso si falla, pero registrando en el log) ----
if (!@mail($para, $asunto, $mensaje, $headers)) {
    error_log("Fallo al enviar asesoría desde: {$email} (nombre: {$nombre})");
}

// ---- Redirigir con éxito ----
header('Location: asesoria.php?enviado=1');
exit;
?>
