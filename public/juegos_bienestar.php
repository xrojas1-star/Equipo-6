<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$usuario_id = $_SESSION['user_id'];
$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';
$rol = $_SESSION['rol'] ?? 'visitante';

// Cargar datos directamente desde PHP
require_once '../config/Database.php';
$_db = new Database();
$_conn = $_db->getConnection();

// Puntos disponibles
$_stmt = $_conn->prepare("SELECT puntos_disponibles FROM usuario_puntos WHERE usuario_id = ?");
$_stmt->execute([$usuario_id]);
$_ptRow = $_stmt->fetch(PDO::FETCH_ASSOC);
$puntos_iniciales = $_ptRow ? (int)$_ptRow['puntos_disponibles'] : 0;

// Stats personales
$_stmt = $_conn->prepare("
    SELECT MAX(puntaje) AS mejor_puntaje,
           COUNT(*) AS partidas_jugadas,
           SUM(puntos_ganados) AS puntos_ganados_total
    FROM tetris_partidas WHERE usuario_id = ?
");
$_stmt->execute([$usuario_id]);
$_personal = $_stmt->fetch(PDO::FETCH_ASSOC);
$mi_mejor    = $_personal && $_personal['mejor_puntaje']       ? (int)$_personal['mejor_puntaje']       : 0;
$mi_partidas = $_personal && $_personal['partidas_jugadas']    ? (int)$_personal['partidas_jugadas']    : 0;
$mi_pts_gan  = $_personal && $_personal['puntos_ganados_total']? (int)$_personal['puntos_ganados_total']: 0;

// Top 10 leaderboard
$_stmt = $_conn->prepare("
    SELECT u.nombre, u.foto_perfil,
           MAX(tp.puntaje) AS mejor_puntaje
    FROM tetris_partidas tp
    JOIN usuarios u ON u.id = tp.usuario_id
    GROUP BY tp.usuario_id, u.nombre, u.foto_perfil
    ORDER BY mejor_puntaje DESC
    LIMIT 10
");
$_stmt->execute();
$_top10 = $_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Juegos Bienestar</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/juegos_bienestar.css">
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
            <li class="nav-cursos"><a href="cursos.php">🎓 Cursos</a></li>
            <li class="nav-juegos active"><a href="juegos_bienestar.php">🎮 Juegos Bienestar</a></li>
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
            <h1><i class="fa-solid fa-gamepad"></i> Juegos Bienestar</h1>
            <p>Juega, gana puntos y desbloquea cursos exclusivos</p>
        </div>
        <div class="puntos-badge">
            <i class="fa-solid fa-star"></i>
            <span id="puntos-display"><?= $puntos_iniciales ?></span>
            <span class="puntos-label">puntos disponibles</span>
        </div>
    </div>

    <div class="games-layout">

        <!-- TETRIS -->
        <div class="tetris-section">
            <div class="game-card">
                <div class="game-header">
                    <h2><i class="fa-solid fa-border-all"></i> Tetris Vitalis</h2>
                    <div class="game-stats">
                        <div class="stat">
                            <span class="stat-label">Puntaje</span>
                            <span class="stat-value" id="score">0</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Nivel</span>
                            <span class="stat-value" id="level">1</span>
                        </div>
                        <div class="stat">
                            <span class="stat-label">Líneas</span>
                            <span class="stat-value" id="lines">0</span>
                        </div>
                    </div>
                </div>

                <div class="tetris-wrapper">
                    <canvas id="tetris-canvas" width="300" height="600"></canvas>
                    <div class="tetris-side">
                        <div class="next-piece-box">
                            <p>Siguiente</p>
                            <canvas id="next-canvas" width="120" height="120"></canvas>
                        </div>
                        <div class="controls-hint">
                            <p><strong>Controles</strong></p>
                            <p>← → Mover</p>
                            <p>↑ Rotar</p>
                            <p>↓ Bajar</p>
                            <p>Espacio: Drop</p>
                        </div>
                    </div>
                </div>

                <div class="game-controls">
                    <button id="btn-start" class="btn-game btn-start">
                        <i class="fa-solid fa-play"></i> Iniciar
                    </button>
                    <button id="btn-pause" class="btn-game btn-pause" disabled>
                        <i class="fa-solid fa-pause"></i> Pausar
                    </button>
                </div>

                <!-- GAME OVER OVERLAY -->
                <div id="game-over-overlay" class="game-over-overlay hidden">
                    <div class="game-over-content">
                        <h3>¡Juego Terminado!</h3>
                        <p>Puntaje final: <strong id="final-score">0</strong></p>
                        <p class="puntos-ganados-text">+<strong id="puntos-ganados">0</strong> puntos Vitalis ganados</p>
                        <button id="btn-restart" class="btn-game btn-start">
                            <i class="fa-solid fa-rotate-right"></i> Jugar de nuevo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- LEADERBOARD -->
        <div class="leaderboard-section">
            <div class="leaderboard-card">
                <h2><i class="fa-solid fa-trophy"></i> Tabla de Clasificación</h2>

                <!-- Stats personales -->
                <div class="personal-stats">
                    <h3><i class="fa-solid fa-user"></i> Mis estadísticas</h3>
                    <div class="personal-grid">
                        <div class="personal-stat">
                            <span class="ps-value" id="mi-mejor">0</span>
                            <span class="ps-label">Mejor puntaje</span>
                        </div>
                        <div class="personal-stat">
                            <span class="ps-value" id="mi-partidas">0</span>
                            <span class="ps-label">Partidas</span>
                        </div>
                        <div class="personal-stat">
                            <span class="ps-value" id="mi-puntos-ganados">0</span>
                            <span class="ps-label">Pts ganados</span>
                        </div>
                    </div>
                </div>

                <!-- Top 10 -->
                <div class="top10-list" id="top10-list">
                    <div class="loading-lb">
                        <i class="fa-solid fa-spinner fa-spin"></i> Cargando...
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
// ===================== TETRIS ENGINE =====================
const canvas = document.getElementById('tetris-canvas');
const ctx = canvas.getContext('2d');
const nextCanvas = document.getElementById('next-canvas');
const nextCtx = nextCanvas.getContext('2d');

const COLS = 10, ROWS = 20, BLOCK = 30;

const COLORS = [
    null,
    '#00f5ff', // I - cyan
    '#ffd700', // O - gold
    '#a855f7', // T - purple
    '#22c55e', // S - green
    '#ef4444', // Z - red
    '#3b82f6', // J - blue
    '#f97316', // L - orange
];

const SHAPES = [
    null,
    [[0,0,0,0],[1,1,1,1],[0,0,0,0],[0,0,0,0]], // I
    [[2,2],[2,2]],                               // O
    [[0,3,0],[3,3,3],[0,0,0]],                   // T
    [[0,4,4],[4,4,0],[0,0,0]],                   // S
    [[5,5,0],[0,5,5],[0,0,0]],                   // Z
    [[6,0,0],[6,6,6],[0,0,0]],                   // J
    [[0,0,7],[7,7,7],[0,0,0]],                   // L
];

let board, piece, nextPiece, score, level, lines, gameLoop, isPaused, isGameOver;

function createBoard() {
    return Array.from({length: ROWS}, () => Array(COLS).fill(0));
}

function randomPiece() {
    const id = Math.floor(Math.random() * 7) + 1;
    const shape = SHAPES[id].map(row => [...row]);
    return { id, shape, x: Math.floor(COLS / 2) - Math.floor(shape[0].length / 2), y: 0 };
}

function drawBlock(context, x, y, colorId, size = BLOCK) {
    if (!colorId) return;
    const color = COLORS[colorId];
    context.fillStyle = color;
    context.fillRect(x * size + 1, y * size + 1, size - 2, size - 2);
    context.fillStyle = 'rgba(255,255,255,0.2)';
    context.fillRect(x * size + 1, y * size + 1, size - 2, 4);
    context.fillStyle = 'rgba(0,0,0,0.2)';
    context.fillRect(x * size + 1, y * size + size - 5, size - 2, 4);
}

function drawBoard() {
    ctx.fillStyle = '#0a0a0f';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // Grid lines
    ctx.strokeStyle = 'rgba(255,255,255,0.03)';
    for (let r = 0; r < ROWS; r++) {
        for (let c = 0; c < COLS; c++) {
            ctx.strokeRect(c * BLOCK, r * BLOCK, BLOCK, BLOCK);
        }
    }

    // Board cells
    board.forEach((row, r) => {
        row.forEach((cell, c) => {
            if (cell) drawBlock(ctx, c, r, cell);
        });
    });
}

function drawPiece(p) {
    p.shape.forEach((row, r) => {
        row.forEach((cell, c) => {
            if (cell) drawBlock(ctx, p.x + c, p.y + r, cell);
        });
    });
}

function drawGhost() {
    let ghost = { ...piece, shape: piece.shape.map(r => [...r]) };
    while (!collision(ghost, 0, 1)) ghost.y++;
    ctx.globalAlpha = 0.2;
    ghost.shape.forEach((row, r) => {
        row.forEach((cell, c) => {
            if (cell) drawBlock(ctx, ghost.x + c, ghost.y + r, cell);
        });
    });
    ctx.globalAlpha = 1;
}

function drawNext() {
    nextCtx.fillStyle = '#0a0a0f';
    nextCtx.fillRect(0, 0, 120, 120);
    const size = 24;
    const offsetX = Math.floor((4 - nextPiece.shape[0].length) / 2);
    const offsetY = Math.floor((4 - nextPiece.shape.length) / 2);
    nextPiece.shape.forEach((row, r) => {
        row.forEach((cell, c) => {
            if (cell) {
                nextCtx.fillStyle = COLORS[cell];
                nextCtx.fillRect((offsetX + c) * size + 1, (offsetY + r) * size + 1, size - 2, size - 2);
            }
        });
    });
}

function collision(p, dx = 0, dy = 0, shape = null) {
    const s = shape || p.shape;
    return s.some((row, r) =>
        row.some((cell, c) => {
            if (!cell) return false;
            const nx = p.x + c + dx;
            const ny = p.y + r + dy;
            return nx < 0 || nx >= COLS || ny >= ROWS || (ny >= 0 && board[ny][nx]);
        })
    );
}

function merge() {
    piece.shape.forEach((row, r) => {
        row.forEach((cell, c) => {
            if (cell) board[piece.y + r][piece.x + c] = cell;
        });
    });
}

function clearLines() {
    let cleared = 0;
    for (let r = ROWS - 1; r >= 0; r--) {
        if (board[r].every(cell => cell !== 0)) {
            board.splice(r, 1);
            board.unshift(Array(COLS).fill(0));
            cleared++;
            r++;
        }
    }
    if (cleared > 0) {
        const pts = [0, 100, 300, 500, 800][cleared] * level;
        score += pts;
        lines += cleared;
        level = Math.floor(lines / 10) + 1;
        updateStats();
    }
}

function updateStats() {
    document.getElementById('score').textContent = score.toLocaleString();
    document.getElementById('level').textContent = level;
    document.getElementById('lines').textContent = lines;
}

function getSpeed() {
    return Math.max(100, 800 - (level - 1) * 70);
}

function gameStep() {
    if (isPaused || isGameOver) return;
    if (!collision(piece, 0, 1)) {
        piece.y++;
    } else {
        merge();
        clearLines();
        piece = nextPiece;
        nextPiece = randomPiece();
        drawNext();
        if (collision(piece)) {
            endGame();
            return;
        }
    }
    render();
    gameLoop = setTimeout(gameStep, getSpeed());
}

function render() {
    drawBoard();
    drawGhost();
    drawPiece(piece);
}

function startGame() {
    board = createBoard();
    score = 0; level = 1; lines = 0;
    isPaused = false; isGameOver = false;
    piece = randomPiece();
    nextPiece = randomPiece();
    updateStats();
    drawNext();
    document.getElementById('game-over-overlay').classList.add('hidden');
    document.getElementById('btn-start').disabled = true;
    document.getElementById('btn-pause').disabled = false;
    clearTimeout(gameLoop);
    gameStep();
}

function endGame() {
    isGameOver = true;
    clearTimeout(gameLoop);
    document.getElementById('btn-start').disabled = false;
    document.getElementById('btn-pause').disabled = true;
    document.getElementById('final-score').textContent = score.toLocaleString();
    const ptsGanados = Math.floor(score / 100);
    document.getElementById('puntos-ganados').textContent = ptsGanados;
    document.getElementById('game-over-overlay').classList.remove('hidden');
    guardarPartida(score);
}

async function guardarPartida(puntaje) {
    try {
        const res = await fetch('../controllers/TetrisController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'guardar_partida', puntaje })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('puntos-display').textContent = data.puntos_disponibles.toLocaleString();
            cargarLeaderboard();
        }
    } catch(e) { console.error(e); }
}

async function cargarLeaderboard() {
    const list = document.getElementById('top10-list');
    list.innerHTML = '<div class="loading-lb"><i class="fa-solid fa-spinner fa-spin"></i> Cargando...</div>';
    try {
        const res = await fetch('../controllers/TetrisController.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ accion: 'leaderboard' })
        });
        const data = await res.json();
        if (!data.success) {
            list.innerHTML = '<p class="empty-lb">Error al cargar. <a href="#" onclick="cargarLeaderboard();return false;">Reintentar</a></p>';
            return;
        }

        document.getElementById('puntos-display').textContent = (data.puntos_disponibles || 0).toLocaleString();
        document.getElementById('mi-mejor').textContent = (data.personal?.mejor_puntaje || 0).toLocaleString();
        document.getElementById('mi-partidas').textContent = data.personal?.partidas_jugadas || 0;
        document.getElementById('mi-puntos-ganados').textContent = data.personal?.puntos_ganados_total || 0;

        if (!data.top10 || data.top10.length === 0) {
            list.innerHTML = '<p class="empty-lb">Aún no hay partidas registradas.<br>¡Sé el primero!</p>';
            return;
        }

        list.innerHTML = data.top10.map((p, i) => {
            const foto = p.foto_perfil ? `uploads/fotos/${p.foto_perfil}` : 'assets/img/perfil-sin-foto.png';
            const medal = i === 0 ? '🥇' : i === 1 ? '🥈' : i === 2 ? '🥉' : `#${i+1}`;
            return `
                <div class="lb-row ${i < 3 ? 'top-three' : ''}">
                    <span class="lb-rank">${medal}</span>
                    <img src="${foto}" class="lb-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                    <span class="lb-name">${p.nombre}</span>
                    <span class="lb-score">${parseInt(p.mejor_puntaje).toLocaleString()}</span>
                </div>
            `;
        }).join('');
    } catch(e) { console.error(e); }
}

// Controles teclado
document.addEventListener('keydown', e => {
    if (isGameOver || isPaused) return;
    if (!piece) return;
    switch(e.key) {
        case 'ArrowLeft':
            if (!collision(piece, -1, 0)) { piece.x--; render(); }
            e.preventDefault(); break;
        case 'ArrowRight':
            if (!collision(piece, 1, 0)) { piece.x++; render(); }
            e.preventDefault(); break;
        case 'ArrowDown':
            if (!collision(piece, 0, 1)) { piece.y++; score += 1; updateStats(); render(); }
            e.preventDefault(); break;
        case 'ArrowUp':
            rotatePiece(); e.preventDefault(); break;
        case ' ':
            hardDrop(); e.preventDefault(); break;
    }
});

function rotatePiece() {
    const rotated = piece.shape[0].map((_, i) => piece.shape.map(row => row[i]).reverse());
    if (!collision(piece, 0, 0, rotated)) {
        piece.shape = rotated;
        render();
    }
}

function hardDrop() {
    while (!collision(piece, 0, 1)) { piece.y++; score += 2; }
    updateStats();
    render();
    merge();
    clearLines();
    piece = nextPiece;
    nextPiece = randomPiece();
    drawNext();
    if (collision(piece)) { endGame(); return; }
    clearTimeout(gameLoop);
    gameStep();
}

// Botones
document.getElementById('btn-start').addEventListener('click', startGame);
document.getElementById('btn-restart').addEventListener('click', startGame);
document.getElementById('btn-pause').addEventListener('click', () => {
    isPaused = !isPaused;
    const btn = document.getElementById('btn-pause');
    btn.innerHTML = isPaused
        ? '<i class="fa-solid fa-play"></i> Reanudar'
        : '<i class="fa-solid fa-pause"></i> Pausar';
    if (!isPaused) gameStep();
});

// Init
document.addEventListener('DOMContentLoaded', () => {
    drawBoard();
    // Leaderboard ya cargado desde PHP — solo se refresca al terminar una partida
});
</script>
</body>
</html>