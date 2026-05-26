<?php
session_start();
if (!isset($_SESSION['user_id']) && empty($_SESSION['es_visitante'])) {
    header("Location: login.php");
    exit();
}
if (!empty($_SESSION['user_id'])) { unset($_SESSION['es_visitante']); }
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once __DIR__ . '/config/Database.php';
$database = new Database();
$db = $database->getConnection();

$stmtFoto = $db->prepare("SELECT foto_perfil FROM usuarios WHERE id = :id");
$stmtFoto->bindParam(':id', $_SESSION['user_id']);
$stmtFoto->execute();
$rowFoto    = $stmtFoto->fetch(PDO::FETCH_ASSOC);
$fotoActual = (!empty($rowFoto['foto_perfil']))
    ? 'uploads/fotos/' . $rowFoto['foto_perfil']
    : 'assets/img/perfil-sin-foto.png';

$userId = $_SESSION['user_id'];
$stmtPosts = $db->prepare("
    SELECT p.*,
           u.nombre,
           u.foto_perfil,
           TIMESTAMPDIFF(MINUTE, p.fecha_creacion, NOW()) as mins_ago,
           COALESCE(SUM(CASE WHEN l.tipo='like'    THEN 1 ELSE 0 END), 0) as total_likes,
           COALESCE(SUM(CASE WHEN l.tipo='dislike' THEN 1 ELSE 0 END), 0) as total_dislikes,
           COALESCE(MAX(CASE WHEN l.usuario_id = :uid AND l.tipo='like'    THEN 1 ELSE 0 END), 0) as yo_di_like,
           COALESCE(MAX(CASE WHEN l.usuario_id = :uid AND l.tipo='dislike' THEN 1 ELSE 0 END), 0) as yo_di_dislike,
           COALESCE(MAX(c.total), 0) as total_comentarios
    FROM posts p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN post_likes l ON l.post_id = p.id
    LEFT JOIN (SELECT post_id, COUNT(*) as total FROM comentarios GROUP BY post_id) c ON c.post_id = p.id
    GROUP BY p.id
    ORDER BY p.fecha_creacion DESC
");
$stmtPosts->bindParam(':uid', $userId);
$stmtPosts->execute();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js"></script>
</head>
<body>

<aside class="sidebar">
    <a href="home.php" class="logo-link">
        <img src="assets/img/logovitalis.png" alt="Vitalis" class="sidebar-logo">
    </a>
    <nav>
        <ul>
            <li><a href="home.php">🏠 Inicio</a></li>
            <li class="active"><a href="blog.php">📝 Blog</a></li>
            <li><a href="consejos.php">🔍 Consejos</a></li>
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
    <div class="search-wrapper">
        <input type="text" placeholder="¿Qué estás buscando?" class="search" id="searchInput">
    </div>

    <div class="blog-layout">
        <main class="main">

            <!-- COMPOSITOR -->
            <div class="composer">
                <div class="composer-top">
                    <img src="<?php echo htmlspecialchars($fotoActual); ?>" class="composer-avatar" alt="Tu foto"
                         onerror="this.src='assets/img/perfil-sin-foto.png'">
                    <form action="/controllers/PostController.php" method="POST"
                          enctype="multipart/form-data" id="postForm">
                        <input type="hidden" name="accion" value="crear">
                        <textarea name="contenido" id="postContent"
                            placeholder="¿Qué está pasando en tu salud hoy? Usa #hashtags"
                            maxlength="500" rows="3"></textarea>

                        <div id="mediaPreview" style="display:none;">
                            <img id="imgPreviewEl" src="" alt="preview" class="media-preview-img">
                            <button type="button" class="remove-media" onclick="removeMedia()">✕</button>
                        </div>

                        <div id="videoPreview" style="display:none;" class="video-preview-box">
                            <i class="fas fa-video"></i>
                            <span id="videoLabel"></span>
                            <button type="button" class="remove-media" onclick="removeVideo()">✕</button>
                        </div>

                        <input type="file" name="imagen" id="imagenInput"
                               accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                        <input type="text" name="video_url" id="videoUrlInput" style="display:none;">
                    </form>
                </div>

                <div class="composer-actions">
                    <div class="composer-btns">
                        <button class="composer-btn" onclick="document.getElementById('imagenInput').click()" title="Imagen">
                            <i class="fas fa-image"></i>
                        </button>
                        <button class="composer-btn" onclick="toggleVideoInput()" title="Video URL">
                            <i class="fas fa-video"></i>
                        </button>
                        <span class="char-counter" id="charCounter">0/500</span>
                    </div>
                    <button class="btn-post" onclick="submitPost()">Postear</button>
                </div>

                <div id="videoInputBox" style="display:none;" class="video-input-box">
                    <i class="fas fa-link"></i>
                    <input type="text" placeholder="Pega URL de YouTube o video..." id="videoUrlField">
                    <button type="button" onclick="applyVideo()">Añadir</button>
                </div>
            </div>

            <!-- FEED -->
            <?php if (empty($posts)): ?>
            <div class="empty-feed">
                <div class="empty-icon">🌱</div>
                <p>Sé el primero en compartir algo con la comunidad Vitalis</p>
            </div>
            <?php else: ?>

            <?php foreach ($posts as $post):
                $fotoPoster = (!empty($post['foto_perfil']))
                    ? 'uploads/fotos/' . $post['foto_perfil']
                    : 'assets/img/perfil-sin-foto.png';
                $initials  = strtoupper(substr($post['nombre'], 0, 2));
                $tiempo    = timeAgo($post['mins_ago']);
                $esMio     = ($post['usuario_id'] == $userId);
                $esAdmin   = (($_SESSION['user_role'] ?? '') === 'admin');
                $yoDiLike     = (bool)$post['yo_di_like'];
                $yoDiDislike  = (bool)$post['yo_di_dislike'];
                $numLikes     = (int)$post['total_likes'];
                $numDislikes     = (int)$post['total_dislikes'];
                $numComentarios  = (int)$post['total_comentarios'];
            ?>
            <div class="post" id="post-<?php echo $post['id']; ?>">

                <div class="post-header">
                    <div class="post-avatar-wrap">
                        <img src="<?php echo htmlspecialchars($fotoPoster); ?>"
                             class="post-avatar-img" alt="<?php echo htmlspecialchars($post['nombre']); ?>"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="post-avatar-initials" style="display:none;"><?php echo $initials; ?></div>
                    </div>
                    <div class="post-author-block">
                        <span class="post-author"><?php echo htmlspecialchars($post['nombre']); ?></span>
                        <span class="post-time"><?php echo $tiempo; ?></span>
                    </div>
                    <?php if ($esMio || $esAdmin): ?>
                    <a href="/controllers/PostController.php?accion=eliminar&id=<?php echo $post['id']; ?>"
                       class="post-delete" onclick="return confirm('¿Eliminar este post?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="post-body">
                    <div class="post-content"><?php echo renderHashtags($post['contenido']); ?></div>

                    <?php if ($post['imagen']): ?>
                    <div class="post-media">
                        <img src="uploads/posts/<?php echo htmlspecialchars($post['imagen']); ?>"
                             alt="imagen" class="post-img">
                    </div>
                    <?php endif; ?>

                    <?php if ($post['video_url']): ?>
                    <div class="post-media">
                        <?php
                        $vurl = $post['video_url'];
                        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $vurl, $m)) {
                            echo '<iframe class="post-video" src="https://www.youtube.com/embed/'.$m[1].'" frameborder="0" allowfullscreen></iframe>';
                        } else {
                            echo '<video class="post-video" controls><source src="'.htmlspecialchars($vurl).'"></video>';
                        }
                        ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($post['hashtags']): ?>
                    <div class="post-tags">
                        <?php foreach (explode(' ', $post['hashtags']) as $tag):
                            if (!$tag) continue; ?>
                        <span class="tag-pill"><?php echo htmlspecialchars($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="post-actions">
                    <button class="action-btn like-btn <?php echo $yoDiLike ? 'active-like' : ''; ?>"
                            onclick="reaccionar(<?php echo $post['id']; ?>, 'like', this)">
                        <i class="fas fa-thumbs-up"></i>
                        <span class="like-count"><?php echo $numLikes; ?></span>
                    </button>
                    <button class="action-btn dislike-btn <?php echo $yoDiDislike ? 'active-dislike' : ''; ?>"
                            onclick="reaccionar(<?php echo $post['id']; ?>, 'dislike', this)">
                        <i class="fas fa-thumbs-down"></i>
                        <span class="dislike-count"><?php echo $numDislikes; ?></span>
                    </button>
                    <button class="action-btn comment-btn"
                            onclick="toggleComentarios(<?php echo $post['id']; ?>)">
                        <i class="far fa-comment"></i>
                        <span class="comment-count"><?php echo $numComentarios; ?></span>
                    </button>
                </div>

                <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display:none;">
                    <div class="comments-list" id="comments-list-<?php echo $post['id']; ?>">
                        <div class="loading-coms"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>
                    </div>
                    <div class="comment-composer">
                        <img src="<?php echo htmlspecialchars($fotoActual); ?>" class="comment-avatar"
                             onerror="this.src='assets/img/perfil-sin-foto.png'">
                        <div class="comment-input-wrap">
                            <input type="text" class="comment-input"
                                   id="input-<?php echo $post['id']; ?>"
                                   placeholder="Escribe un comentario..."
                                   onkeydown="if(event.key==='Enter') enviarComentario(<?php echo $post['id']; ?>)">
                            <button class="comment-send" onclick="enviarComentario(<?php echo $post['id']; ?>)">
                                <i class="fas fa-paper-plane"></i>
                            </button>
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
                <h3>A quién seguir</h3>
                <p>Carlos Herrera</p>
                <p>Laura Díaz</p>
            </div>
        </aside>
    </div>
</div>

<!-- Modal logout -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h2>¿Deseas cerrar sesión?</h2>
        <p>Se perderán los cambios no guardados.</p>
        <div class="modal-btns">
            <button class="btn-confirmar" onclick="window.location.href='/controllers/LogoutController.php'">Sí, Salir</button>
            <button class="btn-cancelar" onclick="document.getElementById('logoutModal').style.display='none'">No, Cancelar</button>
        </div>
    </div>
</div>

<script>
// ---- Contador caracteres ----
const textarea = document.getElementById('postContent');
const counter  = document.getElementById('charCounter');
textarea.addEventListener('input', () => {
    const len = textarea.value.length;
    counter.textContent = len + '/500';
    counter.style.color = len > 450 ? '#ef4444' : '#64748b';
});

function submitPost() {
    if (!textarea.value.trim()) {
        textarea.placeholder = '¡Escribe algo primero!';
        textarea.focus();
        return;
    }
    document.getElementById('postForm').submit();
}

document.getElementById('imagenInput').addEventListener('change', function() {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgPreviewEl').src = e.target.result;
        document.getElementById('mediaPreview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
});

function removeMedia() {
    document.getElementById('imagenInput').value = '';
    document.getElementById('mediaPreview').style.display = 'none';
}

function toggleVideoInput() {
    const box = document.getElementById('videoInputBox');
    box.style.display = box.style.display === 'none' ? 'flex' : 'none';
}
function applyVideo() {
    const url = document.getElementById('videoUrlField').value.trim();
    if (!url) return;
    document.getElementById('videoUrlInput').value = url;
    document.getElementById('videoLabel').textContent = url.length > 40 ? url.substring(0,40)+'...' : url;
    document.getElementById('videoPreview').style.display = 'flex';
    document.getElementById('videoInputBox').style.display = 'none';
}
function removeVideo() {
    document.getElementById('videoUrlInput').value = '';
    document.getElementById('videoPreview').style.display = 'none';
    document.getElementById('videoUrlField').value = '';
}

function reaccionar(postId, tipo, btnClicado) {
    const fd = new FormData();
    fd.append('post_id', postId);
    fd.append('tipo', tipo);
    fetch('/controllers/LikeController.php', { method:'POST', body: fd })
        .then(r => r.text())
        .then(text => {
            let data;
            try { data = JSON.parse(text); }
            catch(e) { console.error('Respuesta inválida:', text); return; }
            if (data.error) { console.error('Error:', data.error); return; }
            const postEl     = document.getElementById('post-' + postId);
            const likeBtn    = postEl.querySelector('.like-btn');
            const dislikeBtn = postEl.querySelector('.dislike-btn');
            likeBtn.classList.remove('active-like');
            dislikeBtn.classList.remove('active-dislike');
            if (data.mi_reaccion === 'like') {
                likeBtn.classList.add('active-like');
                btnClicado.classList.add('pop');
                setTimeout(() => btnClicado.classList.remove('pop'), 300);
            } else if (data.mi_reaccion === 'dislike') {
                dislikeBtn.classList.add('active-dislike');
                btnClicado.classList.add('pop');
                setTimeout(() => btnClicado.classList.remove('pop'), 300);
            }
            likeBtn.querySelector('.like-count').textContent       = Number(data.likes)    || 0;
            dislikeBtn.querySelector('.dislike-count').textContent = Number(data.dislikes) || 0;
        })
        .catch(err => console.error('Error fetch likes:', err));
}

const comentariosAbiertos = new Set();

function toggleComentarios(postId) {
    const section = document.getElementById('comments-' + postId);
    const btn     = document.querySelector('#post-' + postId + ' .comment-btn');
    if (comentariosAbiertos.has(postId)) {
        section.style.display = 'none';
        comentariosAbiertos.delete(postId);
        btn.classList.remove('active-comment');
    } else {
        section.style.display = 'block';
        comentariosAbiertos.add(postId);
        btn.classList.add('active-comment');
        cargarComentarios(postId);
        document.getElementById('input-' + postId).focus();
    }
}

function cargarComentarios(postId) {
    const list = document.getElementById('comments-list-' + postId);
    fetch('/controllers/ComentarioController.php?accion=obtener&post_id=' + postId)
        .then(r => r.json())
        .then(coms => {
            if (!coms.length) {
                list.innerHTML = '<p class="no-comments">Sé el primero en comentar 💬</p>';
                return;
            }
            list.innerHTML = coms.map(c => renderComment(c, postId)).join('');
        })
        .catch(() => { list.innerHTML = '<p class="no-comments">Error al cargar comentarios.</p>'; });
}

function renderComment(c, postId) {
    const foto = c.foto_perfil ? 'uploads/fotos/' + c.foto_perfil : 'assets/img/perfil-sin-foto.png';
    const fecha = new Date(c.fecha).toLocaleString('es-MX', {day:'2-digit',month:'short',hour:'2-digit',minute:'2-digit'});
    const esMio = c.usuario_id == <?php echo $_SESSION['user_id']; ?>;
    const esAdmin = <?php echo ($_SESSION['user_role'] ?? '') === 'admin' ? 'true' : 'false'; ?>;
    const delBtn = (esMio || esAdmin)
        ? `<button class="comment-delete" onclick="eliminarComentario(${c.id}, ${postId})" title="Eliminar"><i class="fas fa-trash-alt"></i></button>`
        : '';
    return `
        <div class="comment-item" id="comment-item-${c.id}">
            <img src="${foto}" class="comment-avatar-sm" onerror="this.src='assets/img/perfil-sin-foto.png'">
            <div class="comment-bubble">
                <div class="comment-bubble-header">
                    <span class="comment-author">${c.nombre}</span>
                    <span class="comment-date">${fecha}</span>
                    ${delBtn}
                </div>
                <p class="comment-text">${c.contenido}</p>
            </div>
        </div>`;
}

function enviarComentario(postId) {
    const input = document.getElementById('input-' + postId);
    const texto = input.value.trim();
    if (!texto) return;
    const fd = new FormData();
    fd.append('accion', 'crear');
    fd.append('post_id', postId);
    fd.append('contenido', texto);
    fetch('/controllers/ComentarioController.php', { method:'POST', body: fd })
        .then(r => r.json())
        .then(data => {
            if (data.error === 'groseria') {
                alert(data.mensaje);
                return;
            }
            if (!data.ok) return;
            const list = document.getElementById('comments-list-' + postId);
            const nocom = list.querySelector('.no-comments');
            if (nocom) nocom.remove();
            list.insertAdjacentHTML('beforeend', renderComment(data.comentario, postId));
            list.lastElementChild.scrollIntoView({ behavior:'smooth', block:'nearest' });
            input.value = '';
            const countEl = document.querySelector('#post-' + postId + ' .comment-count');
            countEl.textContent = parseInt(countEl.textContent || 0) + 1;
        })
        .catch(console.error);
}

function eliminarComentario(comId, postId) {
    if (!confirm('¿Eliminar este comentario?')) return;
    fetch('/controllers/ComentarioController.php?accion=eliminar&id=' + comId)
        .then(r => r.json())
        .then(data => {
            if (!data.ok) return;
            document.getElementById('comment-item-' + comId)?.remove();
            const countEl = document.querySelector('#post-' + postId + ' .comment-count');
            const current = parseInt(countEl.textContent || 0);
            if (current > 0) countEl.textContent = current - 1;
        })
        .catch(console.error);
}

document.getElementById('searchInput').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.post').forEach(post => {
        post.style.display = post.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
</body>
</html>