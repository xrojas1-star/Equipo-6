<?php
session_start();
if (!isset($_SESSION['user_id']) && empty($_SESSION['es_visitante'])) {
    header("Location: login.php");
    exit();
}
// Si tiene sesión real, limpiar flag visitante
if (!empty($_SESSION['user_id'])) { unset($_SESSION['es_visitante']); }
// Evita que el navegador guarde esta página en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Consejos</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/consejos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <a href="home.php" class="logo-link">
        <img src="assets/img/logovitalis.png" alt="Vitalis" class="sidebar-logo">
    </a>
    <nav>
        <ul>
            <li><a href="home.php">🏠 Inicio</a></li>
            <li><a href="blog.php">📝 Blog</a></li>
            <li class="active"><a href="consejos.php">🔍 Consejos</a></li>
            <li><a href="salud.php">💚 Salud</a></li>
            <li><a href="bienestar.php">❤️ Bienestar</a></li>
            <li class="nav-cursos"><a href="cursos.php">🎓 Cursos</a></li>
            <li class="nav-juegos"><a href="juegos_bienestar.php">🎮 Juegos Bienestar</a></li>
            <?php if (empty($_SESSION['es_visitante'])): ?>
            <li><a href="perfil.php">👤 Mi Perfil</a></li>
            <?php endif; ?>
            <li><a href="notificaciones.php">🔔 Notificaciones</a></li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="panel_de_administracion.php">⚙️ Panel Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<div class="page-body">
    <section class="main-panel">
        <div class="panel-header">
            <div class="title-group">
                <h2 class="section-title">Consejos y Bienestar <span class="date-badge">15 Oct 2024</span></h2>
                <p class="subtitle">Tu guía diaria para una vida saludable</p>
            </div>
        </div>

        <div class="main-cards">
            <div class="content-card">
                <div class="card-image nutrition">
                    <h3>Nutrición Inteligente: El Poder de los Superalimentos</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li><i class="fas fa-check"></i> 5 Superalimentos clave</li>
                        <li><i class="fas fa-check"></i> Planificación semanal</li>
                        <li><i class="fas fa-check"></i> Recetas sencillas</li>
                    </ul>
                    <button class="btn-main">Leer más</button>
                </div>
            </div>

            <div class="content-card">
                <div class="card-image mental">
                    <h3>Salud Mental: Micro-Pausas de Mindfulness</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li><i class="fas fa-check"></i> Técnicas de 5 minutos</li>
                        <li><i class="fas fa-check"></i> Regla 20-20-20 visual</li>
                        <li><i class="fas fa-check"></i> Manejo del estrés</li>
                    </ul>
                    <button class="btn-main">Ver video</button>
                </div>
            </div>

            <div class="content-card">
                <div class="card-image mobility">
                    <h3>Movilidad Activa: Mejora tu Rutina</h3>
                </div>
                <div class="card-body">
                    <ul>
                        <li><i class="fas fa-check"></i> Movilidad articular vs. Estiramiento</li>
                        <li><i class="fas fa-check"></i> Calentamiento pre-Yoga</li>
                        <li><i class="fas fa-check"></i> Ejercicios para oficina</li>
                    </ul>
                    <button class="btn-main">Empezar rutina</button>
                </div>
            </div>
        </div>

        <div class="bottom-grid">
            <div class="quick-tips">
                <h3>💡 Consejos Rápidos del Día</h3>
                <div class="tip-item">
                    <span>💧 Recuerda beber agua</span>
                    <input type="checkbox" checked>
                </div>
                <div class="tip-item">
                    <span>🚶 Camina 10 min tras comer</span>
                    <input type="checkbox">
                </div>
                <div class="tip-item">
                    <span>🧠 Tómate un descanso de pantalla</span>
                    <input type="checkbox">
                </div>
            </div>

            <div class="community-section">
                <h3>🤝 Comunidad: Comparte tus Consejos</h3>
                <div class="comment-input">
                    <input type="text" placeholder="Envía un consejo...">
                </div>
            </div>
        </div>
    </section>
</div>


<!-- Modal de confirmación de logout -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h2>¿Deseas cerrar sesión?</h2>
        <p>Se perderán los progresos no guardados en tus rutinas de salud actuales.</p>
        <div class="modal-btns">
            <button class="btn-confirmar" onclick="confirmarLogout()">Sí, Salir</button>
            <button class="btn-cancelar" onclick="cerrarModal()">No, Cancelar</button>
        </div>
    </div>
</div>

<script>
function abrirModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}
function cerrarModal() {
    document.getElementById('logoutModal').style.display = 'none';
}
function confirmarLogout() {
    window.location.href = '../controllers/LogoutController.php';
}
</script>

</body>
</html>