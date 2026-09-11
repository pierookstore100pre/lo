<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = htmlspecialchars(trim($_POST['nombre'] ?? ''));
    $email   = htmlspecialchars(trim($_POST['email'] ?? ''));
    $telefono= htmlspecialchars(trim($_POST['telefono'] ?? ''));
    $consulta= htmlspecialchars(trim($_POST['consulta'] ?? ''));

    $para = 'ventas@lacompudelolo.com';
    $asunto = 'Nueva solicitud de asesoría';
    $mensaje = "Nombre: $nombre\nEmail: $email\nTeléfono: $telefono\n\nConsulta:\n$consulta";
    $headers = "From: no-reply@lacompudelolo.com";

    @mail($para, $asunto, $mensaje, $headers);

    header('Location: asesoria.php?enviado=ok');
    exit;
}
header('Location: asesoria.php');
exit;