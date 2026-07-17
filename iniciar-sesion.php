<?php
// ============================================
// 1. PROCESAR EL FORMULARIO (ANTES DE CUALQUIER HTML)
// ============================================
// Iniciar sesión para usar $_SESSION

// Incluir conexión
include('conexion.php');

// Si el usuario ya está logueado, lo mandamos al inicio
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $clave = $_POST['clave'];

    if (empty($email) || empty($clave)) {
        $error = '❌ Ingresa tu email y contraseña.';
    } else {
        // Buscar el usuario en la BD
        $email_seguro = mysqli_real_escape_string($conexion, $email);
        $sql = "SELECT id, nombre, email, password FROM usuarios WHERE email = '$email_seguro'";
        $resultado = $conexion->query($sql);

        if ($resultado->num_rows == 1) {
            $usuario = $resultado->fetch_assoc();
            
            // Verificar la contraseña
            if (password_verify($clave, $usuario['password'])) {
                // Guardar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                
                // Redirigir al inicio
                header("Location: index.php");
                exit;
            } else {
                $error = '❌ Contraseña incorrecta.';
            }
        } else {
            $error = '❌ No existe una cuenta con este email.';
        }
    }
}

// ============================================
// 2. AHORA SÍ, INCLUIMOS EL HEADER (HTML)
// ============================================
include('header.php');
?>

<!-- CONTENIDO ESPECÍFICO DE INICIAR SESIÓN -->
<div class="container" style="padding: 40px 0;">
    <h2 class="section-title">Iniciar Sesión</h2>
    <p>Aquí puedes iniciar sesión con tu cuenta.</p>

    <!-- Mostrar mensajes de error o éxito -->
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['registro']) && $_GET['registro'] == 'ok'): ?>
        <div class="alert alert-success">✅ Registro exitoso. ¡Ya puedes iniciar sesión!</div>
    <?php endif; ?>

    <div class="card mx-auto" style="max-width: 380px; margin-top:100px;">
        <div class="card-body">
            <h4 class="card-title mb-4">Iniciar Sesión</h4>
            
            <!-- FORMULARIO CON METHOD POST Y NOMBRES DE CAMPOS -->
            <form method="POST" action="">
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div> <!-- form-group// -->
                
                <div class="form-group">
                    <input type="password" class="form-control" name="clave" placeholder="Password" required>
                </div> <!-- form-group// -->
                
                <div class="form-group">
                    <a href="#" class="float-right">¿Olvidó su contraseña?</a>
                </div> <!-- form-group// -->
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Ingresar</button>
                </div> <!-- form-group// -->
            </form>
        </div> <!-- card-body.// -->
    </div> <!-- card .// -->

    <p class="text-center mt-4">¿No tienes cuenta? <a href="registrar.php">Regístrate</a></p>
</div>

<?php include('footer.php'); ?>