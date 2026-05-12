<?php
// 1. Iniciamos la sesión para saber quién entró
session_start();

// 2. Protegemos la página: si no hay un usuario logueado, lo pateamos al login
if (!isset($_SESSION['user_id']) && empty($_SESSION['es_visitante'])) {
    header("Location: login.php");
    exit();
}
// Si tiene sesión real, limpiar flag visitante
if (!empty($_SESSION['user_id'])) { unset($_SESSION['es_visitante']); }

// 3. Evita que el navegador guarde esta página en caché
//    Así el botón "atrás" no puede regresar aquí sin verificar sesión
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Inicio</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <header class="navbar">
        <div class="logo">
            <img src="assets/img/logovitalis.png" alt="Vitalis Logo">
        </div>
        <nav>
            <ul>
                <li><a href="#" class="active">Inicio</a></li>
                <li><a href="blog.php">Blog</a></li>
                <li><a href="tipsalud.php">Consejos de Salud</a></li>
                <li><a href="bienestar.php">Bienestar</a></li>
            </ul>
        </nav>
        <div style="display:flex; align-items:center; gap:10px;">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="panel_de_administracion.php" class="btn-admin-nav">⚙️ Panel Admin</a>
            <?php endif; ?>
            <?php if (!empty($_SESSION['es_visitante'])): ?>
            <a href="login.php" class="btn-nav" style="background:transparent;border:2px solid #147a96;color:#147a96;">Iniciar sesión</a>
            <a href="registro.php" class="btn-nav">Registrarse</a>
            <?php else: ?>
            <a href="#" class="btn-nav" onclick="abrirModal(); return false;">Cerrar sesión</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
            <p>Descubre consejos, artículos y recursos para mejorar tu bienestar y llevar una vida más saludable.</p>
            <a href="#" class="btn-primary">Comienza tu camino saludable</a>
        </div>
    </section>

    <main class="container">
        <div class="section-title">
            <h2>Últimos artículos</h2>
        </div>

        <div class="articles-grid" id="articlesGrid">
            <article class="card">
                <div class="card-image">
                    <img src="assets/img/nutricion.png" alt="Nutrición">
                    <span class="badge badge-nutricion">Nutrición</span>
                </div>
                <div class="card-body">
                    <h3>Los mejores alimentos para tener energía todo el día</h3>
                    <p>Descubre qué alimentos que ayudan a aumentar tu energía y mejorar tu salud.</p>
                    <span class="date">24 abril, 2024</span>
                </div>
            </article>

            <article class="card">
                <div class="card-image">
                    <img src="assets/img/ejercicio.png" alt="Ejercicio">
                    <span class="badge badge-ejercicio">Ejercicio</span>
                </div>
                <div class="card-body">
                    <h3>5 Ejercicios para Fortalecer el Core</h3>
                    <p>Entrena tu core con estos ejercicios efectivos y esenciales que puedes hacer en casa.</p>
                    <span class="date">24 abril, 2024</span>
                </div>
            </article>

            <article class="card">
                <div class="card-image">
                    <img src="assets/img/meditar.png" alt="Bienestar">
                    <span class="badge badge-bienestar">Bienestar</span>
                </div>
                <div class="card-body">
                    <h3>Mindfulness: Guía para Principiantes</h3>
                    <p>Aprende las bases del mindfulness y cómo meditar para mejorar tu salud mental.</p>
                    <span class="date">24 abril, 2024</span>
                </div>
            </article>

            <!-- Artículos extra (ocultos por defecto) -->
            <article class="card extra-content" style="display:none;">
                <div class="card-image">
                    <img src="assets/img/nutricion.png" alt="Nutrición">
                    <span class="badge badge-nutricion">Nutrición</span>
                </div>
                <div class="card-body">
                    <h3>Receta de Tostada de Aguacate Perfecta</h3>
                    <p>Pan integral, huevo y especias para una energía sostenible todo el día.</p>
                    <span class="date">20 abril, 2024</span>
                </div>
            </article>

            <article class="card extra-content" style="display:none;">
                <div class="card-image">
                    <img src="assets/img/ejercicio.png" alt="Ejercicio">
                    <span class="badge badge-ejercicio">Ejercicio</span>
                </div>
                <div class="card-body">
                    <h3>Introducción al Tai Chi para Todos</h3>
                    <p>Historia, beneficios y posturas básicas para conectar mente y cuerpo.</p>
                    <span class="date">18 abril, 2024</span>
                </div>
            </article>

            <article class="card extra-content" style="display:none;">
                <div class="card-image">
                    <img src="assets/img/meditar.png" alt="Bienestar">
                    <span class="badge badge-bienestar">Bienestar</span>
                </div>
                <div class="card-body">
                    <h3>El Arte del Descanso y la Recuperación</h3>
                    <p>Técnicas de relajación y la importancia del sueño para tu salud.</p>
                    <span class="date">15 abril, 2024</span>
                </div>
            </article>
        </div>

        <div class="view-more">
            <button id="btn-show-more" class="btn-secondary" onclick="mostrarMas()">Ver más artículos</button>
            <button id="btn-show-less" class="btn-secondary" style="display:none;" onclick="mostrarMenos()">Mostrar menos</button>
        </div>
    </main>


    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="assets/img/logovitalis.png" alt="Vitalis Logo" class="footer-logo">
                <p>Promoviendo un estilo de vida saludable alineado con el <strong>ODS 3</strong> de las Naciones Unidas.</p>
                <div class="social-icons">
                    <a href="https://www.tiktok.com/@vitalis_salud1" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.instagram.com/vitalis_salud.1" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>

            <div class="footer-section">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="home.php">Inicio</a></li>
                    <li><a href="blog.php">Nuestro Blog</a></li>
                    <li><a href="tipsalud.php">Tips de Salud</a></li>
                    <li><a href="bienestar.php">Bienestar</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Comunidad</h3>
                <ul>
                    <?php if (empty($_SESSION['es_visitante'])): ?>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="#" id="openPrivacidad">Privacidad</a></li>
                    <li><a href="#" id="openTerminos">Términos de uso</a></li>
                    <li><a href="#" id="openContacto">Contacto</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3>Nuestro Compromiso</h3>
                <p>Facilitamos el acceso a información preventiva de calidad.</p>
                <div class="ods-tag">
                    <i class="fas fa-heartbeat"></i>
                    <span>Objetivo ODS 3</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 <strong>VITALIS</strong>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <!-- Modal Logout -->
    <div id="logoutModal" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <div style="font-size:32px; margin-bottom:12px;">🚪</div>
            <h2>¿Deseas cerrar sesión?</h2>
            <p>Se perderán los progresos no guardados en tus rutinas de salud.</p>
            <div class="modal-logout-btns">
                <button onclick="confirmarLogout()" class="btn-logout-si">Sí, Salir</button>
                <button onclick="cerrarModal()" class="btn-logout-no">No, Cancelar</button>
            </div>
        </div>
    </div>

    <!-- Modales de footer -->
    <div id="modalTerminos" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Términos y Condiciones</h2>
            <p style="text-align:left; font-size:13px; color:#555; line-height:1.6;">
                <strong>Vitalis</strong> es una plataforma informativa y educativa. NO proporciona diagnósticos médicos.<br><br>
                <strong>Uso Permitido:</strong> El usuario se compromete a utilizar la página de manera ética y legal.<br><br>
                <strong>Propiedad Intelectual:</strong> Todo el diseño, logotipos y código fuente son propiedad de VITALIS.
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalTerminos')" class="btn-logout-si">Aceptar</button>
            </div>
        </div>
    </div>

    <div id="modalContacto" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Contacto</h2>
            <p style="font-size:13px; color:#555; line-height:1.6;">
                ¿Tienes dudas o sugerencias? Escríbenos:<br><br>
                <strong style="font-size:16px; color:#147a96;">saludybienestarvitalis@gmail.com</strong>
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalContacto')" class="btn-logout-si">Cerrar</button>
            </div>
        </div>
    </div>

    <div id="modalPrivacidad" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Política de Privacidad</h2>
            <p style="text-align:left; font-size:13px; color:#555; line-height:1.6;">
                En <strong>VITALIS</strong>, tu privacidad es nuestra prioridad.<br><br>
                Solo recolectamos información básica para mejorar tu experiencia.<br><br>
                Implementamos medidas de seguridad para proteger tu información.
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalPrivacidad')" class="btn-logout-si">Aceptar</button>
            </div>
        </div>
    </div>

    <style>
    .modal-logout-overlay {
        position: fixed; top:0; left:0;
        width:100%; height:100%;
        background: rgba(0,0,0,0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .modal-logout-card {
        background: white;
        padding: 35px;
        border-radius: 20px;
        text-align: center;
        max-width: 450px;
        width: 90%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .modal-logout-card h2 { font-size: 20px; color: #004d40; margin-bottom: 10px; }
    .modal-logout-card p  { font-size: 14px; color: #666; margin-bottom: 5px; line-height:1.5; }
    .modal-logout-btns    { display:flex; gap:12px; justify-content:center; margin-top:20px; }
    .btn-logout-si  { background:#00796b; color:white; border:none; padding:12px 25px; border-radius:10px; font-weight:bold; cursor:pointer; }
    .btn-logout-si:hover { background:#004d40; }
    .btn-logout-no  { background:#e0f2f1; color:#00796b; border:2px solid #00796b; padding:12px 25px; border-radius:10px; font-weight:bold; cursor:pointer; }
    .btn-logout-no:hover { background:#b2dfdb; }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
    // Ver más / menos artículos
    function mostrarMas() {
        document.querySelectorAll('.extra-content').forEach(c => c.style.display = 'block');
        document.getElementById('btn-show-more').style.display = 'none';
        document.getElementById('btn-show-less').style.display = 'inline-block';
    }
    function mostrarMenos() {
        document.querySelectorAll('.extra-content').forEach(c => c.style.display = 'none');
        document.getElementById('btn-show-less').style.display = 'none';
        document.getElementById('btn-show-more').style.display = 'inline-block';
        document.querySelector('.section-title').scrollIntoView({behavior:'smooth'});
    }
    // Modal logout
    function abrirModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }
    function cerrarModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    function confirmarLogout() {
        window.location.href = '../controllers/LogoutController.php';
    }
    // Modales footer
    function cerrarModalFooter(id) {
        document.getElementById(id).style.display = 'none';
    }
    document.getElementById('openTerminos').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalTerminos').style.display='flex';
    });
    document.getElementById('openContacto').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalContacto').style.display='flex';
    });
    document.getElementById('openPrivacidad').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalPrivacidad').style.display='flex';
    });
    </script>

</body>
</html>