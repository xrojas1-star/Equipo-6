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
    <title>Vitalis | Salud</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/salud.css?v=7">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js?v=4"></script>
    <style>
    /* ── MODALES ── */
    .s-overlay {
        position:fixed;inset:0;background:rgba(0,0,0,0.55);
        z-index:10000;display:none;justify-content:center;
        align-items:flex-start;padding:24px 16px;overflow-y:auto;
    }
    .s-overlay.open{display:flex;}
    .s-modal {
        background:#fff;border-radius:22px;max-width:700px;
        width:100%;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,0.2);
        overflow:hidden;animation:sSlide 0.35s ease;
    }
    @keyframes sSlide{from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)}}
    .s-hero{padding:26px 28px 22px;color:#fff;}
    .s-hero-tag{display:inline-block;background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.4);
        padding:3px 12px;border-radius:999px;font-size:11px;font-weight:700;
        font-family:'Outfit',sans-serif;margin-bottom:10px;}
    .s-hero h2{font-size:20px;font-weight:800;font-family:'Outfit',sans-serif;margin-bottom:4px;line-height:1.3;}
    .s-hero p{font-size:13px;font-family:'Outfit',sans-serif;opacity:0.85;margin:0;}
    .hg1{background:linear-gradient(135deg,#2bbbad,#00897b);}
    .hg2{background:linear-gradient(135deg,#29b6f6,#0277bd);}
    .hg3{background:linear-gradient(135deg,#4fc3f7,#01579b);}
    .hg4{background:linear-gradient(135deg,#80deea,#006064);}
    .hg5{background:linear-gradient(135deg,#ef5350,#b71c1c);}
    .hg6{background:linear-gradient(135deg,#ffa726,#e65100);}
    .hg7{background:linear-gradient(135deg,#66bb6a,#1b5e20);}
    .hg8{background:linear-gradient(135deg,#ec407a,#880e4f);}
    .hg9{background:linear-gradient(135deg,#ba68c8,#6a1b9a);}
    .hg10{background:linear-gradient(135deg,#f06292,#c2185b);}
    .hg11{background:linear-gradient(135deg,#66bb6a,#1b5e20);}
    .hg12{background:linear-gradient(135deg,#26a69a,#004d40);}
    .hg13{background:linear-gradient(135deg,#ffca28,#f57f17);}
    .hg14{background:linear-gradient(135deg,#f48fb1,#880e4f);}
    .hg15{background:linear-gradient(135deg,#ce93d8,#6a1b9a);}
    .hg16{background:linear-gradient(135deg,#ffcc80,#e65100);}
    .s-body{padding:22px 28px 28px;}
    .s-body>p{font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;line-height:1.7;margin-bottom:12px;}
    .s-stitle{font-size:15px;font-weight:800;color:#1a2332;font-family:'Outfit',sans-serif;
        padding-left:10px;border-left:3px solid #2bbbad;margin:18px 0 10px;}
    .s-metrics{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin:14px 0;}
    .s-metric{border-radius:12px;padding:14px;border:1px solid rgba(43,187,173,0.2);
        display:flex;flex-direction:column;gap:3px;background:#f8fffe;}
    .sm-val{font-size:22px;font-weight:800;font-family:'Outfit',sans-serif;}
    .sm-lab{font-size:12px;color:#64748b;font-family:'Outfit',sans-serif;font-weight:600;}
    .sm-desc{font-size:11px;color:#94a3b8;font-family:'Outfit',sans-serif;}
    .s-steps{display:flex;flex-direction:column;gap:8px;margin-bottom:14px;}
    .s-step{display:flex;gap:10px;align-items:flex-start;background:#f0fffe;
        border:1px solid rgba(43,187,173,0.2);border-radius:10px;padding:10px 12px;}
    .s-step-n{width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#2bbbad,#00897b);
        color:#fff;font-size:11px;font-weight:700;font-family:'Outfit',sans-serif;
        display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .s-step p{font-size:13px;color:#4a5568;font-family:'Outfit',sans-serif;line-height:1.5;margin:0;}
    .s-step p strong{color:#1a2332;}
    .s-card{display:flex;gap:12px;background:#f8fffe;border:1px solid rgba(43,187,173,0.18);
        border-radius:12px;padding:13px 15px;margin-bottom:10px;}
    .s-card-icon{font-size:24px;flex-shrink:0;}
    .s-card strong{display:block;font-size:13px;font-weight:700;color:#1a2332;font-family:'Outfit',sans-serif;margin-bottom:4px;}
    .s-card p{font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;line-height:1.6;margin:0;}
    .s-list{list-style:none;padding:0;margin:0 0 14px;}
    .s-list li{padding:7px 0 7px 22px;position:relative;border-bottom:1px solid #f0f4f8;
        font-size:13px;color:#4a5568;font-family:'Outfit',sans-serif;line-height:1.5;}
    .s-list li:last-child{border-bottom:none;}
    .s-list li::before{content:"✔";position:absolute;left:0;color:#2bbbad;font-size:11px;top:8px;}
    .s-tip{background:#e8f8f7;border:1px solid #b2dfdb;border-radius:12px;
        padding:12px 16px;margin:12px 0;font-size:13px;color:#00695c;font-family:'Outfit',sans-serif;line-height:1.6;}
    .s-tip strong{display:block;margin-bottom:4px;font-size:14px;}
    .s-warn{background:#fff3e0;border:1px solid #ffcc80;border-radius:12px;
        padding:12px 16px;margin:12px 0;font-size:13px;color:#e65100;font-family:'Outfit',sans-serif;line-height:1.6;}
    .s-warn strong{display:block;margin-bottom:4px;}
    .s-danger{background:#ffebee;border:1px solid #ef9a9a;border-radius:12px;
        padding:12px 16px;margin:12px 0;font-size:13px;color:#c62828;font-family:'Outfit',sans-serif;line-height:1.6;}
    .s-danger strong{display:block;margin-bottom:4px;}
    .semaforo{display:flex;flex-direction:column;gap:8px;margin-bottom:14px;}
    .sem-item{display:flex;align-items:center;gap:12px;background:#f8fffe;
        border:1px solid rgba(43,187,173,0.2);border-radius:10px;padding:10px 14px;}
    .sem-dot{width:16px;height:16px;border-radius:50%;flex-shrink:0;}
    .sem-green{background:#43a047;}.sem-yellow{background:#ff9800;}.sem-red{background:#ef5350;}
    .sem-item p{font-size:13px;color:#4a5568;font-family:'Outfit',sans-serif;margin:0;line-height:1.4;}
    .sem-item p strong{color:#1a2332;}
    .s-btn-close{display:block;width:100%;margin-top:18px;
        background:linear-gradient(135deg,#2bbbad,#00897b);color:#fff;border:none;padding:13px;
        border-radius:999px;font-size:14px;font-weight:700;font-family:'Outfit',sans-serif;cursor:pointer;
        transition:transform 0.2s,box-shadow 0.2s;box-shadow:0 4px 14px rgba(43,187,173,0.3);}
    .s-btn-close:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(43,187,173,0.4);}
    /* dark mode modales */
    body.dark-mode .s-modal{background:#0d1117;}
    body.dark-mode .s-stitle{color:#e2e8f0;}
    body.dark-mode .s-body>p{color:#94a3b8;}
    body.dark-mode .s-step{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .s-step p{color:#94a3b8;}
    body.dark-mode .s-step p strong{color:#e2e8f0;}
    body.dark-mode .s-card{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .s-card strong{color:#e2e8f0;}
    body.dark-mode .s-card p{color:#94a3b8;}
    body.dark-mode .s-metric{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .sm-lab{color:#64748b;}
    body.dark-mode .sm-desc{color:#475569;}
    body.dark-mode .s-list li{color:#94a3b8;border-bottom-color:#1e2830;}
    body.dark-mode .s-tip{background:#0d2a26;border-color:#134e4a;color:#4ade80;}
    body.dark-mode .s-warn{background:#1e1200;border-color:#e65100;color:#ffa726;}
    body.dark-mode .s-danger{background:#1e0a0a;border-color:#c62828;color:#ef9a9a;}
    body.dark-mode .sem-item{background:#0d1f1e;border-color:#1e2830;}
    body.dark-mode .sem-item p{color:#94a3b8;}
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
      <p class="panel-subtitle">Información médica organizada por condición de salud</p>
    </div>

    <div class="health-tabs">
      <button class="tab-btn active" data-tab="general">💚 General</button>
      <button class="tab-btn" data-tab="asma">🌬️ Asma</button>
      <button class="tab-btn" data-tab="diabetes">🩸 Diabetes</button>
      <button class="tab-btn" data-tab="hipertension">❤️ Hipertensión</button>
      <button class="tab-btn" data-tab="mayor">👴 Adulto Mayor</button>
      <button class="tab-btn" data-tab="embarazo">🤰 Embarazo</button>
    </div>

    <!-- ═══ GENERAL ═══ -->
    <div class="tab-content active" id="tab-general">
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header sh-metricas"><h3>❤️ Mis Métricas de Salud</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Peso, talla e IMC actualizados</li>
              <li><i class="fas fa-check"></i> Presión arterial normal: 120/80 mmHg</li>
              <li><i class="fas fa-check"></i> Glucosa en ayunas: 70-100 mg/dL</li>
              <li><i class="fas fa-check"></i> Colesterol total: menos de 200 mg/dL</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-g1')">Ver Mis Métricas</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header sh-chequeos"><h3>📅 Chequeos Preventivos</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Chequeo general anual recomendado</li>
              <li><i class="fas fa-check"></i> Dentista: cada 6 meses</li>
              <li><i class="fas fa-check"></i> Examen de visión: cada año</li>
              <li><i class="fas fa-check"></i> Exámenes de sangre básicos anuales</li>
            </ul>
            <button class="btn-primary">Agendar Cita</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header sh-suplementos"><h3>💊 Suplementos Esenciales</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Vitamina D: fundamental para huesos e inmunidad</li>
              <li><i class="fas fa-check"></i> Magnesio: reduce estrés y mejora sueño</li>
              <li><i class="fas fa-check"></i> Omega-3: antiinflamatorio natural</li>
              <li><i class="fas fa-check"></i> Consulta a tu médico antes de suplementar</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-g3')">Más Información</button>
          </div>
        </div>
      </div>
      <div class="info-banner banner-green">
        <div class="banner-icon">💡</div>
        <div><strong>Consejo General:</strong> Un chequeo médico anual puede detectar enfermedades en etapas tempranas cuando son más fáciles de tratar. ¡Agenda el tuyo hoy!</div>
      </div>
      <div class="metrics-grid">
        <div class="metric-box"><div class="metric-label">Presión Arterial Normal</div><div class="metric-value" style="color:#2bbbad">120/80</div><div class="metric-unit">mmHg</div></div>
        <div class="metric-box"><div class="metric-label">Glucosa en Ayunas</div><div class="metric-value" style="color:#43a047">70–100</div><div class="metric-unit">mg/dL</div></div>
        <div class="metric-box"><div class="metric-label">Frecuencia Cardíaca</div><div class="metric-value" style="color:#ef5350">60–100</div><div class="metric-unit">lpm</div></div>
        <div class="metric-box"><div class="metric-label">IMC Saludable</div><div class="metric-value" style="color:#ff7043">18.5–24.9</div><div class="metric-unit">kg/m²</div></div>
      </div>
    </div>

    <!-- ═══ ASMA ═══ -->
    <div class="tab-content" id="tab-asma">
      <div class="condition-header asma-header">
        <div class="condition-icon">🌬️</div>
        <div><h3>Asma Bronquial</h3><p>Información y herramientas para controlar tu asma día a día</p></div>
      </div>
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header" class="sh-control-sint"><h3>🫁 Control de Síntomas</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Reconoce tus desencadenantes personales</li>
              <li><i class="fas fa-check"></i> Lleva siempre tu inhalador de rescate</li>
              <li><i class="fas fa-check"></i> Plan de acción por escrito con tu médico</li>
              <li><i class="fas fa-check"></i> Usa espaciador con tu inhalador MDI</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-a1')">Plan de Acción</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-pico-flujo"><h3>📊 Pico Flujo: Tu Semáforo</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> <span style="color:#43a047;font-weight:700;">Verde 80-100%:</span> Bien, sigue tu rutina</li>
              <li><i class="fas fa-check"></i> <span style="color:#ff9800;font-weight:700;">Amarillo 50-79%:</span> Usa inhalador de rescate</li>
              <li><i class="fas fa-check"></i> <span style="color:#ef5350;font-weight:700;">Rojo &lt;50%:</span> Busca atención médica urgente</li>
              <li><i class="fas fa-check"></i> Mídelo cada mañana para detectar empeoramiento</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-a2')">Cómo Medirlo</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-entorno"><h3>🏠 Control del Entorno</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Lava ropa de cama semanalmente en agua caliente</li>
              <li><i class="fas fa-check"></i> Evita alfombras y peluches que acumulen polvo</li>
              <li><i class="fas fa-check"></i> Ventila tu hogar diariamente</li>
              <li><i class="fas fa-check"></i> No fumes ni permitas humo en casa</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-a3')">Guía del Hogar</button>
          </div>
        </div>
      </div>
      <div class="info-banner banner-blue">
        <div class="banner-icon">⚠️</div>
        <div><strong>Señales de Crisis:</strong> Dificultad para hablar, labios azulados, inhalador sin efecto en 20 minutos → Llama a emergencias inmediatamente.</div>
      </div>
    </div>

    <!-- ═══ DIABETES ═══ -->
    <div class="tab-content" id="tab-diabetes">
      <div class="condition-header diabetes-header">
        <div class="condition-icon">🩸</div>
        <div><h3>Diabetes Tipo 1 y Tipo 2</h3><p>Control glucémico y hábitos para vivir bien con diabetes</p></div>
      </div>
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header" class="sh-glucosa"><h3>🩺 Valores de Glucosa</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Ayunas: 80–130 mg/dL (objetivo)</li>
              <li><i class="fas fa-check"></i> 2h post-comida: menos de 180 mg/dL</li>
              <li><i class="fas fa-check"></i> Hipoglucemia: menos de 70 mg/dL — actúa ya</li>
              <li><i class="fas fa-check"></i> HbA1c objetivo: menos de 7%</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-d1')">Tabla de Valores</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-alim-diab"><h3>🍽️ Alimentación Diabética</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Método del plato: ½ verduras, ¼ proteína, ¼ carbohidratos</li>
              <li><i class="fas fa-check"></i> Índice glucémico bajo: avena, legumbres, quinoa</li>
              <li><i class="fas fa-check"></i> Come cada 3-4 horas para estabilizar glucosa</li>
              <li><i class="fas fa-check"></i> Cuenta carbohidratos con app o tabla</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-d2')">Plan de Alimentación</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-pies"><h3>👣 Cuidado de Pies</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Revisa tus pies cada día buscando heridas</li>
              <li><i class="fas fa-check"></i> Usa calzado cómodo y sin costuras internas</li>
              <li><i class="fas fa-check"></i> Hidrata bien la piel, evita entre los dedos</li>
              <li><i class="fas fa-check"></i> Visita al podólogo al menos 2 veces al año</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-d3')">Guía de Pies</button>
          </div>
        </div>
      </div>
      <div class="metrics-grid">
        <div class="metric-box"><div class="metric-label">Glucosa Ayunas Objetivo</div><div class="metric-value" style="color:#43a047">80–130</div><div class="metric-unit">mg/dL</div></div>
        <div class="metric-box"><div class="metric-label">Post-comida (2h)</div><div class="metric-value" style="color:#ff7043">&lt;180</div><div class="metric-unit">mg/dL</div></div>
        <div class="metric-box"><div class="metric-label">HbA1c Objetivo</div><div class="metric-value" style="color:#2bbbad">&lt;7%</div><div class="metric-unit">hemoglobina</div></div>
        <div class="metric-box"><div class="metric-label">Hipoglucemia</div><div class="metric-value" style="color:#ef5350">&lt;70</div><div class="metric-unit">mg/dL — actúa</div></div>
      </div>
    </div>

    <!-- ═══ HIPERTENSIÓN ═══ -->
    <div class="tab-content" id="tab-hipertension">
      <div class="condition-header hiper-header">
        <div class="condition-icon">❤️</div>
        <div><h3>Hipertensión Arterial</h3><p>Mantén tu presión bajo control con hábitos y seguimiento</p></div>
      </div>
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header" class="sh-presion"><h3>📏 Lecturas de Presión</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Normal: menor de 120/80 mmHg</li>
              <li><i class="fas fa-check"></i> Elevada: 120-129/menor de 80 mmHg</li>
              <li><i class="fas fa-check"></i> Hipertensión Fase 1: 130-139/80-89 mmHg</li>
              <li><i class="fas fa-check"></i> Mide siempre en reposo, 5 min sentado</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-h1')">Tabla de Valores</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-dash"><h3>🧂 Dieta DASH</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Sodio máximo: 1,500 mg/día en hipertensos</li>
              <li><i class="fas fa-check"></i> Aumenta potasio: plátano, papa, espinaca</li>
              <li><i class="fas fa-check"></i> Lee etiquetas: el 70% del sodio está oculto</li>
              <li><i class="fas fa-check"></i> Evita embutidos, enlatados y comida rápida</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-h2')">Plan DASH</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-ejercicio"><h3>🏃 Ejercicio y Medicación</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Cardio moderado 30 min, 5 días/semana</li>
              <li><i class="fas fa-check"></i> Nunca suspendas medicación sin consultar</li>
              <li><i class="fas fa-check"></i> Mide presión antes de ejercitar</li>
              <li><i class="fas fa-check"></i> Evita ejercitar si presión mayor de 160/100</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-h3')">Rutinas Seguras</button>
          </div>
        </div>
      </div>
      <div class="info-banner banner-red">
        <div class="banner-icon">🚨</div>
        <div><strong>Crisis Hipertensiva:</strong> Si tu presión supera 180/120 mmHg con síntomas → Ve a urgencias de inmediato.</div>
      </div>
      <div class="metrics-grid">
        <div class="metric-box"><div class="metric-label">Presión Normal</div><div class="metric-value" style="color:#2bbbad">&lt;120/80</div><div class="metric-unit">mmHg</div></div>
        <div class="metric-box"><div class="metric-label">Hipertensión Fase 1</div><div class="metric-value" style="color:#ff9800">130–139</div><div class="metric-unit">sistólica mmHg</div></div>
        <div class="metric-box"><div class="metric-label">Crisis Hipertensiva</div><div class="metric-value" style="color:#ef5350">&gt;180</div><div class="metric-unit">sistólica — urgencias</div></div>
        <div class="metric-box"><div class="metric-label">Sodio Máximo</div><div class="metric-value" style="color:#ba68c8">1,500</div><div class="metric-unit">mg/día</div></div>
      </div>
    </div>

    <!-- ═══ ADULTO MAYOR ═══ -->
    <div class="tab-content" id="tab-mayor">
      <div class="condition-header mayor-header">
        <div class="condition-icon">👴</div>
        <div><h3>Salud en el Adulto Mayor (+60 años)</h3><p>Mantente activo, independiente y saludable en cada etapa</p></div>
      </div>
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header" class="sh-osea"><h3>🦴 Salud Ósea</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Calcio: 1,200 mg/día en mayores de 70 años</li>
              <li><i class="fas fa-check"></i> Vitamina D: fundamental para absorber calcio</li>
              <li><i class="fas fa-check"></i> Densitometría ósea recomendada desde los 65</li>
              <li><i class="fas fa-check"></i> Ejercicio de impacto suave para fortalecer huesos</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-m1')">Guía de Osteoporosis</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-cognitiva"><h3>🧠 Salud Cognitiva</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Estimulación diaria: lectura, puzzles, música</li>
              <li><i class="fas fa-check"></i> Socialización activa reduce riesgo de demencia</li>
              <li><i class="fas fa-check"></i> El ejercicio aeróbico mejora la memoria</li>
              <li><i class="fas fa-check"></i> Controla hipertensión: protege el cerebro</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-m2')">Ejercicios Mentales</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-caidas"><h3>⚖️ Prevención de Caídas</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Ejercicios de equilibrio: párate en un pie</li>
              <li><i class="fas fa-check"></i> Retira alfombras y cables del suelo en casa</li>
              <li><i class="fas fa-check"></i> Iluminación adecuada en pasillos y escaleras</li>
              <li><i class="fas fa-check"></i> Revisa tu visión y audición anualmente</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-m3')">Plan Anticaídas</button>
          </div>
        </div>
      </div>
      <div class="info-banner banner-green">
        <div class="banner-icon">💚</div>
        <div><strong>Vacunas Importantes:</strong> Influenza (anual), Neumococo, Tétanos, Herpes Zóster y COVID-19. Consulta con tu médico el esquema actualizado.</div>
      </div>
      <div class="metrics-grid">
        <div class="metric-box"><div class="metric-label">Calcio Diario (+70 años)</div><div class="metric-value" style="color:#66bb6a">1,200</div><div class="metric-unit">mg/día</div></div>
        <div class="metric-box"><div class="metric-label">Proteína Recomendada</div><div class="metric-value" style="color:#2bbbad">1.2</div><div class="metric-unit">g/kg de peso/día</div></div>
        <div class="metric-box"><div class="metric-label">Actividad Física</div><div class="metric-value" style="color:#ff7043">150</div><div class="metric-unit">min/semana moderada</div></div>
        <div class="metric-box"><div class="metric-label">Agua Mínima</div><div class="metric-value" style="color:#29b6f6">8</div><div class="metric-unit">vasos/día</div></div>
      </div>
    </div>

    <!-- ═══ EMBARAZO ═══ -->
    <div class="tab-content" id="tab-embarazo">
      <div class="condition-header emb-header">
        <div class="condition-icon">🤰</div>
        <div><h3>Salud Durante el Embarazo</h3><p>Cuida tu salud y la de tu bebé en cada trimestre</p></div>
      </div>
      <div class="cards-container">
        <div class="info-card">
          <div class="card-header" class="sh-prenatal"><h3>📅 Control Prenatal</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Primera consulta antes de la semana 10</li>
              <li><i class="fas fa-check"></i> Mínimo 8 consultas prenatales recomendadas</li>
              <li><i class="fas fa-check"></i> Ultrasonidos: semana 12, 20 y 32</li>
              <li><i class="fas fa-check"></i> Pruebas de glucosa en semana 24-28</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-e1')">Calendario Prenatal</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-suple-emb"><h3>💊 Suplementos del Embarazo</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Ácido fólico: desde antes de quedar embarazada</li>
              <li><i class="fas fa-check"></i> Hierro: previene anemia en madre y bebé</li>
              <li><i class="fas fa-check"></i> Calcio: desde el segundo trimestre</li>
              <li><i class="fas fa-check"></i> Yodo: fundamental para desarrollo cerebral del bebé</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-e2')">Lista de Suplementos</button>
          </div>
        </div>
        <div class="info-card">
          <div class="card-header" class="sh-alertas"><h3>🚨 Señales de Alerta</h3></div>
          <div class="card-content">
            <ul>
              <li><i class="fas fa-check"></i> Sangrado vaginal → busca atención de inmediato</li>
              <li><i class="fas fa-check"></i> Hinchazón repentina de cara y manos</li>
              <li><i class="fas fa-check"></i> Dolor de cabeza intenso con visión borrosa</li>
              <li><i class="fas fa-check"></i> Sin movimientos fetales después de semana 28</li>
            </ul>
            <button class="btn-primary" onclick="abrirS('s-e3')">Cuándo Ir a Urgencias</button>
          </div>
        </div>
      </div>
      <div class="info-banner banner-pink">
        <div class="banner-icon">🌸</div>
        <div><strong>Alimentos a Evitar:</strong> Atún frecuente, alcohol, quesos no pasteurizados, carnes crudas, embutidos no calentados.</div>
      </div>
      <div class="metrics-grid">
        <div class="metric-box"><div class="metric-label">Ácido Fólico</div><div class="metric-value" style="color:#f48fb1">400</div><div class="metric-unit">mcg/día mínimo</div></div>
        <div class="metric-box"><div class="metric-label">Hierro</div><div class="metric-value" style="color:#ce93d8">27</div><div class="metric-unit">mg/día</div></div>
        <div class="metric-box"><div class="metric-label">Calcio</div><div class="metric-value" style="color:#2bbbad">1,000</div><div class="metric-unit">mg/día</div></div>
        <div class="metric-box"><div class="metric-label">Consultas Prenatales</div><div class="metric-value" style="color:#ff7043">8+</div><div class="metric-unit">mínimo recomendado</div></div>
      </div>
    </div>

  </section>
</div>

<!-- ══════════════════════════════════════
     MODALES
══════════════════════════════════════ -->

<!-- G1: Mis Métricas -->
<div class="s-overlay" id="s-g1" onclick="cerrarSi(event,'s-g1')"><div class="s-modal">
  <div class="s-hero hg1"><span class="s-hero-tag">💚 General · Métricas</span><h2>Mis Métricas de Salud</h2><p>Valores de referencia para adultos sanos</p></div>
  <div class="s-body">
    <p>Conocer tus valores de referencia te permite detectar cambios a tiempo. Aquí están los rangos normales para un adulto sano.</p>
    <div class="s-metrics">
      <div class="s-metric"><span class="sm-val" style="color:#2bbbad">120/80</span><span class="sm-lab">Presión Arterial</span><span class="sm-desc">mmHg — valor óptimo</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#43a047">70–100</span><span class="sm-lab">Glucosa en Ayunas</span><span class="sm-desc">mg/dL — rango normal</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ef5350">60–100</span><span class="sm-lab">Frecuencia Cardíaca</span><span class="sm-desc">lpm — en reposo</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ff7043">18.5–24.9</span><span class="sm-lab">IMC Saludable</span><span class="sm-desc">kg/m² — peso normal</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ba68c8">&lt;200</span><span class="sm-lab">Colesterol Total</span><span class="sm-desc">mg/dL — deseable</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#26a69a">95–100%</span><span class="sm-lab">Saturación de Oxígeno</span><span class="sm-desc">SpO2 — normal</span></div>
    </div>
    <div class="s-stitle">¿Cómo calcular tu IMC?</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Divide tu peso en kilogramos entre tu estatura en metros al cuadrado.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p><strong>Fórmula:</strong> IMC = Peso (kg) ÷ Estatura² (m). Ejemplo: 70kg ÷ (1.70 × 1.70) = 24.2</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Menos de 18.5 = Bajo peso · 18.5–24.9 = Normal · 25–29.9 = Sobrepeso · 30+ = Obesidad</p></div>
    </div>
    <div class="s-tip"><strong>💡 Chequeo anual</strong>Mide tus métricas al menos una vez al año con tu médico. La detección temprana es la mejor medicina preventiva.</div>
    <button class="s-btn-close" onclick="cerrarS('s-g1')">Entendido ✓</button>
  </div>
</div></div>

<!-- G3: Suplementos -->
<div class="s-overlay" id="s-g3" onclick="cerrarSi(event,'s-g3')"><div class="s-modal">
  <div class="s-hero hg1"><span class="s-hero-tag">💚 General · Suplementos</span><h2>Suplementos Esenciales</h2><p>Qué tomar, cuánto y por qué — con evidencia científica</p></div>
  <div class="s-body">
    <p>Los suplementos no reemplazan una dieta equilibrada, pero sí complementan deficiencias comunes. Estos son los más respaldados por la evidencia científica.</p>
    <div class="s-card"><span class="s-card-icon">☀️</span><div><strong>Vitamina D — 1,000 a 2,000 UI/día</strong><p>El 70% de la población tiene déficit. Es fundamental para huesos, inmunidad y estado de ánimo. Tómala con una comida que tenga grasa para mejor absorción. Fuentes: sol, salmón, yema de huevo.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🟤</span><div><strong>Magnesio — 300 a 400 mg/día</strong><p>Interviene en más de 300 reacciones enzimáticas. Reduce el estrés, mejora la calidad del sueño y alivia el estreñimiento. El glicinato de magnesio es el mejor tolerado por el estómago.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🐟</span><div><strong>Omega-3 — 1,000 mg de EPA+DHA/día</strong><p>Antiinflamatorio natural. Reduce triglicéridos, mejora la salud cardiovascular y el rendimiento cognitivo. Elige suplementos con certificado de pureza (sin metales pesados).</p></div></div>
    <div class="s-card"><span class="s-card-icon">🦠</span><div><strong>Probióticos — según cepa y necesidad</strong><p>Mejoran la salud intestinal, el sistema inmune y pueden reducir la ansiedad. Lactobacillus acidophilus y Bifidobacterium son las cepas más estudiadas.</p></div></div>
    <div class="s-warn"><strong>⚠️ Siempre consulta a tu médico</strong>Los suplementos pueden interactuar con medicamentos. No automediques dosis altas sin supervisión profesional.</div>
    <button class="s-btn-close" onclick="cerrarS('s-g3')">Entendido ✓</button>
  </div>
</div></div>

<!-- A1: Control síntomas asma -->
<div class="s-overlay" id="s-a1" onclick="cerrarSi(event,'s-a1')"><div class="s-modal">
  <div class="s-hero hg2"><span class="s-hero-tag">🌬️ Asma · Control</span><h2>Plan de Acción para el Asma</h2><p>Qué hacer en cada situación para controlar tus síntomas</p></div>
  <div class="s-body">
    <p>Un plan de acción escrito puede reducir las visitas a urgencias hasta un 70%. Conoce qué hacer en cada zona de tu semáforo personal.</p>
    <div class="s-stitle">Hábitos de control diario</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Lleva siempre tu <strong>inhalador de rescate</strong> contigo — nunca salgas sin él.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Toma tu <strong>medicación de control</strong> (corticoide inhalado) todos los días, aunque no tengas síntomas.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Usa siempre un <strong>espaciador</strong> con tu inhalador MDI — mejora la eficacia hasta un 60%.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Registra en tu diario cuántas veces usas el inhalador de rescate por semana. Si es más de 2 veces, habla con tu médico.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Identifica y evita tus <strong>desencadenantes personales</strong>: polvo, mascotas, frío, estrés, perfumes, ejercicio intenso.</p></div>
    </div>
    <div class="s-stitle">Técnica correcta de inhalación</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Agita el inhalador 5 veces. Retira la tapa.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Exhala completamente el aire de tus pulmones antes de inhalar.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Coloca la boquilla entre los dientes, cierra los labios. Activa el inhalador mientras inhalas lenta y profundamente (3-5 segundos).</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Retén la respiración 10 segundos para que el medicamento llegue a los bronquios.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Exhala lentamente. Si necesitas segunda dosis, espera 30 segundos.</p></div>
    </div>
    <div class="s-danger"><strong>🚨 Llama a emergencias si:</strong> Los labios o uñas se ponen azulados · No puedes completar una oración · El inhalador de rescate no hace efecto en 20 min · Ves costillas al respirar.</div>
    <button class="s-btn-close" onclick="cerrarS('s-a1')">Entendido ✓</button>
  </div>
</div></div>

<!-- A2: Pico flujo -->
<div class="s-overlay" id="s-a2" onclick="cerrarSi(event,'s-a2')"><div class="s-modal">
  <div class="s-hero hg3"><span class="s-hero-tag">🌬️ Asma · Pico Flujo</span><h2>Pico Flujo: Cómo Medirlo</h2><p>Tu semáforo personal para el asma</p></div>
  <div class="s-body">
    <p>El medidor de pico flujo mide qué tan rápido puedes exhalar el aire. Es tu termómetro del asma — te avisa antes de que los síntomas empeoren.</p>
    <div class="s-stitle">Cómo medirlo correctamente</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Coloca el marcador en cero. Párate o siéntate erguido.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Inhala lo más profundo que puedas.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Coloca la boquilla en la boca y cierra los labios firmemente alrededor.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Exhala tan fuerte y rápido como puedas en un solo golpe.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Anota el resultado. Repite 3 veces y quédate con el valor más alto.</p></div>
      <div class="s-step"><span class="s-step-n">6</span><p>Mídelo cada mañana antes de tomar medicación para conocer tu patrón.</p></div>
    </div>
    <div class="s-stitle">Tu semáforo personal</div>
    <div class="semaforo">
      <div class="sem-item"><div class="sem-dot sem-green"></div><p><strong>Verde 80-100% de tu mejor valor:</strong> Estás bien. Sigue tu medicación habitual y tu rutina normal.</p></div>
      <div class="sem-item"><div class="sem-dot sem-yellow"></div><p><strong>Amarillo 50-79%:</strong> Precaución. Usa tu inhalador de rescate. Si no mejora en 20 min, llama a tu médico.</p></div>
      <div class="sem-item"><div class="sem-dot sem-red"></div><p><strong>Rojo menos de 50%:</strong> Alerta. Usa el inhalador de rescate y busca atención médica de inmediato.</p></div>
    </div>
    <div class="s-tip"><strong>💡 Tu mejor valor personal</strong>El 100% es tu mejor marca personal (cuando estás bien). Tu médico te ayudará a calcularlo. Mídelo durante 2-3 semanas sin síntomas para establecer tu línea base.</div>
    <button class="s-btn-close" onclick="cerrarS('s-a2')">Entendido ✓</button>
  </div>
</div></div>

<!-- A3: Entorno asma -->
<div class="s-overlay" id="s-a3" onclick="cerrarSi(event,'s-a3')"><div class="s-modal">
  <div class="s-hero hg4"><span class="s-hero-tag">🌬️ Asma · Entorno</span><h2>Guía del Hogar Libre de Alérgenos</h2><p>Cómo adaptar tu casa para respirar mejor</p></div>
  <div class="s-body">
    <p>El 90% de las crisis asmáticas ocurren en interiores. Controlar el entorno del hogar es tan importante como la medicación.</p>
    <div class="s-stitle">Dormitorio — zona prioritaria</div>
    <ul class="s-list">
      <li>Lava la ropa de cama cada semana en agua a más de 60°C — mata los ácaros</li>
      <li>Usa fundas antiácaros en colchón y almohadas</li>
      <li>Elimina peluches, alfombras y cortinas pesadas del cuarto</li>
      <li>Mantén la humedad entre 40-50% — el exceso favorece el moho y los ácaros</li>
    </ul>
    <div class="s-stitle">Toda la casa</div>
    <ul class="s-list">
      <li>Ventila 10 minutos al día preferiblemente en horarios de baja contaminación</li>
      <li>Usa aspiradora con filtro HEPA — las normales esparcen el polvo</li>
      <li>Evita velas, incienso y ambientadores en spray</li>
      <li>No uses productos de limpieza con cloro o amoniaco — usa productos sin fragancia</li>
      <li>Si tienes mascotas, mantenlas fuera del dormitorio y báñalas semanalmente</li>
    </ul>
    <div class="s-stitle">Cocina</div>
    <ul class="s-list">
      <li>Usa siempre la campana extractora al cocinar</li>
      <li>Revisa que no haya fugas de gas o humedad bajo el fregadero</li>
      <li>Limpia el refrigerador mensualmente para prevenir moho</li>
    </ul>
    <div class="s-tip"><strong>💡 Calidad del aire exterior</strong>En días de alta contaminación, polvo o frío intenso, usa mascarilla KN95 o N95. Revisa el índice de calidad del aire de tu ciudad antes de salir.</div>
    <button class="s-btn-close" onclick="cerrarS('s-a3')">Entendido ✓</button>
  </div>
</div></div>

<!-- D1: Valores glucosa -->
<div class="s-overlay" id="s-d1" onclick="cerrarSi(event,'s-d1')"><div class="s-modal">
  <div class="s-hero hg5"><span class="s-hero-tag">🩸 Diabetes · Glucosa</span><h2>Tabla de Valores de Glucosa</h2><p>Rangos de referencia y qué hacer en cada situación</p></div>
  <div class="s-body">
    <p>Conocer estos valores y actuar a tiempo puede prevenir complicaciones graves. Guarda esta tabla y consúltala siempre que tengas dudas.</p>
    <div class="s-metrics">
      <div class="s-metric"><span class="sm-val" style="color:#43a047">80–130</span><span class="sm-lab">Glucosa en Ayunas</span><span class="sm-desc">mg/dL — objetivo diabético</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ff7043">&lt;180</span><span class="sm-lab">Post-comida (2h)</span><span class="sm-desc">mg/dL — objetivo</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#2bbbad">&lt;7%</span><span class="sm-lab">HbA1c Objetivo</span><span class="sm-desc">hemoglobina glicosilada</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ef5350">&lt;70</span><span class="sm-lab">Hipoglucemia</span><span class="sm-desc">mg/dL — actúa ya</span></div>
    </div>
    <div class="s-stitle">¿Qué hacer en una hipoglucemia? (glucosa menor de 70)</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Detente de inmediato. Siéntate o recuéstate si sientes mareo.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Toma <strong>15 gramos de glucosa rápida:</strong> 3-4 tabletas de glucosa, 150ml de jugo natural o refresco regular, o 1 cucharada de miel.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Espera <strong>15 minutos</strong> y vuelve a medir tu glucosa.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Si sigue menor de 70, repite el paso 2. Si no mejoras en 30 min, llama a emergencias.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Una vez que la glucosa supere 100, come un snack con proteína (queso, nueces) para estabilizar.</p></div>
    </div>
    <div class="s-danger"><strong>🚨 Hiperglucemia mayor de 300 mg/dL</strong>Busca atención médica. Síntomas: sed extrema, orina frecuente, visión borrosa, aliento afrutado. Puede indicar cetoacidosis.</div>
    <button class="s-btn-close" onclick="cerrarS('s-d1')">Entendido ✓</button>
  </div>
</div></div>

<!-- D2: Alimentación diabética -->
<div class="s-overlay" id="s-d2" onclick="cerrarSi(event,'s-d2')"><div class="s-modal">
  <div class="s-hero hg6"><span class="s-hero-tag">🩸 Diabetes · Alimentación</span><h2>Plan de Alimentación Diabética</h2><p>El Método del Plato y alimentos clave para controlar la glucosa</p></div>
  <div class="s-body">
    <p>El Método del Plato es la herramienta más sencilla para controlar la glucosa sin contar calorías ni pescar alimentos.</p>
    <div class="s-card"><span class="s-card-icon">🥦</span><div><strong>½ del plato — Verduras no almidonadas</strong><p>Brócoli, espinaca, pepino, zanahoria, tomate, calabacín. Bajas en carbohidratos, ricas en fibra. La fibra ralentiza la absorción de glucosa evitando picos.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🍗</span><div><strong>¼ del plato — Proteína magra</strong><p>Pollo sin piel, pescado, huevo, tofu, legumbres. La proteína no eleva la glucosa y da saciedad prolongada. Cocina a la plancha, vapor u horno.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🌾</span><div><strong>¼ del plato — Carbohidratos complejos</strong><p>Arroz integral, avena, quinoa, camote, tortilla de maíz. Índice glucémico bajo: suben la glucosa despacio y de forma controlada.</p></div></div>
    <div class="s-stitle">Alimentos que debes reducir al mínimo</div>
    <ul class="s-list">
      <li>Azúcar, miel, piloncillo y edulcorantes calóricos</li>
      <li>Refrescos, jugos y bebidas azucaradas</li>
      <li>Pan blanco, arroz blanco, tortillas de harina</li>
      <li>Frituras, fast food y ultraprocesados</li>
    </ul>
    <div class="s-tip"><strong>💡 Truco comprobado</strong>Una cucharada de vinagre de manzana antes de las comidas reduce la glucosa post-prandial hasta un 20%. Media cucharadita de canela al día mejora la sensibilidad a la insulina.</div>
    <button class="s-btn-close" onclick="cerrarS('s-d2')">Entendido ✓</button>
  </div>
</div></div>

<!-- D3: Cuidado pies -->
<div class="s-overlay" id="s-d3" onclick="cerrarSi(event,'s-d3')"><div class="s-modal">
  <div class="s-hero hg7"><span class="s-hero-tag">🩸 Diabetes · Pies</span><h2>Guía de Cuidado de Pies</h2><p>Rutina diaria para prevenir el pie diabético</p></div>
  <div class="s-body">
    <p>El pie diabético es la complicación más prevenible de la diabetes. Una revisión diaria de 2 minutos puede evitar amputaciones.</p>
    <div class="s-stitle">Revisión diaria — 2 minutos cada noche</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Con buena luz, examina cada pie: plantas, talones, entre los dedos y uñas. Usa un espejo si no alcanzas bien.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Busca: ampollas, cortes, enrojecimiento, hinchazón, callosidades o zonas que no sientes.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Lava los pies con agua tibia (no caliente — podrías quemarte sin sentirlo) y jabón suave.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Sécalos bien, especialmente entre los dedos. La humedad favorece las infecciones por hongos.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Aplica crema hidratante en toda la planta y el talón, pero NO entre los dedos.</p></div>
    </div>
    <div class="s-stitle">Reglas esenciales</div>
    <ul class="s-list">
      <li>Nunca camines descalzo, ni en casa — siempre calzado cómodo</li>
      <li>Elige zapatos con puntera ancha, sin costuras internas y con suela antideslizante</li>
      <li>Revisa el interior del zapato antes de ponértelo — pueden haber objetos dentro</li>
      <li>Córtate las uñas en línea recta, nunca en curva — previene uñas encarnadas</li>
      <li>No trates callosidades con instrumentos cortantes en casa</li>
    </ul>
    <div class="s-danger"><strong>🚨 Consulta de inmediato si ves:</strong> Herida que no cierra en 2 días · Piel oscurecida o negra · Olor inusual · Pus o líquido · Fiebre junto con dolor en el pie.</div>
    <button class="s-btn-close" onclick="cerrarS('s-d3')">Entendido ✓</button>
  </div>
</div></div>

<!-- H1: Tabla presión -->
<div class="s-overlay" id="s-h1" onclick="cerrarSi(event,'s-h1')"><div class="s-modal">
  <div class="s-hero hg8"><span class="s-hero-tag">❤️ Hipertensión · Valores</span><h2>Tabla de Lecturas de Presión Arterial</h2><p>Qué significan tus números y cómo interpretarlos</p></div>
  <div class="s-body">
    <p>La presión arterial se mide en dos números: sistólica (cuando el corazón late) y diastólica (entre latidos). Ambos importan.</p>
    <div class="s-metrics">
      <div class="s-metric"><span class="sm-val" style="color:#2bbbad">&lt;120/80</span><span class="sm-lab">Normal</span><span class="sm-desc">Mantén tus hábitos saludables</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#66bb6a">120–129</span><span class="sm-lab">Elevada</span><span class="sm-desc">Sistólica / diastólica &lt;80</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ff9800">130–139</span><span class="sm-lab">Hipertensión Fase 1</span><span class="sm-desc">o diastólica 80-89 mmHg</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ef5350">&gt;140/90</span><span class="sm-lab">Hipertensión Fase 2</span><span class="sm-desc">Requiere medicación</span></div>
    </div>
    <div class="s-stitle">Cómo medir correctamente en casa</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Siéntate en silencio 5 minutos antes de medir. Espalda apoyada, pies planos en el suelo.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Coloca el brazalete a la altura del corazón, directamente sobre la piel (sin ropa encima).</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>No hables, no cruces las piernas, no uses el teléfono durante la medición.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p>Toma 2 lecturas con 1 minuto de diferencia y promedia los resultados.</p></div>
      <div class="s-step"><span class="s-step-n">5</span><p>Mídela siempre a la misma hora: ideal en la mañana antes de tomar medicación.</p></div>
    </div>
    <div class="s-danger"><strong>🚨 Crisis Hipertensiva: mayor de 180/120 mmHg</strong>Con síntomas (dolor de cabeza intenso, visión borrosa, dolor en el pecho, dificultad para hablar) → Ve a urgencias de inmediato.</div>
    <button class="s-btn-close" onclick="cerrarS('s-h1')">Entendido ✓</button>
  </div>
</div></div>

<!-- H2: Plan DASH -->
<div class="s-overlay" id="s-h2" onclick="cerrarSi(event,'s-h2')"><div class="s-modal">
  <div class="s-hero hg9"><span class="s-hero-tag">❤️ Hipertensión · Dieta</span><h2>Plan DASH para Hipertensos</h2><p>La dieta más efectiva para reducir la presión arterial</p></div>
  <div class="s-body">
    <p>La Dieta DASH reduce la presión sistólica hasta 11 mmHg en 2 semanas — comparable con un medicamento, sin efectos secundarios.</p>
    <div class="s-card"><span class="s-card-icon">🧂</span><div><strong>Reduce el sodio a 1,500 mg/día</strong><p>El 70% del sodio viene de alimentos procesados. Lee etiquetas: evita productos con más de 200mg de sodio por porción. Usa limón, ajo, hierbas y especias para sazonar.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🍌</span><div><strong>Aumenta el potasio — 4,700 mg/día</strong><p>El potasio contrarresta el sodio. Fuentes: plátano, papa con cáscara, espinaca, aguacate, frijoles, yogur. Come al menos 3 porciones al día.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🥛</span><div><strong>Lácteos bajos en grasa y calcio</strong><p>2-3 porciones al día de leche, yogur o queso bajo en grasa. El calcio y el magnesio relajan los vasos sanguíneos y contribuyen a reducir la presión.</p></div></div>
    <div class="s-stitle">Alimentos que debes eliminar</div>
    <ul class="s-list">
      <li>Embutidos, jamón, tocino — altísimos en sodio</li>
      <li>Enlatados y sopas instantáneas — una lata puede contener el sodio de todo el día</li>
      <li>Alcohol — más de 1 copa al día eleva la presión directamente</li>
      <li>Cafeína en exceso — más de 3 tazas de café al día</li>
    </ul>
    <div class="s-tip"><strong>💡 Resultado real</strong>Combinando dieta DASH + ejercicio moderado de 30 min diarios, la presión puede bajar 10-15 mmHg en 4 semanas. Muchos pacientes reducen o eliminan la medicación bajo supervisión médica.</div>
    <button class="s-btn-close" onclick="cerrarS('s-h2')">Entendido ✓</button>
  </div>
</div></div>

<!-- H3: Rutinas seguras hipertensión -->
<div class="s-overlay" id="s-h3" onclick="cerrarSi(event,'s-h3')"><div class="s-modal">
  <div class="s-hero hg10"><span class="s-hero-tag">❤️ Hipertensión · Ejercicio</span><h2>Rutinas Seguras para Hipertensos</h2><p>Cómo ejercitarte sin poner en riesgo tu corazón</p></div>
  <div class="s-body">
    <p>30 minutos de cardio moderado al día puede reducir la presión sistólica entre 4-9 mmHg — equivalente a un medicamento de baja dosis.</p>
    <div class="s-stitle">Antes de cada sesión</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p>Mide tu presión. Si está <strong>mayor de 160/100 mmHg</strong>, no hagas ejercicio intenso ese día.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p>Nunca suspendas tu medicación antes de ejercitar sin consultar a tu médico.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p>Calienta siempre 10 minutos caminando suave. Los arranques bruscos provocan picos de presión peligrosos.</p></div>
    </div>
    <div class="s-stitle">Los mejores ejercicios</div>
    <div class="s-card"><span class="s-card-icon">🚶</span><div><strong>Caminata rápida — 30-45 min, 5 días/semana</strong><p>Intensidad donde puedes hablar con ligero esfuerzo. Reduce la sistólica hasta 7 mmHg con constancia de 3 meses.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🚲</span><div><strong>Bicicleta estacionaria — 30 min, 3-5 veces/semana</strong><p>Intensidad controlable, sin impacto articular. Ideal para empezar si llevas tiempo sin hacer ejercicio.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🏊</span><div><strong>Natación — 30 min, 3 veces/semana</strong><p>El agua reduce la carga cardiovascular. Baja la presión de forma inmediata y sostenida.</p></div></div>
    <div class="s-warn"><strong>⚠️ Evita estos ejercicios</strong>Pesas muy pesadas · Planchas prolongadas (más de 1 minuto) · Sprints o alta intensidad. Provocan picos de presión peligrosos.</div>
    <button class="s-btn-close" onclick="cerrarS('s-h3')">Entendido ✓</button>
  </div>
</div></div>

<!-- M1: Osteoporosis -->
<div class="s-overlay" id="s-m1" onclick="cerrarSi(event,'s-m1')"><div class="s-modal">
  <div class="s-hero hg11"><span class="s-hero-tag">👴 Adulto Mayor · Huesos</span><h2>Guía de Osteoporosis y Salud Ósea</h2><p>Cómo mantener tus huesos fuertes después de los 60</p></div>
  <div class="s-body">
    <p>A partir de los 50 años se pierde masa ósea de forma natural. Con los hábitos correctos puedes frenar esta pérdida y reducir el riesgo de fracturas.</p>
    <div class="s-metrics">
      <div class="s-metric"><span class="sm-val" style="color:#66bb6a">1,200</span><span class="sm-lab">Calcio Diario (+70 años)</span><span class="sm-desc">mg/día</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ffca28">2,000</span><span class="sm-lab">Vitamina D Recomendada</span><span class="sm-desc">UI/día para mayores</span></div>
    </div>
    <div class="s-stitle">Fuentes de calcio en la alimentación</div>
    <ul class="s-list">
      <li>Lácteos: 1 taza de leche = 300mg de calcio</li>
      <li>Sardinas con hueso: 1 lata = 350mg de calcio</li>
      <li>Tofu firme: 100g = 350mg de calcio</li>
      <li>Brócoli cocido: 1 taza = 180mg de calcio</li>
      <li>Almendras: 30g = 75mg de calcio</li>
    </ul>
    <div class="s-stitle">Ejercicios que fortalecen los huesos</div>
    <div class="s-card"><span class="s-card-icon">🚶</span><div><strong>Caminata diaria — 30 min</strong><p>El impacto suave de caminar estimula la formación de hueso. Caminar es mejor que nadar para los huesos porque el agua elimina el impacto.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🏋️</span><div><strong>Ejercicios de fuerza — 2 veces/semana</strong><p>Bandas elásticas o pesas ligeras. La carga muscular sobre el hueso estimula su densificación. Empieza con supervisión de un fisioterapeuta.</p></div></div>
    <div class="s-tip"><strong>💡 Densitometría ósea</strong>Se recomienda desde los 65 años en mujeres y 70 en hombres. Si ya tienes diagnóstico de osteoporosis, tu médico puede indicar medicación específica además de calcio y vitamina D.</div>
    <button class="s-btn-close" onclick="cerrarS('s-m1')">Entendido ✓</button>
  </div>
</div></div>

<!-- M2: Salud cognitiva -->
<div class="s-overlay" id="s-m2" onclick="cerrarSi(event,'s-m2')"><div class="s-modal">
  <div class="s-hero hg12"><span class="s-hero-tag">👴 Adulto Mayor · Mente</span><h2>Ejercicios Mentales y Salud Cognitiva</h2><p>Cómo mantener la mente activa y prevenir el deterioro cognitivo</p></div>
  <div class="s-body">
    <p>El cerebro tiene neuroplasticidad hasta el final de la vida: puede formar nuevas conexiones si se estimula correctamente. La clave es la constancia y la variedad.</p>
    <div class="s-stitle">Actividades de mayor beneficio cognitivo</div>
    <div class="s-card"><span class="s-card-icon">📚</span><div><strong>Leer 30 min al día</strong><p>Activa múltiples áreas cerebrales simultáneamente. Cualquier género sirve. Los audiolibros tienen el mismo beneficio si la vista es un obstáculo.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🎵</span><div><strong>Aprender a tocar un instrumento</strong><p>Es el estímulo cognitivo más completo que existe: coordina movimiento, memoria, ritmo y emoción. Incluso 20 min al día tienen efecto.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🗣️</span><div><strong>Aprender un idioma nuevo</strong><p>Obliga al cerebro a crear redes neuronales completamente nuevas. Duolingo o clases en grupo son opciones accesibles para cualquier edad.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🧩</span><div><strong>Juegos de estrategia y memoria</strong><p>Ajedrez, dominó, crucigramas, sudoku. 20-30 min al día. Los juegos con otras personas añaden el beneficio de la interacción social.</p></div></div>
    <div class="s-stitle">Factores que protegen el cerebro</div>
    <ul class="s-list">
      <li>Ejercicio aeróbico 150 min/semana — mejora el flujo sanguíneo cerebral</li>
      <li>Sueño de calidad 7-8 horas — el cerebro elimina toxinas durante el sueño</li>
      <li>Control de la hipertensión y diabetes — ambas dañan los vasos cerebrales</li>
      <li>Vida social activa — la soledad acelera el deterioro cognitivo</li>
    </ul>
    <button class="s-btn-close" onclick="cerrarS('s-m2')">Entendido ✓</button>
  </div>
</div></div>

<!-- M3: Anticaídas -->
<div class="s-overlay" id="s-m3" onclick="cerrarSi(event,'s-m3')"><div class="s-modal">
  <div class="s-hero hg13"><span class="s-hero-tag">👴 Adulto Mayor · Caídas</span><h2>Plan Anticaídas</h2><p>Ejercicios y adaptaciones para prevenir caídas en el hogar</p></div>
  <div class="s-body">
    <p>Las caídas son la principal causa de lesiones graves en adultos mayores. El 45% se pueden prevenir con ejercicios específicos y ajustes en el hogar.</p>
    <div class="s-stitle">Ejercicios de equilibrio — 10 min al día</div>
    <div class="s-steps">
      <div class="s-step"><span class="s-step-n">1</span><p><strong>Pararse en un pie:</strong> apóyate en una silla, levanta un pie 10 segundos. Alterna. Con práctica, hazlo sin apoyo.</p></div>
      <div class="s-step"><span class="s-step-n">2</span><p><strong>Sentarse y pararse:</strong> desde la silla, levántate sin usar los brazos (si puedes). 10 repeticiones. Fortalece los músculos anti-caída.</p></div>
      <div class="s-step"><span class="s-step-n">3</span><p><strong>Caminata en línea:</strong> camina poniendo un pie delante del otro como cuerda floja, 5 pasos de ida y vuelta.</p></div>
      <div class="s-step"><span class="s-step-n">4</span><p><strong>Elevación de talones:</strong> de pie apoyado en la silla, sube en puntillas y baja lentamente. 15 repeticiones.</p></div>
    </div>
    <div class="s-stitle">Adaptaciones en el hogar</div>
    <ul class="s-list">
      <li>Retira alfombras y cables del suelo — son la causa principal de tropiezos</li>
      <li>Instala barras de apoyo en el baño junto al inodoro y en la ducha</li>
      <li>Iluminación con sensor de movimiento en pasillos y baño nocturno</li>
      <li>Usa calzado con suela antideslizante, nunca pantuflas sin sujeción</li>
      <li>Mantén pasillos despejados de muebles y objetos en el suelo</li>
    </ul>
    <div class="s-tip"><strong>💡 Revisión visual y auditiva</strong>La mala visión y la pérdida auditiva duplican el riesgo de caídas. Un chequeo anual de vista y oído puede ser la mejor prevención. Usa lentes o audífonos si los necesitas.</div>
    <button class="s-btn-close" onclick="cerrarS('s-m3')">Entendido ✓</button>
  </div>
</div></div>

<!-- E1: Control prenatal -->
<div class="s-overlay" id="s-e1" onclick="cerrarSi(event,'s-e1')"><div class="s-modal">
  <div class="s-hero hg14"><span class="s-hero-tag">🤰 Embarazo · Control Prenatal</span><h2>Calendario de Control Prenatal</h2><p>Qué revisiones necesitas y cuándo en cada trimestre</p></div>
  <div class="s-body">
    <p>El control prenatal regular reduce el riesgo de complicaciones en un 70%. Conoce qué esperar en cada etapa de tu embarazo.</p>
    <div class="s-stitle">Primer Trimestre (semanas 1-13)</div>
    <ul class="s-list">
      <li>Primera consulta antes de la semana 10 — confirma embarazo y fecha probable</li>
      <li>Análisis de sangre completo: hemograma, grupo sanguíneo, glucosa, tiroides</li>
      <li>Ultrasonido semana 11-13: mide nucal y detecta cromosompatías</li>
      <li>Inicia ácido fólico 400-800 mcg/día si no lo tomabas ya</li>
    </ul>
    <div class="s-stitle">Segundo Trimestre (semanas 14-27)</div>
    <ul class="s-list">
      <li>Ultrasonido morfológico semana 18-22: detecta malformaciones y revela el sexo</li>
      <li>Prueba de glucosa semana 24-28: descarta diabetes gestacional</li>
      <li>Vacuna antitetánica y antigripal — seguras y recomendadas en embarazo</li>
      <li>Inicio de calcio e hierro si no los tomas ya</li>
    </ul>
    <div class="s-stitle">Tercer Trimestre (semanas 28-40)</div>
    <ul class="s-list">
      <li>Ultrasonido semana 32: mide crecimiento fetal y posición</li>
      <li>Prueba Estreptococo B semana 35-37: define protocolo de parto</li>
      <li>Consultas cada 2 semanas desde semana 36, cada semana desde semana 38</li>
      <li>Plan de parto: habla con tu médico sobre tus preferencias</li>
    </ul>
    <div class="s-tip"><strong>💡 Mínimo 8 consultas</strong>La OMS recomienda mínimo 8 contactos prenatales. En embarazos de bajo riesgo pueden ser 6-8. En embarazos de alto riesgo, más frecuentes según indicación médica.</div>
    <button class="s-btn-close" onclick="cerrarS('s-e1')">Entendido ✓</button>
  </div>
</div></div>

<!-- E2: Suplementos embarazo -->
<div class="s-overlay" id="s-e2" onclick="cerrarSi(event,'s-e2')"><div class="s-modal">
  <div class="s-hero hg15"><span class="s-hero-tag">🤰 Embarazo · Suplementos</span><h2>Lista de Suplementos del Embarazo</h2><p>Qué tomar, cuánto y desde cuándo</p></div>
  <div class="s-body">
    <p>Durante el embarazo las necesidades nutricionales aumentan significativamente. Estos suplementos son esenciales y seguros bajo supervisión médica.</p>
    <div class="s-metrics">
      <div class="s-metric"><span class="sm-val" style="color:#f48fb1">400</span><span class="sm-lab">Ácido Fólico</span><span class="sm-desc">mcg/día — desde antes del embarazo</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ce93d8">27</span><span class="sm-lab">Hierro</span><span class="sm-desc">mg/día durante el embarazo</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#2bbbad">1,000</span><span class="sm-lab">Calcio</span><span class="sm-desc">mg/día desde 2° trimestre</span></div>
      <div class="s-metric"><span class="sm-val" style="color:#ff7043">200</span><span class="sm-lab">DHA (Omega-3)</span><span class="sm-desc">mg/día para el cerebro del bebé</span></div>
    </div>
    <div class="s-card"><span class="s-card-icon">🟢</span><div><strong>Ácido Fólico — El más importante</strong><p>Previene defectos del tubo neural. Empieza 3 meses ANTES de quedar embarazada si es planeado. Si ya estás embarazada, empiézalo ahora mismo. Fuentes: espinaca, lentejas, aguacate.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🔴</span><div><strong>Hierro — Tómalo con vitamina C</strong><p>Un vaso de jugo de naranja con el hierro mejora su absorción hasta un 300%. Evitarlo con café, té o lácteos que bloquean su absorción. Puede causar estreñimiento — bebe mucha agua.</p></div></div>
    <div class="s-card"><span class="s-card-icon">🧠</span><div><strong>DHA — Cerebro y ojos del bebé</strong><p>El DHA es el principal ácido graso del cerebro. Se acumula en el feto especialmente en el 3° trimestre. Elige pescados bajos en mercurio: salmón, sardina, tilapia. Máximo 2-3 veces/semana.</p></div></div>
    <div class="s-warn"><strong>⚠️ Alimentos prohibidos en el embarazo</strong>Alcohol (ninguna cantidad es segura) · Atún en lata frecuente · Quesos no pasteurizados · Carnes crudas o poco cocidas · Embutidos fríos sin calentar.</div>
    <button class="s-btn-close" onclick="cerrarS('s-e2')">Entendido ✓</button>
  </div>
</div></div>

<!-- E3: Señales de alerta embarazo -->
<div class="s-overlay" id="s-e3" onclick="cerrarSi(event,'s-e3')"><div class="s-modal">
  <div class="s-hero hg16"><span class="s-hero-tag">🤰 Embarazo · Urgencias</span><h2>Cuándo Ir a Urgencias en el Embarazo</h2><p>Señales de alerta que nunca debes ignorar</p></div>
  <div class="s-body">
    <p>Conocer las señales de alarma puede salvar la vida tuya y la de tu bebé. Ante cualquier duda, ve a urgencias — siempre es mejor una visita de más que una de menos.</p>
    <div class="s-stitle">Ve a urgencias DE INMEDIATO si presentas:</div>
    <div class="s-danger"><strong>🚨 Señales de urgencia máxima</strong></div>
    <ul class="s-list">
      <li>Sangrado vaginal en cualquier trimestre — aunque sea poco</li>
      <li>Dolor abdominal intenso o contracciones regulares antes de la semana 37</li>
      <li>Hinchazón repentina y severa de cara, manos o pies</li>
      <li>Dolor de cabeza intenso que no cede con paracetamol + visión borrosa o destellos</li>
      <li>Fiebre mayor de 38°C durante el embarazo</li>
      <li>Bebé sin movimientos por más de 2 horas después de la semana 28</li>
      <li>Salida de líquido amniótico (rompe la bolsa)</li>
      <li>Dificultad para respirar súbita sin causa aparente</li>
    </ul>
    <div class="s-stitle">Señales que requieren consulta el mismo día</div>
    <ul class="s-list">
      <li>Ardor o dolor al orinar — puede ser infección urinaria</li>
      <li>Picazón intensa en el cuerpo sin erupción visible (especialmente nocturna)</li>
      <li>Náuseas o vómitos tan intensos que no puedes retener líquidos</li>
      <li>Reducción notoria de los movimientos fetales</li>
    </ul>
    <div class="s-tip"><strong>💡 Recuerda</strong>Nunca te automediques durante el embarazo. El único analgésico seguro es el paracetamol en dosis normales. Evita ibuprofeno, aspirina y antiinflamatorios.</div>
    <button class="s-btn-close" onclick="cerrarS('s-e3')">Entendido ✓</button>
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

document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('tab-' + this.dataset.tab).classList.add('active');
  });
});

function abrirS(id) {
  document.getElementById(id).classList.add('open');
  document.body.style.overflow = 'hidden';
}
function cerrarS(id) {
  document.getElementById(id).classList.remove('open');
  document.body.style.overflow = '';
}
function cerrarSi(e, id) {
  if (e.target === document.getElementById(id)) cerrarS(id);
}
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.s-overlay.open').forEach(el => el.classList.remove('open'));
    document.body.style.overflow = '';
  }
});
</script>
</body>
</html>