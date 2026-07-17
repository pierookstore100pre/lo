<?php
// ============================================
// 1. INICIAR SESIÓN Y CONEXIÓN (PRIMERO)
// ============================================

include('conexion.php');

// Variable para mensajes
$mensaje = '';

// ============================================
// 2. PROCESAR EL FORMULARIO (LÓGICA PHP)
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];
    $confirmar_clave = $_POST['confirmar_clave'];

    if (empty($nombre) || empty($email) || empty($clave)) {
        $mensaje = '❌ Todos los campos son obligatorios.';
    } elseif ($clave !== $confirmar_clave) {
        $mensaje = '❌ Las contraseñas no coinciden.';
    } else {
        $email_check = mysqli_real_escape_string($conexion, $email);
        $sql_check = "SELECT id FROM usuarios WHERE email = '$email_check'";
        $resultado_check = $conexion->query($sql_check);

        if ($resultado_check->num_rows > 0) {
            $mensaje = '❌ Este correo electrónico ya está registrado.';
        } else {
            $clave_hash = password_hash($clave, PASSWORD_DEFAULT);
            $nombre_seguro = mysqli_real_escape_string($conexion, $nombre);

            $sql = "INSERT INTO usuarios (nombre, email, password) 
                    VALUES ('$nombre_seguro', '$email_check', '$clave_hash')";

            if ($conexion->query($sql)) {
                header("Location: iniciar-sesion.php?registro=ok");
                exit;
            } else {
                $mensaje = '❌ Error al registrar: ' . $conexion->error;
            }
        }
    }
}
?>

<!-- ============================================ -->
<!-- 3. AHORA SÍ, INCLUIMOS EL HEADER (HTML)     ===========================================================-->
<!-- ============================================ -->
<?php include('header.php'); ?>

<!-- CONTENIDO ESPECÍFICO DE REGISTRO -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Registrar</h2>
    <p>Aquí puedes registrarte para crear una cuenta.</p>

    <!-- Mostrar mensajes de error/éxito -->
    <?php if ($mensaje): ?>
        <div class="alert alert-danger"><?= $mensaje ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['registro']) && $_GET['registro'] == 'ok'): ?>
        <div class="alert alert-success">✅ ¡Registro exitoso! Ahora inicia sesión.</div>
    <?php endif; ?>

    <div class="card mx-auto" style="max-width:520px; margin-top:40px;">
        <article class="card-body">
            <header class="mb-4"><h4 class="card-title">Llene los campos</h4></header>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Tu nombre completo" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" placeholder="tu@email.com" required>
                    <small class="form-text text-muted">Nunca compartiremos tu correo electrónico con nadie más.</small>
                </div>

                <div class="form-group">
                    <label>Crear contraseña</label>
                    <input class="form-control" type="password" name="clave" placeholder="Mínimo 6 caracteres" required>
                </div>

                <div class="form-group">
                    <label>Repetir contraseña</label>
                    <input class="form-control" type="password" name="confirmar_clave" placeholder="Repite la contraseña" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Registrar</button>
                </div>
            </form>
        </article>
    </div>

    <p class="text-center mt-4">¿Ya tienes una cuenta? <a href="iniciar-sesion.php">Iniciar Sesión</a></p>
</div>

<?php include('footer.php'); ?>