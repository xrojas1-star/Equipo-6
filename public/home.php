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
    <title>Vitalis | Inicio</title>
    <link rel="stylesheet" href="assets/css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js?v=4"></script>
    <style>
    /* ── MODAL ARTÍCULO ── */
    .art-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: flex-start;
        padding: 30px 16px;
        overflow-y: auto;
    }
    .art-overlay.open { display: flex; }

    .art-modal {
        background: #fff;
        border-radius: 20px;
        max-width: 740px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: artSlideIn 0.35s ease;
        margin: auto;
    }
    @keyframes artSlideIn {
        from { opacity:0; transform: translateY(30px); }
        to   { opacity:1; transform: translateY(0); }
    }

    .art-hero { width:100%; height:220px; object-fit:cover; display:block; }

    .art-body { padding: 32px 36px 36px; }

    .art-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 14px;
    }
    .art-badge.nutricion { background: #008eb3; }
    .art-badge.ejercicio  { background: #1e81b0; }
    .art-badge.bienestar  { background: #156580; }

    .art-title { font-size: 22px; font-weight: 800; color: #1a2332; margin-bottom: 6px; line-height: 1.3; }
    .art-date  { font-size: 12px; color: #aaa; margin-bottom: 22px; display: block; }

    .art-content { font-size: 14px; color: #4a5568; line-height: 1.85; }
    .art-content h4 {
        font-size: 15px; font-weight: 700; color: #1a2332;
        margin: 22px 0 8px; padding-left: 10px;
        border-left: 3px solid #147a96;
    }
    .art-content ul { list-style:none; padding:0; margin:0 0 14px; }
    .art-content ul li {
        padding: 6px 0 6px 22px;
        position: relative;
        border-bottom: 1px solid #f0f4f8;
    }
    .art-content ul li::before {
        content: "✔"; position:absolute; left:0;
        color: #147a96; font-size:12px; top:7px;
    }
    .art-content .tip-box {
        background: #e8f8f7; border: 1px solid #b2dfdb;
        border-radius: 12px; padding: 14px 18px; margin: 18px 0;
        font-size: 13px; color: #00695c; line-height: 1.6;
    }
    .art-content .tip-box strong { display:block; margin-bottom:4px; }

    .art-close-btn {
        display: block; margin: 24px auto 0;
        background: #147a96; color: #fff; border: none;
        padding: 12px 36px; border-radius: 999px;
        font-size: 14px; font-weight: 700; cursor: pointer;
        transition: background 0.2s, transform 0.2s;
    }
    .art-close-btn:hover { background: #0f5e75; transform: translateY(-2px); }

    .card { cursor: pointer; }
    .read-link {
        display: inline-block; margin-top: 10px;
        font-size: 13px; color: #147a96; font-weight: 600;
        text-decoration: none;
    }
    .read-link:hover { text-decoration: underline; }
    </style>
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
                <li><a href="tipsalud.php">Consejos de Salud</a></li>
                <li><a href="bienestar.php">Bienestar</a></li>
            </ul>
        </nav>
        <div style="display:flex; align-items:center; gap:10px;">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <a href="panel_de_administracion.php" class="btn-admin-nav">⚙️ Panel Admin</a>
            <?php endif; ?>
            <?php if (!empty($_SESSION['es_visitante'])): ?>
            <a href="login.php" class="btn-nav" style="background:transparent;border:2px solid #147a96;color:#147a96;">Iniciar sesión</a>
            <a href="registro.php" class="btn-nav">Registrarse</a>
            <?php else: ?>
            <a href="#" class="btn-nav" onclick="abrirModal(); return false;">Cerrar sesión</a>
            <?php endif; ?>
        </div>
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

        <div class="articles-grid" id="articlesGrid">

            <article class="card" onclick="abrirArticulo('art1')">
                <div class="card-image">
                    <img src="assets/img/nutricion.png" alt="Nutrición">
                    <span class="badge badge-nutricion">Nutrición</span>
                </div>
                <div class="card-body">
                    <h3>Los mejores alimentos para tener energía todo el día</h3>
                    <p>Descubre qué alimentos ayudan a aumentar tu energía y mejorar tu salud.</p>
                    <span class="date">24 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

            <article class="card" onclick="abrirArticulo('art2')">
                <div class="card-image">
                    <img src="assets/img/ejercicio.png" alt="Ejercicio">
                    <span class="badge badge-ejercicio">Ejercicio</span>
                </div>
                <div class="card-body">
                    <h3>5 Ejercicios para Fortalecer el Core</h3>
                    <p>Entrena tu core con estos ejercicios efectivos que puedes hacer en casa.</p>
                    <span class="date">24 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

            <article class="card" onclick="abrirArticulo('art3')">
                <div class="card-image">
                    <img src="assets/img/meditar.png" alt="Bienestar">
                    <span class="badge badge-bienestar">Bienestar</span>
                </div>
                <div class="card-body">
                    <h3>Mindfulness: Guía para Principiantes</h3>
                    <p>Aprende las bases del mindfulness y cómo meditar para mejorar tu salud mental.</p>
                    <span class="date">24 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

            <article class="card extra-content" style="display:none;" onclick="abrirArticulo('art4')">
                <div class="card-image">
                    <img src="assets/img/nutricion.png" alt="Nutrición">
                    <span class="badge badge-nutricion">Nutrición</span>
                </div>
                <div class="card-body">
                    <h3>Receta de Tostada de Aguacate Perfecta</h3>
                    <p>Pan integral, huevo y especias para una energía sostenida todo el día.</p>
                    <span class="date">20 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

            <article class="card extra-content" style="display:none;" onclick="abrirArticulo('art5')">
                <div class="card-image">
                    <img src="assets/img/ejercicio.png" alt="Ejercicio">
                    <span class="badge badge-ejercicio">Ejercicio</span>
                </div>
                <div class="card-body">
                    <h3>Introducción al Tai Chi para Todos</h3>
                    <p>Historia, beneficios y posturas básicas para conectar mente y cuerpo.</p>
                    <span class="date">18 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

            <article class="card extra-content" style="display:none;" onclick="abrirArticulo('art6')">
                <div class="card-image">
                    <img src="assets/img/meditar.png" alt="Bienestar">
                    <span class="badge badge-bienestar">Bienestar</span>
                </div>
                <div class="card-body">
                    <h3>El Arte del Descanso y la Recuperación</h3>
                    <p>Técnicas de relajación y la importancia del sueño para tu salud.</p>
                    <span class="date">15 abril, 2024</span><br>
                    <span class="read-link">Leer artículo →</span>
                </div>
            </article>

        </div>

        <div class="view-more">
            <button id="btn-show-more" class="btn-secondary" onclick="mostrarMas()">Ver más artículos</button>
            <button id="btn-show-less" class="btn-secondary" style="display:none;" onclick="mostrarMenos()">Mostrar menos</button>
        </div>
    </main>

    <!-- ═══ MODALES DE ARTÍCULOS ═══ -->

    <!-- ART 1: Mejores alimentos para energía -->
    <div class="art-overlay" id="art1" onclick="cerrarSiFondo(event,'art1')">
      <div class="art-modal">
        <img src="assets/img/nutricion.png" alt="Nutrición" class="art-hero">
        <div class="art-body">
          <span class="art-badge nutricion">Nutrición</span>
          <h2 class="art-title">Los mejores alimentos para tener energía todo el día</h2>
          <span class="art-date">24 de abril, 2024 · 5 min de lectura</span>
          <div class="art-content">
            <p>Mantener niveles de energía estables depende de elegir alimentos que proveen energía sostenida sin causar picos ni caídas bruscas de glucosa. La clave no es comer más, sino comer mejor.</p>
            <h4>1. Avena integral</h4>
            <p>Su fibra soluble (betaglucano) ralentiza la digestión y mantiene estable el azúcar en sangre durante horas. Una porción en el desayuno puede mantenerte alerta hasta el mediodía.</p>
            <h4>2. Huevos</h4>
            <p>Ricos en proteína completa, colina y vitaminas del grupo B, los huevos son fundamentales para la producción de energía celular. La colina apoya la función cerebral y la concentración.</p>
            <h4>3. Plátano</h4>
            <p>Contiene glucosa, fructosa y sacarosa junto con fibra, ofreciendo energía inmediata y sostenida. Su magnesio es necesario para convertir alimentos en energía a nivel celular.</p>
            <h4>4. Frutos secos (nueces, almendras)</h4>
            <p>Combinación ideal de grasas saludables, proteínas y magnesio. Las nueces aportan omega-3, que reduce la inflamación y mejora el rendimiento cognitivo.</p>
            <h4>5. Legumbres (lentejas, frijoles, garbanzos)</h4>
            <p>Son fuente de hierro, fundamental para el transporte de oxígeno. La deficiencia de hierro es una de las causas más comunes de fatiga crónica. Combínalas con vitamina C para mejorar su absorción.</p>
            <h4>6. Agua</h4>
            <p>La deshidratación leve (perder el 1-2% del peso en agua) reduce la concentración y aumenta el cansancio. 8 vasos diarios son el mínimo recomendado.</p>
            <div class="tip-box">
              <strong>💡 Consejo Vitalis</strong>
              Evita los ultraprocesados y azúcares simples: elevan la glucosa rápidamente pero generan una caída brusca 1-2 horas después, causando somnolencia y fatiga.
            </div>
            <h4>Patrón alimentario recomendado</h4>
            <ul>
              <li>Desayuno: avena, fruta y proteína (huevo o yogur)</li>
              <li>Media mañana: puñado de frutos secos</li>
              <li>Comida: legumbres o proteína magra con verduras</li>
              <li>Merienda: yogur natural con fruta</li>
              <li>Cena: ligera — verduras, huevo o pescado</li>
            </ul>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art1')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <!-- ART 2: 5 Ejercicios Core -->
    <div class="art-overlay" id="art2" onclick="cerrarSiFondo(event,'art2')">
      <div class="art-modal">
        <img src="assets/img/ejercicio.png" alt="Ejercicio" class="art-hero">
        <div class="art-body">
          <span class="art-badge ejercicio">Ejercicio</span>
          <h2 class="art-title">5 Ejercicios para Fortalecer el Core</h2>
          <span class="art-date">24 de abril, 2024 · 4 min de lectura</span>
          <div class="art-content">
            <p>El core comprende los músculos del abdomen, espalda baja, pelvis y caderas. Fortalecerlo mejora la postura, previene lesiones y aumenta el rendimiento en cualquier actividad física.</p>
            <div class="tip-box">
              <strong>⚠️ Antes de empezar</strong>
              Realiza 5 minutos de calentamiento suave. Si tienes dolor lumbar crónico, consulta a un fisioterapeuta antes de comenzar.
            </div>
            <h4>1. Plancha frontal (Plank)</h4>
            <p>Apóyate en antebrazos y puntillas, cuerpo recto de cabeza a talones, abdomen contraído. Mantén 20-30 segundos e incrementa progresivamente. Activa recto abdominal, transverso y oblicuos simultáneamente.</p>
            <h4>2. Dead Bug</h4>
            <p>Acostado boca arriba, brazos hacia el techo y rodillas a 90°. Baja lentamente el brazo derecho y la pierna izquierda sin despegar la espalda del suelo. Alterna lados. 3 series de 8 repeticiones.</p>
            <h4>3. Bird Dog</h4>
            <p>En cuatro puntos, extiende simultáneamente el brazo derecho y la pierna izquierda. Mantén 3 segundos y alterna. 3 series de 10 repeticiones por lado. Mejora el equilibrio y activa glúteos.</p>
            <h4>4. Puente de glúteos</h4>
            <p>Acostado boca arriba, rodillas flexionadas. Eleva las caderas formando una línea recta hombros-rodillas. Contrae glúteos en la cima, baja lentamente. 3 series de 15 repeticiones.</p>
            <h4>5. Plancha lateral</h4>
            <p>Apóyate en un antebrazo y el borde del pie. Eleva las caderas en línea recta. Mantén 15-20 segundos por lado. Trabaja oblicuos y cuadrado lumbar, esenciales para la estabilidad lateral.</p>
            <h4>Rutina semanal recomendada</h4>
            <ul>
              <li>Frecuencia: 3-4 veces por semana</li>
              <li>Descanso entre series: 30-45 segundos</li>
              <li>Aumenta duración o repeticiones cada 2 semanas</li>
              <li>Combina con cardio moderado para mejores resultados</li>
            </ul>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art2')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <!-- ART 3: Mindfulness -->
    <div class="art-overlay" id="art3" onclick="cerrarSiFondo(event,'art3')">
      <div class="art-modal">
        <img src="assets/img/meditar.png" alt="Bienestar" class="art-hero">
        <div class="art-body">
          <span class="art-badge bienestar">Bienestar</span>
          <h2 class="art-title">Mindfulness: Guía para Principiantes</h2>
          <span class="art-date">24 de abril, 2024 · 5 min de lectura</span>
          <div class="art-content">
            <p>El mindfulness es la capacidad de prestar atención al momento presente de forma intencional y sin juzgar. Tiene base científica sólida: reduce el estrés, la ansiedad y mejora la calidad del sueño con solo 8 semanas de práctica diaria.</p>
            <h4>¿Qué sucede en el cerebro?</h4>
            <p>La práctica regular reduce la actividad de la amígdala (centro del miedo) y fortalece la corteza prefrontal, responsable de la regulación emocional y la toma de decisiones.</p>
            <h4>Ejercicio 1: Respiración consciente (5 minutos)</h4>
            <ul>
              <li>Siéntate cómodamente con la espalda recta</li>
              <li>Cierra los ojos y dirige tu atención a la respiración</li>
              <li>Observa cómo el aire entra y sale, sin modificarlo</li>
              <li>Cuando tu mente se distraiga (es normal), regresa suavemente</li>
              <li>Practica cada mañana antes de revisar el teléfono</li>
            </ul>
            <h4>Ejercicio 2: Escaneo corporal (10 minutos)</h4>
            <ul>
              <li>Acuéstate boca arriba en un lugar tranquilo</li>
              <li>Lleva la atención a los pies y sube lentamente por el cuerpo</li>
              <li>Observa sensaciones sin intentar cambiarlas: calor, tensión, hormigueo</li>
              <li>Termina en la cabeza y respira profundo 3 veces</li>
            </ul>
            <h4>Ejercicio 3: Regla 5-4-3-2-1 (para ansiedad)</h4>
            <ul>
              <li>5 cosas que puedes VER a tu alrededor</li>
              <li>4 cosas que puedes TOCAR físicamente</li>
              <li>3 cosas que puedes ESCUCHAR</li>
              <li>2 cosas que puedes OLER</li>
              <li>1 cosa que puedes SABOREAR</li>
            </ul>
            <div class="tip-box">
              <strong>💡 Consejo Vitalis</strong>
              5 minutos diarios durante 30 días tienen mayor impacto que 1 hora una vez a la semana. Empieza pequeño y sé consistente.
            </div>
            <h4>Apps recomendadas</h4>
            <ul>
              <li>Headspace: guías en español para principiantes</li>
              <li>Calm: meditaciones, música y relatos para dormir</li>
              <li>Insight Timer: miles de meditaciones gratuitas</li>
              <li>Smiling Mind: diseñada especialmente para jóvenes</li>
            </ul>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art3')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <!-- ART 4: Tostada de aguacate -->
    <div class="art-overlay" id="art4" onclick="cerrarSiFondo(event,'art4')">
      <div class="art-modal">
        <img src="assets/img/nutricion.png" alt="Nutrición" class="art-hero">
        <div class="art-body">
          <span class="art-badge nutricion">Nutrición</span>
          <h2 class="art-title">Receta de Tostada de Aguacate Perfecta</h2>
          <span class="art-date">20 de abril, 2024 · 3 min de lectura</span>
          <div class="art-content">
            <p>La tostada de aguacate combina carbohidratos complejos, grasas saludables y proteína en una comida simple. Aquí la versión nutricionalmente optimizada.</p>
            <h4>Ingredientes (1 porción)</h4>
            <ul>
              <li>1 rebanada de pan integral de centeno o masa madre</li>
              <li>½ aguacate maduro (80-100 g)</li>
              <li>1 huevo pochado, cocido o frito con poco aceite</li>
              <li>Jugo de ½ limón</li>
              <li>Sal de mar y pimienta negra al gusto</li>
              <li>Opcional: hojuelas de chile, semillas de hemp o chía</li>
            </ul>
            <h4>Preparación</h4>
            <ul>
              <li>Tuesta el pan hasta que esté dorado y crujiente</li>
              <li>Aplasta el aguacate con tenedor, añade limón, sal y pimienta</li>
              <li>Extiende la mezcla sobre el pan uniformemente</li>
              <li>Coloca el huevo encima (pochado da la mejor textura)</li>
              <li>Agrega hojuelas de chile y semillas al gusto</li>
            </ul>
            <h4>Valor nutricional aproximado</h4>
            <ul>
              <li>Calorías: 320-380 kcal por porción</li>
              <li>Proteína: 12-15 g (con huevo)</li>
              <li>Grasas saludables: 18-22 g (monoinsaturadas del aguacate)</li>
              <li>Fibra: 8-10 g (pan integral + aguacate)</li>
              <li>Sin azúcares añadidos</li>
            </ul>
            <div class="tip-box">
              <strong>💡 Por qué funciona</strong>
              El aguacate aporta ácido oleico que reduce el colesterol LDL. El pan integral provee energía de lenta liberación. El huevo completa el perfil proteico. Juntos mantienen la saciedad 3-4 horas.
            </div>
            <h4>Variaciones saludables</h4>
            <ul>
              <li>Versión vegana: omite el huevo, agrega tofu sedoso a la plancha</li>
              <li>Versión proteica: salmón ahumado en lugar del huevo</li>
              <li>Versión mediterránea: tomate cherry, orégano y aceitunas negras</li>
            </ul>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art4')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <!-- ART 5: Tai Chi -->
    <div class="art-overlay" id="art5" onclick="cerrarSiFondo(event,'art5')">
      <div class="art-modal">
        <img src="assets/img/ejercicio.png" alt="Ejercicio" class="art-hero">
        <div class="art-body">
          <span class="art-badge ejercicio">Ejercicio</span>
          <h2 class="art-title">Introducción al Tai Chi para Todos</h2>
          <span class="art-date">18 de abril, 2024 · 5 min de lectura</span>
          <div class="art-content">
            <p>El Tai Chi Chuan es un arte marcial chino de movimientos lentos y fluidos coordinados con la respiración. Hoy cuenta con amplia evidencia científica que avala sus beneficios para la salud en todas las edades.</p>
            <h4>Beneficios respaldados por la ciencia</h4>
            <ul>
              <li>Reduce hasta un 45% el riesgo de caídas en adultos mayores</li>
              <li>Disminuye la presión arterial sistólica en 5-10 mmHg en hipertensos</li>
              <li>Mejora equilibrio, coordinación y flexibilidad articular</li>
              <li>Reduce síntomas de ansiedad y depresión leve</li>
              <li>Mejora la calidad del sueño en personas con insomnio</li>
              <li>Alivia el dolor en artritis y fibromialgia</li>
            </ul>
            <h4>Los 5 principios básicos</h4>
            <ul>
              <li>Mente tranquila: concentración suave, sin pensamientos invasivos</li>
              <li>Cuerpo relajado: sin tensión muscular innecesaria</li>
              <li>Postura correcta: columna recta, rodillas ligeramente flexionadas</li>
              <li>Movimiento fluido: lento, continuo y circular, como agua</li>
              <li>Respiración abdominal: profunda y coordinada con el movimiento</li>
            </ul>
            <h4>Secuencia de inicio: 3 primeros movimientos</h4>
            <ul>
              <li>Apertura: pies al ancho de hombros, brazos suben al frente y bajan lentamente</li>
              <li>Empuje doble: palmas empujan al frente transfiriendo peso al pie adelantado</li>
              <li>Giro de cintura: gira suavemente a cada lado, brazos siguen como péndulos</li>
            </ul>
            <div class="tip-box">
              <strong>💡 Para empezar</strong>
              20 minutos, 3 veces por semana son suficientes para sentir los beneficios en 4-6 semanas. Busca clases en parques públicos o el programa de Yang Jwing-Ming en YouTube.
            </div>
            <h4>¿Para quién es ideal?</h4>
            <ul>
              <li>Adultos mayores que buscan mejorar equilibrio y prevenir caídas</li>
              <li>Personas con hipertensión, artritis o estrés crónico</li>
              <li>Quienes no pueden hacer ejercicio de alto impacto</li>
              <li>Cualquier persona que quiera conectar mente y cuerpo</li>
            </ul>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art5')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <!-- ART 6: Descanso y recuperación -->
    <div class="art-overlay" id="art6" onclick="cerrarSiFondo(event,'art6')">
      <div class="art-modal">
        <img src="assets/img/meditar.png" alt="Bienestar" class="art-hero">
        <div class="art-body">
          <span class="art-badge bienestar">Bienestar</span>
          <h2 class="art-title">El Arte del Descanso y la Recuperación</h2>
          <span class="art-date">15 de abril, 2024 · 5 min de lectura</span>
          <div class="art-content">
            <p>El descanso es una función biológica activa y esencial. Durante el sueño, el cerebro elimina toxinas metabólicas, consolida la memoria, regula hormonas y repara tejidos. Dormir bien no es un lujo, es medicina.</p>
            <h4>¿Cuánto sueño necesitamos?</h4>
            <ul>
              <li>Adultos (18-64 años): 7-9 horas por noche</li>
              <li>Adultos mayores (+65): 7-8 horas</li>
              <li>Adolescentes (14-17): 8-10 horas</li>
              <li>Niños en edad escolar: 9-11 horas</li>
            </ul>
            <h4>Las 4 fases del sueño</h4>
            <ul>
              <li>Fase 1 (sueño ligero): transición vigilia-sueño, 5-10 minutos</li>
              <li>Fase 2 (sueño estable): desaceleración cardíaca, memoria a corto plazo</li>
              <li>Fase 3 (sueño profundo): reparación física, hormona del crecimiento, inmunidad</li>
              <li>Fase REM: procesamiento emocional, aprendizaje y creatividad</li>
            </ul>
            <h4>8 hábitos de higiene del sueño basados en evidencia</h4>
            <ul>
              <li>Horario fijo: duerme y despierta a la misma hora, incluso fines de semana</li>
              <li>Oscuridad total: la luz inhibe la producción de melatonina</li>
              <li>Temperatura fresca: 18-20°C es el rango óptimo para dormir</li>
              <li>Sin pantallas 60 min antes: la luz azul suprime la melatonina hasta 3 horas</li>
              <li>Sin cafeína después de las 2pm: su vida media es de 5-7 horas</li>
              <li>Rutina de relajación: baño tibio o lectura señalan al cerebro que es hora de dormir</li>
              <li>No uses la cama para trabajar: asocia la cama solo con sueño</li>
              <li>Evita el alcohol: fragmenta el sueño profundo y REM aunque induzca somnolencia</li>
            </ul>
            <h4>La siesta: cuándo y cómo</h4>
            <p>Una siesta de 10-20 minutos entre 1pm y 3pm mejora el rendimiento cognitivo hasta un 34%. Más de 30 minutos puede generar inercia del sueño y dificultar el sueño nocturno.</p>
            <div class="tip-box">
              <strong>💡 Señal de alerta</strong>
              Si necesitas alarma para despertarte o sientes somnolencia intensa durante el día, probablemente tienes deuda de sueño acumulada. Ningún café ni suplemento puede reemplazar el sueño.
            </div>
          </div>
          <button class="art-close-btn" onclick="cerrarArticulo('art6')">Cerrar artículo</button>
        </div>
      </div>
    </div>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <img src="assets/img/logovitalis.png" alt="Vitalis Logo" class="footer-logo">
                <p>Promoviendo un estilo de vida saludable alineado con el <strong>ODS 3</strong> de las Naciones Unidas.</p>
                <div class="social-icons">
                    <a href="https://www.tiktok.com/@vitalis_salud1" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    <a href="https://www.instagram.com/vitalis_salud.1" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-x-twitter"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Explorar</h3>
                <ul>
                    <li><a href="home.php">Inicio</a></li>
                    <li><a href="blog.php">Nuestro Blog</a></li>
                    <li><a href="tipsalud.php">Tips de Salud</a></li>
                    <li><a href="bienestar.php">Bienestar</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Comunidad</h3>
                <ul>
                    <?php if (empty($_SESSION['es_visitante'])): ?>
                    <li><a href="perfil.php">Mi Perfil</a></li>
                    <?php endif; ?>
                    <li><a href="#" id="openPrivacidad">Privacidad</a></li>
                    <li><a href="#" id="openTerminos">Términos de uso</a></li>
                    <li><a href="#" id="openContacto">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Nuestro Compromiso</h3>
                <p>Facilitamos el acceso a información preventiva de calidad.</p>
                <div class="ods-tag">
                    <i class="fas fa-heartbeat"></i>
                    <span>Objetivo ODS 3</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 <strong>VITALIS</strong>. Todos los derechos reservados.</p>
        </div>
    </footer>

    <div id="logoutModal" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <div style="font-size:32px; margin-bottom:12px;">🚪</div>
            <h2>¿Deseas cerrar sesión?</h2>
            <p>Se perderán los progresos no guardados en tus rutinas de salud.</p>
            <div class="modal-logout-btns">
                <button onclick="confirmarLogout()" class="btn-logout-si">Sí, Salir</button>
                <button onclick="cerrarModal()" class="btn-logout-no">No, Cancelar</button>
            </div>
        </div>
    </div>

    <div id="modalTerminos" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Términos y Condiciones</h2>
            <p style="text-align:left; font-size:13px; color:#555; line-height:1.6;">
                <strong>Vitalis</strong> es una plataforma informativa y educativa. NO proporciona diagnósticos médicos.<br><br>
                <strong>Uso Permitido:</strong> El usuario se compromete a utilizar la página de manera ética y legal.<br><br>
                <strong>Propiedad Intelectual:</strong> Todo el diseño, logotipos y código fuente son propiedad de VITALIS.
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalTerminos')" class="btn-logout-si">Aceptar</button>
            </div>
        </div>
    </div>

    <div id="modalContacto" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Contacto</h2>
            <p style="font-size:13px; color:#555; line-height:1.6;">
                ¿Tienes dudas o sugerencias? Escríbenos:<br><br>
                <strong style="font-size:16px; color:#147a96;">saludybienestarvitalis@gmail.com</strong>
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalContacto')" class="btn-logout-si">Cerrar</button>
            </div>
        </div>
    </div>

    <div id="modalPrivacidad" class="modal-logout-overlay">
        <div class="modal-logout-card">
            <h2 style="color:#147a96; margin-bottom:15px;">Política de Privacidad</h2>
            <p style="text-align:left; font-size:13px; color:#555; line-height:1.6;">
                En <strong>VITALIS</strong>, tu privacidad es nuestra prioridad.<br><br>
                Solo recolectamos información básica para mejorar tu experiencia.<br><br>
                Implementamos medidas de seguridad para proteger tu información.
            </p>
            <div class="modal-logout-btns" style="margin-top:20px;">
                <button onclick="cerrarModalFooter('modalPrivacidad')" class="btn-logout-si">Aceptar</button>
            </div>
        </div>
    </div>

    <style>
    .modal-logout-overlay {
        position: fixed; top:0; left:0; width:100%; height:100%;
        background: rgba(0,0,0,0.5); display:none;
        justify-content:center; align-items:center; z-index:9999;
    }
    .modal-logout-card {
        background:white; padding:35px; border-radius:20px;
        text-align:center; max-width:450px; width:90%;
        box-shadow:0 10px 30px rgba(0,0,0,0.15);
    }
    .modal-logout-card h2 { font-size:20px; color:#004d40; margin-bottom:10px; }
    .modal-logout-card p  { font-size:14px; color:#666; margin-bottom:5px; line-height:1.5; }
    .modal-logout-btns    { display:flex; gap:12px; justify-content:center; margin-top:20px; }
    .btn-logout-si  { background:#00796b; color:white; border:none; padding:12px 25px; border-radius:10px; font-weight:bold; cursor:pointer; }
    .btn-logout-si:hover { background:#004d40; }
    .btn-logout-no  { background:#e0f2f1; color:#00796b; border:2px solid #00796b; padding:12px 25px; border-radius:10px; font-weight:bold; cursor:pointer; }
    .btn-logout-no:hover { background:#b2dfdb; }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
    // ── Artículos ──
    function abrirArticulo(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function cerrarArticulo(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    function cerrarSiFondo(e, id) {
        if (e.target === document.getElementById(id)) cerrarArticulo(id);
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.art-overlay.open').forEach(el => {
                el.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
    });

    // ── Ver más / menos ──
    function mostrarMas() {
        document.querySelectorAll('.extra-content').forEach(c => c.style.display = 'block');
        document.getElementById('btn-show-more').style.display = 'none';
        document.getElementById('btn-show-less').style.display = 'inline-block';
    }
    function mostrarMenos() {
        document.querySelectorAll('.extra-content').forEach(c => c.style.display = 'none');
        document.getElementById('btn-show-less').style.display = 'none';
        document.getElementById('btn-show-more').style.display = 'inline-block';
        document.querySelector('.section-title').scrollIntoView({behavior:'smooth'});
    }

    // ── Modal logout ──
    function abrirModal() { document.getElementById('logoutModal').style.display = 'flex'; }
    function cerrarModal() { document.getElementById('logoutModal').style.display = 'none'; }
    function confirmarLogout() { window.location.href = '/controllers/LogoutController.php'; }

    // ── Modales footer ──
    function cerrarModalFooter(id) { document.getElementById(id).style.display = 'none'; }
    document.getElementById('openTerminos').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalTerminos').style.display='flex';
    });
    document.getElementById('openContacto').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalContacto').style.display='flex';
    });
    document.getElementById('openPrivacidad').addEventListener('click', function(e){
        e.preventDefault(); document.getElementById('modalPrivacidad').style.display='flex';
    });
    </script>

</body>
</html>