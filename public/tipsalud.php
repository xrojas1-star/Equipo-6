<?php
session_start();
if (!isset($_SESSION['user_id']) && empty($_SESSION['es_visitante'])) {
    header("Location: login.php");
    exit();
}
// Si tiene sesión real, limpiar flag visitante
if (!empty($_SESSION['user_id'])) { unset($_SESSION['es_visitante']); }
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Tips de Salud</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/tipsalud.css">
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
            <li class="active"><a href="tipsalud.php">🔍 Consejos</a></li>
            <li><a href="salud.php">💚 Salud</a></li>
            <li><a href="bienestar.php">❤️ Bienestar</a></li>
            <li class="nav-cursos"><a href="cursos.php">🎓 Cursos</a></li>
            <li class="nav-juegos"><a href="juegos_bienestar.php">🎮 Juegos Bienestar</a></li>
            <?php if (empty($_SESSION['es_visitante'])): ?>
            <li><a href="perfil.php">👤 Mi Perfil</a></li>
            <?php endif; ?>
            <li><a href="notificaciones.php">🔔 Notificaciones</a></li>
            <li><a href="#" onclick="abrirModal(); return false;">🚪 Cerrar sesión</a></li>
        </ul>
    </nav>
</aside>

<div class="page-body">
    <header class="tips-header">
        <h1>¡MEJORA TU VIDA HOY! DESCUBRE NUESTROS CONSEJOS DIARIOS DE BIENESTAR</h1>
    </header>

    <section class="grid-tips">
        <article class="card">
            <div class="card-header">TIP DE SALUD DEL DÍA</div>
            <div class="card-content">
                <div class="text">
                    <h2>HIDRÁTATE CORRECTAMENTE</h2>
                    <p>BEBE SUFICIENTE AGUA DURANTE EL DÍA (2-3 LITROS)</p>
                    <p class="footer-text">💡 Ayuda a tu cuerpo a funcionar mejor, mejora tu energía y digestión.</p>
                </div>
                <div class="icon">💧</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">TIP 2</div>
            <div class="card-content">
                <div class="text">
                    <h2>LA IMPORTANCIA DEL SUEÑO</h2>
                    <p class="footer-text">😴 Dormir lo suficiente es esencial para tu salud física y mental.</p>
                </div>
                <div class="icon">🌙</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">TIP 3</div>
            <div class="card-content">
                <div class="text">
                    <h2>ALIMENTACIÓN EQUILIBRADA</h2>
                    <p class="footer-text">🥗 Ayuda a tu cuerpo a funcionar mejor, mejora tu energía y digestión.</p>
                </div>
                <div class="icon">🍎</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">TIP 4</div>
            <div class="card-content">
                <div class="text">
                    <h2>EJERCICIO REGULAR</h2>
                    <p class="footer-text">🏃‍♀️ Mejora tu salud cardiovascular y aumenta tu energía.</p>
                </div>
                <div class="icon">💪</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">TIP 5</div>
            <div class="card-content">
                <div class="text">
                    <h2>MEDITACIÓN</h2>
                    <p class="footer-text">🧘 Reduce el estrés y mejora la concentración.</p>
                </div>
                <div class="icon">🧘</div>
            </div>
        </article>

        <article class="card">
            <div class="card-header">TIP 6</div>
            <div class="card-content">
                <div class="text">
                    <h2>HIGIENE PERSONAL</h2>
                    <p class="footer-text">🧼 Mantén una buena higiene para prevenir enfermedades.</p>
                </div>
                <div class="icon">🚰</div>
            </div>
        </article>
    </section>
</div>

<!-- Modal logout -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h2>¿Deseas cerrar sesión?</h2>
        <p>Se perderán los progresos no guardados.</p>
        <div class="modal-btns">
            <button class="btn-confirmar" onclick="confirmarLogout()">Sí, Salir</button>
            <button class="btn-cancelar" onclick="cerrarModal()">No, Cancelar</button>
        </div>
    </div>
</div>

<script>
function abrirModal()    { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal()   { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout(){ window.location.href = '../controllers/LogoutController.php'; }
</script>

</body>
</html>