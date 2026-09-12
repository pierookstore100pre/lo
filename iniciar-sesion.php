<?php
include_once('conexion.php');
include('header.php');
?>

<?php
// ============================================
// 1. PROCESAR EL FORMULARIO (ANTES DE CUALQUIER HTML)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluir conexión
include('conexion.php');

// Si el usuario ya está logueado, redirigir al inicio
if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$email_val = '';

// Procesar el formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $email_val = $email;

    if (empty($email) || empty($clave)) {
        $error = 'Por favor ingresa tu correo electrónico y contraseña.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un correo electrónico válido.';
    } else {
        // ---- Buscar usuario con consulta preparada ----
        $stmt = $conexion->prepare(
            "SELECT id, nombre, email, password FROM usuarios WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado && $resultado->num_rows === 1) {
            $usuario = $resultado->fetch_assoc();
            $stmt->close();

            // Verificar la contraseña cifrada
            if (password_verify($clave, $usuario['password'])) {
                // Regenerar el ID de sesión para prevenir session fixation
                session_regenerate_id(true);

                // Guardar variables de sesión
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email']  = $usuario['email'];

                // Redirigir al inicio
                header("Location: index.php");
                exit;
            } else {
                $error = 'El correo electrónico o la contraseña son incorrectos.';
            }
        } else {
            $stmt->close();
            $error = 'El correo electrónico o la contraseña son incorrectos.';
        }
    }
}
?>

<!-- PÁGINA DE INICIO DE SESIÓN SENCILLA Y PROFESIONAL -->
<main class="login-wrapper py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">

                <div class="card login-card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <!-- Logo e Identidad -->
                        <div class="text-center mb-4">
                            <a href="index.php" class="d-inline-block mb-3">
                                <img src="./img/logo-clean.png" alt="La Compu de Lolo" style="height: 48px; width: auto; object-fit: contain;">
                            </a>
                            <h4 class="font-weight-bold text-dark mb-1">Iniciar Sesión</h4>
                            <p class="text-muted small mb-0">Ingresa a tu cuenta para gestionar tus compras</p>
                        </div>

                        <!-- Mensajes de alerta del servidor -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center small mb-4" role="alert" style="border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['registro']) && $_GET['registro'] == 'ok'): ?>
                            <div class="alert alert-success text-center small mb-4" role="alert" style="border-radius: 8px;">
                                <i class="bi bi-check-circle-fill mr-1"></i> ¡Registro exitoso! Ya puedes iniciar sesión con tu cuenta.
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_GET['mensaje'])): ?>
                            <div class="alert alert-info text-center small mb-4" role="alert" style="border-radius: 8px;">
                                <i class="bi bi-info-circle-fill mr-1"></i> <?= htmlspecialchars($_GET['mensaje']) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario POST -->
                        <form method="POST" action="" id="loginForm" novalidate>
                            
                            <!-- Campo: Correo electrónico -->
                            <div class="form-group mb-3">
                                <label for="email" class="font-weight-bold small text-dark mb-1">Correo electrónico</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                    </div>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        class="form-control border-left-0 pl-0" 
                                        placeholder="tu@correo.com" 
                                        autocomplete="email"
                                        required
                                        value="<?= htmlspecialchars($email_val) ?>"
                                    >
                                </div>
                            </div>

                            <!-- Campo: Contraseña -->
                            <div class="form-group mb-3">
                                <label for="clave" class="font-weight-bold small text-dark mb-1">Contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                    </div>
                                    <input 
                                        type="password" 
                                        id="clave" 
                                        name="clave" 
                                        class="form-control border-left-0 border-right-0 px-0" 
                                        placeholder="Ingresa tu contraseña" 
                                        autocomplete="current-password"
                                        required
                                    >
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light border border-left-0 text-muted px-3" id="togglePasswordBtn" title="Mostrar contraseña">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Opciones: Recordarme & Olvidé contraseña -->
                            <div class="d-flex align-items-center justify-content-between mb-4 small">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                                    <label class="custom-control-label text-secondary" for="remember">Recordarme</label>
                                </div>
                                <a href="#" onclick="alert('Para restablecer tu contraseña, por favor contáctanos a través de Asesoría Gratuita.'); return false;" class="text-primary font-weight-bold">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <!-- Botón Ingresar -->
                            <button type="submit" id="btnLoginSubmit" class="btn btn-primary btn-block font-weight-bold py-2.5 shadow-sm" style="border-radius: 8px; font-size: 1rem;">
                                <span id="btnSubmitText">Ingresar</span>
                            </button>

                        </form>

                        <hr class="my-4">

                        <!-- Registro -->
                        <div class="text-center">
                            <p class="small text-muted mb-2">¿Aún no tienes una cuenta?</p>
                            <a href="registrar.php" class="btn btn-outline-primary btn-block font-weight-bold py-2" style="border-radius: 8px;">
                                Crear una cuenta
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- SCRIPT JS INTERACTIVO DE LOGIN -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar / Ocultar contraseña
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('clave');
    const toggleIcon = document.getElementById('toggleIcon');

    if (toggleBtn && passwordInput && toggleIcon) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            toggleBtn.setAttribute('title', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    // Estado Loading al enviar
    const loginForm = document.getElementById('loginForm');
    const btnSubmit = document.getElementById('btnLoginSubmit');
    const btnText = document.getElementById('btnSubmitText');

    if (loginForm && btnSubmit && btnText) {
        loginForm.addEventListener('submit', function() {
            const email = document.getElementById('email').value.trim();
            const clave = document.getElementById('clave').value;
            if (email !== '' && clave !== '') {
                btnSubmit.disabled = true;
                btnText.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Ingresando...';
            }
        });
    }
});
</script>

<?php include('footer.php'); ?>