<?php
session_start();
// Solo visitantes — si tiene cuenta real que use el blog normal
if (!empty($_SESSION['user_id'])) {
    header("Location: blog.php"); exit();
}
if (!isset($_SESSION['es_visitante'])) {
    header("Location: index.php"); exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once __DIR__ . '/config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Posts con conteos
$stmtPosts = $db->query("
    SELECT p.*,
           u.nombre, u.foto_perfil,
           TIMESTAMPDIFF(MINUTE, p.fecha_creacion, NOW()) as mins_ago,
           COALESCE(SUM(CASE WHEN l.tipo='like'    THEN 1 ELSE 0 END),0) as total_likes,
           COALESCE(SUM(CASE WHEN l.tipo='dislike' THEN 1 ELSE 0 END),0) as total_dislikes,
           COALESCE((SELECT COUNT(*) FROM comentarios c WHERE c.post_id=p.id),0) as total_comentarios
    FROM posts p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN post_likes l ON l.post_id = p.id
    GROUP BY p.id
    ORDER BY p.fecha_creacion DESC
");
$posts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

function timeAgo($mins) {
    if ($mins < 1)    return 'ahora';
    if ($mins < 60)   return $mins . 'm';
    if ($mins < 1440) return round($mins/60) . 'h';
    return round($mins/1440) . 'd';
}

function renderHashtags($texto) {
    return preg_replace('/#(\w+)/u',
        '<span class="hashtag">#$1</span>',
        htmlspecialchars($texto));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Blog</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/blog.css">
    <link rel="stylesheet" href="assets/css/blog_visitante.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Sidebar visitante -->
<aside class="sidebar">
    <a href="index.php" class="logo-link">
        <img src="assets/img/logovitalis.png" alt="Vitalis" class="sidebar-logo">
    </a>
    <nav>
        <ul>
            <li class="active"><a href="blog_visitante.php">📝 Blog</a></li>
        </ul>
    </nav>
    <!-- Banner de registro -->
    <div class="visitor-banner">
        <p>¿Quieres participar?</p>
        <a href="registro.php" class="vb-btn-register">Crear cuenta</a>
        <a href="login.php"    class="vb-btn-login">Iniciar sesión</a>
    </div>
</aside>

<div class="page-body">

    <!-- Banner informativo -->
    <div class="visitor-info-bar">
        <i class="fas fa-eye"></i>
        <span>Estás navegando como <strong>Visitante</strong> — puedes leer publicaciones y ver comentarios.</span>
        <div class="vib-actions">
            <a href="login.php"    class="vib-btn vib-login">Iniciar sesión</a>
            <a href="registro.php" class="vib-btn vib-register">Registrarse</a>
        </div>
    </div>

    <div class="search-wrapper">
        <input type="text" placeholder="¿Qué estás buscando?" class="search" id="searchInput">
    </div>

    <div class="blog-layout">
        <main class="main">

            <!-- COMPOSITOR BLOQUEADO -->
            <div class="composer composer-locked" onclick="showLoginPrompt()">
                <div class="composer-top">
                    <div class="composer-avatar-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="locked-placeholder">
                        <span>Inicia sesión para publicar algo...</span>
                    </div>
                </div>
                <div class="composer-actions">
                    <div class="composer-btns">
                        <button class="composer-btn" disabled><i class="fas fa-image"></i></button>
                        <button class="composer-btn" disabled><i class="fas fa-video"></i></button>
                    </div>
                    <button class="btn-post btn-post-locked" onclick="showLoginPrompt()">Postear</button>
                </div>
            </div>

            <!-- FEED -->
            <?php if (empty($posts)): ?>
            <div class="empty-feed">
                <div class="empty-icon">🌱</div>
                <p>Aún no hay publicaciones</p>
            </div>
            <?php else: ?>
            <?php foreach ($posts as $post):
                $fotoPoster = (!empty($post['foto_perfil']))
                    ? 'uploads/fotos/' . $post['foto_perfil']
                    : 'assets/img/perfil-sin-foto.png';
                $initials = strtoupper(substr($post['nombre'], 0, 2));
                $tiempo   = timeAgo($post['mins_ago']);
            ?>
            <div class="post" id="post-<?= $post['id'] ?>">
                <!-- Cabecera -->
                <div class="post-header">
                    <div class="post-avatar-wrap">
                        <img src="<?= htmlspecialchars($fotoPoster) ?>"
                             class="post-avatar-img" alt="<?= htmlspecialchars($post['nombre']) ?>"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="post-avatar-initials" style="display:none;"><?= $initials ?></div>
                    </div>
                    <div class="post-author-block">
                        <span class="post-author"><?= htmlspecialchars($post['nombre']) ?></span>
                        <span class="post-time"><?= $tiempo ?></span>
                    </div>
                </div>

                <!-- Cuerpo -->
                <div class="post-body">
                    <div class="post-content"><?= renderHashtags($post['contenido']) ?></div>

                    <?php if ($post['imagen']): ?>
                    <div class="post-media">
                        <img src="uploads/posts/<?= htmlspecialchars($post['imagen']) ?>"
                             alt="imagen" class="post-img">
                    </div>
                    <?php endif; ?>

                    <?php if ($post['video_url']): ?>
                    <div class="post-media">
                        <?php
                        $vurl = $post['video_url'];
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $vurl, $m)) {
                            echo '<iframe class="post-video" src="https://www.youtube.com/embed/'.$m[1].'" frameborder="0" allowfullscreen></iframe>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($post['hashtags']): ?>
                    <div class="post-tags">
                        <?php foreach (explode(' ', $post['hashtags']) as $tag): if (!$tag) continue; ?>
                        <span class="tag-pill"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Acciones (solo lectura) -->
                <div class="post-actions">
                    <button class="action-btn" onclick="showLoginPrompt()" title="Inicia sesión para reaccionar">
                        <i class="fas fa-thumbs-up"></i>
                        <span><?= $post['total_likes'] ?></span>
                    </button>
                    <button class="action-btn" onclick="showLoginPrompt()" title="Inicia sesión para reaccionar">
                        <i class="fas fa-thumbs-down"></i>
                        <span><?= $post['total_dislikes'] ?></span>
                    </button>
                    <button class="action-btn" onclick="toggleComentariosVisitante(<?= $post['id'] ?>)">
                        <i class="far fa-comment"></i>
                        <span><?= $post['total_comentarios'] ?></span>
                    </button>
                </div>

                <!-- Comentarios solo lectura -->
                <div class="comments-section" id="comments-<?= $post['id'] ?>" style="display:none;">
                    <div class="comments-list" id="comments-list-<?= $post['id'] ?>">
                        <div class="loading-coms"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>
                    </div>
                    <!-- Input bloqueado -->
                    <div class="comment-composer" onclick="showLoginPrompt()" style="cursor:pointer;">
                        <div class="composer-avatar-placeholder sm"><i class="fas fa-user"></i></div>
                        <div class="comment-input-wrap locked-input">
                            <span class="locked-text">Inicia sesión para comentar...</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
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
                <h3>¡Únete a Vitalis!</h3>
                <p style="font-size:13px;color:#64748b;margin-bottom:12px;">Crea tu cuenta y comienza a publicar, reaccionar y comentar.</p>
                <a href="registro.php" class="card-join-btn">Crear cuenta gratis</a>
            </div>
        </aside>
    </div>
</div>

<!-- Modal login prompt -->
<div id="loginPromptModal" class="modal-overlay" style="display:none;">
    <div class="modal-card">
        <div class="modal-icon">🔒</div>
        <h2>Inicia sesión para continuar</h2>
        <p>Necesitas una cuenta para publicar, reaccionar y comentar en Vitalis.</p>
        <div class="modal-btns">
            <a href="login.php"    class="btn-confirmar">Iniciar sesión</a>
            <a href="registro.php" class="btn-cancelar" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">Registrarse</a>
        </div>
        <button onclick="document.getElementById('loginPromptModal').style.display='none'"
                style="margin-top:12px;background:none;border:none;color:#aaa;cursor:pointer;font-size:13px;">
            Seguir navegando
        </button>
    </div>
</div>

<script>
function showLoginPrompt() {
    document.getElementById('loginPromptModal').style.display = 'flex';
}

// Comentarios solo lectura
function toggleComentariosVisitante(postId) {
    const section = document.getElementById('comments-' + postId);
    const visible = section.style.display !== 'none';
    section.style.display = visible ? 'none' : 'block';
    if (!visible) cargarComentariosVisitante(postId);
}

function cargarComentariosVisitante(postId) {
    const list = document.getElementById('comments-list-' + postId);
    fetch('/controllers/ComentarioController.php?accion=obtener&post_id=' + postId)
        .then(r => r.json())
        .then(coms => {
            if (!coms.length) {
                list.innerHTML = '<p class="no-comments">Sin comentarios aún.</p>';
                return;
            }
            list.innerHTML = coms.map(c => {
                const foto  = c.foto_perfil ? 'uploads/fotos/' + c.foto_perfil : 'assets/img/perfil-sin-foto.png';
                const fecha = new Date(c.fecha).toLocaleString('es-MX',{day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
                return `<div class="comment-item">
                    <img src="${foto}" class="comment-avatar-sm" onerror="this.src='assets/img/perfil-sin-foto.png'">
                    <div class="comment-bubble">
                        <div class="comment-bubble-header">
                            <span class="comment-author">${c.nombre}</span>
                            <span class="comment-date">${fecha}</span>
                        </div>
                        <p class="comment-text">${c.contenido}</p>
                    </div>
                </div>`;
            }).join('');
        });
}

// Búsqueda
document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.post').forEach(p => {
        p.style.display = p.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>