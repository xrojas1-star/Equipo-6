<?php
// 1. Iniciamos la sesión para saber quién entró
session_start();

// 2. Protegemos la página: si no hay un usuario logueado, lo pateamos al login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Inicio</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <script src="https://kit.fontawesome.com/your-code.js" crossorigin="anonymous"></script>
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
                <li><a href="#">Consejos de Salud</a></li>
                <li><a href="#">Bienestar</a></li>
            </ul>
        </nav>
        <a href="../controllers/LogoutController.php" class="btn-nav">Cerrar sesión</a>
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

        <div class="articles-grid">
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
        </div>

        <div class="view-more">
            <a href="#" class="btn-secondary">Ver más artículos</a>
        </div>
    </main>

</body>
</html>