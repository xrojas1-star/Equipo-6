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
    <title>Vitalis | Salud</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/salud.css">
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
            <li class="active"><a href="salud.php">💚 Salud</a></li>
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
    <section class="health-panel">
        <div class="panel-header-main">
            <h2 class="section-title">Tu Panel de Salud</h2>
            <p class="panel-subtitle">Seguimiento integral de tus métricas y citas de salud</p>
        </div>

        <div class="cards-container">
            <div class="info-card">
                <div class="card-header metrics-header">
                    <h3>❤️ Mis Métricas de Salud</h3>
                </div>
                <div class="card-content">
                    <ul>
                        <li>✔️ Último chequeo: Sept 2024</li>
                        <li>✔️ Tendencias de 30 días</li>
                        <li>✔️ Ver detalles completos</li>
                    </ul>
                    <button class="btn-primary">Ver Detalles</button>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header appointments-header">
                    <h3>📅 Historial de Citas Médicas</h3>
                </div>
                <div class="card-content">
                    <ul>
                        <li>✔️ Cita: Dr. Ruiz (Gastro)</li>
                        <li>✔️ Resultados analítica: Listos</li>
                        <li>✔️ Agendar nueva cita</li>
                    </ul>
                    <button class="btn-primary">Ver Historial</button>
                </div>
            </div>

            <div class="info-card">
                <div class="card-header medication-header">
                    <h3>💊 Recordatorios de Medicación</h3>
                </div>
                <div class="card-content">
                    <ul>
                        <li>✔️ Suplemento Vit. D: 9am</li>
                        <li>✔️ Magnesio: 8pm</li>
                        <li>✔️ Próxima renovación de receta</li>
                    </ul>
                    <button class="btn-primary">Gestionar Medicación</button>
                </div>
            </div>
        </div>

        <div class="bottom-card">
            <h3>🤝 Comunidad</h3>
            <div class="community-input">
                <input type="text" placeholder="Envía un consejo...">
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