<!-- ========================= FOOTER PRINCIPAL ========================= -->
<footer class="main-footer">
    <div class="container">
        <div class="row justify-content-between">
            <!-- Marca y Descripción (5 Columnas) -->
            <div class="col-lg-3 col-md-12 mb-4 mb-lg-0">
                <a href="index.php" class="d-inline-block mb-3">
                    <img src="./img/logo-clean.png" alt="La Compu de Lolo" style="height: 44px; width: auto; object-fit: contain;">
                </a>
                <p class="text-secondary mb-3" style="max-width: 260px; line-height: 1.6;">
                    Tecnología, componentes de cómputo y asesoría técnica especializada en el Perú desde 2013.
                </p>
                <div class="d-flex align-items-center">
                    <a href="https://facebook.com" target="_blank" class="footer-social-btn" aria-label="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://instagram.com" target="_blank" class="footer-social-btn" aria-label="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://api.whatsapp.com/send?phone=51925000899" target="_blank" class="footer-social-btn" aria-label="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                </div>
            </div>

            <!-- Navegación (2 Columnas) -->
            <div class="col-lg-2 col-md-4 col-6 mb-4 mb-lg-0">
                <h6 class="footer-title">Navegación</h6>
                <ul class="footer-links">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="tienda.php">Tienda</a></li>
                    <li><a href="tienda.php">Categorías</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                </ul>
            </div>

            <!-- Ayuda y Atención (2 Columnas) -->
            <div class="col-lg-2 col-md-4 col-6 mb-4 mb-md-0">
                <h6 class="footer-title">Ayuda</h6>
                <ul class="footer-links">
                    <li><a href="nosotros.php">Garantía oficial</a></li>
                    <li><a href="checkout.php">Formas de pago</a></li>
                    <li><a href="asesoria.php">Asesoría técnica</a></li>
                    <li><a href="iniciar-sesion.php">Mi Cuenta</a></li>
                </ul>
            </div>

            <!-- Contacto y Ubicación (3 Columnas) -->
            <div class="col-lg-3 col-md-4 col-12">
                <h6 class="footer-title">Contacto</h6>
                
                <!-- Minimapa de Google Maps de la Ubicación del Negocio -->
                <div class="footer-minimap-wrap mb-3" style="border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,0.15); box-shadow: 0 4px 12px rgba(0,0,0,0.25); position: relative;">
                    <a href="https://maps.app.goo.gl/bshLAzWm4DNLdv3L9" target="_blank" rel="noopener" title="Abrir ubicación de La Compu de Lolo en Google Maps" style="display: block; position: relative;">
                        <iframe 
                            src="https://maps.google.com/maps?q=La+Compu+de+Lolo&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                            width="100%" 
                            height="125" 
                            style="border:0; display:block; pointer-events: none;" 
                            allowfullscreen="" 
                            loading="lazy"
                            aria-label="Ubicación de La Compu de Lolo en Google Maps">
                        </iframe>
                        <div style="position: absolute; bottom: 6px; right: 6px; background: rgba(15, 23, 42, 0.85); color: #ffffff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; backdrop-filter: blur(4px);">
                            <i class="bi bi-geo-alt-fill text-primary mr-1"></i> Abrir en Maps
                        </div>
                    </a>
                </div>

                <ul class="footer-links mb-3">
                    <li>
                        <i class="bi bi-whatsapp text-success mr-1"></i>
                        <a href="https://api.whatsapp.com/send?phone=51925000899" target="_blank">+51 925 000 899</a>
                    </li>
                    <li>
                        <i class="bi bi-envelope text-primary mr-1"></i>
                        <a href="mailto:ventas@lacompudelolo.com">ventas@lacompudelolo.com</a>
                    </li>
                    <li class="small text-muted mt-2">
                        <i class="bi bi-clock mr-1"></i> Lun a Vie: 9:00 am - 8:00 pm
                    </li>
                </ul>

                <!-- Medios de Pago Específicos: Plin, Efectivo, Tarjeta -->
                <div>
                    <h6 class="footer-title mb-2" style="font-size: 0.8rem;">Medios de Pago</h6>
                    <div class="d-flex flex-wrap">
                        <span class="payment-badge" title="Plin">Plin</span>
                        <span class="payment-badge" title="Efectivo">Efectivo</span>
                        <span class="payment-badge" title="Tarjeta">Tarjeta</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barra Legal Inferior con Año Dinámico -->
        <div class="legal-bar d-flex flex-column flex-sm-row justify-content-between align-items-center text-center text-sm-left">
            <p class="mb-2 mb-sm-0">&copy; <?= date('Y') ?> La Compu de Lolo. Todos los derechos reservados.</p>
            <p class="mb-0 small text-muted">Tecnología y Asesoría Especializada desde 2013</p>
        </div>
    </div>
</footer>

</body>
</html>