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
$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Usuario');
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
    <script src="assets/js/darkmode.js?v=4"></script>
    <style>
    /* ── MODALES ── */
    .notif-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding: 24px 16px;
        overflow-y: auto;
    }
    .notif-overlay.open { display: flex; }

    .notif-modal {
        background: #fff;
        border-radius: 22px;
        max-width: 680px;
        width: 100%;
        margin: auto;
        box-shadow: 0 24px 60px rgba(0,0,0,0.22);
        overflow: hidden;
        animation: nSlideIn 0.35s ease;
    }
    @keyframes nSlideIn { from{opacity:0;transform:translateY(28px)}to{opacity:1;transform:translateY(0)} }

    .n-hero {
        padding: 28px 30px 24px;
        color: #fff;
    }
    .n-hero-badge {
        display: inline-block;
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.35);
        padding: 4px 14px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
        font-family: 'Outfit', sans-serif;
        margin-bottom: 10px;
    }
    .n-hero h2 { font-size: 20px; font-weight: 800; font-family: 'Outfit', sans-serif; margin-bottom: 4px; line-height: 1.25; }
    .n-hero p  { font-size: 13px; font-family: 'Outfit', sans-serif; opacity: 0.85; }
    .hero-yoga     { background: linear-gradient(135deg,#ba68c8,#6a1b9a); }
    .hero-core     { background: linear-gradient(135deg,#2bbbad,#00897b); }
    .hero-nutri    { background: linear-gradient(135deg,#43a047,#1b5e20); }
    .hero-mind     { background: linear-gradient(135deg,#26c6da,#006064); }
    .hero-salud    { background: linear-gradient(135deg,#ef5350,#b71c1c); }
    .hero-progreso { background: linear-gradient(135deg,#ffa726,#e65100); }

    .n-body { padding: 24px 30px 30px; }

    .n-section-title {
        font-size: 15px; font-weight: 800; color: #1a2332;
        font-family: 'Outfit', sans-serif;
        margin-bottom: 14px;
        padding-left: 10px;
        border-left: 3px solid #2bbbad;
    }

    /* Stepper */
    .stepper-nav {
        display: flex; align-items: center; justify-content: center;
        gap: 0; margin-bottom: 20px;
    }
    .step-dot {
        width: 30px; height: 30px; border-radius: 50%;
        border: 2px solid rgba(43,187,173,0.3);
        background: #fff; color: #64748b;
        font-size: 12px; font-weight: 700;
        font-family: 'Outfit', sans-serif;
        cursor: pointer; transition: all 0.2s; flex-shrink: 0;
    }
    .step-dot.active { background: linear-gradient(135deg,#2bbbad,#00897b); color:#fff; border-color:transparent; box-shadow:0 2px 8px rgba(43,187,173,0.4); }
    .step-line { flex:1; height:2px; background: rgba(43,187,173,0.2); }

    .paso-container { animation: nSlideIn 0.3s ease both; }
    .paso-container.hidden { display: none; }
    .paso-num   { font-size: 11px; font-weight: 700; color: #00897b; font-family:'Outfit',sans-serif; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:8px; }
    .paso-emoji { font-size: 36px; margin-bottom: 8px; }
    .paso-tit   { font-size: 17px; font-weight: 800; color: #1a2332; font-family:'Outfit',sans-serif; margin-bottom: 4px; }
    .paso-time  { font-size: 13px; color: #00897b; font-family:'Outfit',sans-serif; font-weight:600; margin-bottom:14px; }

    .instrucciones { display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
    .inst-item {
        display:flex; gap:10px; align-items:flex-start;
        background:#f0fffe; border:1px solid rgba(43,187,173,0.2);
        border-radius:10px; padding:10px 12px;
    }
    .inst-num {
        width:22px; height:22px; border-radius:50%;
        background: linear-gradient(135deg,#2bbbad,#00897b);
        color:#fff; font-size:11px; font-weight:700;
        font-family:'Outfit',sans-serif;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .inst-item p { font-size:13px; color:#64748b; font-family:'Outfit',sans-serif; line-height:1.5; margin:0; }
    .inst-item p strong { color:#1a2332; }

    .paso-beneficio {
        font-size:13px; color:#00695c; font-family:'Outfit',sans-serif;
        background:#e8f8f7; border:1px solid #b2dfdb;
        border-radius:10px; padding:10px 14px; line-height:1.5;
        margin-bottom: 4px;
    }

    .paso-btns {
        display:flex; align-items:center; justify-content:space-between;
        gap:10px; margin-top:16px;
    }
    .btn-prev, .btn-next {
        padding:9px 20px; border-radius:999px;
        font-size:13px; font-weight:700;
        font-family:'Outfit',sans-serif; cursor:pointer;
        transition:all 0.2s; border:none;
    }
    .btn-next { background:linear-gradient(135deg,#2bbbad,#00897b); color:#fff; box-shadow:0 4px 12px rgba(43,187,173,0.3); }
    .btn-next:hover { transform:translateY(-2px); }
    .btn-prev { background:#f1f5f9; color:#64748b; }
    .btn-prev:hover:not(:disabled) { background:#e2e8f0; }
    .btn-prev:disabled { opacity:0.4; cursor:not-allowed; }
    .paso-ind { font-size:13px; color:#64748b; font-family:'Outfit',sans-serif; font-weight:600; }

    /* Concepto card */
    .concept-card {
        display:flex; gap:12px;
        background:#f8fffe; border:1px solid rgba(43,187,173,0.2);
        border-radius:12px; padding:14px 16px; margin-bottom:10px;
    }
    .concept-icon { font-size:26px; flex-shrink:0; margin-top:2px; }
    .concept-card strong { display:block; font-size:13px; font-weight:700; color:#1a2332; font-family:'Outfit',sans-serif; margin-bottom:5px; }
    .concept-card p { font-size:13px; color:#64748b; font-family:'Outfit',sans-serif; line-height:1.6; margin:0; }

    /* Tip box */
    .tip-box {
        background:#e8f8f7; border:1px solid #b2dfdb;
        border-radius:12px; padding:14px 16px; margin:14px 0;
        font-size:13px; color:#00695c; font-family:'Outfit',sans-serif; line-height:1.6;
    }
    .tip-box strong { display:block; margin-bottom:5px; font-size:14px; }

    /* Progress bar en modal */
    .modal-prog { display:flex; align-items:center; gap:10px; margin-bottom:18px; font-size:13px; color:#64748b; font-family:'Outfit',sans-serif; }
    .prog-bar { flex:1; height:7px; background:#e9ecef; border-radius:999px; overflow:hidden; }
    .prog-fill { height:100%; background:linear-gradient(90deg,#2bbbad,#00897b); border-radius:999px; }

    /* Métricas */
    .metricas-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:14px; }
    .metrica-box {
        border-radius:12px; padding:14px;
        border:1px solid rgba(43,187,173,0.2);
        display:flex; flex-direction:column; gap:3px;
    }
    .m-ok   { background:#f0fdf4; border-color:#a7f3d0; }
    .m-warn { background:#fffbeb; border-color:#fcd34d; }
    .m-val  { font-size:20px; font-weight:800; color:#1a2332; font-family:'Outfit',sans-serif; }
    .m-lab  { font-size:12px; color:#64748b; font-family:'Outfit',sans-serif; font-weight:600; }
    .m-trend{ font-size:11px; font-weight:600; font-family:'Outfit',sans-serif; color:#2e7d32; }
    .m-warn .m-trend { color:#d97706; }

    /* Logros */
    .logros { display:flex; flex-direction:column; gap:8px; margin-bottom:14px; }
    .logro {
        display:flex; gap:10px; align-items:flex-start;
        background:#f8fffe; border:1px solid rgba(43,187,173,0.2);
        border-radius:10px; padding:10px 14px;
    }
    .logro-icon { font-size:18px; flex-shrink:0; }
    .logro p { font-size:13px; color:#64748b; font-family:'Outfit',sans-serif; line-height:1.5; margin:0; }

    /* Botones cierre */
    .btn-close-modal {
        display:block; width:100%; margin-top:16px;
        background:linear-gradient(135deg,#2bbbad,#00897b);
        color:#fff; border:none; padding:12px;
        border-radius:999px; font-size:14px; font-weight:700;
        font-family:'Outfit',sans-serif; cursor:pointer;
        transition:transform 0.2s, box-shadow 0.2s;
        box-shadow:0 4px 14px rgba(43,187,173,0.3);
    }
    .btn-close-modal:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(43,187,173,0.4); }
    .btn-close-ghost {
        display:block; width:100%; margin-top:8px;
        background:#f1f5f9; color:#64748b; border:none;
        padding:11px; border-radius:999px; font-size:13px;
        font-weight:600; font-family:'Outfit',sans-serif; cursor:pointer;
        transition:background 0.2s;
    }
    .btn-close-ghost:hover { background:#e2e8f0; }
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
            <?= $userName ?>
        </h2>

        <!-- ALERTAS PRIORITARIAS -->
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

        <!-- TABLA DE NOTIFICACIONES -->
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
                    <!-- RUTINAS -->
                    <tr id="row-yoga">
                        <td>⏰ Rutina Diaria</td>
                        <td>Yoga Matutino: 6 posturas · 20 min · ¡A punto de empezar!</td>
                        <td>hace 15 min</td>
                        <td><span class="status unread">No Leída</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-yoga','row-yoga')">Ver</button></td>
                    </tr>
                    <tr id="row-core">
                        <td>⏰ Rutina Diaria</td>
                        <td>Fortalecimiento del Core: 5 ejercicios · 15 min</td>
                        <td>hace 1 hora</td>
                        <td><span class="status unread">No Leída</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-core','row-core')">Ver</button></td>
                    </tr>
                    <!-- CURSOS -->
                    <tr id="row-nutri">
                        <td>🎓 Curso</td>
                        <td>Nutrición Saludable · Módulo 2: Macronutrientes disponible</td>
                        <td>hace 30 min</td>
                        <td><span class="status unread">No Leída</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-nutri','row-nutri')">Ver</button></td>
                    </tr>
                    <tr id="row-mind">
                        <td>🎓 Curso</td>
                        <td>Mindfulness · Módulo 3: Meditación para el trabajo desbloqueado</td>
                        <td>hace 2 horas</td>
                        <td><span class="status unread">No Leída</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-mind','row-mind')">Ver</button></td>
                    </tr>
                    <!-- SALUD -->
                    <tr id="row-eval">
                        <td>💚 Salud</td>
                        <td>Evaluación de Bienestar Corporal: Nuevos resultados disponibles</td>
                        <td>hace 2 horas</td>
                        <td><span class="status urgent">No Leída (Urgente)</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-eval','row-eval')">Ver</button></td>
                    </tr>
                    <tr class="read-row" id="row-prog">
                        <td>📈 Progresión</td>
                        <td>Plan de Nutrición Keto: Semana 4 completada</td>
                        <td>hace 22 horas</td>
                        <td><span class="status read">Leída</span></td>
                        <td><button class="action-btn" onclick="abrirNotif('modal-prog','row-prog')">Ver</button></td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination">
                <i class="fas fa-chevron-left"></i> 24 abr 2024 <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </section>
</div>

<!-- ═══════════ MODAL: RUTINA YOGA ═══════════ -->
<div class="notif-overlay" id="modal-yoga" onclick="cerrarSiFondo(event,'modal-yoga')">
  <div class="notif-modal">
    <div class="n-hero hero-yoga">
      <span class="n-hero-badge">⏰ Rutina Diaria · 20 min · Principiante</span>
      <h2>Yoga Matutino</h2>
      <p>6 posturas paso a paso para comenzar el día con energía</p>
    </div>
    <div class="n-body">
      <div class="stepper-nav">
        <button class="step-dot active" onclick="irPaso('y',0)" id="y-d0">1</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('y',1)" id="y-d1">2</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('y',2)" id="y-d2">3</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('y',3)" id="y-d3">4</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('y',4)" id="y-d4">5</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('y',5)" id="y-d5">6</button>
      </div>

      <div class="paso-container" id="y-p0">
        <div class="paso-num">Postura 1 de 6</div>
        <div class="paso-emoji">🙏</div>
        <h3 class="paso-tit">Postura de la Montaña (Tadasana)</h3>
        <p class="paso-time">⏱️ 2 minutos</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>De pie, junta los pies o sepáralos al ancho de caderas. Distribuye el peso equitativamente.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Contrae ligeramente el abdomen, relaja los hombros alejándolos de las orejas.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Brazos colgando naturalmente, palmas hacia adelante. Cabeza en posición neutra.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Cierra los ojos. Inhala 4 segundos, exhala 4 segundos. Repite 5 veces.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Siente cómo tu cuerpo se activa gradualmente. Esta postura establece la intención de tu práctica.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Mejora la postura, activa la conciencia corporal y centra la mente.</div>
      </div>

      <div class="paso-container hidden" id="y-p1">
        <div class="paso-num">Postura 2 de 6</div>
        <div class="paso-emoji">🌅</div>
        <h3 class="paso-tit">Saludo al Sol (Surya Namaskar)</h3>
        <p class="paso-time">⏱️ 4 minutos — 2 rondas</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Desde la Montaña, inhala y eleva los brazos sobre la cabeza, palmas enfrentadas.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Exhala y dobla hacia adelante (flexión de cadera), rodillas ligeramente flexionadas. Manos al suelo.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Inhala, eleva el torso hasta la mitad con espalda plana. Mirada al frente, manos en espinillas.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Exhala, baja al suelo. Inhala y eleva el pecho (Cobra). Exhala: Perro cara abajo — mantén 3 respiraciones.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Inhala, camina los pies al frente. Exhala: dobla. Inhala: sube brazos al cielo. Exhala: manos al corazón.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Calienta todo el cuerpo, mejora circulación y sincroniza movimiento con respiración.</div>
      </div>

      <div class="paso-container hidden" id="y-p2">
        <div class="paso-num">Postura 3 de 6</div>
        <div class="paso-emoji">🦁</div>
        <h3 class="paso-tit">Guerrero I (Virabhadrasana I)</h3>
        <p class="paso-time">⏱️ 3 minutos — 1.5 min por lado</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Da un gran paso atrás con el pie izquierdo (~1 metro). Pie derecho apunta al frente.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Pie trasero rota 45° hacia afuera. Dobla la rodilla delantera a 90°, sin sobrepasar los dedos.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Eleva los brazos sobre la cabeza, palmas enfrentadas. Caderas de frente. Mira hacia arriba suavemente.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Mantén 5 respiraciones lentas. Siente el estiramiento en muslo trasero y apertura del pecho.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Regresa al centro y repite con el otro lado el mismo tiempo.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Fortalece piernas y glúteos, abre caderas y pecho, mejora equilibrio y concentración.</div>
      </div>

      <div class="paso-container hidden" id="y-p3">
        <div class="paso-num">Postura 4 de 6</div>
        <div class="paso-emoji">🐱</div>
        <h3 class="paso-tit">Gato-Vaca (Marjaryasana-Bitilasana)</h3>
        <p class="paso-time">⏱️ 3 minutos</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Posición de cuatro puntos: muñecas bajo hombros, rodillas bajo caderas.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p><strong>INHALA — Vaca:</strong> baja el abdomen, eleva cabeza y coxis. Espalda arqueada hacia abajo.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p><strong>EXHALA — Gato:</strong> redondea la espalda hacia el techo, mete el ombligo, baja la cabeza.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Fluye entre ambas posturas durante 8-10 respiraciones completas y lentas.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Mueve la columna de forma fluida, como una ola. Siente cada vértebra moverse.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Moviliza la columna, alivia tensión lumbar y cervical.</div>
      </div>

      <div class="paso-container hidden" id="y-p4">
        <div class="paso-num">Postura 5 de 6</div>
        <div class="paso-emoji">🧒</div>
        <h3 class="paso-tit">Postura del Niño (Balasana)</h3>
        <p class="paso-time">⏱️ 3 minutos</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Desde cuatro puntos, lleva las rodillas al ancho de la colchoneta.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Siéntate hacia atrás sobre los talones mientras extiendes los brazos al frente.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Frente descansa en el suelo o en las manos apiladas. Entrega el peso a la gravedad.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Respira profundo hacia la espalda — siente cómo se expande con cada inhalación.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Permanece quieto. Esta es una postura de descanso y restauración.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Estira espalda baja, caderas y hombros. Activa el sistema nervioso parasimpático.</div>
      </div>

      <div class="paso-container hidden" id="y-p5">
        <div class="paso-num">Postura 6 de 6 — Cierre</div>
        <div class="paso-emoji">⭐</div>
        <h3 class="paso-tit">Savasana — Relajación Final</h3>
        <p class="paso-time">⏱️ 5 minutos</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Recuéstate boca arriba, brazos ligeramente separados, palmas hacia arriba.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Los pies caen naturalmente hacia afuera. Cierra los ojos. Entrega el cuerpo al suelo.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Escanea de pies a cabeza: relaja conscientemente cada zona del cuerpo.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Deja que la respiración sea natural. Solo observa sin controlar.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Permanece 5 minutos. Gira suavemente a un lado antes de levantarte.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Beneficio:</strong> Integra los beneficios de toda la práctica. Es la postura más importante del yoga.</div>
        <div class="tip-box" style="margin-top:12px">🎉 <strong>¡Rutina completada!</strong> 20 minutos de yoga matutino. Tu cuerpo y mente están listos para el día.</div>
      </div>

      <div class="paso-btns">
        <button class="btn-prev" id="y-prev" onclick="pasoAtras('y')" disabled>← Anterior</button>
        <span class="paso-ind" id="y-ind">1 / 6</span>
        <button class="btn-next" id="y-next" onclick="pasoSig('y')">Siguiente →</button>
      </div>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-yoga')">Cerrar</button>
    </div>
  </div>
</div>

<!-- ═══════════ MODAL: RUTINA CORE ═══════════ -->
<div class="notif-overlay" id="modal-core" onclick="cerrarSiFondo(event,'modal-core')">
  <div class="notif-modal">
    <div class="n-hero hero-core">
      <span class="n-hero-badge">⏰ Rutina Diaria · 15 min · Intermedio</span>
      <h2>Fortalecimiento del Core</h2>
      <p>5 ejercicios para un núcleo fuerte y una espalda sana</p>
    </div>
    <div class="n-body">
      <div class="stepper-nav">
        <button class="step-dot active" onclick="irPaso('c',0)" id="c-d0">1</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('c',1)" id="c-d1">2</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('c',2)" id="c-d2">3</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('c',3)" id="c-d3">4</button>
        <div class="step-line"></div>
        <button class="step-dot" onclick="irPaso('c',4)" id="c-d4">5</button>
      </div>

      <div class="paso-container" id="c-p0">
        <div class="paso-num">Ejercicio 1 de 5</div><div class="paso-emoji">🧱</div>
        <h3 class="paso-tit">Plancha Frontal (Plank)</h3>
        <p class="paso-time">⏱️ 3 series × 30 segundos · Descanso: 20 seg</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Boca abajo, apóyate en antebrazos (codos bajo hombros) y puntas de los pies.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Eleva el cuerpo en línea recta de cabeza a talones. Sin dejar caer ni elevar las caderas.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Contrae el abdomen como si fueras a recibir un golpe. Aprieta también los glúteos.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Mirada al suelo (cuello neutro). Respira de forma continua, no aguantes la respiración.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>30 segundos, descansa 20 seg, repite 3 veces. Si tiemblas es normal: ¡está funcionando!</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Músculos:</strong> Recto abdominal, transverso, oblicuos y estabilizadores de columna.</div>
      </div>

      <div class="paso-container hidden" id="c-p1">
        <div class="paso-num">Ejercicio 2 de 5</div><div class="paso-emoji">🐛</div>
        <h3 class="paso-tit">Dead Bug (Insecto Muerto)</h3>
        <p class="paso-time">⏱️ 3 series × 8 rep por lado · Descanso: 30 seg</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Boca arriba. Eleva brazos al techo y rodillas a 90°.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Presiona la zona lumbar contra el suelo — este contacto es CRÍTICO en todo momento.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p><strong>INHALA:</strong> Baja lentamente el brazo derecho hacia atrás y la pierna izquierda al suelo (4 segundos).</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p><strong>EXHALA:</strong> Regresa al inicio. Alterna: brazo izquierdo + pierna derecha. Eso es 1 rep.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>8 reps por lado. No toques el suelo si perderías el contacto lumbar.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Músculos:</strong> Transverso abdominal, coordinación lumbo-pélvica.</div>
      </div>

      <div class="paso-container hidden" id="c-p2">
        <div class="paso-num">Ejercicio 3 de 5</div><div class="paso-emoji">🐦</div>
        <h3 class="paso-tit">Bird Dog</h3>
        <p class="paso-time">⏱️ 3 series × 10 rep por lado · Descanso: 30 seg</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Cuatro puntos: muñecas bajo hombros, rodillas bajo caderas. Espalda plana como una mesa.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Activa el abdomen antes de comenzar: exhala y mete el ombligo hacia la columna.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p><strong>INHALA:</strong> Extiende brazo derecho al frente y pierna izquierda atrás, paralelos al suelo.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Caderas niveladas — no las rotes. Mantén 3 segundos.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p><strong>EXHALA:</strong> Regresa controladamente. Alterna lados. 10 reps por lado = 1 serie.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Músculos:</strong> Glúteos, dorsal ancho, estabilizadores profundos de columna.</div>
      </div>

      <div class="paso-container hidden" id="c-p3">
        <div class="paso-num">Ejercicio 4 de 5</div><div class="paso-emoji">🌉</div>
        <h3 class="paso-tit">Puente de Glúteos (Glute Bridge)</h3>
        <p class="paso-time">⏱️ 3 series × 15 repeticiones · Descanso: 30 seg</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>Boca arriba, rodillas a 90°, pies planos al ancho de caderas. Brazos a los lados.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p><strong>EXHALA:</strong> Aprieta glúteos y eleva caderas hasta línea recta hombros-rodillas.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>En la cima: mantén 2 segundos apretando al máximo. Pies empujan el suelo activamente.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p><strong>INHALA:</strong> Baja lentamente (3 segundos), sin tocar del todo el suelo para mantener tensión.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>15 repeticiones × 3 series. Variante avanzada: levanta un pie del suelo.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Músculos:</strong> Glúteo mayor, isquiotibiales, espinales. Alivia el dolor lumbar.</div>
      </div>

      <div class="paso-container hidden" id="c-p4">
        <div class="paso-num">Ejercicio 5 de 5 — Cierre</div><div class="paso-emoji">🔲</div>
        <h3 class="paso-tit">Plancha Lateral</h3>
        <p class="paso-time">⏱️ 3 series × 20 seg por lado · Descanso: 20 seg</p>
        <div class="instrucciones">
          <div class="inst-item"><span class="inst-num">1</span><p>De lado, apóyate en el antebrazo derecho (codo bajo el hombro) y el borde del pie.</p></div>
          <div class="inst-item"><span class="inst-num">2</span><p>Eleva caderas formando línea recta lateral: tobillo, cadera y hombro alineados.</p></div>
          <div class="inst-item"><span class="inst-num">3</span><p>Brazo libre en la cadera o extendido al techo. Cuello alineado con la columna.</p></div>
          <div class="inst-item"><span class="inst-num">4</span><p>Activa el oblicuo del lado de apoyo. Respira de forma continua. 20 segundos.</p></div>
          <div class="inst-item"><span class="inst-num">5</span><p>Cambia al lado izquierdo. Descansa 20 seg. Repite 3 veces por lado.</p></div>
        </div>
        <div class="paso-beneficio">💚 <strong>Músculos:</strong> Oblicuos y cuadrado lumbar. Esencial para la estabilidad lateral.</div>
        <div class="tip-box" style="margin-top:12px">🎉 <strong>¡Rutina de Core completada!</strong> Realízala 3-4 veces por semana para ver resultados en 4 semanas.</div>
      </div>

      <div class="paso-btns">
        <button class="btn-prev" id="c-prev" onclick="pasoAtras('c')" disabled>← Anterior</button>
        <span class="paso-ind" id="c-ind">1 / 5</span>
        <button class="btn-next" id="c-next" onclick="pasoSig('c')">Siguiente →</button>
      </div>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-core')">Cerrar</button>
    </div>
  </div>
</div>

<!-- ═══════════ MODAL: CURSO NUTRICIÓN ═══════════ -->
<div class="notif-overlay" id="modal-nutri" onclick="cerrarSiFondo(event,'modal-nutri')">
  <div class="notif-modal">
    <div class="n-hero hero-nutri">
      <span class="n-hero-badge">🎓 Curso · Módulo 2 disponible</span>
      <h2>Macronutrientes y su Función</h2>
      <p>Nutrición Saludable para Principiantes</p>
    </div>
    <div class="n-body">
      <div class="modal-prog">
        <span>Progreso</span>
        <div class="prog-bar"><div class="prog-fill" style="width:40%"></div></div>
        <span>40%</span>
      </div>
      <h4 class="n-section-title">📚 Módulo 2: Los 3 Macronutrientes</h4>
      <p style="font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;margin-bottom:14px;line-height:1.6">Los macronutrientes son los nutrientes que el cuerpo necesita en grandes cantidades. Son la base de cualquier plan de alimentación saludable.</p>
      <div class="concept-card">
        <span class="concept-icon">🍞</span>
        <div>
          <strong>Carbohidratos — La fuente de energía principal</strong>
          <p>4 kcal/g. Combustible del cerebro y los músculos. Complejos (avena, arroz integral, legumbres) = energía lenta y estable. Simples (azúcar, refrescos) = picos de glucosa.</p>
          <p style="color:#00897b;font-weight:600;font-size:12px">Recomendación: 45–65% de tus calorías diarias.</p>
        </div>
      </div>
      <div class="concept-card">
        <span class="concept-icon">🥩</span>
        <div>
          <strong>Proteínas — Los ladrillos del cuerpo</strong>
          <p>4 kcal/g. Construyen y reparan tejidos, producen enzimas, hormonas y anticuerpos. Proteínas completas (huevo, carne, pescado, soya) = 9 aminoácidos esenciales.</p>
          <p style="color:#00897b;font-weight:600;font-size:12px">Recomendación: 0.8–1.2 g por kg de peso corporal/día.</p>
        </div>
      </div>
      <div class="concept-card">
        <span class="concept-icon">🥑</span>
        <div>
          <strong>Grasas — Esenciales, no enemigas</strong>
          <p>9 kcal/g. Absorción de vitaminas A, D, E, K. Producción de hormonas. Grasas insaturadas (aceite de oliva, aguacate, nueces) son las más saludables.</p>
          <p style="color:#00897b;font-weight:600;font-size:12px">Recomendación: 20–35% de tus calorías diarias.</p>
        </div>
      </div>
      <div class="tip-box">
        <strong>✅ Tarea del módulo</strong>
        Durante 3 días registra lo que comes e identifica qué macronutriente predomina en cada comida. Esto te ayudará a entender tus hábitos actuales.
      </div>
      <button class="btn-close-modal" onclick="cerrarNotif('modal-nutri')">✓ Módulo completado — Continuar</button>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-nutri')">Cerrar</button>
    </div>
  </div>
</div>

<!-- ═══════════ MODAL: CURSO MINDFULNESS ═══════════ -->
<div class="notif-overlay" id="modal-mind" onclick="cerrarSiFondo(event,'modal-mind')">
  <div class="notif-modal">
    <div class="n-hero hero-mind">
      <span class="n-hero-badge">🎓 Curso · Módulo 3 desbloqueado</span>
      <h2>Meditación para el Trabajo</h2>
      <p>Mindfulness y Gestión del Estrés</p>
    </div>
    <div class="n-body">
      <div class="modal-prog">
        <span>Progreso</span>
        <div class="prog-bar"><div class="prog-fill" style="width:60%"></div></div>
        <span>60%</span>
      </div>
      <h4 class="n-section-title">🧘 Módulo 3: Mindfulness en el Trabajo</h4>
      <p style="font-size:13px;color:#64748b;font-family:'Outfit',sans-serif;margin-bottom:14px;line-height:1.6">El estrés laboral es la principal causa de burnout. Estas técnicas las puedes aplicar en cualquier momento de tu jornada, sin salir de tu escritorio.</p>
      <div class="concept-card">
        <span class="concept-icon">⏱️</span>
        <div>
          <strong>La pausa de 1 minuto</strong>
          <p>Cada hora, pon una alarma discreta. Cuando suene: cierra los ojos, toma 3 respiraciones profundas (inhala 4 seg, exhala 6 seg). Activa el nervioso parasimpático y reduce el cortisol.</p>
        </div>
      </div>
      <div class="concept-card">
        <span class="concept-icon">🖥️</span>
        <div>
          <strong>Regla 20-20-20 para los ojos</strong>
          <p>Cada 20 minutos de pantalla, mira un objeto a 6 metros de distancia durante 20 segundos. Reduce fatiga ocular y es momento perfecto para una micro-pausa de mindfulness.</p>
        </div>
      </div>
      <div class="concept-card">
        <span class="concept-icon">🚶</span>
        <div>
          <strong>Caminata consciente</strong>
          <p>En tu próximo descanso, camina 5 minutos prestando atención a cada paso: el contacto del pie con el suelo, el movimiento de los brazos, los sonidos del entorno. Sin teléfono.</p>
        </div>
      </div>
      <div class="tip-box">
        <strong>✅ Práctica del módulo</strong>
        Aplica la pausa de 1 minuto durante 5 días laborales consecutivos. Anota cómo te sientes antes y después de cada pausa. Notarás diferencia desde el primer día.
      </div>
      <button class="btn-close-modal" onclick="cerrarNotif('modal-mind')">✓ Módulo completado — Continuar</button>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-mind')">Cerrar</button>
    </div>
  </div>
</div>

<!-- ═══════════ MODAL: EVALUACIÓN SALUD ═══════════ -->
<div class="notif-overlay" id="modal-eval" onclick="cerrarSiFondo(event,'modal-eval')">
  <div class="notif-modal">
    <div class="n-hero hero-salud">
      <span class="n-hero-badge">💚 Evaluación Mensual — Mayo 2026</span>
      <h2>Resultados de Bienestar Corporal</h2>
      <p>Resumen de tus métricas de salud del mes</p>
    </div>
    <div class="n-body">
      <h4 class="n-section-title">📊 Tus Métricas del Mes</h4>
      <div class="metricas-grid">
        <div class="metrica-box m-ok"><span class="m-val">72 kg</span><span class="m-lab">Peso corporal</span><span class="m-trend">↓ -0.5 kg vs mes anterior</span></div>
        <div class="metrica-box m-ok"><span class="m-val">118/78</span><span class="m-lab">Presión arterial</span><span class="m-trend">✓ Dentro del rango normal</span></div>
        <div class="metrica-box m-warn"><span class="m-val">92 mg/dL</span><span class="m-lab">Glucosa en ayunas</span><span class="m-trend">⚠ Monitorear (normal &lt;100)</span></div>
        <div class="metrica-box m-ok"><span class="m-val">7.2 h</span><span class="m-lab">Sueño promedio</span><span class="m-trend">↑ +0.4 h vs mes anterior</span></div>
      </div>
      <div class="tip-box">
        <strong>💡 Recomendación</strong>
        Tu glucosa está en el límite superior del rango normal. Reduce azúcares simples y aumenta fibra diaria para mantenerla estable.
      </div>
      <button class="btn-close-modal" onclick="cerrarNotif('modal-eval')">Entendido</button>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-eval')">Cerrar</button>
    </div>
  </div>
</div>

<!-- ═══════════ MODAL: PROGRESIÓN ═══════════ -->
<div class="notif-overlay" id="modal-prog" onclick="cerrarSiFondo(event,'modal-prog')">
  <div class="notif-modal">
    <div class="n-hero hero-progreso">
      <span class="n-hero-badge">📈 Progresión — Semana 4</span>
      <h2>Plan de Nutrición Completado</h2>
      <p>¡Tu constancia está dando resultados!</p>
    </div>
    <div class="n-body">
      <h4 class="n-section-title">🏆 Logros de la Semana 4</h4>
      <div class="logros">
        <div class="logro"><span class="logro-icon">✅</span><p>Completaste los 7 días del plan sin saltarte ninguna comida planificada.</p></div>
        <div class="logro"><span class="logro-icon">💧</span><p>Alcanzaste la meta de hidratación (2 litros/día) en 5 de 7 días.</p></div>
        <div class="logro"><span class="logro-icon">🥦</span><p>Aumentaste el consumo de verduras en un 30% comparado con la semana 1.</p></div>
        <div class="logro"><span class="logro-icon">📉</span><p>Redujiste el consumo de azúcares añadidos en un 45% desde el inicio.</p></div>
      </div>
      <div class="tip-box">
        <strong>🎯 Objetivo Semana 5</strong>
        Incorpora una porción de proteína en cada una de tus 3 comidas principales. Esto mejorará la saciedad y ayudará a mantener la masa muscular.
      </div>
      <button class="btn-close-modal" onclick="cerrarNotif('modal-prog')">¡Seguir adelante!</button>
      <button class="btn-close-ghost" onclick="cerrarNotif('modal-prog')">Cerrar</button>
    </div>
  </div>
</div>

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
// ── Logout ──
function abrirModal() { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal() { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout() { window.location.href = '/controllers/LogoutController.php'; }

// ── Modales de notificaciones ──
function abrirNotif(modalId, rowId) {
    document.getElementById(modalId).classList.add('open');
    document.body.style.overflow = 'hidden';
    // Marcar como leída visualmente
    const row = document.getElementById(rowId);
    if (row) {
        row.classList.add('read-row');
        const badge = row.querySelector('.status');
        if (badge && !badge.classList.contains('read')) {
            badge.className = 'status read';
            badge.textContent = 'Leída';
        }
    }
}
function cerrarNotif(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}
function cerrarSiFondo(e, id) {
    if (e.target === document.getElementById(id)) cerrarNotif(id);
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.notif-overlay.open').forEach(el => el.classList.remove('open'));
        document.body.style.overflow = '';
    }
});

// ── Stepper paso a paso ──
const pasos  = { y:0, c:0 };
const totales = { y:6, c:5 };

function irPaso(r, idx) {
    const tot = totales[r];
    document.getElementById(r+'-p'+pasos[r]).classList.add('hidden');
    document.getElementById(r+'-d'+pasos[r]).classList.remove('active');
    pasos[r] = idx;
    document.getElementById(r+'-p'+idx).classList.remove('hidden');
    document.getElementById(r+'-d'+idx).classList.add('active');
    document.getElementById(r+'-ind').textContent = (idx+1)+' / '+tot;
    document.getElementById(r+'-prev').disabled = idx === 0;
    const btn = document.getElementById(r+'-next');
    if (idx === tot-1) {
        btn.textContent = '✓ Finalizar';
        btn.onclick = function() {
            cerrarNotif('modal-'+(r==='y'?'yoga':'core'));
            pasos[r]=0; irPaso(r,0);
        };
    } else {
        btn.textContent = 'Siguiente →';
        btn.onclick = function(){ pasoSig(r); };
    }
}
function pasoSig(r)   { if(pasos[r] < totales[r]-1) irPaso(r, pasos[r]+1); }
function pasoAtras(r) { if(pasos[r] > 0) irPaso(r, pasos[r]-1); }
</script>
</body>
</html>