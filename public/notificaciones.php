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
    <title>Vitalis | Notificaciones</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/notificaciones.css">
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
            <li><a href="consejos.php">🔍 Consejos</a></li>
            <li><a href="salud.php">💚 Salud</a></li>
            <li><a href="bienestar.php">❤️ Bienestar</a></li>
            <li class="nav-cursos"><a href="cursos.php">🎓 Cursos</a></li>
            <li class="nav-juegos"><a href="juegos_bienestar.php">🎮 Juegos Bienestar</a></li>
            <?php if (empty($_SESSION['es_visitante'])): ?>
            <li><a href="perfil.php">👤 Mi Perfil</a></li>
            <?php endif; ?>
            <li class="active"><a href="notificaciones.php">🔔 Notificaciones</a></li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li><a href="panel_de_administracion.php">⚙️ Panel Admin</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</aside>

<div class="page-body">
    <section class="main-panel">
        <h2 class="section-title">Panel de Notificaciones:
            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
        </h2>

        <div class="priority-alerts">
            <p class="subtitle">Alertas de Salud Prioritarias</p>
            <div class="cards-container">
                <div class="card water">
                    <i class="fas fa-tint"></i>
                    <p><strong>Alerta de Hidratación:</strong> ¡Es hora de beber agua! (11:00)</p>
                </div>
                <div class="card keto">
                    <i class="fas fa-utensils"></i>
                    <p><strong>Plan de Nutrición Keto:</strong> Recordatorio para tu cena.</p>
                </div>
                <div class="card stats">
                    <i class="fas fa-chart-line"></i>
                    <p><strong>Evaluación Mensual:</strong> Tu informe está listo.</p>
                </div>
            </div>
        </div>

        <div class="notifications-table">
            <table>
                <thead>
                    <tr>
                        <th>Tipo de Notificación</th>
                        <th>Detalles</th>
                        <th>Fecha/Hora</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>⏰ Recordatorio Diario</td>
                        <td>Rutina Yoga Mañana: ¡A punto de empezar!</td>
                        <td>hace 15 min</td>
                        <td><span class="status unread">No Leída</span></td>
                        <td><button class="action-btn">...</button></td>
                    </tr>
                    <tr>
                        <td>💚 Salud</td>
                        <td>Evaluación de Bienestar Corporal: Nuevos resultados</td>
                        <td>hace 2 horas</td>
                        <td><span class="status urgent">No Leída (Urgente)</span></td>
                        <td><button class="action-btn">...</button></td>
                    </tr>
                    <tr class="read-row">
                        <td>📈 Progresión</td>
                        <td>Plan de Nutrición Keto: Semana 4 completada.</td>
                        <td>hace 22 horas</td>
                        <td><span class="status read">Leída</span></td>
                        <td><button class="action-btn">...</button></td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination">
                <i class="fas fa-chevron-left"></i> 24 abr 2024 <i class="fas fa-chevron-right"></i>
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