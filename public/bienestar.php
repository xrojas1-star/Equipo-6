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
    <title>Vitalis | Bienestar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/bienestar.css">
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
            <li><a href="consejos.php">🔍 Consejos</a></li>
            <li><a href="salud.php">💚 Salud</a></li>
            <li class="active"><a href="bienestar.php">❤️ Bienestar</a></li>
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
    <header>
        <h2 class="section-title">Tu Panel de Bienestar</h2>
        <p class="panel-subtitle">Tu guía para una vida en equilibrio</p>
    </header>

    <div class="cards-container">
        <div class="info-card">
            <div class="card-header wellness-header">
                <h3>🧘 Bienestar Holístico: El Arte del Balance</h3>
            </div>
            <div class="card-content">
                <ul>
                    <li><span class="bullet-check">✔</span> Técnicas de equilibrio mente-cuerpo</li>
                    <li><span class="bullet-check">✔</span> Prácticas de auto-cuidado diario</li>
                    <li><span class="bullet-check">✔</span> Rituales de noche para un buen sueño</li>
                </ul>
                <button class="btn-primary">Explorar Prácticas</button>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header mental-header">
                <h3>🌿 Salud Mental & Resiliencia</h3>
            </div>
            <div class="card-content">
                <ul>
                    <li><span class="bullet-check">✔</span> Ejercicios de gratitud</li>
                    <li><span class="bullet-check">✔</span> Manejo de la ansiedad y estrés</li>
                    <li><span class="bullet-check">✔</span> Construyendo relaciones positivas</li>
                </ul>
                <button class="btn-primary">Aprender Más</button>
            </div>
        </div>

        <div class="info-card">
            <div class="card-header environment-header">
                <h3>🏠 Entorno & Bienestar Ambiental</h3>
            </div>
            <div class="card-content">
                <ul>
                    <li><span class="bullet-check">✔</span> Organización para la calma</li>
                    <li><span class="bullet-check">✔</span> Ergonomía y micro-descansos</li>
                    <li><span class="bullet-check">✔</span> Plantas para purificar el aire</li>
                </ul>
                <button class="btn-primary">Crear tu Espacio</button>
            </div>
        </div>
    </div>

    <div class="bottom-grid">
        <div class="bottom-card">
            <h3>💡 Micro-Pasos de Bienestar</h3>
            <div class="step-item">
                <label><span>💧</span> 5 min de respiración profunda</label>
                <input type="checkbox" checked>
            </div>
            <div class="step-item">
                <label><span>🚶</span> Sal a la luz del día</label>
                <input type="checkbox">
            </div>
        </div>

        <div class="bottom-card">
            <h3>🤝 Comunidad</h3>
            <textarea placeholder="Comparte tu progreso..."></textarea>
            <button class="btn-send">Enviar</button>
        </div>
    </div>
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