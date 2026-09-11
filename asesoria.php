<?php

include 'header.php';
?>

<main class="container my-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <h1 class="text-center">Asesoría Gratuita</h1>
            <p class="lead text-center">Te ayudamos a elegir los mejores productos para ti.</p>
            <hr>
            <?php if (isset($_GET['enviado'])): ?>
            <div class="alert alert-success text-center">¡Solicitud enviada! Te contactaremos pronto.</div>
            <?php endif; ?>

            <form action="enviar-asesoria.php" method="POST">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre completo</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono (opcional)</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono">
                </div>
                <div class="mb-3">
                    <label for="consulta" class="form-label">¿En qué necesitas asesoría?</label>
                    <textarea class="form-control" id="consulta" name="consulta" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Enviar solicitud</button>
            </form>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>