<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['user_id'];
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
$rol = $_SESSION['rol'] ?? 'visitante';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Cursos</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/cursos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- SIDEBAR -->
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
            <li class="nav-cursos active"><a href="cursos.php">🎓 Cursos</a></li>
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

<!-- CONTENIDO PRINCIPAL -->
<div class="page-body">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-left">
            <h1><i class="fa-solid fa-graduation-cap"></i> Cursos de Bienestar</h1>
            <p>Desbloquea cursos exclusivos con tus puntos de Juegos Bienestar</p>
        </div>
        <div class="puntos-header">
            <div class="puntos-badge">
                <i class="fa-solid fa-star"></i>
                <div>
                    <span class="pts-value" id="puntos-display">0</span>
                    <span class="pts-label">puntos disponibles</span>
                </div>
            </div>
            <a href="juegos_bienestar.php" class="btn-ganar-pts">
                <i class="fa-solid fa-gamepad"></i> Ganar puntos
            </a>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="filtros-bar">
        <button class="filtro-btn active" data-cat="todos">Todos</button>
        <button class="filtro-btn" data-cat="nutricion">🥗 Nutrición</button>
        <button class="filtro-btn" data-cat="ejercicio">💪 Ejercicio</button>
        <button class="filtro-btn" data-cat="bienestar">🧘 Bienestar</button>
        <button class="filtro-btn" data-cat="salud">❤️ Salud</button>
        <button class="filtro-btn" data-cat="mindfulness">🌿 Mindfulness</button>
    </div>

    <!-- GRID CURSOS -->
    <div class="cursos-grid" id="cursos-grid">
        <div class="loading-cursos">
            <i class="fa-solid fa-spinner fa-spin"></i>
            <p>Cargando cursos...</p>
        </div>
    </div>

</div>

<!-- TOAST NOTIFICACION -->
<div id="toast" class="toast hidden"></div>

<script>
let puntosDisponibles = 0;
let todosCursos = [];

const CATEGORIA_ICONS = {
    nutricion: '🥗',
    ejercicio: '💪',
    bienestar: '🧘',
    salud: '❤️',
    mindfulness: '🌿',
    otro: '📚'
};

async function cargarCursos() {
    try {
        const res = await fetch('../controllers/CursoController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'listar' })
        });
        const data = await res.json();
        if (!data.success) return;

        puntosDisponibles = data.puntos_disponibles;
        todosCursos = data.cursos;
        document.getElementById('puntos-display').textContent = puntosDisponibles.toLocaleString();
        renderCursos(todosCursos);
    } catch(e) { console.error(e); }
}

function renderCursos(cursos) {
    const grid = document.getElementById('cursos-grid');
    if (!cursos.length) {
        grid.innerHTML = '<p class="empty-cursos">No hay cursos disponibles en esta categoría.</p>';
        return;
    }

    grid.innerHTML = cursos.map(c => {
        const desbloqueado = c.desbloqueado == 1;
        const puedePagar = puntosDisponibles >= c.costo_puntos;
        const icon = CATEGORIA_ICONS[c.categoria] || '📚';

        return `
        <div class="curso-card ${desbloqueado ? 'desbloqueado' : ''}" data-cat="${c.categoria}">
            <div class="curso-cat-badge">${icon} ${c.categoria}</div>
            <div class="curso-icon-bg">
                <span class="curso-big-icon">${icon}</span>
            </div>
            <div class="curso-body">
                <h3>${c.titulo}</h3>
                <p>${c.descripcion}</p>
            </div>
            <div class="curso-footer">
                ${desbloqueado
                    ? `<div class="curso-desbloqueado-badge"><i class="fa-solid fa-check-circle"></i> Desbloqueado</div>`
                    : `<div class="curso-costo ${puedePagar ? 'puede-pagar' : 'no-puede'}">
                            <i class="fa-solid fa-star"></i> ${c.costo_puntos} puntos
                       </div>
                       <button class="btn-desbloquear ${puedePagar ? '' : 'disabled'}"
                           onclick="desbloquear(${c.id})"
                           ${puedePagar ? '' : 'disabled'}>
                           ${puedePagar ? '<i class="fa-solid fa-lock-open"></i> Desbloquear' : '<i class="fa-solid fa-lock"></i> Puntos insuficientes'}
                       </button>`
                }
            </div>
        </div>`;
    }).join('');
}

async function desbloquear(cursoId) {
    try {
        const res = await fetch('../controllers/CursoController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'desbloquear', curso_id: cursoId })
        });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            puntosDisponibles = data.puntos_disponibles;
            document.getElementById('puntos-display').textContent = puntosDisponibles.toLocaleString();
            await cargarCursos();
        }
    } catch(e) { console.error(e); }
}

function showToast(msg, type = 'success') {
    const toast = document.getElementById('toast');
    toast.textContent = msg;
    toast.className = `toast ${type}`;
    toast.classList.remove('hidden');
    setTimeout(() => toast.classList.add('hidden'), 3500);
}

// Filtros
document.querySelectorAll('.filtro-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.filtro-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.dataset.cat;
        const filtrados = cat === 'todos' ? todosCursos : todosCursos.filter(c => c.categoria === cat);
        renderCursos(filtrados);
    });
});

cargarCursos();
</script>
</body>
</html>