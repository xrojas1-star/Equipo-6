<?php
// 1. Iniciamos la sesión para proteger la página
session_start();

// 2. Si no hay sesión, lo mandamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Vitalis | Blog</title>
    <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body>

<div class="container">

    <aside class="sidebar">
        <h2 class="logo">VITALIS</h2>
        <nav>
            <ul>
                <li><a href="home.php" style="text-decoration:none; color:inherit;">🏠 Inicio</a></li>
                <li class="active">📝 Blog</li>
                <li>🔍 Consejos</li>
                <li>💚 Salud</li>
                <li>❤️ Bienestar</li>
                <li>👤 Mi Perfil</li>
                <li>🔔 Notificaciones</li>
            </ul>
        </nav>
    </aside>

    <main class="main">
        <input type="text" placeholder="¿Qué estás buscando?" class="search">

        <div class="post">
            <h4>Maria López · 3h</h4>
            <h3>10 Superalimentos para Mejorar tu Salud</h3>
            <p>Descubre cuáles son los superalimentos que pueden potenciar tu salud y bienestar.</p>
            <div class="tags">
                <span>#Nutrición</span>
                <span>#Superalimentos</span>
            </div>
        </div>

        <div class="post">
            <h4>Andrea Cárdenas · 3h</h4>
            <h3>5 Ejercicios para Fortalecer el Core</h3>
            <p>Descubre una rutina efectiva para tonificar tu core.</p>
            <div class="tags">
                <span>#Ejercicio</span>
                <span>#Core</span>
                <span>#Superación</span>
            </div>
        </div>

        <div class="post">
            <h4>Felipe Angeles · 4h</h4>
            <h3>Mindfulness: Guía para Principiantes</h3>
            <p>Aprende cómo iniciar en mindfulness.</p>
            <div class="tags">
                <span>#Bienestar</span>
                <span>#Mindfulness</span>
            </div>
        </div>
    </main>

    <aside class="right">
        <div class="card">
            <h3>Tendencias</h3>
            <ul>
                <li>🥗 Nutrición Saludable</li>
                <li>🏋️ Ejercicio en Casa</li>
                <li>🧘 Meditación</li>
                <li>📋 Hábitos Saludables</li>
            </ul>
        </div>

        <div class="card">
            <h3>A quién seguir</h3>
            <p>Carlos Herrera</p>
            <p>Laura Díaz</p>
        </div>
    </aside>

</div>

</body>
</html>