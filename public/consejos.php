<?php
session_start();
if (!isset($_SESSION['user_id']) && empty($_SESSION['es_visitante'])) {
    header("Location: login.php");
    exit();
}
if (!empty($_SESSION['user_id'])) { unset($_SESSION['es_visitante']); }
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
    <link rel="stylesheet" href="assets/css/consejos.css?v=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js?v=4"></script>
    <style>
    /* ══ OVERLAY ══ */
    .m-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding: 24px 16px;
        overflow-y: auto;
    }
    .m-overlay.open { display: flex; }

    .m-box {
        background: #fff;
        border-radius: 22px;
        max-width: 700px;
        width: 100%;
        margin: auto;
        box-shadow: 0 24px 60px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: mSlide 0.35s ease;
    }
    @keyframes mSlide { from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)} }

    .m-hero {
        padding: 26px 28px 22px;
        color: #fff;
    }
    .m-hero-tag {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.4);
        padding: 3px 12px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
        font-family: 'Outfit', sans-serif;
        margin-bottom: 10px;
    }
    .m-hero h2 { font-size: 20px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 4px; line-height: 1.3; }
    .m-hero p  { font-size: 13px; font-family: 'Outfit', sans-serif; opacity: 0.85; margin: 0; }

    /* colores hero */
    .h-green  { background: linear-gradient(135deg,#43a047,#1b5e20); }
    .h-teal   { background: linear-gradient(135deg,#26c6da,#00838f); }
    .h-orange { background: linear-gradient(135deg,#ff7043,#bf360c); }
    .h-blue   { background: linear-gradient(135deg,#29b6f6,#0277bd); }
    .h-blue2  { background: linear-gradient(135deg,#4fc3f7,#01579b); }
    .h-cyan   { background: linear-gradient(135deg,#80deea,#006064); }
    .h-red    { background: linear-gradient(135deg,#ef5350,#b71c1c); }
    .h-red2   { background: linear-gradient(135deg,#ff7043,#e64a19); }
    .h-amber  { background: linear-gradient(135deg,#ffa726,#e65100); }
    .h-pink   { background: linear-gradient(135deg,#ec407a,#880e4f); }
    .h-pink2  { background: linear-gradient(135deg,#f06292,#c2185b); }
    .h-purple { background: linear-gradient(135deg,#ba68c8,#6a1b9a); }
    .h-lime   { background: linear-gradient(135deg,#66bb6a,#1b5e20); }
    .h-teal2  { background: linear-gradient(135deg,#26a69a,#004d40); }
    .h-yellow { background: linear-gradient(135deg,#ffca28,#f57f17); }
    .h-rose   { background: linear-gradient(135deg,#f48fb1,#880e4f); }
    .h-violet { background: linear-gradient(135deg,#ce93d8,#6a1b9a); }
    .h-peach  { background: linear-gradient(135deg,#ffcc80,#e65100); }

    /* body del modal */
    .m-body { padding: 24px 28px 28px; }

    .m-stitle {
        font-size: 15px; font-weight: 800; color: #1a2332;
        font-family: 'Outfit', sans-serif;
        padding-left: 10px;
        border-left: 3px solid #2bbbad;
        margin: 20px 0 10px;
    }
    .m-body > p {
        font-size: 13px; color: #64748b;
        font-family: 'Outfit', sans-serif;
        line-height: 1.7; margin-bottom: 12px;
    }

    /* pasos numerados */
    .m-steps { display: flex; flex-direction: column; gap: 8px; margin-bottom: 14px; }
    .m-step {
        display: flex; gap: 10px; align-items: flex-start;
        background: #f0fffe;
        border: 1px solid rgba(43,187,173,0.2);
        border-radius: 10px; padding: 10px 12px;
    }
    .m-step-n {
        width: 22px; height: 22px; border-radius: 50%;
        background: linear-gradient(135deg,#2bbbad,#00897b);
        color: #fff; font-size: 11px; font-weight: 700;
        font-family: 'Outfit', sans-serif;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .m-step p { font-size: 13px; color: #4a5568; font-family: 'Outfit', sans-serif; line-height: 1.5; margin: 0; }
    .m-step p strong { color: #1a2332; }

    /* concepto cards */
    .m-card {
        display: flex; gap: 12px;
        background: #f8fffe;
        border: 1px solid rgba(43,187,173,0.18);
        border-radius: 12px; padding: 14px 16px; margin-bottom: 10px;
    }
    .m-card-icon { font-size: 26px; flex-shrink: 0; }
    .m-card strong { display: block; font-size: 13px; font-weight: 700; color: #1a2332; font-family: 'Outfit', sans-serif; margin-bottom: 5px; }
    .m-card p { font-size: 13px; color: #64748b; font-family: 'Outfit', sans-serif; line-height: 1.6; margin: 0; }

    /* tip box */
    .m-tip {
        background: #e8f8f7; border: 1px solid #b2dfdb;
        border-radius: 12px; padding: 12px 16px; margin: 14px 0;
        font-size: 13px; color: #00695c;
        font-family: 'Outfit', sans-serif; line-height: 1.6;
    }
    .m-tip strong { display: block; margin-bottom: 5px; font-size: 14px; }

    /* warning box */
    .m-warn {
        background: #fff3e0; border: 1px solid #ffcc80;
        border-radius: 12px; padding: 12px 16px; margin: 14px 0;
        font-size: 13px; color: #e65100;
        font-family: 'Outfit', sans-serif; line-height: 1.6;
    }
    .m-warn strong { display: block; margin-bottom: 5px; }

    /* lista simple */
    .m-list { list-style: none; padding: 0; margin: 0 0 14px; }
    .m-list li {
        padding: 7px 0 7px 22px; position: relative;
        border-bottom: 1px solid #f0f4f8;
        font-size: 13px; color: #4a5568;
        font-family: 'Outfit', sans-serif; line-height: 1.5;
    }
    .m-list li:last-child { border-bottom: none; }
    .m-list li::before { content: "✔"; position: absolute; left: 0; color: #2bbbad; font-size: 11px; top: 8px; }

    /* botón cerrar */
    .m-btn-close {
        display: block; width: 100%; margin-top: 20px;
        background: linear-gradient(135deg,#2bbbad,#00897b);
        color: #fff; border: none; padding: 13px;
        border-radius: 999px; font-size: 14px; font-weight: 700;
        font-family: 'Outfit', sans-serif; cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 4px 14px rgba(43,187,173,0.3);
    }
    .m-btn-close:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(43,187,173,0.4); }

    /* dark mode modales */
    body.dark-mode .m-box { background: #0d1117; }
    body.dark-mode .m-stitle { color: #e2e8f0; }
    body.dark-mode .m-body > p { color: #94a3b8; }
    body.dark-mode .m-step { background: #0d1f1e; border-color: #1e2830; }
    body.dark-mode .m-step p { color: #94a3b8; }
    body.dark-mode .m-step p strong { color: #e2e8f0; }
    body.dark-mode .m-card { background: #0d1f1e; border-color: #1e2830; }
    body.dark-mode .m-card strong { color: #e2e8f0; }
    body.dark-mode .m-card p { color: #94a3b8; }
    body.dark-mode .m-tip { background: #0d2a26; border-color: #134e4a; color: #4ade80; }
    body.dark-mode .m-warn { background: #1e1200; border-color: #e65100; color: #ffa726; }
    body.dark-mode .m-list li { color: #94a3b8; border-bottom-color: #1e2830; }
    </style>
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
        <p class="subtitle">Consejos personalizados según tu condición de salud</p>
      </div>
    </div>

    <div class="category-filter">
      <button class="cat-btn active" data-cat="todos">🌟 Todos</button>
      <button class="cat-btn" data-cat="normal">💪 Personas Sanas</button>
      <button class="cat-btn" data-cat="asma">🌬️ Asma</button>
      <button class="cat-btn" data-cat="diabetes">🩸 Diabetes</button>
      <button class="cat-btn" data-cat="hipertension">❤️ Hipertensión</button>
      <button class="cat-btn" data-cat="mayor">👴 Adulto Mayor</button>
      <button class="cat-btn" data-cat="embarazo">🤰 Embarazo</button>
    </div>

    <div class="main-cards" id="cardsContainer">

      <!-- NORMAL 1 -->
      <div class="content-card" data-cat="normal todos">
        <div class="card-image nutrition"><span class="card-tag">💪 Personas Sanas</span><h3>Nutrición Inteligente: El Poder de los Superalimentos</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Come 5 porciones de frutas y verduras al día</li><li><i class="fas fa-check"></i> Prioriza proteínas magras: pollo, pescado, legumbres</li><li><i class="fas fa-check"></i> Hidratación: 8 vasos de agua diarios</li><li><i class="fas fa-check"></i> Reduce ultraprocesados y azúcares añadidos</li></ul>
          <button class="btn-main" onclick="abrirM('m-n1')">Leer más</button>
        </div>
      </div>

      <!-- NORMAL 2 -->
      <div class="content-card" data-cat="normal todos">
        <div class="card-image mental"><span class="card-tag">💪 Personas Sanas</span><h3>Salud Mental: Micro-Pausas de Mindfulness</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Practica 10 min de meditación al despertar</li><li><i class="fas fa-check"></i> Regla 20-20-20 para descanso visual</li><li><i class="fas fa-check"></i> Diario de gratitud: 3 cosas positivas al día</li><li><i class="fas fa-check"></i> Desconecta pantallas 1h antes de dormir</li></ul>
          <button class="btn-main" onclick="abrirM('m-n2')">Ver técnicas</button>
        </div>
      </div>

      <!-- NORMAL 3 -->
      <div class="content-card" data-cat="normal todos">
        <div class="card-image mobility"><span class="card-tag">💪 Personas Sanas</span><h3>Movilidad Activa: Mejora tu Rutina Física</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> 150 min de actividad moderada por semana</li><li><i class="fas fa-check"></i> Combina cardio, fuerza y flexibilidad</li><li><i class="fas fa-check"></i> Calentamiento siempre antes de ejercitar</li><li><i class="fas fa-check"></i> Descansa al menos 1 día entre sesiones intensas</li></ul>
          <button class="btn-main" onclick="abrirM('m-n3')">Empezar rutina</button>
        </div>
      </div>

      <!-- ASMA 1 -->
      <div class="content-card" data-cat="asma todos">
        <div class="card-image asma-card"><span class="card-tag">🌬️ Asma</span><h3>Respiración y Control del Asma en el Día a Día</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Lleva siempre tu inhalador de rescate</li><li><i class="fas fa-check"></i> Evita ejercicio en ambientes con polvo o humo</li><li><i class="fas fa-check"></i> Técnica de respiración diafragmática diaria</li><li><i class="fas fa-check"></i> Usa mascarilla en días de alta contaminación</li></ul>
          <button class="btn-main" onclick="abrirM('m-a1')">Ver técnicas</button>
        </div>
      </div>

      <!-- ASMA 2 -->
      <div class="content-card" data-cat="asma todos">
        <div class="card-image asma-card2"><span class="card-tag">🌬️ Asma</span><h3>Actividad Física Segura para Personas con Asma</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Natación: el ejercicio más recomendado para asmáticos</li><li><i class="fas fa-check"></i> Calienta 15 min antes de cualquier ejercicio</li><li><i class="fas fa-check"></i> Evita deportes de alta intensidad en frío</li><li><i class="fas fa-check"></i> Monitorea síntomas con un diario de asma</li></ul>
          <button class="btn-main" onclick="abrirM('m-a2')">Conocer más</button>
        </div>
      </div>

      <!-- ASMA 3 -->
      <div class="content-card" data-cat="asma todos">
        <div class="card-image asma-card3"><span class="card-tag">🌬️ Asma</span><h3>Alimentación Anti-inflamatoria para Asmáticos</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Aumenta omega-3: salmón, nueces, chía</li><li><i class="fas fa-check"></i> Vitamina D: exposición solar 20 min/día</li><li><i class="fas fa-check"></i> Evita sulfitos: vino, conservas, mariscos</li><li><i class="fas fa-check"></i> Jengibre y cúrcuma como antiinflamatorios naturales</li></ul>
          <button class="btn-main" onclick="abrirM('m-a3')">Ver dieta</button>
        </div>
      </div>

      <!-- DIABETES 1 -->
      <div class="content-card" data-cat="diabetes todos">
        <div class="card-image diabetes-card"><span class="card-tag">🩸 Diabetes</span><h3>Control de Glucosa: Hábitos Clave Diarios</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Monitorea tu glucosa antes y después de comer</li><li><i class="fas fa-check"></i> No saltes comidas: mantén horarios regulares</li><li><i class="fas fa-check"></i> Aprende a leer el índice glucémico de alimentos</li><li><i class="fas fa-check"></i> Hidratación abundante reduce picos de glucosa</li></ul>
          <button class="btn-main" onclick="abrirM('m-d1')">Guía de control</button>
        </div>
      </div>

      <!-- DIABETES 2 -->
      <div class="content-card" data-cat="diabetes todos">
        <div class="card-image diabetes-card2"><span class="card-tag">🩸 Diabetes</span><h3>Alimentación para Diabéticos: Plato del Buen Comer</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> 50% verduras no almidonadas en cada plato</li><li><i class="fas fa-check"></i> Carbohidratos complejos: avena, quinoa, legumbres</li><li><i class="fas fa-check"></i> Evita azúcares simples y refrescos</li><li><i class="fas fa-check"></i> Canela y vinagre de manzana mejoran sensibilidad a insulina</li></ul>
          <button class="btn-main" onclick="abrirM('m-d2')">Ver dieta</button>
        </div>
      </div>

      <!-- DIABETES 3 -->
      <div class="content-card" data-cat="diabetes todos">
        <div class="card-image diabetes-card3"><span class="card-tag">🩸 Diabetes</span><h3>Ejercicio Seguro con Diabetes Tipo 1 y 2</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Caminar 30 min tras cada comida reduce glucosa</li><li><i class="fas fa-check"></i> Lleva siempre glucosa de emergencia al ejercitar</li><li><i class="fas fa-check"></i> Revisa pies antes y después del ejercicio</li><li><i class="fas fa-check"></i> El entrenamiento de fuerza mejora resistencia a insulina</li></ul>
          <button class="btn-main" onclick="abrirM('m-d3')">Rutinas seguras</button>
        </div>
      </div>

      <!-- HIPERTENSIÓN 1 -->
      <div class="content-card" data-cat="hipertension todos">
        <div class="card-image hiper-card"><span class="card-tag">❤️ Hipertensión</span><h3>Reducir la Presión Arterial con Cambios de Estilo de Vida</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Dieta DASH: baja en sodio, rica en potasio</li><li><i class="fas fa-check"></i> Limita el sodio a menos de 2,300 mg/día</li><li><i class="fas fa-check"></i> Reduce el alcohol y elimina el tabaco</li><li><i class="fas fa-check"></i> Técnicas de relajación reducen presión hasta 5 mmHg</li></ul>
          <button class="btn-main" onclick="abrirM('m-h1')">Dieta DASH</button>
        </div>
      </div>

      <!-- HIPERTENSIÓN 2 -->
      <div class="content-card" data-cat="hipertension todos">
        <div class="card-image hiper-card2"><span class="card-tag">❤️ Hipertensión</span><h3>Ejercicio Seguro para Controlar la Presión</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Cardio moderado: caminata, bicicleta, natación</li><li><i class="fas fa-check"></i> Evita esfuerzos isométricos intensos (pesas pesadas)</li><li><i class="fas fa-check"></i> Mide presión antes de ejercitarte</li><li><i class="fas fa-check"></i> 30 min diarios reduce presión sistólica 4-9 mmHg</li></ul>
          <button class="btn-main" onclick="abrirM('m-h2')">Ver rutinas</button>
        </div>
      </div>

      <!-- HIPERTENSIÓN 3 -->
      <div class="content-card" data-cat="hipertension todos">
        <div class="card-image hiper-card3"><span class="card-tag">❤️ Hipertensión</span><h3>Manejo del Estrés en Hipertensos</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Respiración 4-7-8: inhala 4s, retén 7s, exhala 8s</li><li><i class="fas fa-check"></i> Yoga y tai-chi reducen presión arterial</li><li><i class="fas fa-check"></i> Duerme 7-8 horas: el insomnio eleva la presión</li><li><i class="fas fa-check"></i> Identifica y evita tus detonadores de estrés</li></ul>
          <button class="btn-main" onclick="abrirM('m-h3')">Técnicas de calma</button>
        </div>
      </div>

      <!-- ADULTO MAYOR 1 -->
      <div class="content-card" data-cat="mayor todos">
        <div class="card-image mayor-card"><span class="card-tag">👴 Adulto Mayor</span><h3>Nutrición Esencial para Mayores de 60 Años</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Aumenta calcio y vitamina D para huesos fuertes</li><li><i class="fas fa-check"></i> Proteínas en cada comida para evitar sarcopenia</li><li><i class="fas fa-check"></i> Fibra diaria: frutas, verduras, legumbres</li><li><i class="fas fa-check"></i> Hidratación: la sed disminuye con la edad</li></ul>
          <button class="btn-main" onclick="abrirM('m-m1')">Ver guía</button>
        </div>
      </div>

      <!-- ADULTO MAYOR 2 -->
      <div class="content-card" data-cat="mayor todos">
        <div class="card-image mayor-card2"><span class="card-tag">👴 Adulto Mayor</span><h3>Ejercicios de Equilibrio y Fuerza para Mayores</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Ejercicios de equilibrio previenen caídas</li><li><i class="fas fa-check"></i> Caminata diaria 20-30 min a paso moderado</li><li><i class="fas fa-check"></i> Ejercicios con bandas elásticas para fuerza</li><li><i class="fas fa-check"></i> Tai-chi: equilibrio, flexibilidad y calma mental</li></ul>
          <button class="btn-main" onclick="abrirM('m-m2')">Ver rutinas</button>
        </div>
      </div>

      <!-- ADULTO MAYOR 3 -->
      <div class="content-card" data-cat="mayor todos">
        <div class="card-image mayor-card3"><span class="card-tag">👴 Adulto Mayor</span><h3>Salud Mental y Social en la Tercera Edad</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Mantén actividad social activa: grupos, talleres</li><li><i class="fas fa-check"></i> Estimulación cognitiva: lectura, crucigramas, música</li><li><i class="fas fa-check"></i> Voluntariado: propósito y conexión social</li><li><i class="fas fa-check"></i> Habla con tu médico si sientes tristeza persistente</li></ul>
          <button class="btn-main" onclick="abrirM('m-m3')">Más información</button>
        </div>
      </div>

      <!-- EMBARAZO 1 -->
      <div class="content-card" data-cat="embarazo todos">
        <div class="card-image emb-card"><span class="card-tag">🤰 Embarazo</span><h3>Nutrición Durante el Embarazo: Lo Esencial</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Ácido fólico desde el primer trimestre</li><li><i class="fas fa-check"></i> Hierro y calcio: fundamentales para el bebé</li><li><i class="fas fa-check"></i> Evita: atún, alcohol, quesos no pasteurizados</li><li><i class="fas fa-check"></i> Come cada 3-4 horas para evitar náuseas</li></ul>
          <button class="btn-main" onclick="abrirM('m-e1')">Guía nutricional</button>
        </div>
      </div>

      <!-- EMBARAZO 2 -->
      <div class="content-card" data-cat="embarazo todos">
        <div class="card-image emb-card2"><span class="card-tag">🤰 Embarazo</span><h3>Actividad Física Segura en el Embarazo</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> Caminata y natación: los más recomendados</li><li><i class="fas fa-check"></i> Yoga prenatal para flexibilidad y relajación</li><li><i class="fas fa-check"></i> Evita deportes de contacto y riesgo de caídas</li><li><i class="fas fa-check"></i> Consulta siempre a tu ginecólogo antes de ejercitar</li></ul>
          <button class="btn-main" onclick="abrirM('m-e2')">Ejercicios seguros</button>
        </div>
      </div>

      <!-- EMBARAZO 3 -->
      <div class="content-card" data-cat="embarazo todos">
        <div class="card-image emb-card3"><span class="card-tag">🤰 Embarazo</span><h3>Bienestar Emocional Durante la Gestación</h3></div>
        <div class="card-body">
          <ul><li><i class="fas fa-check"></i> La ansiedad prenatal es normal: busca apoyo</li><li><i class="fas fa-check"></i> Técnicas de respiración para el parto</li><li><i class="fas fa-check"></i> Vinculación prenatal: habla y música para el bebé</li><li><i class="fas fa-check"></i> Descansa suficiente: el sueño es medicamento</li></ul>
          <button class="btn-main" onclick="abrirM('m-e3')">Apoyo emocional</button>
        </div>
      </div>

    </div><!-- /main-cards -->

    <div class="bottom-grid">
      <div class="quick-tips">
        <h3>💡 Consejos Rápidos del Día</h3>
        <div class="tip-item"><span>💧 Recuerda beber agua</span><input type="checkbox" checked></div>
        <div class="tip-item"><span>🚶 Camina 10 min tras comer</span><input type="checkbox"></div>
        <div class="tip-item"><span>🧠 Tómate un descanso de pantalla</span><input type="checkbox"></div>
        <div class="tip-item"><span>🌞 Sal al sol 15 minutos hoy</span><input type="checkbox"></div>
        <div class="tip-item"><span>😴 Duerme entre 7 y 8 horas</span><input type="checkbox"></div>
      </div>
      <div class="community-section">
        <h3>🤝 Comunidad: Comparte tus Consejos</h3>
        <p class="community-desc">¿Tienes un consejo que ha funcionado para ti? ¡Compártelo!</p>
        <div class="comment-input">
          <input type="text" placeholder="Escribe tu consejo...">
          <button class="btn-send-tip">Enviar</button>
        </div>
      </div>
    </div>

  </section>
</div>

<!-- ═══════════════════════════════════════════
     18 MODALES
════════════════════════════════════════════ -->

<!-- N1: Nutrición Inteligente -->
<div class="m-overlay" id="m-n1" onclick="cerrarSi(event,'m-n1')"><div class="m-box">
  <div class="m-hero h-green"><span class="m-hero-tag">💪 Personas Sanas · Nutrición</span><h2>Nutrición Inteligente: El Poder de los Superalimentos</h2><p>Guía completa para una alimentación que te dé energía real todo el día</p></div>
  <div class="m-body">
    <p>Una alimentación inteligente no significa comer menos, sino elegir mejor. Los superalimentos son alimentos densos en nutrientes que aportan beneficios comprobados con porciones pequeñas.</p>
    <div class="m-stitle">Los 6 superalimentos esenciales</div>
    <div class="m-card"><span class="m-card-icon">🫐</span><div><strong>Arándanos — Antioxidantes potentes</strong><p>Reducen el estrés oxidativo, mejoran la memoria y protegen el corazón. Una taza al día es suficiente. Puedes agregarlos a avena, yogur o smoothies.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🥑</span><div><strong>Aguacate — Grasas saludables</strong><p>Rico en ácido oleico (reduce colesterol LDL), potasio y vitamina E. Media pieza al día mejora la absorción de vitaminas liposolubles de otras verduras.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🥦</span><div><strong>Brócoli — Vitamina C y fibra</strong><p>Contiene sulforafano, compuesto con propiedades anticancerígenas. Una taza cocida aporta más vitamina C que una naranja. Al vapor conserva mejor sus nutrientes.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🐟</span><div><strong>Salmón — Omega-3 y proteína</strong><p>El omega-3 EPA y DHA reduce inflamación, mejora la salud cardiovascular y el rendimiento cerebral. 2-3 porciones por semana cubren tus necesidades.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🌰</span><div><strong>Nueces — Cerebro y corazón</strong><p>Son las únicas nueces con omega-3 vegetal (ALA). Un puñado (30g) al día reduce el riesgo cardiovascular y mejora la función cognitiva.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🫘</span><div><strong>Lentejas — Hierro y proteína vegetal</strong><p>Una taza cocida aporta 18g de proteína y el 37% del hierro diario recomendado. Combínalas con vitamina C para maximizar la absorción del hierro.</p></div></div>
    <div class="m-tip"><strong>💡 Consejo práctico</strong>No necesitas comerlos todos en un día. Incorpora 1-2 por comida de forma natural. La constancia durante semanas es lo que genera resultados reales.</div>
    <button class="m-btn-close" onclick="cerrarM('m-n1')">Entendido ✓</button>
  </div>
</div></div>

<!-- N2: Mindfulness -->
<div class="m-overlay" id="m-n2" onclick="cerrarSi(event,'m-n2')"><div class="m-box">
  <div class="m-hero h-teal"><span class="m-hero-tag">💪 Personas Sanas · Salud Mental</span><h2>Micro-Pausas de Mindfulness</h2><p>Técnicas prácticas para reducir el estrés en cualquier momento del día</p></div>
  <div class="m-body">
    <p>El mindfulness no requiere horas ni silencio absoluto. Estas micro-pausas de 1 a 10 minutos activan el sistema nervioso parasimpático y reducen el cortisol de forma inmediata.</p>
    <div class="m-stitle">Técnica 1 — Respiración 4-4-4 (1 minuto)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Siéntate cómodamente con la espalda recta. Cierra los ojos si puedes.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Inhala</strong> contando mentalmente 4 segundos lentos.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Retén</strong> el aire contando 4 segundos sin tensión.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Exhala</strong> lentamente contando 4 segundos vaciando completamente los pulmones.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p>Repite 4-5 veces. Notarás calma inmediata desde la segunda ronda.</p></div>
    </div>
    <div class="m-stitle">Técnica 2 — Regla 20-20-20 para ojos y mente</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Cada 20 minutos de pantalla, detente completamente.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Mira un objeto a unos 6 metros de distancia durante 20 segundos.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>En esos 20 segundos, solo observa sin pensar en tareas. Deja que la mente descanse.</p></div>
    </div>
    <div class="m-stitle">Técnica 3 — Escáner corporal rápido (5 minutos)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Cierra los ojos y lleva la atención a los pies. ¿Sientes tensión, calor, hormigueo?</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Sube lentamente: pantorrillas, rodillas, muslos, abdomen, pecho, hombros, cuello, cara.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>En cada zona, si sientes tensión, exhala visualizando que esa tensión se disuelve.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Al llegar a la cabeza, toma 3 respiraciones profundas y abre los ojos lentamente.</p></div>
    </div>
    <div class="m-tip"><strong>💡 Para empezar hoy</strong>Pon una alarma cada hora con el texto "Pausa". Cuando suene, practica la respiración 4-4-4. En una semana notarás la diferencia en tu nivel de estrés.</div>
    <button class="m-btn-close" onclick="cerrarM('m-n2')">Entendido ✓</button>
  </div>
</div></div>

<!-- N3: Rutina física -->
<div class="m-overlay" id="m-n3" onclick="cerrarSi(event,'m-n3')"><div class="m-box">
  <div class="m-hero h-orange"><span class="m-hero-tag">💪 Personas Sanas · Ejercicio</span><h2>Movilidad Activa: Rutina de 3 Días</h2><p>Plan semanal combinando cardio, fuerza y flexibilidad paso a paso</p></div>
  <div class="m-body">
    <p>La OMS recomienda 150 minutos de actividad moderada por semana. Esta rutina de 3 días lo cumple perfectamente combinando los 3 tipos de ejercicio esenciales.</p>
    <div class="m-stitle">Día 1 — Cardio moderado (30-40 min)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Calentamiento 5 min:</strong> camina a paso normal aumentando la velocidad gradualmente.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Cardio principal 25 min:</strong> trote suave, bicicleta o natación a intensidad que te permita hablar pero con esfuerzo.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Enfriamiento 5 min:</strong> disminuye el ritmo progresivamente hasta caminar despacio.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Estiramientos 5 min:</strong> cuádriceps, isquiotibiales y pantorrillas, 30 segundos cada uno.</p></div>
    </div>
    <div class="m-stitle">Día 2 — Fuerza funcional (30 min)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Sentadillas:</strong> 3 series × 12 repeticiones. Pies al ancho de hombros, espalda recta, rodillas sin sobrepasar los pies.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Flexiones:</strong> 3 series × 8-10 repeticiones. Cuerpo recto, baja hasta que el pecho casi toque el suelo.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Plancha:</strong> 3 series × 30 segundos. Abdomen contraído, cuerpo en línea recta.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Zancadas:</strong> 3 series × 10 por pierna. Rodilla delantera a 90°, rodilla trasera casi toca el suelo.</p></div>
    </div>
    <div class="m-stitle">Día 3 — Flexibilidad y movilidad (20-25 min)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Yoga suave o estiramientos globales:</strong> 5 posiciones de 45 segundos cada una.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Rotaciones articulares:</strong> cuello, hombros, caderas, rodillas y tobillos — 10 círculos cada una.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Respiración consciente:</strong> termina con 5 minutos de respiración profunda en el suelo.</p></div>
    </div>
    <div class="m-tip"><strong>💡 Consejo clave</strong>Descansa al menos 1 día entre sesiones de fuerza. Los días de descanso son cuando los músculos realmente crecen y se reparan.</div>
    <button class="m-btn-close" onclick="cerrarM('m-n3')">Entendido ✓</button>
  </div>
</div></div>

<!-- A1: Control Asma -->
<div class="m-overlay" id="m-a1" onclick="cerrarSi(event,'m-a1')"><div class="m-box">
  <div class="m-hero h-blue"><span class="m-hero-tag">🌬️ Asma · Técnicas de Respiración</span><h2>Control del Asma: Técnicas de Respiración</h2><p>Métodos respiratorios validados clínicamente para asmáticos</p></div>
  <div class="m-body">
    <p>Las técnicas de respiración correctas pueden reducir la frecuencia e intensidad de las crisis asmáticas y mejorar la función pulmonar con práctica constante.</p>
    <div class="m-stitle">Técnica 1 — Respiración diafragmática (10 min diarios)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Acuéstate boca arriba o siéntate cómodamente. Coloca una mano en el pecho y otra en el abdomen.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Inhala lentamente por la nariz durante 4 segundos. Solo debe moverse la mano del abdomen — el pecho permanece quieto.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Exhala despacio por la boca con labios fruncidos (como si soplaras una vela) durante 6 segundos.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Repite 10 veces. Practica 2 veces al día: mañana y noche.</p></div>
    </div>
    <div class="m-stitle">Técnica 2 — Respiración con labios fruncidos (para crisis leve)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Relaja los hombros y el cuello. No te inclines hacia adelante con pánico.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Inhala suavemente por la nariz 2 segundos.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Exhala por la boca con los labios fruncidos durante 4 segundos. Esto mantiene las vías abiertas.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Usa siempre tu inhalador de rescate si los síntomas no mejoran en 10 minutos.</p></div>
    </div>
    <div class="m-warn"><strong>⚠️ Señales de emergencia</strong>Si sientes dificultad para hablar, labios azulados o el inhalador no hace efecto en 20 minutos → llama a emergencias de inmediato.</div>
    <div class="m-stitle">Hábitos de control diario</div>
    <ul class="m-list">
      <li>Lleva siempre tu inhalador — nunca salgas sin él</li>
      <li>Usa mascarilla en días de alta contaminación o frío intenso</li>
      <li>Evita ambientes con humo, polvo, aerosoles o pinturas</li>
      <li>Mantén un diario de síntomas para identificar tus desencadenantes</li>
      <li>Revisa tu técnica de inhalación con tu médico cada 6 meses</li>
    </ul>
    <button class="m-btn-close" onclick="cerrarM('m-a1')">Entendido ✓</button>
  </div>
</div></div>

<!-- A2: Actividad física asma -->
<div class="m-overlay" id="m-a2" onclick="cerrarSi(event,'m-a2')"><div class="m-box">
  <div class="m-hero h-blue2"><span class="m-hero-tag">🌬️ Asma · Ejercicio Seguro</span><h2>Actividad Física Segura para Asmáticos</h2><p>Cómo ejercitarte sin provocar una crisis — guía paso a paso</p></div>
  <div class="m-body">
    <p>El ejercicio es beneficioso para los asmáticos: fortalece los pulmones y mejora la capacidad respiratoria. La clave está en el calentamiento, la intensidad y el entorno adecuado.</p>
    <div class="m-stitle">Protocolo de ejercicio seguro</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Antes de ejercitar:</strong> verifica que llevas el inhalador. Si tu médico lo indica, usa el broncodilatador preventivo 15 min antes.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Calentamiento 15 min:</strong> camina aumentando ritmo gradualmente. Nunca empieces en frío — es el principal detonador de broncoespasmo.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Ejercicio principal:</strong> mantén intensidad moderada (puedes hablar con esfuerzo). Evita sprints o esfuerzos máximos.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Enfriamiento 10 min:</strong> disminuye el ritmo de forma progresiva. Una parada brusca puede provocar broncoespasmo post-ejercicio.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p><strong>Después:</strong> anota en tu diario cómo te sentiste. Esto ayuda a ajustar la intensidad en futuras sesiones.</p></div>
    </div>
    <div class="m-stitle">Los mejores ejercicios para asmáticos</div>
    <div class="m-card"><span class="m-card-icon">🏊</span><div><strong>Natación — El más recomendado</strong><p>El ambiente húmedo y cálido de la piscina reduce el riesgo de broncoespasmo. Mejora la capacidad pulmonar y la postura. 3 sesiones de 30 min por semana es ideal.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🚶</span><div><strong>Caminata y senderismo suave</strong><p>Bajo impacto y ritmo controlable. Usa mascarilla en días fríos o de alta contaminación. Evita zonas con mucho pasto en temporada de alergia.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🧘</span><div><strong>Yoga y pilates</strong><p>Mejoran el control de la respiración y reducen el estrés, que es un detonador común del asma. Elige clases en espacios sin aromas fuertes.</p></div></div>
    <div class="m-tip"><strong>💡 Temperatura y ambiente</strong>Evita ejercitar al aire libre cuando la temperatura sea menor a 10°C o el índice de calidad del aire sea malo. El interior climatizado es tu aliado.</div>
    <button class="m-btn-close" onclick="cerrarM('m-a2')">Entendido ✓</button>
  </div>
</div></div>

<!-- A3: Dieta anti-inflamatoria asma -->
<div class="m-overlay" id="m-a3" onclick="cerrarSi(event,'m-a3')"><div class="m-box">
  <div class="m-hero h-cyan"><span class="m-hero-tag">🌬️ Asma · Alimentación</span><h2>Alimentación Anti-inflamatoria para Asmáticos</h2><p>Qué comer y qué evitar para reducir la inflamación de las vías respiratorias</p></div>
  <div class="m-body">
    <p>La inflamación crónica de las vías respiratorias es el núcleo del asma. Ciertos alimentos reducen esa inflamación y otros la disparan. Esta guía te muestra cuáles son cuáles.</p>
    <div class="m-stitle">Alimentos que AYUDAN al asma</div>
    <div class="m-card"><span class="m-card-icon">🐟</span><div><strong>Omega-3: salmón, sardinas, nueces, chía</strong><p>Reducen la inflamación bronquial. 2-3 porciones de pescado graso por semana o 1 cucharada de semillas de chía diaria.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🟡</span><div><strong>Cúrcuma y jengibre</strong><p>La curcumina inhibe vías inflamatorias. Agrega media cucharadita de cúrcuma a tus comidas o prepara un té de jengibre con limón cada mañana.</p></div></div>
    <div class="m-card"><span class="m-card-icon">☀️</span><div><strong>Vitamina D: exposición solar + alimentos</strong><p>El déficit de vitamina D se asocia con mayor severidad del asma. 20 minutos de sol en brazos y piernas al día. También: yema de huevo, champiñones, leche fortificada.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🍎</span><div><strong>Frutas ricas en vitamina C y quercetina</strong><p>Manzanas, moras, cítricos y cebollas contienen quercetina, que estabiliza las células que liberan histamina. Una manzana al día tiene evidencia real en reducción de síntomas.</p></div></div>
    <div class="m-stitle">Alimentos que AGRAVAN el asma</div>
    <ul class="m-list">
      <li>Sulfitos: vino (especialmente tinto), conservas, mariscos enlatados, frutas deshidratadas</li>
      <li>Alimentos ultraprocesados con aditivos y colorantes artificiales</li>
      <li>Alcohol: dilata y luego inflama las vías respiratorias</li>
      <li>Alimentos a los que seas alérgico (huevo, leche, trigo en casos específicos)</li>
    </ul>
    <div class="m-tip"><strong>💡 Lleva un diario alimentario</strong>Durante 2 semanas anota lo que comes y cómo te sientes respiratoriamente. Muchos asmáticos descubren detonadores alimentarios específicos que no conocían.</div>
    <button class="m-btn-close" onclick="cerrarM('m-a3')">Entendido ✓</button>
  </div>
</div></div>

<!-- D1: Control glucosa -->
<div class="m-overlay" id="m-d1" onclick="cerrarSi(event,'m-d1')"><div class="m-box">
  <div class="m-hero h-red"><span class="m-hero-tag">🩸 Diabetes · Control Glucémico</span><h2>Control de Glucosa: Hábitos Clave Diarios</h2><p>Rutina diaria para mantener tu glucosa estable durante todo el día</p></div>
  <div class="m-body">
    <p>El control de la glucosa no depende solo de la medicación — depende en un 70% de los hábitos diarios. Esta guía te enseña la rutina que marca la diferencia.</p>
    <div class="m-stitle">Valores de referencia que debes conocer</div>
    <div class="m-card"><span class="m-card-icon">📊</span><div><strong>Glucosa en ayunas objetivo: 80-130 mg/dL</strong><p>Mídela cada mañana antes de comer, siempre a la misma hora. El registro constante revela patrones que tu médico puede usar para ajustar tu tratamiento.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🍽️</span><div><strong>Glucosa 2h post-comida: menos de 180 mg/dL</strong><p>Mídela exactamente 2 horas después de la primera mordida. Valores consistentemente altos indican que ese alimento o porción necesita ajustarse.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🚨</span><div><strong>Hipoglucemia: menos de 70 mg/dL — actúa ya</strong><p>Toma 15g de glucosa rápida: 3-4 tabletas de glucosa, 150ml de jugo o refresco regular. Espera 15 min y mide de nuevo. Si sigue baja, repite.</p></div></div>
    <div class="m-stitle">Rutina diaria de 5 pasos</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Al despertar:</strong> mide glucosa en ayunas. Anótala siempre en tu libreta o app.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>En cada comida:</strong> mantén horarios fijos. Nunca saltes comidas — provoca hipoglucemia o rebote hiperglucémico.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>2h después de comer:</strong> mide glucosa post-prandial. Anota qué comiste para identificar qué alimentos te suben más.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Después de cada comida:</strong> camina 15-20 min. Esto reduce la glucosa post-prandial hasta un 30%.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p><strong>Al dormir:</strong> mide antes de acostarte. Ideal: 100-140 mg/dL. Si está baja, come un snack pequeño con proteína.</p></div>
    </div>
    <div class="m-tip"><strong>💡 Hidratación y glucosa</strong>Beber suficiente agua (8 vasos/día) ayuda a los riñones a eliminar el exceso de glucosa. La deshidratación concentra la glucosa en sangre y la eleva artificialmente.</div>
    <button class="m-btn-close" onclick="cerrarM('m-d1')">Entendido ✓</button>
  </div>
</div></div>

<!-- D2: Dieta diabéticos -->
<div class="m-overlay" id="m-d2" onclick="cerrarSi(event,'m-d2')"><div class="m-box">
  <div class="m-hero h-red2"><span class="m-hero-tag">🩸 Diabetes · Alimentación</span><h2>Plato del Buen Comer para Diabéticos</h2><p>Cómo construir cada comida para mantener la glucosa estable</p></div>
  <div class="m-body">
    <p>El Método del Plato es la herramienta más sencilla y eficaz para controlar la glucosa sin contar calorías. Divide tu plato visualmente en tres secciones.</p>
    <div class="m-stitle">El Método del Plato</div>
    <div class="m-card"><span class="m-card-icon">🥦</span><div><strong>½ del plato — Verduras no almidonadas</strong><p>Espinaca, brócoli, pepino, tomate, zanahoria, chayote, calabacín. Son bajas en carbohidratos y ricas en fibra. La fibra ralentiza la absorción de glucosa.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🍗</span><div><strong>¼ del plato — Proteína magra</strong><p>Pollo sin piel, pescado, claras de huevo, tofu, legumbres. La proteína no eleva la glucosa y da saciedad prolongada. Cocida a la plancha, vapor u horneada.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🌾</span><div><strong>¼ del plato — Carbohidratos complejos</strong><p>Arroz integral, avena, quinoa, camote, tortilla de maíz (con moderación). Son de índice glucémico bajo: suben la glucosa despacio y de forma controlada.</p></div></div>
    <div class="m-stitle">Alimentos que debes reducir al mínimo</div>
    <ul class="m-list">
      <li>Azúcar, miel, piloncillo y cualquier edulcorante calórico</li>
      <li>Refrescos, jugos y bebidas azucaradas (incluyendo los "light" con colorantes)</li>
      <li>Pan blanco, arroz blanco, tortillas de harina refinada</li>
      <li>Frituras, fast food y productos ultraprocesados</li>
      <li>Frutas muy dulces en exceso: mango, uvas, plátano maduro</li>
    </ul>
    <div class="m-tip"><strong>💡 Canela y vinagre de manzana</strong>Media cucharadita de canela en polvo al día mejora la sensibilidad a la insulina en diabéticos tipo 2. Una cucharada de vinagre de manzana antes de las comidas reduce la glucosa post-prandial hasta un 20%.</div>
    <button class="m-btn-close" onclick="cerrarM('m-d2')">Entendido ✓</button>
  </div>
</div></div>

<!-- D3: Ejercicio diabetes -->
<div class="m-overlay" id="m-d3" onclick="cerrarSi(event,'m-d3')"><div class="m-box">
  <div class="m-hero h-amber"><span class="m-hero-tag">🩸 Diabetes · Ejercicio Seguro</span><h2>Ejercicio Seguro con Diabetes</h2><p>Rutinas que bajan la glucosa sin riesgo de hipoglucemia</p></div>
  <div class="m-body">
    <p>El ejercicio es uno de los medicamentos más potentes para la diabetes: reduce la glucosa de forma inmediata y mejora la sensibilidad a la insulina a largo plazo. Pero requiere precauciones específicas.</p>
    <div class="m-stitle">Antes de cada sesión de ejercicio</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Mide tu glucosa. Si está <strong>menor de 100 mg/dL</strong> come un snack con carbohidratos antes de empezar.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Si está <strong>mayor de 250 mg/dL</strong> no hagas ejercicio intenso — la glucosa puede subir más. Espera a que baje.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Lleva siempre contigo glucosa de emergencia: tabletas, jugo pequeño o dulces.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Revisa tus pies antes de ejercitar: busca rozaduras, ampollas o heridas. El pie diabético necesita cuidado especial.</p></div>
    </div>
    <div class="m-stitle">Las 3 rutinas más recomendadas</div>
    <div class="m-card"><span class="m-card-icon">🚶</span><div><strong>Caminata post-comida — 15-30 min</strong><p>Caminar después de comer reduce la glucosa post-prandial hasta un 30%. Es el ejercicio más sencillo y efectivo para diabéticos. Hazlo a paso moderado, no lento.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🏋️</span><div><strong>Entrenamiento de fuerza — 2-3 veces/semana</strong><p>Los músculos actúan como esponjas de glucosa. El entrenamiento de fuerza mejora la resistencia a la insulina durante 24-48 horas post-ejercicio. Usa pesas ligeras o bandas elásticas.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🏊</span><div><strong>Natación — Excelente para pie diabético</strong><p>Sin impacto en los pies, mejora la circulación y quema glucosa eficientemente. 30 min, 3 veces por semana es ideal.</p></div></div>
    <div class="m-tip"><strong>💡 Mide después de ejercitar</strong>La glucosa puede seguir bajando hasta 24 horas post-ejercicio. Mide al terminar y 2 horas después para conocer tu respuesta personal.</div>
    <button class="m-btn-close" onclick="cerrarM('m-d3')">Entendido ✓</button>
  </div>
</div></div>

<!-- H1: Dieta DASH -->
<div class="m-overlay" id="m-h1" onclick="cerrarSi(event,'m-h1')"><div class="m-box">
  <div class="m-hero h-pink"><span class="m-hero-tag">❤️ Hipertensión · Alimentación</span><h2>Dieta DASH para Controlar la Presión</h2><p>El plan alimentario más respaldado científicamente contra la hipertensión</p></div>
  <div class="m-body">
    <p>La Dieta DASH (Dietary Approaches to Stop Hypertension) reduce la presión sistólica hasta 11 mmHg en 2 semanas, comparable con un medicamento, sin efectos secundarios.</p>
    <div class="m-stitle">Los 4 pilares de la Dieta DASH</div>
    <div class="m-card"><span class="m-card-icon">🧂</span><div><strong>Reducir el sodio a menos de 1,500 mg/día</strong><p>El 70% del sodio que consumimos viene de alimentos procesados, no del salero. Lee etiquetas: evita productos con más de 200mg de sodio por porción. Sazona con hierbas, limón y ajo.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🍌</span><div><strong>Aumentar el potasio: 4,700 mg/día</strong><p>El potasio contrarresta el efecto del sodio. Fuentes: plátano, papa cocida con cáscara, espinaca, aguacate, frijoles. Come al menos 3 porciones de estos al día.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🥛</span><div><strong>Calcio y magnesio suficientes</strong><p>Lácteos bajos en grasa, tofu, sardinas (con hueso), semillas de ajonjolí. El magnesio relaja los vasos sanguíneos. Nueces, legumbres y verduras de hoja verde son excelentes fuentes.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🌾</span><div><strong>Granos integrales en lugar de refinados</strong><p>Pan integral, arroz integral, avena, quinoa. La fibra soluble reduce la presión y mejora el perfil lipídico.</p></div></div>
    <div class="m-stitle">Lo que DEBES reducir o eliminar</div>
    <ul class="m-list">
      <li>Embutidos, jamón, salchicha, tocino: altísimos en sodio</li>
      <li>Enlatados y sopas instantáneas: una lata puede tener el sodio de todo el día</li>
      <li>Alcohol: más de 1 copa al día eleva la presión de forma directa</li>
      <li>Cafeína en exceso: más de 3 tazas de café al día puede elevar la presión</li>
    </ul>
    <div class="m-tip"><strong>💡 Resultado en 2 semanas</strong>Siguiendo la Dieta DASH con sodio restringido, la presión sistólica baja en promedio 8-14 mmHg. Combínala con ejercicio moderado para duplicar el efecto.</div>
    <button class="m-btn-close" onclick="cerrarM('m-h1')">Entendido ✓</button>
  </div>
</div></div>

<!-- H2: Ejercicio hipertensión -->
<div class="m-overlay" id="m-h2" onclick="cerrarSi(event,'m-h2')"><div class="m-box">
  <div class="m-hero h-pink2"><span class="m-hero-tag">❤️ Hipertensión · Ejercicio</span><h2>Ejercicio Seguro para Hipertensos</h2><p>Rutinas que reducen la presión sin poner en riesgo tu corazón</p></div>
  <div class="m-body">
    <p>30 minutos de cardio moderado al día puede reducir la presión sistólica entre 4-9 mmHg. Esto equivale a un medicamento antihipertensivo de baja dosis.</p>
    <div class="m-stitle">Protocolo de seguridad antes de ejercitar</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Mide tu presión antes de empezar. <strong>Si está por encima de 160/100 mmHg</strong>, no hagas ejercicio intenso ese día.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Nunca suspendas tu medicación antihipertensiva antes de ejercitar por tu cuenta.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Calentamiento de 10 minutos siempre. Los cambios bruscos de presión durante el arranque son los más peligrosos.</p></div>
    </div>
    <div class="m-stitle">Los mejores ejercicios para hipertensos</div>
    <div class="m-card"><span class="m-card-icon">🚶</span><div><strong>Caminata rápida — El más seguro</strong><p>30-45 min, 5 días por semana. A una intensidad donde puedas hablar pero con ligero esfuerzo. Reduce la presión sistólica hasta 7 mmHg con constancia de 3 meses.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🚲</span><div><strong>Bicicleta estacionaria o ciclismo suave</strong><p>Sin impacto articular, intensidad controlable. 30 min, 3-5 veces por semana. La bicicleta estática permite monitorear el esfuerzo fácilmente.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🏊</span><div><strong>Natación</strong><p>El agua reduce la carga cardiovascular. 30 min de nado moderado baja la presión de forma inmediata y sostenida.</p></div></div>
    <div class="m-warn"><strong>⚠️ Ejercicios que debes EVITAR</strong>Levantamiento de pesas muy pesadas, ejercicios isométricos prolongados (como plancha por más de 1 min) y actividades de muy alta intensidad como sprints. Provocan picos de presión peligrosos.</div>
    <div class="m-tip"><strong>💡 Mide después de ejercitar</strong>La presión baja 5-10 mmHg durante las 12 horas siguientes al ejercicio. Ese es el mejor momento para tu descanso post-entrenamiento.</div>
    <button class="m-btn-close" onclick="cerrarM('m-h2')">Entendido ✓</button>
  </div>
</div></div>

<!-- H3: Estrés hipertensión -->
<div class="m-overlay" id="m-h3" onclick="cerrarSi(event,'m-h3')"><div class="m-box">
  <div class="m-hero h-purple"><span class="m-hero-tag">❤️ Hipertensión · Estrés</span><h2>Manejo del Estrés en Hipertensos</h2><p>Técnicas validadas para bajar la presión desde el sistema nervioso</p></div>
  <div class="m-body">
    <p>El estrés crónico mantiene el sistema nervioso simpático activado, lo que eleva la presión arterial de forma sostenida. Estas técnicas activan el sistema parasimpático — el "freno" natural del estrés.</p>
    <div class="m-stitle">Técnica 4-7-8 — La más efectiva para hipertensos</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Siéntate con la espalda recta. Coloca la punta de la lengua detrás de los dientes superiores.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Inhala</strong> silenciosamente por la nariz contando <strong>4 segundos</strong>.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Retén</strong> el aire contando <strong>7 segundos</strong> (sin tensión ni esfuerzo excesivo).</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Exhala</strong> completamente por la boca haciendo un sonido suave durante <strong>8 segundos</strong>.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p>Repite 4 veces. Practica 2 veces al día: mañana y noche. La presión baja de forma measurable en 2 semanas.</p></div>
    </div>
    <div class="m-stitle">Otras estrategias comprobadas</div>
    <div class="m-card"><span class="m-card-icon">🧘</span><div><strong>Yoga restaurativo</strong><p>3 sesiones de 30 min por semana. El yoga con posturas pasivas y respiración profunda reduce la presión sistólica hasta 5 mmHg en 12 semanas. Evita posturas invertidas si tu presión está alta.</p></div></div>
    <div class="m-card"><span class="m-card-icon">😴</span><div><strong>Sueño de calidad</strong><p>Dormir menos de 6 horas eleva la presión hasta 8 mmHg. Mantén horario fijo, oscuridad total y temperatura de 18-20°C. El insomnio crónico es un factor de riesgo independiente para hipertensión.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🎵</span><div><strong>Música y naturaleza</strong><p>Escuchar música clásica o sonidos de naturaleza 30 min/día activa el nervio vago y reduce la presión sistólica 3-5 mmHg. Funciona especialmente bien antes de dormir.</p></div></div>
    <button class="m-btn-close" onclick="cerrarM('m-h3')">Entendido ✓</button>
  </div>
</div></div>

<!-- M1: Nutrición adulto mayor -->
<div class="m-overlay" id="m-m1" onclick="cerrarSi(event,'m-m1')"><div class="m-box">
  <div class="m-hero h-lime"><span class="m-hero-tag">👴 Adulto Mayor · Nutrición</span><h2>Nutrición Esencial para Mayores de 60</h2><p>Qué necesita tu cuerpo después de los 60 y cómo dárselo</p></div>
  <div class="m-body">
    <p>A partir de los 60, el metabolismo cambia: la absorción de nutrientes disminuye, la sensación de sed se reduce y el riesgo de sarcopenia (pérdida de músculo) aumenta. La alimentación se vuelve más importante que nunca.</p>
    <div class="m-stitle">Nutrientes prioritarios después de los 60</div>
    <div class="m-card"><span class="m-card-icon">🦴</span><div><strong>Calcio — 1,200 mg/día en mayores de 70</strong><p>Lácteos bajos en grasa, sardinas con hueso, tofu, brócoli, almendras. Sin calcio suficiente, el cuerpo lo toma de los huesos aumentando el riesgo de osteoporosis.</p></div></div>
    <div class="m-card"><span class="m-card-icon">☀️</span><div><strong>Vitamina D — Fundamental para absorber calcio</strong><p>20 min de sol en brazos al día. Alimentos: salmón, atún, yema de huevo, leche fortificada. El 70% de los adultos mayores tiene déficit de vitamina D.</p></div></div>
    <div class="m-card"><span class="m-card-icon">💪</span><div><strong>Proteína — 1.2g/kg de peso al día</strong><p>Para prevenir la sarcopenia (pérdida de músculo) que aumenta el riesgo de caídas. Una porción de proteína en cada comida: huevo, legumbres, pollo, pescado o lácteos.</p></div></div>
    <div class="m-card"><span class="m-card-icon">💧</span><div><strong>Hidratación — 8 vasos mínimo aunque no tengas sed</strong><p>La sensación de sed disminuye con la edad. La deshidratación en adultos mayores causa confusión, mareos y aumenta el riesgo de caídas. Pon alarmas para recordar beber agua.</p></div></div>
    <div class="m-stitle">Plan de comidas sencillo</div>
    <ul class="m-list">
      <li>Desayuno: avena con leche + fruta + huevo revuelto</li>
      <li>Media mañana: yogur natural con nueces</li>
      <li>Comida: proteína (pollo/pescado) + verduras + legumbres</li>
      <li>Merienda: fruta con queso cottage</li>
      <li>Cena: sopa de verduras + tortilla con frijoles</li>
    </ul>
    <button class="m-btn-close" onclick="cerrarM('m-m1')">Entendido ✓</button>
  </div>
</div></div>

<!-- M2: Ejercicio adulto mayor -->
<div class="m-overlay" id="m-m2" onclick="cerrarSi(event,'m-m2')"><div class="m-box">
  <div class="m-hero h-teal2"><span class="m-hero-tag">👴 Adulto Mayor · Ejercicio</span><h2>Ejercicios de Equilibrio y Fuerza para Mayores</h2><p>Rutina segura paso a paso para prevenir caídas y mantener la independencia</p></div>
  <div class="m-body">
    <p>Las caídas son la principal causa de lesiones graves en adultos mayores. El 45% de las caídas se pueden prevenir con ejercicios específicos de equilibrio y fuerza realizados 3 veces por semana.</p>
    <div class="m-stitle">Rutina diaria — 20 minutos (de pie o sentado)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p><strong>Equilibrio en un pie (2 min):</strong> apóyate en una silla con una mano. Levanta un pie del suelo 10 segundos. Alterna. Con práctica, hazlo sin apoyo.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p><strong>Sentarse y pararse (10 repeticiones):</strong> desde una silla, párate sin usar los brazos si puedes. Lentamente. Este ejercicio fortalece cuádriceps y glúteos — los músculos anti-caída.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p><strong>Elevación de talones (15 repeticiones):</strong> de pie apoyado en la silla, sube en puntillas y baja lentamente. Fortalece pantorrillas y mejora el equilibrio dinámico.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p><strong>Caminata en línea recta (5 min):</strong> camina poniendo un pie delante del otro como si fuera una cuerda floja. Usa una pared si necesitas apoyo.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p><strong>Rotaciones de hombros y cuello (2 min):</strong> círculos suaves hacia adelante y atrás. Mantiene la movilidad articular y previene rigidez.</p></div>
    </div>
    <div class="m-tip"><strong>💡 Tai-chi — El más recomendado por la ciencia</strong>Reduce el riesgo de caídas hasta un 45% (Harvard Medical School). Busca clases en centros comunitarios o parques — muchas son gratuitas. 20 min, 3 veces por semana son suficientes.</div>
    <div class="m-warn"><strong>⚠️ Seguridad primero</strong>Usa calzado con suela antideslizante. Ejercita cerca de una pared o silla de apoyo. Si sientes mareo o dolor en el pecho, detente inmediatamente.</div>
    <button class="m-btn-close" onclick="cerrarM('m-m2')">Entendido ✓</button>
  </div>
</div></div>

<!-- M3: Salud mental adulto mayor -->
<div class="m-overlay" id="m-m3" onclick="cerrarSi(event,'m-m3')"><div class="m-box">
  <div class="m-hero h-yellow"><span class="m-hero-tag">👴 Adulto Mayor · Salud Mental</span><h2>Salud Mental y Social en la Tercera Edad</h2><p>Cómo mantener la mente activa, las conexiones vivas y el ánimo arriba</p></div>
  <div class="m-body">
    <p>La soledad y el aislamiento social aumentan el riesgo de demencia en un 50% y son tan dañinos para la salud como fumar 15 cigarrillos al día. Mantener la mente y las relaciones activas es medicina.</p>
    <div class="m-stitle">Estimulación cognitiva diaria</div>
    <div class="m-card"><span class="m-card-icon">📚</span><div><strong>Leer 30 minutos al día</strong><p>Activa múltiples áreas cerebrales simultáneamente. Cualquier género sirve. Si la vista dificulta la lectura, los audiolibros tienen el mismo beneficio cognitivo.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🧩</span><div><strong>Juegos de memoria y atención</strong><p>Crucigramas, sudoku, ajedrez, dominó o juegos de cartas. 20-30 min diarios. El reto mental estimula la neuroplasticidad — la capacidad del cerebro de formar nuevas conexiones.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🎨</span><div><strong>Aprender algo nuevo</strong><p>Un idioma, un instrumento, pintura, cocina o artesanías. Aprender algo nuevo es el estímulo cognitivo más potente porque obliga al cerebro a crear redes neuronales completamente nuevas.</p></div></div>
    <div class="m-stitle">Conexión social — tan importante como el ejercicio</div>
    <ul class="m-list">
      <li>Grupos de adultos mayores en centros comunitarios: baile, coro, manualidades</li>
      <li>Voluntariado: da propósito, estructura y conexión intergeneracional</li>
      <li>Videollamadas semanales con familia o amigos que viven lejos</li>
      <li>Mascotas: reducen la soledad, bajan la presión y obligan a salir a caminar</li>
    </ul>
    <div class="m-tip"><strong>💡 Habla con tu médico si sientes tristeza</strong>La depresión en adultos mayores es frecuente y subdiagnosticada — no es "normal de la edad". Tiene tratamiento efectivo. No lo normalices ni lo ignores.</div>
    <button class="m-btn-close" onclick="cerrarM('m-m3')">Entendido ✓</button>
  </div>
</div></div>

<!-- E1: Nutrición embarazo -->
<div class="m-overlay" id="m-e1" onclick="cerrarSi(event,'m-e1')"><div class="m-box">
  <div class="m-hero h-rose"><span class="m-hero-tag">🤰 Embarazo · Nutrición</span><h2>Nutrición Durante el Embarazo</h2><p>Guía completa trimestre a trimestre de lo que necesitas tú y tu bebé</p></div>
  <div class="m-body">
    <p>Durante el embarazo no se trata de "comer por dos en cantidad", sino de alimentarte mejor en calidad. Los primeros 1,000 días de vida (desde la concepción) son los más determinantes para la salud futura del bebé.</p>
    <div class="m-stitle">Suplementos esenciales</div>
    <div class="m-card"><span class="m-card-icon">🟢</span><div><strong>Ácido fólico — Desde antes de quedar embarazada</strong><p>400-800 mcg/día. Previene defectos del tubo neural en el bebé. Si no lo tomaste antes del embarazo, empiézalo ahora. Fuentes alimentarias: espinaca, lentejas, aguacate.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🔴</span><div><strong>Hierro — Previene anemia en madre y bebé</strong><p>27 mg/día durante el embarazo. Tómalo con vitamina C para mejor absorción (un vaso de jugo de naranja). Fuentes: carne roja magra, legumbres, espinaca, cereales fortificados.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🦴</span><div><strong>Calcio — Desde el segundo trimestre</strong><p>1,000 mg/día. Si no consumes suficiente, el bebé toma el calcio de tus huesos. Fuentes: lácteos, tofu, sardinas, brócoli, almendras.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🧠</span><div><strong>Omega-3 DHA — Desarrollo cerebral del bebé</strong><p>200-300 mg/día de DHA. Crítico para el desarrollo del cerebro y la retina del bebé. Pescados bajos en mercurio: salmón, sardina, tilapia. Máximo 2-3 porciones por semana.</p></div></div>
    <div class="m-stitle">Alimentos que DEBES evitar</div>
    <ul class="m-list">
      <li>Alcohol: ninguna cantidad es segura durante el embarazo</li>
      <li>Atún en lata frecuente: alto en mercurio. Máximo 1 lata por semana</li>
      <li>Quesos no pasteurizados: riesgo de listeria (brie, camembert, roquefort)</li>
      <li>Carnes crudas o poco cocidas: steak tartar, sushi de pescado crudo, carpaccio</li>
      <li>Embutidos fríos sin calentar: jamón, salami, paté — caliéntalos hasta que humeen</li>
    </ul>
    <button class="m-btn-close" onclick="cerrarM('m-e1')">Entendido ✓</button>
  </div>
</div></div>

<!-- E2: Ejercicio embarazo -->
<div class="m-overlay" id="m-e2" onclick="cerrarSi(event,'m-e2')"><div class="m-box">
  <div class="m-hero h-violet"><span class="m-hero-tag">🤰 Embarazo · Ejercicio Seguro</span><h2>Actividad Física Segura en el Embarazo</h2><p>Rutinas por trimestre adaptadas a cada etapa de tu gestación</p></div>
  <div class="m-body">
    <p>El ejercicio durante el embarazo sin complicaciones reduce el riesgo de diabetes gestacional, mejora el estado de ánimo, facilita el parto y acelera la recuperación postparto. Siempre consulta con tu médico primero.</p>
    <div class="m-stitle">Los mejores ejercicios por trimestre</div>
    <div class="m-card"><span class="m-card-icon">1️⃣</span><div><strong>Primer trimestre (semanas 1-13)</strong><p>Puedes continuar la mayoría de tus actividades habituales. Caminata, natación, yoga prenatal y pilates son ideales. Evita el sobrecalentamiento — nada de saunas ni clases calientes.</p></div></div>
    <div class="m-card"><span class="m-card-icon">2️⃣</span><div><strong>Segundo trimestre (semanas 14-27)</strong><p>Evita ejercicios boca arriba (comprimen la vena cava). Yoga prenatal, natación y caminata son excelentes. El abdomen ya crece — usa ropa cómoda y soporte adecuado.</p></div></div>
    <div class="m-card"><span class="m-card-icon">3️⃣</span><div><strong>Tercer trimestre (semanas 28-40)</strong><p>Reduce la intensidad según sientas. La caminata y la natación son las más seguras. Los ejercicios de Kegel preparan el suelo pélvico para el parto. Descansa cuando tu cuerpo lo pida.</p></div></div>
    <div class="m-stitle">Ejercicios de Kegel paso a paso</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Identifica los músculos del suelo pélvico: son los que usas para detener el flujo de orina.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Contrae esos músculos durante 5 segundos sin contraer glúteos ni abdomen.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Relaja completamente durante 5 segundos.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Repite 10-15 veces. Hazlos 3 veces al día — puedes hacerlos sentada, acostada o de pie.</p></div>
    </div>
    <div class="m-warn"><strong>⚠️ Detente y consulta de inmediato si sientes:</strong> sangrado vaginal, dolor abdominal intenso, contracciones regulares, mareo intenso, dificultad para respirar o reducción de movimientos del bebé.</div>
    <button class="m-btn-close" onclick="cerrarM('m-e2')">Entendido ✓</button>
  </div>
</div></div>

<!-- E3: Bienestar emocional embarazo -->
<div class="m-overlay" id="m-e3" onclick="cerrarSi(event,'m-e3')"><div class="m-box">
  <div class="m-hero h-peach"><span class="m-hero-tag">🤰 Embarazo · Bienestar Emocional</span><h2>Bienestar Emocional Durante la Gestación</h2><p>Cómo cuidar tu salud mental y crear el vínculo con tu bebé desde el vientre</p></div>
  <div class="m-body">
    <p>El bienestar emocional de la mamá afecta directamente el desarrollo del bebé. El estrés crónico durante el embarazo se asocia con parto prematuro, bajo peso al nacer y mayor ansiedad en el bebé al crecer.</p>
    <div class="m-stitle">Técnica de respiración Lamaze (para el parto)</div>
    <div class="m-steps">
      <div class="m-step"><span class="m-step-n">1</span><p>Con el inicio de una contracción, enfoca tu atención en un punto fijo.</p></div>
      <div class="m-step"><span class="m-step-n">2</span><p>Inhala lentamente por la nariz mientras la contracción aumenta.</p></div>
      <div class="m-step"><span class="m-step-n">3</span><p>Exhala lentamente por la boca durante la contracción. Mantén el ritmo constante.</p></div>
      <div class="m-step"><span class="m-step-n">4</span><p>Cuando la contracción pase, respira normal y descansa completamente.</p></div>
      <div class="m-step"><span class="m-step-n">5</span><p>Practica este patrón 10 minutos al día desde el 7° mes para que sea automático durante el parto.</p></div>
    </div>
    <div class="m-stitle">Vinculación prenatal con tu bebé</div>
    <div class="m-card"><span class="m-card-icon">🗣️</span><div><strong>Háblale desde la semana 18</strong><p>El bebé reconoce la voz de mamá desde el útero. Léele cuentos, cuéntale tu día o simplemente háblale. Esto fortalece el vínculo desde antes del nacimiento.</p></div></div>
    <div class="m-card"><span class="m-card-icon">🎵</span><div><strong>Música clásica suave</strong><p>El bebé puede escuchar desde la semana 20. Mozart, Bach o música de cuna. Evita volúmenes altos — sus oídos son sensibles. El ritmo de la música sincroniza con sus movimientos.</p></div></div>
    <div class="m-card"><span class="m-card-icon">✋</span><div><strong>Masaje abdominal suave</strong><p>Con aceite de almendras o coco, masajea suavemente el abdomen con movimientos circulares. Reduce las estrías, relaja el útero y crea un momento íntimo de conexión.</p></div></div>
    <div class="m-tip"><strong>💡 La ansiedad prenatal es normal — pero tiene límite</strong>Es normal sentir preocupación. Pero si la ansiedad te impide dormir, comer o disfrutar el embarazo, habla con tu médico o busca apoyo psicológico perinatal. No tienes que vivirlo sola.</div>
    <button class="m-btn-close" onclick="cerrarM('m-e3')">Entendido ✓</button>
  </div>
</div></div>

<!-- Modal logout -->
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
// Filtro categorías
document.querySelectorAll('.cat-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    const cat = this.dataset.cat;
    document.querySelectorAll('.content-card').forEach(card => {
      const cats = card.dataset.cat.split(' ');
      if (cat === 'todos' || cats.includes(cat)) {
        card.style.display = '';
        card.style.animation = 'none';
        card.offsetHeight;
        card.style.animation = 'fadeSlideIn 0.4s ease both';
      } else {
        card.style.display = 'none';
      }
    });
  });
});

// Modales
function abrirM(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}
function cerrarM(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}
function cerrarSi(e, id) {
  if (e.target === document.getElementById(id)) cerrarM(id);
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.m-overlay.open').forEach(el => el.classList.remove('open'));
    document.body.style.overflow = '';
  }
});

// Logout
function abrirModal() { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal() { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout() { window.location.href = '/controllers/LogoutController.php'; }
</script>
</body>
</html>