<?php
// ============================================
// 1. INICIAR SESIÓN Y CONEXIÓN (PRIMERO)
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('conexion.php');

// Variable para mensajes
$error  = '';
$nombre_val = '';
$email_val  = '';

// ============================================
// 2. PROCESAR EL FORMULARIO (LÓGICA PHP)
// ============================================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre          = trim($_POST['nombre'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $clave           = $_POST['clave'] ?? '';
    $confirmar_clave = $_POST['confirmar_clave'] ?? '';

    $nombre_val = $nombre;
    $email_val  = $email;

    if (empty($nombre) || empty($email) || empty($clave)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ingresa un correo electrónico válido.';
    } elseif (strlen($clave) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($clave !== $confirmar_clave) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $email_check   = mysqli_real_escape_string($conexion, $email);
        $sql_check     = "SELECT id FROM usuarios WHERE email = '$email_check'";
        $resultado_check = $conexion->query($sql_check);

        if ($resultado_check->num_rows > 0) {
            $error = 'Este correo electrónico ya está registrado.';
        } else {
            $clave_hash    = password_hash($clave, PASSWORD_DEFAULT);
            $nombre_seguro = mysqli_real_escape_string($conexion, $nombre);

            $sql = "INSERT INTO usuarios (nombre, email, password)
                    VALUES ('$nombre_seguro', '$email_check', '$clave_hash')";

            if ($conexion->query($sql)) {
                header("Location: iniciar-sesion.php?registro=ok");
                exit;
            } else {
                $error = 'Error al registrar: ' . $conexion->error;
            }
        }
    }
}
?>
<?php include('header.php'); ?>

<!-- PÁGINA DE REGISTRO -->
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
                            <h4 class="font-weight-bold text-dark mb-1">Crear una cuenta</h4>
                            <p class="text-muted small mb-0">Regístrate para gestionar tus compras y pedidos</p>
                        </div>

                        <!-- Mensajes de error -->
                        <?php if ($error): ?>
                            <div class="alert alert-danger text-center small mb-4" role="alert" style="border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle-fill mr-1"></i> <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <!-- Formulario POST -->
                        <form method="POST" action="" id="registerForm" novalidate>

                            <!-- Campo: Nombre completo -->
                            <div class="form-group mb-3">
                                <label for="nombre" class="font-weight-bold small text-dark mb-1">Nombre completo</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted">
                                            <i class="bi bi-person"></i>
                                        </span>
                                    </div>
                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        class="form-control border-left-0 pl-0"
                                        placeholder="Tu nombre completo"
                                        autocomplete="name"
                                        required
                                        value="<?= htmlspecialchars($nombre_val) ?>"
                                    >
                                </div>
                            </div>

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
                                <small class="form-text text-muted" style="font-size: 0.78rem;">Nunca compartiremos tu correo con nadie más.</small>
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
                                        placeholder="Mínimo 6 caracteres"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light border border-left-0 text-muted px-3" id="togglePasswordBtn" title="Mostrar contraseña">
                                            <i class="bi bi-eye" id="toggleIcon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Campo: Repetir contraseña -->
                            <div class="form-group mb-4">
                                <label for="confirmar_clave" class="font-weight-bold small text-dark mb-1">Repetir contraseña</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-light border-right-0 text-muted">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                    </div>
                                    <input
                                        type="password"
                                        id="confirmar_clave"
                                        name="confirmar_clave"
                                        class="form-control border-left-0 border-right-0 px-0"
                                        placeholder="Repite la contraseña"
                                        autocomplete="new-password"
                                        required
                                    >
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-light border border-left-0 text-muted px-3" id="toggleConfirmBtn" title="Mostrar contraseña">
                                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Registrar -->
                            <button type="submit" id="btnRegisterSubmit" class="btn btn-primary btn-block font-weight-bold py-2 shadow-sm" style="border-radius: 8px; font-size: 1rem;">
                                <span id="btnSubmitText">Crear cuenta</span>
                            </button>

                        </form>

                        <hr class="my-4">

                        <!-- Volver a Login -->
                        <div class="text-center">
                            <p class="small text-muted mb-2">¿Ya tienes una cuenta?</p>
                            <a href="iniciar-sesion.php" class="btn btn-outline-primary btn-block font-weight-bold py-2" style="border-radius: 8px;">
                                Iniciar sesión
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<!-- SCRIPT JS INTERACTIVO -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle: Mostrar / Ocultar contraseña
    function setupToggle(btnId, inputId, iconId) {
        const btn   = document.getElementById(btnId);
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (!btn || !input || !icon) return;
        btn.addEventListener('click', function() {
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
            btn.setAttribute('title', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
        });
    }

    setupToggle('togglePasswordBtn',  'clave',           'toggleIcon');
    setupToggle('toggleConfirmBtn',   'confirmar_clave',  'toggleIconConfirm');

    // Estado Loading al enviar
    const form      = document.getElementById('registerForm');
    const btnSubmit = document.getElementById('btnRegisterSubmit');
    const btnText   = document.getElementById('btnSubmitText');

    if (form && btnSubmit && btnText) {
        form.addEventListener('submit', function() {
            const nombre  = document.getElementById('nombre').value.trim();
            const email   = document.getElementById('email').value.trim();
            const clave   = document.getElementById('clave').value;
            if (nombre !== '' && email !== '' && clave !== '') {
                btnSubmit.disabled = true;
                btnText.innerHTML  = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> Creando cuenta...';
            }
        });
    }
});
</script>

<?php include('footer.php'); ?>