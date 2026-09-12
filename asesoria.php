<?php
// ============================================
// ASESORÍA GRATUITA - Formulario de contacto
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('header.php');
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center">Asesoría Gratuita</h1>
            <p class="lead text-center">Te ayudamos a elegir los mejores productos para ti.</p>
            <hr>

            <?php if (isset($_GET['enviado']) && $_GET['enviado'] === '1'): ?>
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle-fill mr-1"></i> ¡Solicitud enviada! Te contactaremos pronto.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle-fill mr-1"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="enviar-asesoria.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre"
                           maxlength="100" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email"
                           maxlength="150" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono (opcional)</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono"
                           maxlength="20">
                </div>
                <div class="mb-3">
                    <label for="consulta" class="form-label">¿En qué necesitas asesoría?</label>
                    <textarea class="form-control" id="consulta" name="consulta"
                              rows="4" maxlength="1000" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send mr-1"></i> Enviar solicitud
                </button>
            </form>
        </div>
    </div>
</main>

<?php include('footer.php'); ?>
