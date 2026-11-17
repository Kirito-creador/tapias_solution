    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-3">
                    <h6>Horarios</h6>
                    <p class="mb-1"><i class="bi bi-clock"></i> Lun a Dom</p>
                    <p>9:00 AM a 6:00 PM</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Oficina</h6>
                    <p class="mb-1"><i class="bi bi-geo-alt"></i> Cochabamba, Bolivia</p>
                    <a href="https://maps.app.goo.gl/jb2g7p2X9hqTL6o17" target="_blank"><i class="bi bi-map"></i> Ver en Google Maps</a>
                </div>
                <div class="col-md-4 mb-3">
                    <h6>Contacto</h6>
                    <div class="d-flex justify-content-center gap-3 mt-2">
                        <a href="https://wa.me/59169172830" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp fs-4"></i></a>
                        <a href="https://www.facebook.com/share/1HmqoGDtrb/" target="_blank" title="Facebook"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="https://www.instagram.com/tapiasolution?stkn=NXRxem8xNHoxazJ1" target="_blank" title="Instagram"><i class="bi bi-instagram fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr style="border-color: #5a4030;">
            <div class="text-center mt-3">
                <small>&copy; <?= date('Y') ?> - Tapia's Solution | Inmobiliaria en Cochabamba, Bolivia</small>
            </div>
        </div>
    </footer>

    <style>
        /* Esto asegura que el footer siempre esté abajo */
        footer {
            background-color: #3E2A1E;
            color: #F2EBDD;
            padding: 30px 0;
            margin-top: auto !important; 
        }
        footer a { color: #F2EBDD; text-decoration: none; }
        footer h6 { font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
    </style>

    <script>
        // ESTE SCRIPT HACE LA MAGIA EN TODAS LAS PÁGINAS AUTOMÁTICAMENTE
        document.addEventListener("DOMContentLoaded", function() {
            // 1. Convertimos el body en un contenedor Flex de altura completa
            document.body.style.display = "flex";
            document.body.style.flexDirection = "column";
            document.body.style.minHeight = "100vh";

            // 2. Buscamos los contenedores principales y les decimos que ocupen el espacio libre
            let contenedores = document.querySelectorAll(".container, .container-fluid");
            contenedores.forEach(function(div) {
                // Evitamos aplicarlo al contenedor que está dentro del navbar o del footer
                if (!div.closest('nav') && !div.closest('footer')) {
                    div.style.flexGrow = "1";
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>