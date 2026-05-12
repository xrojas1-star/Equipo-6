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

require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();
$userId = $_SESSION['user_id'];

$stmtUser = $db->prepare("SELECT nombre, foto_perfil, foto_portada, portada_posicion, biografia, correo, creado_en FROM usuarios WHERE id = :id");
$stmtUser->bindParam(':id', $userId);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$fotoPerfil      = !empty($user['foto_perfil'])  ? 'uploads/fotos/'    . $user['foto_perfil']  : 'assets/img/perfil-sin-foto.png';
$fotoPortada     = !empty($user['foto_portada']) ? 'uploads/portadas/' . $user['foto_portada'] : null;
$portadaPosicion = $user['portada_posicion'] ?? '50%';
$biografia       = $user['biografia'] ?? '';

$stmtPC = $db->prepare("SELECT COUNT(*) FROM posts WHERE usuario_id=:id"); $stmtPC->execute([':id'=>$userId]); $numPosts = $stmtPC->fetchColumn();
$stmtLC = $db->prepare("SELECT COUNT(*) FROM post_likes WHERE usuario_id=:id AND tipo='like'"); $stmtLC->execute([':id'=>$userId]); $numLikes = $stmtLC->fetchColumn();
$stmtRC = $db->prepare("SELECT COUNT(*) FROM comentarios WHERE usuario_id=:id"); $stmtRC->execute([':id'=>$userId]); $numRespuestas = $stmtRC->fetchColumn();

$stmtPosts = $db->prepare("
    SELECT p.*, COALESCE(SUM(CASE WHEN l.tipo='like' THEN 1 ELSE 0 END),0) as total_likes,
    COALESCE(SUM(CASE WHEN l.tipo='dislike' THEN 1 ELSE 0 END),0) as total_dislikes,
    COALESCE((SELECT COUNT(*) FROM comentarios c WHERE c.post_id=p.id),0) as total_comentarios,
    TIMESTAMPDIFF(MINUTE,p.fecha_creacion,NOW()) as mins_ago
    FROM posts p LEFT JOIN post_likes l ON l.post_id=p.id
    WHERE p.usuario_id=:id GROUP BY p.id ORDER BY p.fecha_creacion DESC");
$stmtPosts->execute([':id'=>$userId]); $misPosts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

$stmtR = $db->prepare("SELECT c.*, p.contenido as post_contenido, u.nombre as post_autor FROM comentarios c JOIN posts p ON p.id=c.post_id JOIN usuarios u ON u.id=p.usuario_id WHERE c.usuario_id=:id ORDER BY c.fecha DESC");
$stmtR->execute([':id'=>$userId]); $misRespuestas = $stmtR->fetchAll(PDO::FETCH_ASSOC);

$stmtM = $db->prepare("SELECT * FROM posts WHERE usuario_id=:id AND imagen IS NOT NULL AND imagen!='' ORDER BY fecha_creacion DESC");
$stmtM->execute([':id'=>$userId]); $misMedia = $stmtM->fetchAll(PDO::FETCH_ASSOC);

$stmtLG = $db->prepare("
    SELECT p.*, u.nombre, u.foto_perfil,
    COALESCE(SUM(CASE WHEN l2.tipo='like' THEN 1 ELSE 0 END),0) as total_likes,
    COALESCE(SUM(CASE WHEN l2.tipo='dislike' THEN 1 ELSE 0 END),0) as total_dislikes,
    COALESCE((SELECT COUNT(*) FROM comentarios c WHERE c.post_id=p.id),0) as total_comentarios,
    TIMESTAMPDIFF(MINUTE,p.fecha_creacion,NOW()) as mins_ago
    FROM post_likes l JOIN posts p ON p.id=l.post_id JOIN usuarios u ON u.id=p.usuario_id
    LEFT JOIN post_likes l2 ON l2.post_id=p.id
    WHERE l.usuario_id=:id AND l.tipo='like' GROUP BY p.id ORDER BY l.fecha DESC");
$stmtLG->execute([':id'=>$userId]); $misLikes = $stmtLG->fetchAll(PDO::FETCH_ASSOC);

function timeAgo($m){ if($m<1)return'ahora'; if($m<60)return $m.'m'; if($m<1440)return round($m/60).'h'; return round($m/1440).'d'; }
function rh($t){ return preg_replace('/#(\w+)/u','<span class="hashtag">#$1</span>',htmlspecialchars($t)); }
$meses=['January'=>'enero','February'=>'febrero','March'=>'marzo','April'=>'abril','May'=>'mayo','June'=>'junio','July'=>'julio','August'=>'agosto','September'=>'septiembre','October'=>'octubre','November'=>'noviembre','December'=>'diciembre'];
$fechaRegistro = strtr(date('F Y', strtotime($user['creado_en'])), $meses);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Mi Perfil</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="dark-toggle-container">
    <input type="checkbox" id="modo-oscuro-check">
    <label for="modo-oscuro-check" class="switch-label">
        <span>☀️</span><span>🌙</span><div class="ball"></div>
    </label>
</div>

<aside class="sidebar">
    <a href="home.php" class="logo-link">
        <img src="assets/img/logovitalis.png" alt="Vitalis" class="sidebar-logo">
    </a>
    <nav><ul>
        <li><a href="home.php">🏠 Inicio</a></li>
        <li><a href="blog.php">📝 Blog</a></li>
        <li><a href="consejos.php">🔍 Consejos</a></li>
        <li><a href="salud.php">💚 Salud</a></li>
        <li><a href="bienestar.php">❤️ Bienestar</a></li>
            <li class="nav-cursos"><a href="cursos.php">🎓 Cursos</a></li>
            <li class="nav-juegos"><a href="juegos_bienestar.php">🎮 Juegos Bienestar</a></li>
        <li class="active"><a href="perfil.php">👤 Mi Perfil</a></li>
        <li><a href="notificaciones.php">🔔 Notificaciones</a></li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li><a href="panel_de_administracion.php">⚙️ Panel Admin</a></li>
        <?php endif; ?>
    </ul></nav>
</aside>

<div class="page-body">
<div class="profile-wrapper">

    <!-- PORTADA -->
    <div class="cover-container">
        <?php if ($fotoPortada): ?>
            <img src="<?= $fotoPortada ?>" class="cover-img"
                 style="object-position: center <?= htmlspecialchars($portadaPosicion) ?>" alt="Portada">
        <?php else: ?>
            <div class="cover-default"></div>
        <?php endif; ?>
    </div>

    <!-- BARRA INFO -->
    <div class="profile-info-bar">
        <div class="profile-avatar-name">
            <div class="profile-photo-container">
                <img src="<?= $fotoPerfil ?>" class="profile-photo" id="profilePhotoPreview"
                     alt="<?= htmlspecialchars($user['nombre']) ?>"
                     onerror="this.src='assets/img/perfil-sin-foto.png'">
                <label for="fotoInput" class="photo-edit-btn" title="Cambiar foto de perfil">
                    <i class="fas fa-camera"></i>
                </label>
                <form id="fotoForm" action="../controllers/FotoController.php" method="POST"
                      enctype="multipart/form-data" style="display:none;">
                    <input type="file" id="fotoInput" name="foto_perfil"
                           accept="image/jpeg,image/png,image/webp,image/gif">
                </form>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user['nombre']) ?></h1>
        </div>
        <button class="btn-editar-perfil" onclick="abrirModalEditar()">
            <i class="fas fa-pen"></i> Editar perfil
        </button>
    </div>

    <!-- DETALLES -->
    <div class="profile-details">
        <p class="profile-role"><span class="role-badge"><?= ucfirst($_SESSION['user_role']) ?></span></p>
        <?php if (!empty($biografia)): ?>
        <p class="profile-bio"><?= htmlspecialchars($biografia) ?></p>
        <?php endif; ?>
        <div class="profile-meta">
            <span><i class="fas fa-calendar-alt"></i> Se unió en <?= $fechaRegistro ?></span>
        </div>
        <div class="profile-stats">
            <span><strong><?= $numPosts ?></strong> Posts</span>
            <span><strong><?= $numRespuestas ?></strong> Respuestas</span>
            <span><strong><?= $numLikes ?></strong> Me gusta dados</span>
        </div>
    </div>

    <!-- TABS -->
    <div class="profile-tabs">
        <button class="tab-btn active" onclick="switchTab('posts',this)">Posts</button>
        <button class="tab-btn" onclick="switchTab('respuestas',this)">Respuestas</button>
        <button class="tab-btn" onclick="switchTab('multimedia',this)">Multimedia</button>
        <button class="tab-btn" onclick="switchTab('megusta',this)">Me gusta</button>
    </div>

    <!-- TAB: POSTS -->
    <div class="tab-content active" id="tab-posts">
        <?php if (empty($misPosts)): ?>
        <div class="empty-tab"><div class="empty-icon">📝</div><p>Aún no has publicado nada.</p><a href="blog.php" class="btn-ir-blog">Ir al Blog</a></div>
        <?php else: foreach ($misPosts as $post): ?>
        <div class="pf-post">
            <div class="pf-post-header">
                <img src="<?= $fotoPerfil ?>" class="pf-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                <div><span class="pf-author"><?= htmlspecialchars($user['nombre']) ?></span><span class="pf-time"><?= timeAgo($post['mins_ago']) ?></span></div>
                <a href="../controllers/PostController.php?accion=eliminar&id=<?= $post['id'] ?>" class="pf-delete" onclick="return confirm('¿Eliminar este post?')"><i class="fas fa-trash-alt"></i></a>
            </div>
            <div class="pf-post-body">
                <div class="pf-content"><?= rh($post['contenido']) ?></div>
                <?php if ($post['imagen']): ?><div class="pf-media"><img src="uploads/posts/<?= htmlspecialchars($post['imagen']) ?>" class="pf-img"></div><?php endif; ?>
                <?php if ($post['video_url'] && preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/',$post['video_url'],$m)): ?>
                <div class="pf-media"><iframe class="pf-video" src="https://www.youtube.com/embed/<?= $m[1] ?>" frameborder="0" allowfullscreen></iframe></div>
                <?php endif; ?>
            </div>
            <div class="pf-post-actions">
                <span><i class="fas fa-thumbs-up"></i> <?= $post['total_likes'] ?></span>
                <span><i class="fas fa-thumbs-down"></i> <?= $post['total_dislikes'] ?></span>
                <span><i class="far fa-comment"></i> <?= $post['total_comentarios'] ?></span>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- TAB: RESPUESTAS -->
    <div class="tab-content" id="tab-respuestas">
        <?php if (empty($misRespuestas)): ?>
        <div class="empty-tab"><div class="empty-icon">💬</div><p>Aún no has respondido ningún post.</p></div>
        <?php else: foreach ($misRespuestas as $resp): ?>
        <div class="pf-post">
            <div class="pf-reply-ref"><i class="fas fa-reply"></i> Respondiste a <strong><?= htmlspecialchars($resp['post_autor']) ?></strong>: <span class="pf-reply-original">"<?= htmlspecialchars(mb_substr($resp['post_contenido'],0,80)) ?>..."</span></div>
            <div class="pf-post-header">
                <img src="<?= $fotoPerfil ?>" class="pf-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                <div><span class="pf-author"><?= htmlspecialchars($user['nombre']) ?></span><span class="pf-time"><?= date('d M Y',strtotime($resp['fecha'])) ?></span></div>
            </div>
            <div class="pf-post-body"><div class="pf-content"><?= htmlspecialchars($resp['contenido']) ?></div></div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <!-- TAB: MULTIMEDIA -->
    <div class="tab-content" id="tab-multimedia">
        <?php if (empty($misMedia)): ?>
        <div class="empty-tab"><div class="empty-icon">🖼️</div><p>No has subido imágenes aún.</p></div>
        <?php else: ?>
        <div class="media-grid">
            <?php foreach ($misMedia as $m): ?>
            <div class="media-item"><img src="uploads/posts/<?= htmlspecialchars($m['imagen']) ?>" alt="media"></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- TAB: ME GUSTA -->
    <div class="tab-content" id="tab-megusta">
        <?php if (empty($misLikes)): ?>
        <div class="empty-tab"><div class="empty-icon">❤️</div><p>Aún no has dado me gusta a ningún post.</p></div>
        <?php else: foreach ($misLikes as $post):
            $fL = !empty($post['foto_perfil']) ? 'uploads/fotos/'.$post['foto_perfil'] : 'assets/img/perfil-sin-foto.png';
        ?>
        <div class="pf-post">
            <div class="pf-post-header">
                <img src="<?= $fL ?>" class="pf-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                <div><span class="pf-author"><?= htmlspecialchars($post['nombre']) ?></span><span class="pf-time"><?= timeAgo($post['mins_ago']) ?></span></div>
            </div>
            <div class="pf-post-body">
                <div class="pf-content"><?= rh($post['contenido']) ?></div>
                <?php if ($post['imagen']): ?><div class="pf-media"><img src="uploads/posts/<?= htmlspecialchars($post['imagen']) ?>" class="pf-img"></div><?php endif; ?>
            </div>
            <div class="pf-post-actions">
                <span class="liked"><i class="fas fa-thumbs-up"></i> <?= $post['total_likes'] ?></span>
                <span><i class="fas fa-thumbs-down"></i> <?= $post['total_dislikes'] ?></span>
                <span><i class="far fa-comment"></i> <?= $post['total_comentarios'] ?></span>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>

</div></div>

<!-- ══ MODAL RECORTE PORTADA ══ -->
<div id="modalCoverCrop" class="crop-overlay">
    <div class="crop-modal">
        <div class="crop-header">
            <button class="crop-back" onclick="cancelarCrop()"><i class="fas fa-arrow-left"></i></button>
            <span>Editar portada</span>
            <button class="crop-apply" onclick="aplicarCrop()">Aplicar</button>
        </div>
        <div class="crop-area" id="cropArea">
            <img id="cropImg" src="" alt="portada" draggable="false">
            <div class="crop-frame"></div>
        </div>
        <div class="crop-hint"><i class="fas fa-arrows-alt"></i> Arrastra la imagen para ajustar</div>
    </div>
</div>

<!-- ══ MODAL EDITAR PERFIL ══ -->
<div id="modalEditarPerfil" class="edit-modal-overlay">
    <div class="edit-modal-card">
        <div class="edit-modal-header">
            <h2>Editar perfil</h2>
            <button class="edit-modal-close" onclick="cerrarModalEditar()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editPerfilForm" action="../controllers/PerfilController.php" method="POST" enctype="multipart/form-data">
            <!-- Preview portada -->
            <div class="edit-portada-wrap" id="editPortadaWrap">
                <?php if ($fotoPortada): ?>
                <img src="<?= $fotoPortada ?>" id="editPortadaImg" class="edit-portada-img"
                     style="object-position: center <?= htmlspecialchars($portadaPosicion) ?>">
                <?php else: ?>
                <div class="edit-portada-default" id="editPortadaImg"></div>
                <?php endif; ?>
                <label for="editPortadaInput" class="edit-portada-btn">
                    <i class="fas fa-camera"></i> Cambiar portada
                </label>
                <input type="file" id="editPortadaInput"
                       accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                <input type="hidden" name="portada_posicion" id="inputPosicion" value="<?= htmlspecialchars($portadaPosicion) ?>">
                <input type="hidden" name="portada_base64" id="portadaBase64" value="">
            </div>

            <!-- Campos -->
            <div class="edit-fields">
                <div class="edit-field-group">
                    <label class="edit-label">Nombre</label>
                    <input type="text" name="nombre" class="edit-input"
                           value="<?= htmlspecialchars($user['nombre']) ?>" maxlength="50" required>
                </div>
                <div class="edit-field-group">
                    <label class="edit-label">
                        Biografía <span class="edit-chars" id="bioChars"><?= strlen($biografia) ?>/160</span>
                    </label>
                    <textarea name="biografia" class="edit-textarea" maxlength="160" rows="3"
                              placeholder="Cuéntanos algo sobre ti..." id="bioTextarea"><?= htmlspecialchars($biografia) ?></textarea>
                </div>
            </div>

            <div class="edit-modal-footer">
                <button type="button" class="edit-btn-cancel" onclick="cerrarModalEditar()">Cancelar</button>
                <button type="submit" class="edit-btn-save">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal logout -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h2>¿Deseas cerrar sesión?</h2>
        <p>Se perderán los progresos no guardados.</p>
        <div class="modal-btns">
            <button class="btn-confirmar" onclick="confirmarLogout()">Sí, Salir</button>
            <button class="btn-cancelar" onclick="cerrarModal()">No, Cancelar</button>
        </div>
    </div>
</div>

<script>
// ── Modo oscuro ──
const checkbox = document.getElementById('modo-oscuro-check');
if (localStorage.getItem('darkMode') === 'true') { document.body.classList.add('dark-mode'); checkbox.checked = true; }
checkbox.addEventListener('change', () => { document.body.classList.toggle('dark-mode', checkbox.checked); localStorage.setItem('darkMode', checkbox.checked); });

// ── Logout ──
function abrirModal()     { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal()    { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout(){ window.location.href = '../controllers/LogoutController.php'; }

// ── Tabs ──
function switchTab(tab, btn) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

// ── Foto de perfil ──
document.getElementById('fotoInput').addEventListener('change', function() {
    const file = this.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { document.getElementById('profilePhotoPreview').src = e.target.result; };
    reader.readAsDataURL(file);
    document.getElementById('fotoForm').submit();
});

// ── Modal editar perfil ──
function abrirModalEditar()  { document.getElementById('modalEditarPerfil').style.display = 'flex'; }
function cerrarModalEditar() { document.getElementById('modalEditarPerfil').style.display = 'none'; }
document.getElementById('modalEditarPerfil').addEventListener('click', function(e){ if(e.target===this) cerrarModalEditar(); });
document.getElementById('bioTextarea').addEventListener('input', function(){ document.getElementById('bioChars').textContent = this.value.length + '/160'; });

// ── CROP DE PORTADA ──
let cropImgSrc = null;
let isDragging = false;
let startY = 0;
let currentOffsetY = 0;  // px offset dentro del contenedor
let imgNaturalH = 0;
let imgDisplayH = 0;
let containerH  = 0;
let minOffset   = 0;
let maxOffset   = 0;

document.getElementById('editPortadaInput').addEventListener('change', function() {
    const file = this.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        cropImgSrc = e.target.result;
        abrirCrop(cropImgSrc);
    };
    reader.readAsDataURL(file);
});

function abrirCrop(src) {
    const modal  = document.getElementById('modalCoverCrop');
    const img    = document.getElementById('cropImg');
    img.src = src;
    modal.style.display = 'flex';

    img.onload = () => {
        const area   = document.getElementById('cropArea');
        containerH   = area.clientHeight;
        const ratio  = img.naturalHeight / img.naturalWidth;
        imgDisplayH  = area.clientWidth * ratio;

        if (imgDisplayH <= containerH) {
            // imagen más alta que el contenedor — centrar
            img.style.top  = ((containerH - imgDisplayH) / 2) + 'px';
            img.style.left = '0';
            currentOffsetY = (containerH - imgDisplayH) / 2;
            minOffset = maxOffset = currentOffsetY;
        } else {
            // imagen más alta — permitir scroll
            currentOffsetY = -(imgDisplayH - containerH) / 2;
            minOffset = -(imgDisplayH - containerH);
            maxOffset = 0;
            img.style.top = currentOffsetY + 'px';
        }
    };
}

function cancelarCrop() {
    document.getElementById('modalCoverCrop').style.display = 'none';
    document.getElementById('editPortadaInput').value = '';
}

function aplicarCrop() {
    const cropImgEl = document.getElementById('cropImg');
    const area      = document.getElementById('cropArea');

    // Dimensiones reales de la imagen en pantalla
    const imgRect  = cropImgEl.getBoundingClientRect();
    const areaRect = area.getBoundingClientRect();

    // Cuánto de la imagen está visible (en px de pantalla)
    const visibleTop    = areaRect.top - imgRect.top;   // px desde top de la img
    const visibleHeight = areaRect.height;

    // Escala: px pantalla → px naturales
    const scale    = cropImgEl.naturalHeight / imgRect.height;
    const srcY     = visibleTop  * scale;
    const srcH     = visibleHeight * scale;
    const srcX     = 0;
    const srcW     = cropImgEl.naturalWidth;

    // Dibujar en canvas exactamente lo que se ve
    const canvas  = document.createElement('canvas');
    canvas.width  = srcW;
    canvas.height = Math.round(srcH);
    const ctx = canvas.getContext('2d');
    ctx.drawImage(cropImgEl, srcX, srcY, srcW, srcH, 0, 0, srcW, Math.round(srcH));

    const base64 = canvas.toDataURL('image/jpeg', 0.92);

    // Guardar base64 en hidden input
    document.getElementById('portadaBase64').value = base64;
    document.getElementById('inputPosicion').value = '50%';

    // Actualizar preview en modal editar perfil
    const wrap = document.getElementById('editPortadaWrap');
    let oldImg = document.getElementById('editPortadaImg');
    if (oldImg) oldImg.remove();
    const newImg = document.createElement('img');
    newImg.id        = 'editPortadaImg';
    newImg.src       = base64;
    newImg.className = 'edit-portada-img';
    wrap.insertBefore(newImg, wrap.firstChild);

    document.getElementById('modalCoverCrop').style.display = 'none';
}

// Drag en el área de crop
const cropArea = document.getElementById('cropArea');
const cropImg  = document.getElementById('cropImg');

cropArea.addEventListener('mousedown', e => {
    isDragging = true; startY = e.clientY; e.preventDefault();
});
window.addEventListener('mousemove', e => {
    if (!isDragging) return;
    const dy = e.clientY - startY;
    startY = e.clientY;
    currentOffsetY = Math.max(minOffset, Math.min(maxOffset, currentOffsetY + dy));
    cropImg.style.top = currentOffsetY + 'px';
});
window.addEventListener('mouseup', () => { isDragging = false; });

// Touch support
cropArea.addEventListener('touchstart', e => {
    isDragging = true; startY = e.touches[0].clientY; e.preventDefault();
}, { passive: false });
cropArea.addEventListener('touchmove', e => {
    if (!isDragging) return;
    const dy = e.touches[0].clientY - startY;
    startY = e.touches[0].clientY;
    currentOffsetY = Math.max(minOffset, Math.min(maxOffset, currentOffsetY + dy));
    cropImg.style.top = currentOffsetY + 'px';
    e.preventDefault();
}, { passive: false });
cropArea.addEventListener('touchend', () => { isDragging = false; });
</script>
</body>
</html>