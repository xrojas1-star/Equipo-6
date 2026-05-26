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
    <title>Vitalis | Bienestar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/bienestar.css?v=6">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js?v=4"></script>
    <style>
    .b-overlay {
        position:fixed;inset:0;background:rgba(0,0,0,0.55);
        z-index:10000;display:none;justify-content:center;
        align-items:flex-start;padding:24px 16px;overflow-y:auto;
    }
    .b-overlay.open{display:flex;}
    .b-modal {
        background:#fff;border-radius:22px;max-width:700px;
        width:100%;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,0.2);
        overflow:hidden;animation:bSlide 0.35s ease;
    }
    @keyframes bSlide{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
    .b-hero{padding:26px 28px 22px;color:#fff;}
    .b-tag{display:inline-block;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.4);
        padding:3px 12px;border-radius:999px;font-size:11px;font-weight:700;
        font-family:'Outfit',sans-serif;margin-bottom:10px;}
    .b-hero h2{font-size:20px;font-weight:800;font-family:'Outfit',sans-serif;margin-bottom:4px;line-height:1.3;}
    .b-hero p{font-size:13px;font-family:'Outfit',sans-serif;opacity:0.85;margin:0;}
    /* hero colors */
    .bh1{background:linear-gradient(135deg,#2bbbad,#00897b);}
    .bh2{background:linear-gradient(135deg,#26c6da,#006064);}
    .bh3{background:linear-gradient(135deg,#66bb6a,#1b5e20);}
    .bh4{background:linear-gradient(135deg,#29b6f6,#0277bd);}
    .bh5{background:linear-gradient(135deg,#4fc3f7,#01579b);}
    .bh6{background:linear-gradient(135deg,#80deea,#006064);}
    .bh7{background:linear-gradient(135deg,#ef5350,#b71c1c);}
    .bh8{background:linear-gradient(135deg,#ffa726,#e65100);}
    .bh9{background:linear-gradient(135deg,#ff7043,#bf360c);}
    .bh10{background:linear-gradient(135deg,#ec407a,#880e4f);}
    .bh11{background:linear-gradient(135deg,#ba68c8,#6a1b9a);}
    .bh12{background:linear-gradient(135deg,#f06292,#c2185b);}
    .bh13{background:linear-gradient(135deg,#66bb6a,#1b5e20);}
    .bh14{background:linear-gradient(135deg,#26a69a,#004d40);}
    .bh15{background:linear-gradient(135deg,#ffca28,#f57f17);}
    .bh16{background:linear-gradient(135deg,#f48fb1,#880e4f);}
    .bh17{background:linear-gradient(135deg,#ce93d8,#6a1b9a);}
    .bh18{background:linear-gradient(135deg,#ffcc80,#e65100);}

    .b-body{padding:22px 28px 28px;}
    .b-body>p{font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;line-height:1.7;margin-bottom:12px;}
    .b-stitle{font-size:15px;font-weight:800;color:#1a2332;font-family:'Outfit',sans-serif;
        padding-left:10px;border-left:3px solid #2bbbad;margin:18px 0 10px;}

    .b-steps{display:flex;flex-direction:column;gap:8px;margin-bottom:14px;}
    .b-step{display:flex;gap:10px;align-items:flex-start;background:#f0fffe;
        border:1px solid rgba(43,187,173,0.2);border-radius:10px;padding:10px 12px;}
    .b-step-n{width:22px;height:22px;border-radius:50%;
        background:linear-gradient(135deg,#2bbbad,#00897b);color:#fff;
        font-size:11px;font-weight:700;font-family:'Outfit',sans-serif;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .b-step p{font-size:13px;color:#4a5568;font-family:'Outfit',sans-serif;line-height:1.5;margin:0;}
    .b-step p strong{color:#1a2332;}

    .b-card{display:flex;gap:12px;background:#f8fffe;border:1px solid rgba(43,187,173,0.18);
        border-radius:12px;padding:13px 15px;margin-bottom:10px;}
    .b-card-icon{font-size:24px;flex-shrink:0;}
    .b-card strong{display:block;font-size:13px;font-weight:700;color:#1a2332;
        font-family:'Outfit',sans-serif;margin-bottom:4px;}
    .b-card p{font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;line-height:1.6;margin:0;}

    .b-list{list-style:none;padding:0;margin:0 0 14px;}
    .b-list li{padding:7px 0 7px 22px;position:relative;border-bottom:1px solid #f0f4f8;
        font-size:13px;color:#4a5568;font-family:'Outfit',sans-serif;line-height:1.5;}
    .b-list li:last-child{border-bottom:none;}
    .b-list li::before{content:"✔";position:absolute;left:0;color:#2bbbad;font-size:11px;top:8px;}

    .b-tip{background:#e8f8f7;border:1px solid #b2dfdb;border-radius:12px;
        padding:12px 16px;margin:12px 0;font-size:13px;color:#00695c;
        font-family:'Outfit',sans-serif;line-height:1.6;}
    .b-tip strong{display:block;margin-bottom:4px;font-size:14px;}
    .b-warn{background:#fff3e0;border:1px solid #ffcc80;border-radius:12px;
        padding:12px 16px;margin:12px 0;font-size:13px;color:#e65100;
        font-family:'Outfit',sans-serif;line-height:1.6;}
    .b-warn strong{display:block;margin-bottom:4px;}

    .b-btn{display:block;width:100%;margin-top:18px;
        background:linear-gradient(135deg,#2bbbad,#00897b);color:#fff;
        border:none;padding:13px;border-radius:999px;font-size:14px;
        font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
        transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 4px 14px rgba(43,187,173,0.3);}
    .b-btn:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(43,187,173,0.4);}

    /* dark mode */
    body.dark-mode .b-modal{background:#0d1117;}
    body.dark-mode .b-stitle{color:#e2e8f0;}
    body.dark-mode .b-body>p{color:#94a3b8;}
    body.dark-mode .b-step{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .b-step p{color:#94a3b8;}
    body.dark-mode .b-step p strong{color:#e2e8f0;}
    body.dark-mode .b-card{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .b-card strong{color:#e2e8f0;}
    body.dark-mode .b-card p{color:#94a3b8;}
    body.dark-mode .b-list li{color:#94a3b8;border-bottom-color:#1e2830;}
    body.dark-mode .b-tip{background:#0d2a26;border-color:#134e4a;color:#4ade80;}
    body.dark-mode .b-warn{background:#1e1200;border-color:#e65100;color:#ffa726;}
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
    <div class="panel-header-main">
        <h2 class="section-title">Tu Panel de Bienestar</h2>
        <p class="panel-subtitle">Rutinas de bienestar adaptadas a cada persona</p>
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

    <div class="cards-container" id="bienCards">

        <!-- NORMAL -->
        <div class="info-card" data-cat="normal todos">
            <div class="card-header" style="background:linear-gradient(135deg,rgba(0,0,0,0.55),rgba(0,0,0,0.65)),url('../img/bienestar.png') center/cover;">
                <span class="card-tag">💪 Personas Sanas</span><h3>Bienestar Holístico: El Arte del Balance</h3>
            </div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Técnicas de equilibrio mente-cuerpo</li><li><span class="bullet-check">✔</span> Prácticas de auto-cuidado diario</li><li><span class="bullet-check">✔</span> Rituales de noche para un buen sueño</li><li><span class="bullet-check">✔</span> Journaling: escribe 5 min al despertar</li></ul>
                <button class="btn-primary" onclick="abrirB('b1')">Explorar Prácticas</button>
            </div>
        </div>

        <div class="info-card" data-cat="normal todos">
            <div class="card-header" style="background:linear-gradient(135deg,rgba(0,0,0,0.55),rgba(0,0,0,0.65)),url('../img/salud_mental.png') center/cover;">
                <span class="card-tag">💪 Personas Sanas</span><h3>Salud Mental & Resiliencia Emocional</h3>
            </div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Ejercicios de gratitud diarios</li><li><span class="bullet-check">✔</span> Manejo de la ansiedad y el estrés</li><li><span class="bullet-check">✔</span> Construyendo relaciones positivas</li><li><span class="bullet-check">✔</span> Método 5-4-3-2-1 contra la ansiedad</li></ul>
                <button class="btn-primary" onclick="abrirB('b2')">Aprender Más</button>
            </div>
        </div>

        <div class="info-card" data-cat="normal todos">
            <div class="card-header" style="background:linear-gradient(135deg,rgba(0,0,0,0.55),rgba(0,0,0,0.65)),url('../img/entorno.png') center/cover;">
                <span class="card-tag">💪 Personas Sanas</span><h3>Entorno & Bienestar Ambiental</h3>
            </div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Organización para la calma mental</li><li><span class="bullet-check">✔</span> Ergonomía y micro-descansos</li><li><span class="bullet-check">✔</span> Plantas purificadoras de aire en casa</li><li><span class="bullet-check">✔</span> Aromaterapia: lavanda para el estrés</li></ul>
                <button class="btn-primary" onclick="abrirB('b3')">Crear tu Espacio</button>
            </div>
        </div>

        <!-- ASMA -->
        <div class="info-card" data-cat="asma todos">
            <div class="card-header asma-grad"><span class="card-tag">🌬️ Asma</span><h3>Bienestar Respiratorio y Calidad de Vida</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Respiración con labios fruncidos para calmar crisis</li><li><span class="bullet-check">✔</span> Yoga pranayama adaptado para asmáticos</li><li><span class="bullet-check">✔</span> Canto o tocar instrumento de viento: fortalece pulmones</li><li><span class="bullet-check">✔</span> Mantén diario de síntomas y desencadenantes</li></ul>
                <button class="btn-primary" onclick="abrirB('b4')">Explorar Técnicas</button>
            </div>
        </div>

        <div class="info-card" data-cat="asma todos">
            <div class="card-header asma-grad2"><span class="card-tag">🌬️ Asma</span><h3>Entorno Libre de Alérgenos</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Purificador de aire HEPA en dormitorio</li><li><span class="bullet-check">✔</span> Humedad relativa 40-50%: evita moho y ácaros</li><li><span class="bullet-check">✔</span> Ropa de cama antialérgica lavada semanalmente</li><li><span class="bullet-check">✔</span> Evita velas aromáticas e incienso</li></ul>
                <button class="btn-primary" onclick="abrirB('b5')">Guía del Hogar</button>
            </div>
        </div>

        <div class="info-card" data-cat="asma todos">
            <div class="card-header asma-grad3"><span class="card-tag">🌬️ Asma</span><h3>Mindfulness para Controlar el Estrés y el Asma</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> El estrés emocional puede desencadenar crisis</li><li><span class="bullet-check">✔</span> Meditación guiada 10 min reduce ansiedad respiratoria</li><li><span class="bullet-check">✔</span> Biofeedback para aprender a controlar la respiración</li><li><span class="bullet-check">✔</span> Grupos de apoyo para asmáticos: no estás solo</li></ul>
                <button class="btn-primary" onclick="abrirB('b6')">Empezar Meditación</button>
            </div>
        </div>

        <!-- DIABETES -->
        <div class="info-card" data-cat="diabetes todos">
            <div class="card-header diabetes-grad"><span class="card-tag">🩸 Diabetes</span><h3>Bienestar Emocional con Diabetes</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> La "fatiga diabética" es real: busca apoyo</li><li><span class="bullet-check">✔</span> Grupos de apoyo presenciales y online</li><li><span class="bullet-check">✔</span> Mindfulness reduce niveles de cortisol y glucosa</li><li><span class="bullet-check">✔</span> Celebra pequeños logros en tu control glucémico</li></ul>
                <button class="btn-primary" onclick="abrirB('b7')">Apoyo Emocional</button>
            </div>
        </div>

        <div class="info-card" data-cat="diabetes todos">
            <div class="card-header diabetes-grad2"><span class="card-tag">🩸 Diabetes</span><h3>Rutinas Diarias para el Control Glucémico</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Mismos horarios de comida cada día</li><li><span class="bullet-check">✔</span> Caminata 15 min post-comida baja glucosa 30%</li><li><span class="bullet-check">✔</span> Sueño 7-8h: el insomnio dispara glucosa</li><li><span class="bullet-check">✔</span> Reducir alcohol: cada copa eleva glucosa</li></ul>
                <button class="btn-primary" onclick="abrirB('b8')">Ver Rutinas</button>
            </div>
        </div>

        <div class="info-card" data-cat="diabetes todos">
            <div class="card-header diabetes-grad3"><span class="card-tag">🩸 Diabetes</span><h3>Tecnología para tu Bienestar Diabético</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Apps de conteo de carbohidratos: MyFitnessPal</li><li><span class="bullet-check">✔</span> Monitores continuos de glucosa (CGM)</li><li><span class="bullet-check">✔</span> Relojes inteligentes para alertas de glucosa</li><li><span class="bullet-check">✔</span> Telemedicina: consultas desde casa</li></ul>
                <button class="btn-primary" onclick="abrirB('b9')">Explorar Tecnología</button>
            </div>
        </div>

        <!-- HIPERTENSIÓN -->
        <div class="info-card" data-cat="hipertension todos">
            <div class="card-header hiper-grad"><span class="card-tag">❤️ Hipertensión</span><h3>Relajación Activa para Reducir la Presión</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Respiración profunda 4-7-8: baja presión en minutos</li><li><span class="bullet-check">✔</span> Música clásica o de naturaleza: reduce 5-10 mmHg</li><li><span class="bullet-check">✔</span> Baño tibio antes de dormir: relaja vasos sanguíneos</li><li><span class="bullet-check">✔</span> Masaje de cuello y hombros reduce tensión arterial</li></ul>
                <button class="btn-primary" onclick="abrirB('b10')">Técnicas de Relajación</button>
            </div>
        </div>

        <div class="info-card" data-cat="hipertension todos">
            <div class="card-header hiper-grad2"><span class="card-tag">❤️ Hipertensión</span><h3>Yoga y Tai-Chi para Hipertensos</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Yoga restaurativo: posturas pasivas y respiración</li><li><span class="bullet-check">✔</span> Tai-chi 30 min/día reduce presión sistólica</li><li><span class="bullet-check">✔</span> Evita posturas invertidas si presión alta</li><li><span class="bullet-check">✔</span> Qigong: movimiento suave con beneficios cardíacos</li></ul>
                <button class="btn-primary" onclick="abrirB('b11')">Empezar Yoga</button>
            </div>
        </div>

        <div class="info-card" data-cat="hipertension todos">
            <div class="card-header hiper-grad3"><span class="card-tag">❤️ Hipertensión</span><h3>Sueño y Recuperación Cardiovascular</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Dormir menos de 6h eleva presión hasta 8 mmHg</li><li><span class="bullet-check">✔</span> Oscuridad total en el cuarto mejora calidad del sueño</li><li><span class="bullet-check">✔</span> Temperatura ideal para dormir: 18-20°C</li><li><span class="bullet-check">✔</span> Evalúa apnea del sueño: empeora hipertensión</li></ul>
                <button class="btn-primary" onclick="abrirB('b12')">Mejorar el Sueño</button>
            </div>
        </div>

        <!-- ADULTO MAYOR -->
        <div class="info-card" data-cat="mayor todos">
            <div class="card-header mayor-grad"><span class="card-tag">👴 Adulto Mayor</span><h3>Bienestar Social y Propósito de Vida</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Voluntariado: da sentido y conexión social</li><li><span class="bullet-check">✔</span> Grupos de baile, coro o manualidades</li><li><span class="bullet-check">✔</span> Aprender algo nuevo cada mes: idioma, instrumento</li><li><span class="bullet-check">✔</span> Mascotas: reducen soledad y estrés</li></ul>
                <button class="btn-primary" onclick="abrirB('b13')">Actividades Sociales</button>
            </div>
        </div>

        <div class="info-card" data-cat="mayor todos">
            <div class="card-header mayor-grad2"><span class="card-tag">👴 Adulto Mayor</span><h3>Movilidad y Flexibilidad en la Tercera Edad</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Estiramientos suaves cada mañana: 10 minutos</li><li><span class="bullet-check">✔</span> Pilates de silla: fortalece sin impacto</li><li><span class="bullet-check">✔</span> Hidroterapia: ejercicio en agua reduce dolor articular</li><li><span class="bullet-check">✔</span> Bastón si es necesario: no es vergüenza, es seguridad</li></ul>
                <button class="btn-primary" onclick="abrirB('b14')">Ejercicios de Silla</button>
            </div>
        </div>

        <div class="info-card" data-cat="mayor todos">
            <div class="card-header mayor-grad3"><span class="card-tag">👴 Adulto Mayor</span><h3>Bienestar Espiritual y Paz Mental</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Meditación y oración: fuente de paz interior</li><li><span class="bullet-check">✔</span> Escribir memorias: gratitud por la vida vivida</li><li><span class="bullet-check">✔</span> Naturaleza: paseos en parque mejoran el ánimo</li><li><span class="bullet-check">✔</span> Habla con tu médico si sientes tristeza persistente</li></ul>
                <button class="btn-primary" onclick="abrirB('b15')">Paz Interior</button>
            </div>
        </div>

        <!-- EMBARAZO -->
        <div class="info-card" data-cat="embarazo todos">
            <div class="card-header emb-grad"><span class="card-tag">🤰 Embarazo</span><h3>Bienestar Emocional durante la Gestación</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> La ansiedad prenatal es normal: busca apoyo</li><li><span class="bullet-check">✔</span> Grupos de mamás: comparte experiencias</li><li><span class="bullet-check">✔</span> Habla con tu pareja: el apoyo reduce el estrés</li><li><span class="bullet-check">✔</span> Psicología perinatal si sientes depresión</li></ul>
                <button class="btn-primary" onclick="abrirB('b16')">Apoyo Emocional</button>
            </div>
        </div>

        <div class="info-card" data-cat="embarazo todos">
            <div class="card-header emb-grad2"><span class="card-tag">🤰 Embarazo</span><h3>Yoga Prenatal y Descanso Activo</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Yoga prenatal desde el 2° trimestre</li><li><span class="bullet-check">✔</span> Técnica de respiración Lamaze para el parto</li><li><span class="bullet-check">✔</span> Postura de dormir: lado izquierdo con almohada</li><li><span class="bullet-check">✔</span> Baños tibios (no calientes) relajan la espalda</li></ul>
                <button class="btn-primary" onclick="abrirB('b17')">Yoga Prenatal</button>
            </div>
        </div>

        <div class="info-card" data-cat="embarazo todos">
            <div class="card-header emb-grad3"><span class="card-tag">🤰 Embarazo</span><h3>Vinculación Prenatal con tu Bebé</h3></div>
            <div class="card-content">
                <ul><li><span class="bullet-check">✔</span> Háblale a tu bebé: reconoce tu voz desde semana 18</li><li><span class="bullet-check">✔</span> Música clásica suave estimula el desarrollo fetal</li><li><span class="bullet-check">✔</span> Masaje abdominal suave: fortalece el vínculo</li><li><span class="bullet-check">✔</span> Diario de embarazo: recuerda cada momento</li></ul>
                <button class="btn-primary" onclick="abrirB('b18')">Vinculación Prenatal</button>
            </div>
        </div>

    </div>

    <div class="bottom-grid">
        <div class="bottom-card">
            <h3>💡 Micro-Pasos de Bienestar</h3>
            <div class="step-item"><label><span>💧</span> 5 min de respiración profunda</label><input type="checkbox" checked></div>
            <div class="step-item"><label><span>🚶</span> Sal a la luz del día</label><input type="checkbox"></div>
            <div class="step-item"><label><span>📖</span> Lee 10 minutos antes de dormir</label><input type="checkbox"></div>
            <div class="step-item"><label><span>🙏</span> Escribe 3 cosas por las que estás agradecido</label><input type="checkbox"></div>
            <div class="step-item"><label><span>📵</span> Sin pantallas 1 hora antes de dormir</label><input type="checkbox"></div>
        </div>
        <div class="bottom-card">
            <h3>🤝 Comunidad de Bienestar</h3>
            <p class="community-desc">¿Tienes una práctica de bienestar que cambió tu vida? ¡Compártela!</p>
            <textarea placeholder="Comparte tu práctica o progreso..."></textarea>
            <button class="btn-send">Enviar</button>
        </div>
    </div>
</div>

<!-- ══ 18 MODALES ══ -->

<!-- B1: Bienestar Holístico -->
<div class="b-overlay" id="b1" onclick="cerrarSi(event,'b1')"><div class="b-modal">
  <div class="b-hero bh1"><span class="b-tag">💪 Personas Sanas · Balance</span><h2>Bienestar Holístico: El Arte del Balance</h2><p>Prácticas diarias para equilibrar mente, cuerpo y entorno</p></div>
  <div class="b-body">
    <p>El bienestar holístico no es un destino, es una práctica diaria. Se trata de pequeñas acciones consistentes que suman grandes cambios con el tiempo.</p>
    <div class="b-stitle">Rutina matutina de 15 minutos</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Journaling (5 min):</strong> Al despertar, antes del teléfono, escribe 3 cosas por las que estás agradecido y 1 intención para el día.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Respiración consciente (5 min):</strong> Inhala 4 seg, retén 4 seg, exhala 4 seg. Repite 10 veces. Activa el sistema nervioso parasimpático.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Movimiento suave (5 min):</strong> Estiramientos, rotaciones articulares o yoga básico. Despierta el cuerpo gradualmente antes de cualquier actividad.</p></div>
    </div>
    <div class="b-stitle">Rituales nocturnos para un sueño profundo</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Apaga pantallas 60 min antes de dormir — la luz azul bloquea la melatonina hasta 3 horas.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Baño tibio 20 min antes de acostarte — la bajada de temperatura corporal induce el sueño.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Lectura de papel o meditación guiada — no revisión de redes sociales.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Oscuridad total y temperatura de 18-20°C en el cuarto para sueño profundo.</p></div>
    </div>
    <div class="b-tip"><strong>💡 La regla de los 2 minutos</strong>Si una práctica de bienestar toma menos de 2 minutos, hazla ahora. La consistencia diaria de acciones pequeñas supera siempre a los grandes esfuerzos ocasionales.</div>
    <button class="b-btn" onclick="cerrarB('b1')">Entendido ✓</button>
  </div>
</div></div>

<!-- B2: Salud Mental -->
<div class="b-overlay" id="b2" onclick="cerrarSi(event,'b2')"><div class="b-modal">
  <div class="b-hero bh2"><span class="b-tag">💪 Personas Sanas · Mente</span><h2>Salud Mental & Resiliencia Emocional</h2><p>Herramientas prácticas para manejar el estrés y la ansiedad</p></div>
  <div class="b-body">
    <p>La resiliencia no es no sentir estrés — es recuperarse de él con herramientas concretas. Estas técnicas están respaldadas por la psicología cognitivo-conductual.</p>
    <div class="b-stitle">Método 5-4-3-2-1 para la ansiedad</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Nombra <strong>5 cosas que puedes VER</strong> a tu alrededor ahora mismo.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Nombra <strong>4 cosas que puedes TOCAR</strong> físicamente. Tócalas.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Nombra <strong>3 cosas que puedes ESCUCHAR</strong> en este momento.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Nombra <strong>2 cosas que puedes OLER</strong> o que te gustan oler.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p>Nombra <strong>1 cosa que puedes SABOREAR</strong>. Esto ancla la mente al presente y corta el ciclo de ansiedad.</p></div>
    </div>
    <div class="b-stitle">Práctica de gratitud diaria</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Cada noche, antes de dormir, escribe 3 cosas específicas por las que estás agradecido hoy.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Sé específico: no "mi familia" sino "la llamada de mi mamá esta tarde". La especificidad activa más el sistema de recompensa cerebral.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>En 21 días continuos, el cerebro empieza a buscar activamente lo positivo en lugar de lo negativo.</p></div>
    </div>
    <div class="b-stitle">Construyendo relaciones positivas</div>
    <ul class="b-list">
      <li>Practica la escucha activa: no interrumpas, haz preguntas, resume lo que escuchaste</li>
      <li>Establece límites sanos: decir "no" con respeto es un acto de cuidado propio y del otro</li>
      <li>Invierte 15 min al día en una conversación significativa sin teléfono</li>
      <li>Reduce el tiempo con personas que drenan tu energía sistemáticamente</li>
    </ul>
    <div class="b-tip"><strong>💡 Busca apoyo profesional si</strong>La ansiedad o tristeza dura más de 2 semanas, interfiere con el trabajo o las relaciones, o sientes que no puedes manejarlo solo. La psicoterapia es tan efectiva como la medicación para muchos trastornos del estado de ánimo.</div>
    <button class="b-btn" onclick="cerrarB('b2')">Entendido ✓</button>
  </div>
</div></div>

<!-- B3: Entorno -->
<div class="b-overlay" id="b3" onclick="cerrarSi(event,'b3')"><div class="b-modal">
  <div class="b-hero bh3"><span class="b-tag">💪 Personas Sanas · Entorno</span><h2>Crea tu Espacio de Bienestar</h2><p>Cómo transformar tu hogar en un ambiente que apoye tu salud</p></div>
  <div class="b-body">
    <p>Tu entorno físico afecta directamente tu estado mental. Un espacio ordenado reduce el cortisol, mejora la concentración y favorece el descanso.</p>
    <div class="b-stitle">Organización para la calma mental</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Regla de los 5 minutos:</strong> Cada día dedica 5 minutos a ordenar un área específica. En una semana tu espacio cambia completamente sin abrumar.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Un lugar para cada cosa:</strong> Los objetos sin lugar fijo generan estrés visual constante. Define un hogar para cada elemento de tu espacio.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Superficies despejadas:</strong> Una superficie libre de objetos en tu habitación principal reduce la carga cognitiva al despertar.</p></div>
    </div>
    <div class="b-stitle">Plantas purificadoras de aire recomendadas</div>
    <div class="b-card"><span class="b-card-icon">🌿</span><div><strong>Pothos (Epipremnum aureum)</strong><p>Casi indestructible, purifica formaldehído y benceno. Ideal para oficinas y dormitorios. Riégala cada 7-10 días y colócala en luz indirecta.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🌵</span><div><strong>Sábila (Aloe vera)</strong><p>Purifica el aire nocturno liberando oxígeno de noche. Perfecta para el dormitorio. Necesita poca agua y luz directa ocasional.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🪴</span><div><strong>Lavanda</strong><p>Su aroma reduce el cortisol y mejora la calidad del sueño. Colócala en el cuarto. Estudios de la Universidad de Miami muestran reducción de ansiedad con exposición al aroma de lavanda.</p></div></div>
    <div class="b-stitle">Ergonomía y micro-descansos</div>
    <ul class="b-list">
      <li>Pantalla del computador a la altura de los ojos — evita tensión cervical</li>
      <li>Cada 30 min: levántate, camina 2 min y estira cuello y hombros</li>
      <li>Silla con soporte lumbar o cojín en la zona baja de la espalda</li>
      <li>Iluminación natural siempre que sea posible — reduce fatiga visual y mejora el ánimo</li>
    </ul>
    <button class="b-btn" onclick="cerrarB('b3')">Entendido ✓</button>
  </div>
</div></div>

<!-- B4: Bienestar Respiratorio Asma -->
<div class="b-overlay" id="b4" onclick="cerrarSi(event,'b4')"><div class="b-modal">
  <div class="b-hero bh4"><span class="b-tag">🌬️ Asma · Respiración</span><h2>Bienestar Respiratorio y Calidad de Vida</h2><p>Técnicas respiratorias y actividades que fortalecen tus pulmones</p></div>
  <div class="b-body">
    <p>Con las técnicas correctas, las personas con asma pueden llevar una vida completamente activa. El entrenamiento respiratorio mejora la capacidad pulmonar y reduce la frecuencia de crisis.</p>
    <div class="b-stitle">Técnica de respiración con labios fruncidos</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Relaja completamente los hombros y el cuello.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Inhala suavemente por la nariz durante 2 segundos.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Frunce los labios como si fueras a soplar una vela que está lejos.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Exhala lentamente durante 4 segundos por la boca fruncida. Esta resistencia mantiene las vías aéreas abiertas más tiempo.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p>Practica 10 minutos al día. Úsala también al inicio de síntomas antes del inhalador de rescate.</p></div>
    </div>
    <div class="b-stitle">Yoga Pranayama para asmáticos</div>
    <div class="b-card"><span class="b-card-icon">🧘</span><div><strong>Respiración Nadi Shodhana (fosas nasales alternas)</strong><p>Tapa la fosa derecha con el pulgar. Inhala por la izquierda 4 seg. Tapa ambas 4 seg. Exhala por la derecha 4 seg. Alterna. 5 minutos al día reduce la broncoconstricción.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🎵</span><div><strong>Cantar o tocar instrumento de viento</strong><p>El canto y el clarinete, flauta u otros instrumentos de viento fortalecen el diafragma y mejoran el control de la respiración. 15-20 min al día tiene efecto similar a la fisioterapia respiratoria.</p></div></div>
    <div class="b-tip"><strong>💡 Diario de síntomas</strong>Registra diariamente: hora, síntomas, posible desencadenante, medicación usada y resultado. En 2 semanas identificarás tus patrones personales y podrás anticipar las crisis.</div>
    <button class="b-btn" onclick="cerrarB('b4')">Entendido ✓</button>
  </div>
</div></div>

<!-- B5: Entorno Alérgenos -->
<div class="b-overlay" id="b5" onclick="cerrarSi(event,'b5')"><div class="b-modal">
  <div class="b-hero bh5"><span class="b-tag">🌬️ Asma · Entorno</span><h2>Guía del Hogar Libre de Alérgenos</h2><p>Cómo adaptar cada área de tu casa para respirar mejor</p></div>
  <div class="b-body">
    <p>El 90% de las crisis asmáticas ocurren en interiores. Estas son las adaptaciones más efectivas por zona del hogar.</p>
    <div class="b-stitle">Dormitorio — zona de máxima prioridad</div>
    <ul class="b-list">
      <li>Fundas antiácaros certificadas en colchón, almohadas y edredón</li>
      <li>Lava ropa de cama semanal en agua a más de 60°C</li>
      <li>Purificador HEPA en el cuarto — cámbialo cada 6-12 meses</li>
      <li>Sin alfombras, peluches ni cortinas pesadas en el dormitorio</li>
      <li>Humedad entre 40-50% — usa deshumidificador si supera ese nivel</li>
    </ul>
    <div class="b-stitle">Sala y cocina</div>
    <ul class="b-list">
      <li>Usa aspiradora con filtro HEPA — las normales esparcen el polvo</li>
      <li>Campana extractora siempre encendida al cocinar</li>
      <li>Ninguna vela aromática, incienso ni ambientador en spray — irritan las vías aéreas</li>
      <li>Limpieza con productos sin fragancia, sin cloro ni amoniaco</li>
    </ul>
    <div class="b-stitle">Si tienes mascotas</div>
    <ul class="b-list">
      <li>Mascotas fuera del dormitorio siempre</li>
      <li>Báñalas al menos una vez por semana</li>
      <li>Usa purificador HEPA en las zonas donde pasan más tiempo</li>
      <li>Considera test de alergia para confirmar si son un desencadenante real</li>
    </ul>
    <div class="b-warn"><strong>⚠️ Días de alta contaminación</strong>Revisa el índice de calidad del aire de tu ciudad antes de salir. En días malos usa mascarilla KN95 o N95 y reduce el tiempo al aire libre.</div>
    <button class="b-btn" onclick="cerrarB('b5')">Entendido ✓</button>
  </div>
</div></div>

<!-- B6: Mindfulness Asma -->
<div class="b-overlay" id="b6" onclick="cerrarSi(event,'b6')"><div class="b-modal">
  <div class="b-hero bh6"><span class="b-tag">🌬️ Asma · Mindfulness</span><h2>Mindfulness para el Asma</h2><p>Cómo reducir el estrés que dispara las crisis asmáticas</p></div>
  <div class="b-body">
    <p>El estrés emocional activa el sistema nervioso simpático, que contrae los bronquios. Las técnicas de mindfulness activan el parasimpático — el relajante natural de las vías respiratorias.</p>
    <div class="b-stitle">Meditación guiada de 10 minutos</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Siéntate con la espalda recta o acuéstate. Cierra los ojos.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Lleva la atención a tu respiración sin intentar controlarla. Solo obsérvala.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Nota el aire entrando fresco por la nariz y saliendo tibio por la boca.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Cuando tu mente se distraiga — y lo hará — regresa suavemente a la respiración sin juzgarte.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p>Practica 10 minutos al día. En 8 semanas los estudios muestran reducción medible de la broncoconstricción inducida por estrés.</p></div>
    </div>
    <div class="b-stitle">Biofeedback respiratorio</div>
    <div class="b-card"><span class="b-card-icon">📱</span><div><strong>Apps de biofeedback respiratorio</strong><p>Breath Ball, Breathwrk o Kardia te muestran en tiempo real cómo tu respiración afecta tu frecuencia cardíaca. Esto te permite aprender a controlar tu sistema nervioso con práctica.</p></div></div>
    <div class="b-card"><span class="b-card-icon">👥</span><div><strong>Grupos de apoyo para asmáticos</strong><p>Compartir experiencias con otros asmáticos reduce la ansiedad anticipatoria (el miedo a tener una crisis). Busca grupos en centros de salud, hospitales o en línea. La comunidad es medicina.</p></div></div>
    <button class="b-btn" onclick="cerrarB('b6')">Entendido ✓</button>
  </div>
</div></div>

<!-- B7: Bienestar Emocional Diabetes -->
<div class="b-overlay" id="b7" onclick="cerrarSi(event,'b7')"><div class="b-modal">
  <div class="b-hero bh7"><span class="b-tag">🩸 Diabetes · Emocional</span><h2>Bienestar Emocional con Diabetes</h2><p>Cómo manejar la fatiga diabética y encontrar apoyo real</p></div>
  <div class="b-body">
    <p>Vivir con diabetes es una carga mental invisible: monitoreo constante, decisiones de alimentación, miedo a complicaciones. La "fatiga diabética" es real y es reconocida médicamente.</p>
    <div class="b-stitle">¿Qué es la fatiga diabética?</div>
    <div class="b-card"><span class="b-card-icon">😔</span><div><strong>Signos de fatiga diabética</strong><p>Cansancio de monitorear y contar todo · Saltarse dosis "porque ya da lo mismo" · Sentir que la diabetes te define · Tristeza o frustración constante con el control. Es normal sentirlo. No significa que fallaste.</p></div></div>
    <div class="b-stitle">Estrategias de apoyo emocional</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Celebra los pequeños logros:</strong> Un día con glucosa estable, una semana sin saltarte medicación — son victorias reales. Reconócelas.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Habla con tu equipo médico:</strong> Cuéntales cuando te sientes agotado. Pueden ajustar el plan para hacerlo más manejable.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Mindfulness y glucosa:</strong> El estrés eleva el cortisol que eleva la glucosa. 10 min de meditación al día rompe ese ciclo.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Grupos de apoyo:</strong> Conecta con otros diabéticos. El simple hecho de ser entendido reduce la fatiga emocional significativamente.</p></div>
    </div>
    <div class="b-tip"><strong>💡 Psicología especializada en diabetes</strong>Existen psicólogos especializados en enfermedades crónicas. La terapia cognitivo-conductual ha demostrado mejorar tanto el bienestar emocional como el control glucémico en personas con diabetes.</div>
    <button class="b-btn" onclick="cerrarB('b7')">Entendido ✓</button>
  </div>
</div></div>

<!-- B8: Rutinas Glucémicas -->
<div class="b-overlay" id="b8" onclick="cerrarSi(event,'b8')"><div class="b-modal">
  <div class="b-hero bh8"><span class="b-tag">🩸 Diabetes · Rutinas</span><h2>Rutinas Diarias para el Control Glucémico</h2><p>Hábitos de estilo de vida que bajan la glucosa de forma natural</p></div>
  <div class="b-body">
    <p>Los hábitos diarios pueden reducir la HbA1c hasta 1-2 puntos porcentuales — comparable con algunos medicamentos, sin efectos secundarios.</p>
    <div class="b-stitle">Rutina diaria completa</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Mañana:</strong> Mide glucosa en ayunas. Desayuna con proteína + fibra para evitar pico matutino. No saltes el desayuno.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Tras cada comida:</strong> Camina 15-20 minutos. La caminata post-comida reduce la glucosa post-prandial hasta un 30% — es el "medicamento" más barato y efectivo.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Mismos horarios:</strong> Come a las mismas horas todos los días. El páncreas aprende el ritmo y regula mejor la insulina.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Noche:</strong> Duerme 7-8 horas. Una noche de mal sueño sube la glucosa en ayunas del día siguiente hasta 15-20 mg/dL.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p><strong>Alcohol con moderación extrema:</strong> Cada copa eleva la glucosa inicialmente y luego provoca hipoglucemia. Si bebes, hazlo con comida y monitorea.</p></div>
    </div>
    <div class="b-tip"><strong>💡 El efecto del sueño en la glucosa</strong>El insomnio crónico reduce la sensibilidad a la insulina de forma similar a ganar 8kg de peso. Priorizar el sueño es parte del tratamiento de la diabetes.</div>
    <button class="b-btn" onclick="cerrarB('b8')">Entendido ✓</button>
  </div>
</div></div>

<!-- B9: Tecnología Diabetes -->
<div class="b-overlay" id="b9" onclick="cerrarSi(event,'b9')"><div class="b-modal">
  <div class="b-hero bh9"><span class="b-tag">🩸 Diabetes · Tecnología</span><h2>Tecnología para tu Bienestar Diabético</h2><p>Las mejores herramientas digitales para gestionar tu diabetes</p></div>
  <div class="b-body">
    <p>La tecnología no reemplaza al médico pero sí complementa enormemente el autocontrol. Estas son las herramientas más útiles y accesibles.</p>
    <div class="b-card"><span class="b-card-icon">📱</span><div><strong>MyFitnessPal — Conteo de carbohidratos</strong><p>Escanea el código de barras del alimento y te muestra automáticamente los carbohidratos, fibra y proteína. Lleva un registro diario para identificar qué alimentos disparan más tu glucosa.</p></div></div>
    <div class="b-card"><span class="b-card-icon">📡</span><div><strong>Monitores Continuos de Glucosa (CGM)</strong><p>Sensores como Libre (Abbott) o Dexcom miden tu glucosa cada 5 minutos sin pinchazos. Te muestran tendencias en tiempo real y alertan cuando baja o sube peligrosamente. Consulta con tu médico si aplica en tu caso.</p></div></div>
    <div class="b-card"><span class="b-card-icon">⌚</span><div><strong>Relojes inteligentes con alertas de glucosa</strong><p>Algunos CGM se sincronizan con Apple Watch, Galaxy Watch o Garmin para mostrar tu glucosa en la muñeca en tiempo real. Ideal para detectar hipoglucemias nocturnas.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🏥</span><div><strong>Telemedicina</strong><p>Plataformas como Nuo, Teladoc o el servicio de tu institución de salud permiten consultas con endocrinólogos, nutriólogos y educadores en diabetes desde casa. Reduce barreras de acceso y permite seguimiento más frecuente.</p></div></div>
    <div class="b-tip"><strong>💡 App gratuita recomendada</strong>mySugr (gratuita) permite registrar glucosa, comidas, insulina y actividad física en un diario digital que puedes compartir con tu médico en cada consulta.</div>
    <button class="b-btn" onclick="cerrarB('b9')">Entendido ✓</button>
  </div>
</div></div>

<!-- B10: Relajación Hipertensión -->
<div class="b-overlay" id="b10" onclick="cerrarSi(event,'b10')"><div class="b-modal">
  <div class="b-hero bh10"><span class="b-tag">❤️ Hipertensión · Relajación</span><h2>Técnicas de Relajación para Bajar la Presión</h2><p>Métodos validados que reducen la presión arterial desde el sistema nervioso</p></div>
  <div class="b-body">
    <p>El estrés crónico mantiene el sistema simpático activo, lo que eleva la presión de forma sostenida. Estas técnicas activan el freno natural del organismo.</p>
    <div class="b-stitle">Técnica 4-7-8 — La más efectiva</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Siéntate con la espalda recta. Coloca la punta de la lengua detrás de los dientes superiores.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Inhala</strong> silenciosamente por la nariz durante <strong>4 segundos</strong>.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Retén</strong> el aire durante <strong>7 segundos</strong>.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Exhala</strong> completamente por la boca durante <strong>8 segundos</strong> haciendo un suave sonido.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p>Repite 4 veces. Practica 2 veces al día: mañana y noche. La presión baja de forma medible en 2-4 semanas.</p></div>
    </div>
    <div class="b-stitle">Otras técnicas comprobadas</div>
    <div class="b-card"><span class="b-card-icon">🎵</span><div><strong>Música y sonidos de naturaleza</strong><p>30 minutos de música clásica o sonidos de lluvia/océano reduce la presión sistólica 3-5 mmHg. Funciona especialmente antes de dormir activando el nervio vago.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🛁</span><div><strong>Baño tibio antes de dormir</strong><p>La vasodilatación producida por el agua tibia (37-38°C) relaja los vasos sanguíneos y baja la presión temporalmente. 15-20 minutos es suficiente.</p></div></div>
    <div class="b-card"><span class="b-card-icon">💆</span><div><strong>Masaje de cuello y hombros</strong><p>El masaje activa el nervio vago y reduce la frecuencia cardíaca y la presión. 10 minutos de auto-masaje en cuello, trapecios y sienes tiene efecto medible.</p></div></div>
    <button class="b-btn" onclick="cerrarB('b10')">Entendido ✓</button>
  </div>
</div></div>

<!-- B11: Yoga Hipertensión -->
<div class="b-overlay" id="b11" onclick="cerrarSi(event,'b11')"><div class="b-modal">
  <div class="b-hero bh11"><span class="b-tag">❤️ Hipertensión · Yoga</span><h2>Yoga y Tai-Chi para Hipertensos</h2><p>Práctica paso a paso adaptada para quienes tienen presión alta</p></div>
  <div class="b-body">
    <p>El yoga restaurativo y el tai-chi reducen la presión sistólica en promedio 5-7 mmHg con práctica regular de 8 semanas. Son seguros y recomendados incluso con medicación.</p>
    <div class="b-stitle">Secuencia de yoga restaurativo (20 min)</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Postura del niño (5 min):</strong> Rodillas separadas, torso adelante, brazos extendidos. Respira profundo hacia la espalda. Relaja completamente el cuello.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Gato-vaca (3 min):</strong> En cuatro puntos, arquea y redondea la espalda coordinando con la respiración. 10 ciclos lentos.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Piernas en la pared (5 min):</strong> Acuéstate y apoya las piernas verticalmente en la pared. Cierra los ojos. Esta postura reduce la presión de forma inmediata.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Savasana (5 min):</strong> Boca arriba, brazos a los lados, ojos cerrados. Escanea el cuerpo y libera cada tensión conscientemente.</p></div>
    </div>
    <div class="b-warn"><strong>⚠️ Posturas que debes evitar</strong>Parada de cabeza · Parada de hombros · Postura del arado · Cualquier inversión donde la cabeza quede más baja que el corazón. Elevan la presión intracraneana peligrosamente.</div>
    <div class="b-tip"><strong>💡 Tai-chi en el parque</strong>El tai-chi practicado al aire libre añade el beneficio del contacto con la naturaleza. Busca grupos en parques locales — muchas comunidades tienen clases gratuitas para adultos.</div>
    <button class="b-btn" onclick="cerrarB('b11')">Entendido ✓</button>
  </div>
</div></div>

<!-- B12: Sueño Hipertensión -->
<div class="b-overlay" id="b12" onclick="cerrarSi(event,'b12')"><div class="b-modal">
  <div class="b-hero bh12"><span class="b-tag">❤️ Hipertensión · Sueño</span><h2>Sueño y Recuperación Cardiovascular</h2><p>Cómo el sueño afecta tu presión y cómo mejorarlo</p></div>
  <div class="b-body">
    <p>Durante el sueño profundo la presión arterial baja naturalmente un 10-20% — fenómeno llamado "dipping nocturno". Quienes no lo tienen tienen mayor riesgo cardiovascular.</p>
    <div class="b-stitle">Protocolo de higiene del sueño para hipertensos</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Horario fijo:</strong> Duerme y despierta a la misma hora, incluso fines de semana. El ritmo circadiano regula la presión arterial.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Oscuridad total:</strong> Usa cortinas blackout o antifaz. Incluso la luz del LED del router afecta la producción de melatonina.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Temperatura 18-20°C:</strong> Un cuarto fresco facilita el descenso de temperatura corporal necesario para dormir profundo.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Sin cafeína después de las 2pm:</strong> La vida media de la cafeína es 5-7 horas. Un café a las 4pm sigue activo en tu sistema a las 9pm.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p><strong>Sin alcohol:</strong> Aunque induce somnolencia, el alcohol fragmenta el sueño profundo y suprime el dipping nocturno.</p></div>
    </div>
    <div class="b-stitle">Apnea del sueño y presión alta</div>
    <div class="b-card"><span class="b-card-icon">😴</span><div><strong>¿Tienes apnea del sueño?</strong><p>El 50% de los hipertensos tiene apnea del sueño no diagnosticada. Señales: ronquido intenso, pausas en la respiración, despertarse sin sentirse descansado, somnolencia diurna. Consulta con tu médico para una polisomnografía.</p></div></div>
    <div class="b-tip"><strong>💡 El CPAP puede eliminar la hipertensión</strong>En personas con apnea severa, el tratamiento con CPAP reduce la presión sistólica hasta 10 mmHg — más que muchos medicamentos.</div>
    <button class="b-btn" onclick="cerrarB('b12')">Entendido ✓</button>
  </div>
</div></div>

<!-- B13: Social Adulto Mayor -->
<div class="b-overlay" id="b13" onclick="cerrarSi(event,'b13')"><div class="b-modal">
  <div class="b-hero bh13"><span class="b-tag">👴 Adulto Mayor · Social</span><h2>Bienestar Social y Propósito de Vida</h2><p>Cómo mantenerse conectado y encontrar significado en cada etapa</p></div>
  <div class="b-body">
    <p>La soledad crónica en adultos mayores aumenta el riesgo de demencia en un 50% y es tan dañina para la salud como fumar 15 cigarrillos al día. La conexión social es medicina.</p>
    <div class="b-stitle">Actividades con mayor impacto en el bienestar</div>
    <div class="b-card"><span class="b-card-icon">🤝</span><div><strong>Voluntariado</strong><p>Da propósito, estructura y conexión intergeneracional. Organizaciones de salud, bibliotecas, escuelas o bancos de alimentos siempre necesitan ayuda. 2-3 horas por semana son suficientes para sentir el beneficio.</p></div></div>
    <div class="b-card"><span class="b-card-icon">💃</span><div><strong>Grupos de baile o coro</strong><p>Combinan actividad física, estimulación cognitiva y conexión social al mismo tiempo. El baile de salón reduce el riesgo de demencia un 76% según estudios longitudinales.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🐕</span><div><strong>Mascotas</strong><p>Reducen la soledad, bajan la presión arterial y la frecuencia cardíaca, y obligan a establecer rutinas y salir a caminar. Los perros en particular son los más beneficiosos para la salud cardiovascular.</p></div></div>
    <div class="b-stitle">Aprendizaje continuo</div>
    <ul class="b-list">
      <li>Aprender un idioma nuevo: estimulación cognitiva máxima</li>
      <li>Clases de pintura, cerámica o artesanías: creatividad + socialización</li>
      <li>Uso de smartphone y videollamadas: conecta con familia lejana</li>
      <li>Talleres en centros comunitarios: cocina, jardinería, fotografía</li>
    </ul>
    <button class="b-btn" onclick="cerrarB('b13')">Entendido ✓</button>
  </div>
</div></div>

<!-- B14: Movilidad Adulto Mayor -->
<div class="b-overlay" id="b14" onclick="cerrarSi(event,'b14')"><div class="b-modal">
  <div class="b-hero bh14"><span class="b-tag">👴 Adulto Mayor · Movilidad</span><h2>Ejercicios de Silla y Movilidad</h2><p>Rutina completa adaptada para mayores de 60 años</p></div>
  <div class="b-body">
    <p>El pilates de silla y los estiramientos matutinos pueden practicarse aunque tengas problemas de equilibrio, artritis o uses bastón. Son seguros y muy efectivos.</p>
    <div class="b-stitle">Rutina matutina de estiramientos (10 min)</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Rotaciones de cuello (1 min):</strong> Sentado, gira suavemente la cabeza de lado a lado. Sin forzar. Luego lleva la oreja al hombro alternando.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Círculos de hombros (1 min):</strong> Sube, lleva atrás, baja y adelante. 10 círculos hacia atrás y 10 hacia adelante. Libera tensión del trapecio.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Flexión de rodillas sentado (2 min):</strong> Sentado, levanta una rodilla hacia el pecho con las manos. Mantén 10 seg. Alterna. 10 repeticiones por pierna.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Estiramientos de pantorrilla (2 min):</strong> Sentado, extiende una pierna y apunta los dedos hacia ti. Mantén 20 seg. Previene calambres y mejora la circulación.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p><strong>Rotaciones de tobillo (2 min):</strong> Círculos con cada tobillo. Mejora la circulación en pies y previene caídas al mejorar la propiocepción.</p></div>
    </div>
    <div class="b-stitle">Pilates de silla — 3 ejercicios clave</div>
    <div class="b-card"><span class="b-card-icon">1️⃣</span><div><strong>Sentarse y pararse</strong><p>El ejercicio más completo para adultos mayores. Párate de la silla sin usar los brazos (si puedes). 10 repeticiones. Fortalece cuádriceps, glúteos y mejora el equilibrio.</p></div></div>
    <div class="b-card"><span class="b-card-icon">2️⃣</span><div><strong>Elevación de pierna lateral</strong><p>Sentado al borde de la silla, levanta una pierna hacia el lado sin inclinar el tronco. 10 reps por lado. Fortalece abductores y estabilizadores de cadera.</p></div></div>
    <div class="b-card"><span class="b-card-icon">3️⃣</span><div><strong>Contracción abdominal suave</strong><p>Sentado, exhala y mete el ombligo hacia la columna. Mantén 5 seg. 15 repeticiones. Fortalece el core sin impacto ni riesgo.</p></div></div>
    <div class="b-tip"><strong>💡 Hidroterapia</strong>El ejercicio en agua reduce el impacto articular un 80% mientras mantiene la resistencia muscular. Si tienes artritis severa, pregunta en tu centro de salud por programas de natación o aquagym para adultos mayores.</div>
    <button class="b-btn" onclick="cerrarB('b14')">Entendido ✓</button>
  </div>
</div></div>

<!-- B15: Paz Interior Adulto Mayor -->
<div class="b-overlay" id="b15" onclick="cerrarSi(event,'b15')"><div class="b-modal">
  <div class="b-hero bh15"><span class="b-tag">👴 Adulto Mayor · Espiritual</span><h2>Bienestar Espiritual y Paz Mental</h2><p>Prácticas para encontrar calma, gratitud y propósito en la tercera edad</p></div>
  <div class="b-body">
    <p>El bienestar espiritual no depende de una religión específica. Se trata de encontrar significado, paz interior y conexión con algo más grande que uno mismo.</p>
    <div class="b-stitle">Meditación y contemplación</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Busca un lugar tranquilo por las mañanas. Siéntate cómodamente 10-15 minutos.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Cierra los ojos. Respira profundo y deja que los pensamientos pasen como nubes sin aferrarte a ninguno.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Si practicas oración, este es el momento. La oración contemplativa tiene efectos medibles en la reducción de ansiedad.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Al terminar, identifica una cosa por la que estás agradecido hoy. Exprésala en voz alta o por escrito.</p></div>
    </div>
    <div class="b-stitle">Escribir memorias — el legado de vida</div>
    <div class="b-card"><span class="b-card-icon">📝</span><div><strong>Diario de vida o memorias</strong><p>Escribir sobre tu vida — los momentos felices, los superados, las lecciones aprendidas — genera gratitud, cierra ciclos emocionales y crea un legado para quienes amas. No necesitas ser escritor: una página por semana es suficiente.</p></div></div>
    <div class="b-stitle">Naturaleza como medicina</div>
    <ul class="b-list">
      <li>Paseos de 20-30 min en parques o jardines reducen el cortisol un 15%</li>
      <li>Jardinería: contacto con la tierra tiene efecto ansiolítico comprobado</li>
      <li>Simplemente sentarse al sol 15 minutos mejora el ánimo y la vitamina D</li>
    </ul>
    <div class="b-tip"><strong>💡 La depresión no es normal de la edad</strong>Si sientes tristeza persistente más de 2 semanas, pérdida de interés en actividades que antes disfrutabas, o pensamientos negativos recurrentes — habla con tu médico. La depresión en adultos mayores tiene tratamiento muy efectivo.</div>
    <button class="b-btn" onclick="cerrarB('b15')">Entendido ✓</button>
  </div>
</div></div>

<!-- B16: Emocional Embarazo -->
<div class="b-overlay" id="b16" onclick="cerrarSi(event,'b16')"><div class="b-modal">
  <div class="b-hero bh16"><span class="b-tag">🤰 Embarazo · Emocional</span><h2>Bienestar Emocional durante la Gestación</h2><p>Cómo cuidar tu salud mental durante el embarazo</p></div>
  <div class="b-body">
    <p>El bienestar emocional de la mamá afecta directamente el desarrollo del bebé. El estrés crónico durante el embarazo se asocia con parto prematuro y mayor ansiedad en el bebé.</p>
    <div class="b-stitle">La ansiedad prenatal es normal — tiene límite</div>
    <div class="b-card"><span class="b-card-icon">💚</span><div><strong>Ansiedad normal en el embarazo</strong><p>Preocuparte por la salud del bebé, el parto o los cambios de vida es completamente normal. La mayoría de embarazadas lo sienten.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🚨</span><div><strong>Cuándo buscar ayuda profesional</strong><p>Si la ansiedad te impide dormir, comer o disfrutar el embarazo · Si tienes pensamientos de hacerte daño · Si lloras sin razón aparente la mayoría de los días. La depresión prenatal afecta al 10-15% de las embarazadas y tiene tratamiento seguro.</p></div></div>
    <div class="b-stitle">Estrategias de apoyo emocional</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p><strong>Grupos de mamás:</strong> Presenciales u online. Compartir miedos y experiencias con otras embarazadas normaliza y alivia enormemente.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p><strong>Comunicación con tu pareja:</strong> Habla sobre tus miedos, necesidades y cambios. El apoyo de la pareja es el factor de protección emocional más importante durante el embarazo.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p><strong>Límites saludables:</strong> Está bien decir no a compromisos sociales cuando estás agotada. El embarazo es un momento de priorizar tu bienestar.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p><strong>Psicología perinatal:</strong> Especialistas en salud mental durante el embarazo y postparto. Una sesión puede cambiar completamente tu perspectiva.</p></div>
    </div>
    <button class="b-btn" onclick="cerrarB('b16')">Entendido ✓</button>
  </div>
</div></div>

<!-- B17: Yoga Prenatal -->
<div class="b-overlay" id="b17" onclick="cerrarSi(event,'b17')"><div class="b-modal">
  <div class="b-hero bh17"><span class="b-tag">🤰 Embarazo · Yoga</span><h2>Yoga Prenatal y Descanso Activo</h2><p>Posturas seguras y respiración Lamaze paso a paso</p></div>
  <div class="b-body">
    <p>El yoga prenatal reduce la ansiedad, alivia el dolor de espalda, mejora la calidad del sueño y prepara el cuerpo para el parto. Es seguro desde el 2° trimestre con instructor certificado.</p>
    <div class="b-stitle">3 posturas seguras para embarazadas</div>
    <div class="b-card"><span class="b-card-icon">🐱</span><div><strong>Gato-Vaca Prenatal</strong><p>En cuatro puntos, arquea y redondea la espalda coordinando con la respiración. Alivia el dolor lumbar del embarazo. 10 ciclos lentos, 2 veces al día. Segura en todos los trimestres.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🧎</span><div><strong>Postura del Niño Modificada</strong><p>Rodillas muy separadas para dar espacio al abdomen. Brazos extendidos al frente. Respira hacia la espalda. Alivia la presión pélvica y relaja el suelo pélvico.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🦁</span><div><strong>Postura del Zapatero (Baddha Konasana)</strong><p>Sentada, junta las plantas de los pies. Suavemente abre las rodillas hacia el suelo. Abre las caderas y estira el suelo pélvico. Prepara para el parto. Con cojín bajo la espalda.</p></div></div>
    <div class="b-stitle">Técnica Lamaze paso a paso</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Con el inicio de una contracción, enfoca tu mirada en un punto fijo frente a ti.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Inhala lentamente por la nariz mientras la contracción aumenta — cuenta 4 segundos.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Exhala lentamente por la boca soplando suavemente durante la contracción — 4-6 segundos.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Cuando la contracción pase: respira normal y descansa completamente entre contracciones.</p></div>
      <div class="b-step"><span class="b-step-n">5</span><p>Practica este patrón 10 min al día desde el 7° mes para que sea automático durante el parto.</p></div>
    </div>
    <div class="b-tip"><strong>💡 Postura para dormir</strong>Desde el 2° trimestre duerme sobre el lado izquierdo con una almohada entre las rodillas. Mejora el flujo sanguíneo al bebé y reduce la presión en la vena cava. Evita boca arriba.</div>
    <button class="b-btn" onclick="cerrarB('b17')">Entendido ✓</button>
  </div>
</div></div>

<!-- B18: Vinculación Prenatal -->
<div class="b-overlay" id="b18" onclick="cerrarSi(event,'b18')"><div class="b-modal">
  <div class="b-hero bh18"><span class="b-tag">🤰 Embarazo · Vínculo</span><h2>Vinculación Prenatal con tu Bebé</h2><p>Cómo construir el vínculo desde el vientre</p></div>
  <div class="b-body">
    <p>La vinculación prenatal activa el sistema de oxitocina en la mamá, reduce el estrés y favorece el desarrollo cerebral del bebé. Empieza desde el embarazo temprano.</p>
    <div class="b-stitle">Actividades de vinculación por semana</div>
    <div class="b-card"><span class="b-card-icon">🗣️</span><div><strong>Háblale desde la semana 18</strong><p>El bebé reconoce la voz de mamá desde el útero. Léele cuentos, cuéntale tu día o simplemente háblale con cariño. Al nacer girará la cabeza hacia tu voz — ya te conoce.</p></div></div>
    <div class="b-card"><span class="b-card-icon">🎵</span><div><strong>Música para el bebé</strong><p>Desde semana 20 puede escuchar. Música clásica suave, canciones de cuna o incluso tu música favorita. Volumen moderado (no audífonos directos en el abdomen). El ritmo de la música sincroniza con sus movimientos.</p></div></div>
    <div class="b-card"><span class="b-card-icon">✋</span><div><strong>Masaje abdominal suave</strong><p>Con aceite de almendras o coco, masajea el abdomen con movimientos circulares lentos. Reduce las estrías, relaja el útero y crea un momento íntimo de conexión. Desde semana 16 el bebé puede sentir la presión suave.</p></div></div>
    <div class="b-stitle">Diario de embarazo</div>
    <div class="b-steps">
      <div class="b-step"><span class="b-step-n">1</span><p>Compra un cuaderno o usa una app de diario de embarazo.</p></div>
      <div class="b-step"><span class="b-step-n">2</span><p>Registra: primeros movimientos, ecografías, cómo te sientes, tus miedos y tus ilusiones.</p></div>
      <div class="b-step"><span class="b-step-n">3</span><p>Escribe cartas a tu bebé — lo que quieres para él, lo que sientes al conocerlo aún sin verlo.</p></div>
      <div class="b-step"><span class="b-step-n">4</span><p>Guarda fotos de la pancita semana a semana. Será el regalo más especial que le puedas dar cuando crezca.</p></div>
    </div>
    <button class="b-btn" onclick="cerrarB('b18')">Entendido ✓</button>
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
function abrirModal() { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal() { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout() { window.location.href = '/controllers/LogoutController.php'; }

document.querySelectorAll('.cat-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        const cat = this.dataset.cat;
        document.querySelectorAll('#bienCards .info-card').forEach(card => {
            const cats = card.dataset.cat.split(' ');
            if (cat === 'todos' || cats.includes(cat)) {
                card.style.display = '';
                card.style.animation = 'none';
                card.offsetHeight;
                card.style.animation = 'fadeSlideIn 0.4s ease both';
            } else { card.style.display = 'none'; }
        });
    });
});

function abrirB(id) {
    document.getElementById(id).classList.add('open');
    document.body.style.overflow = 'hidden';
}
function cerrarB(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function cerrarSi(e, id) {
    if (e.target === document.getElementById(id)) cerrarB(id);
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.b-overlay.open').forEach(el => el.classList.remove('open'));
        document.body.style.overflow = '';
    }
});
</script>
</body>
</html>