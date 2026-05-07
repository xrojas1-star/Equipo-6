<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
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
    <title>Vitalis | Mi Perfil</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<aside class="sidebar">
    <a href="home.php" class="logo">VITALIS</a>
    <nav>
        <ul>
            <li><a href="home.php">🏠 Inicio</a></li>
            <li><a href="blog.php">📝 Blog</a></li>
            <li><a href="consejos.php">🔍 Consejos</a></li>
            <li><a href="salud.php">💚 Salud</a></li>
            <li><a href="bienestar.php">❤️ Bienestar</a></li>
            <li class="active"><a href="perfil.php">👤 Mi Perfil</a></li>
            <li><a href="notificaciones.php">🔔 Notificaciones</a></li>
        </ul>
    </nav>
</aside>

<div class="page-body">
    <section class="profile-section">
        <div class="profile-card-header">
            <div class="profile-info-main">
                <div class="profile-photo-container">
                    <img src="assets/img/perfil-sin-foto.png" alt="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" class="profile-photo">
                    <div class="online-status"></div>
                </div>
                <div class="profile-text">
                    <h1><?php echo htmlspecialchars($_SESSION['user_name']); ?></h1>
                    <p class="specialty">Miembro de Vitalis</p>
                    <div class="stats-row">
                        <span>Programas: <strong>8</strong></span>
                        <span>Puntos: <strong>1250</strong></span>
                        <span>Nivel: <strong>Avanzado</strong></span>
                    </div>
                </div>
            </div>
            <div class="profile-actions">
                <button class="btn-edit"><i class="fas fa-pencil-alt"></i> Editar Perfil</button>
                <button class="btn-planes"><i class="fas fa-folder"></i> Ver Mis Planes</button>
                <button class="btn-support"><i class="fas fa-envelope"></i> Contactar Soporte</button>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="grid-card">
                <h3>Mis Planes de Salud Actuales</h3>
                <ul class="health-list">
                    <li><span><i class="fas fa-check-square"></i> Plan Nutrición Keto</span> <span class="status-tag in-progress">En Progreso</span></li>
                    <li><span><i class="fas fa-check-square"></i> Rutina Yoga Mañana</span> <span class="status-tag completed">Completado</span></li>
                    <li><span><i class="fas fa-square"></i> Mindfulness</span> <span class="status-tag pending">Pendiente</span></li>
                </ul>
            </div>
            <div class="grid-card">
                <h3>Mis Logros y Progresión</h3>
                <p style="font-size:13px; color:#888;"><i class="fas fa-chart-line"></i> Reducción de Grasa Corporal</p>
            </div>
            <div class="grid-card">
                <h3>Mi Equipo de Salud</h3>
                <div class="team-member">
                    <span>Dr. Benitez - Diabetólogo</span>
                </div>
                <div class="team-member">
                    <span>Dra. Garcia - Psicóloga</span>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>