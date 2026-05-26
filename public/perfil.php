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

require_once __DIR__ . '/config/Database.php';
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

$stmtCursos = $db->prepare("
    SELECT c.titulo, c.descripcion, c.categoria, cd.fecha_desbloqueo
    FROM cursos_desbloqueados cd
    JOIN cursos c ON c.id = cd.curso_id
    WHERE cd.usuario_id = :id ORDER BY cd.fecha_desbloqueo DESC
");
$stmtCursos->execute([':id' => $userId]);
$misCursos = $stmtCursos->fetchAll(PDO::FETCH_ASSOC);
$numCursos = count($misCursos);

function timeAgo($m){ if($m<1)return'ahora'; if($m<60)return $m.'m'; if($m<1440)return round($m/60).'h'; return round($m/1440).'d'; }
function rh($t){ return preg_replace('/#(\w+)/u','<span class="hashtag">#$1</span>',htmlspecialchars($t)); }
$meses=['January'=>'enero','February'=>'febrero','March'=>'marzo','April'=>'abril','May'=>'mayo','June'=>'junio','July'=>'julio','August'=>'agosto','September'=>'septiembre','October'=>'octubre','November'=>'noviembre','December'=>'diciembre'];
$fechaRegistro = strtr(date('F Y', strtotime($user['creado_en'])), $meses);
$CATEGORIA_ICONS = ['nutricion'=>'🥗','ejercicio'=>'💪','bienestar'=>'🧘','salud'=>'❤️','mindfulness'=>'🌿','otro'=>'📚'];
?>
<!DOCTYPE html>
<html lang="es" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis | Mi Perfil</title>
    <link rel="stylesheet" href="assets/css/sidebar.css">
    <link rel="stylesheet" href="assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/darkmode.js"></script>
    <style>
        .cursos-tab-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; padding:20px 0; }
        .curso-tab-card { background:#fff; border-radius:16px; border:1px solid #e2e8f0; padding:18px 16px; display:flex; flex-direction:column; gap:8px; transition:transform .2s,box-shadow .2s; }
        body.dark-mode .curso-tab-card { background:#1e2535; border-color:#2d3748; }
        .curso-tab-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.08); }
        .curso-tab-icon { font-size:32px; line-height:1; }
        .curso-tab-cat { font-size:11px; font-weight:700; color:#2bbbad; text-transform:uppercase; letter-spacing:.5px; }
        .curso-tab-titulo { font-size:14px; font-weight:700; color:#1e293b; margin:0; }
        body.dark-mode .curso-tab-titulo { color:#e2e8f0; }
        .curso-tab-desc { font-size:12.5px; color:#64748b; line-height:1.4; margin:0; }
        .curso-tab-fecha { font-size:11px; color:#94a3b8; margin-top:4px; }
        .curso-tab-badge { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; color:#16a34a; background:#f0fdf4; border:1px solid #86efac; border-radius:999px; padding:2px 10px; align-self:flex-start; }

        /* ── BOTÓN TRADUCCIÓN ── */
        .btn-traducir {
            display: flex;
            align-items: center;
            gap: 7px;
            background: transparent;
            border: 2px solid #2bbbad;
            color: #2bbbad;
            padding: 8px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: background .2s, color .2s;
            white-space: nowrap;
        }
        .btn-traducir:hover { background: #2bbbad; color: #fff; }
        .btn-traducir.traduciendo { opacity: .7; cursor: wait; }
        body.dark-mode .btn-traducir { border-color: #2bbbad; color: #2bbbad; }
        body.dark-mode .btn-traducir:hover { background: #2bbbad; color: #fff; }

        /* Spinner de carga */
        .btn-traducir .spin { animation: spin .8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<aside class="sidebar">
    <a href="home.php" class="logo-link">
        <img src="assets/img/logovitalis.png" alt="Vitalis" class="sidebar-logo">
    </a>
    <nav><ul>
        <li><a href="home.php" data-i18n="nav_inicio">🏠 Inicio</a></li>
        <li><a href="blog.php" data-i18n="nav_blog">📝 Blog</a></li>
        <li><a href="consejos.php" data-i18n="nav_consejos">🔍 Consejos</a></li>
        <li><a href="salud.php" data-i18n="nav_salud">💚 Salud</a></li>
        <li><a href="bienestar.php" data-i18n="nav_bienestar">❤️ Bienestar</a></li>
        <li class="nav-cursos"><a href="cursos.php" data-i18n="nav_cursos">🎓 Cursos</a></li>
        <li class="nav-juegos"><a href="juegos_bienestar.php" data-i18n="nav_juegos">🎮 Juegos Bienestar</a></li>
        <li class="active"><a href="perfil.php" data-i18n="nav_perfil">👤 Mi Perfil</a></li>
        <li><a href="notificaciones.php" data-i18n="nav_notif">🔔 Notificaciones</a></li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
        <li><a href="panel_de_administracion.php" data-i18n="nav_admin">⚙️ Panel Admin</a></li>
        <?php endif; ?>
    </ul></nav>
</aside>

<div class="page-body">
<div class="profile-wrapper">

    <div class="cover-container">
        <?php if ($fotoPortada): ?>
            <img src="<?= $fotoPortada ?>" class="cover-img" style="object-position:center <?= htmlspecialchars($portadaPosicion) ?>" alt="Portada">
        <?php else: ?>
            <div class="cover-default"></div>
        <?php endif; ?>
    </div>

    <div class="profile-info-bar">
        <div class="profile-avatar-name">
            <div class="profile-photo-container">
                <img src="<?= $fotoPerfil ?>" class="profile-photo" id="profilePhotoPreview"
                     alt="<?= htmlspecialchars($user['nombre']) ?>"
                     onerror="this.src='assets/img/perfil-sin-foto.png'">
                <label for="fotoInput" class="photo-edit-btn" title="Cambiar foto de perfil">
                    <i class="fas fa-camera"></i>
                </label>
                <form id="fotoForm" action="/controllers/FotoController.php" method="POST" enctype="multipart/form-data" style="display:none;">
                    <input type="file" id="fotoInput" name="foto_perfil" accept="image/jpeg,image/png,image/webp,image/gif">
                </form>
            </div>
            <h1 class="profile-name"><?= htmlspecialchars($user['nombre']) ?></h1>
        </div>
        <!-- BOTONES: Traducir + Editar perfil -->
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <button class="btn-traducir" id="btn-traducir" onclick="toggleIdioma()">
                <i class="fas fa-globe"></i>
                <span id="btn-traducir-label">EN</span>
            </button>
            <button class="btn-editar-perfil" onclick="abrirModalEditar()">
                <i class="fas fa-pen"></i> <span data-i18n="btn_editar">Editar perfil</span>
            </button>
        </div>
    </div>

    <div class="profile-details">
        <p class="profile-role"><span class="role-badge"><?= ucfirst($_SESSION['user_role']) ?></span></p>
        <?php if (!empty($biografia)): ?>
        <p class="profile-bio" id="profile-bio"><?= htmlspecialchars($biografia) ?></p>
        <?php endif; ?>
        <div class="profile-meta">
            <span><i class="fas fa-calendar-alt"></i> <span data-i18n="se_unio">Se unió en</span> <?= $fechaRegistro ?></span>
        </div>
        <div class="profile-stats">
            <span><strong><?= $numPosts ?></strong> <span data-i18n="stat_posts">Posts</span></span>
            <span><strong><?= $numRespuestas ?></strong> <span data-i18n="stat_resp">Respuestas</span></span>
            <span><strong><?= $numLikes ?></strong> <span data-i18n="stat_likes">Me gusta dados</span></span>
            <span><strong><?= $numCursos ?></strong> <span data-i18n="stat_cursos">Cursos</span></span>
        </div>
    </div>

    <div class="profile-tabs">
        <button class="tab-btn active" onclick="switchTab('posts',this)" data-i18n="tab_posts">Posts</button>
        <button class="tab-btn" onclick="switchTab('respuestas',this)" data-i18n="tab_resp">Respuestas</button>
        <button class="tab-btn" onclick="switchTab('multimedia',this)" data-i18n="tab_media">Multimedia</button>
        <button class="tab-btn" onclick="switchTab('megusta',this)" data-i18n="tab_likes">Me gusta</button>
        <button class="tab-btn" onclick="switchTab('cursos',this)" data-i18n="tab_cursos">🎓 Cursos</button>
    </div>

    <div class="tab-content active" id="tab-posts">
        <?php if (empty($misPosts)): ?>
        <div class="empty-tab"><div class="empty-icon">📝</div><p data-i18n="empty_posts">Aún no has publicado nada.</p><a href="blog.php" class="btn-ir-blog" data-i18n="ir_blog">Ir al Blog</a></div>
        <?php else: foreach ($misPosts as $post): ?>
        <div class="pf-post">
            <div class="pf-post-header">
                <img src="<?= $fotoPerfil ?>" class="pf-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                <div><span class="pf-author"><?= htmlspecialchars($user['nombre']) ?></span><span class="pf-time"><?= timeAgo($post['mins_ago']) ?></span></div>
                <a href="/controllers/PostController.php?accion=eliminar&id=<?= $post['id'] ?>" class="pf-delete" onclick="return confirm('¿Eliminar este post?')"><i class="fas fa-trash-alt"></i></a>
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

    <div class="tab-content" id="tab-respuestas">
        <?php if (empty($misRespuestas)): ?>
        <div class="empty-tab"><div class="empty-icon">💬</div><p data-i18n="empty_resp">Aún no has respondido ningún post.</p></div>
        <?php else: foreach ($misRespuestas as $resp): ?>
        <div class="pf-post">
            <div class="pf-reply-ref"><i class="fas fa-reply"></i> <span data-i18n="respondiste">Respondiste a</span> <strong><?= htmlspecialchars($resp['post_autor']) ?></strong>: <span class="pf-reply-original">"<?= htmlspecialchars(mb_substr($resp['post_contenido'],0,80)) ?>..."</span></div>
            <div class="pf-post-header">
                <img src="<?= $fotoPerfil ?>" class="pf-avatar" onerror="this.src='assets/img/perfil-sin-foto.png'">
                <div><span class="pf-author"><?= htmlspecialchars($user['nombre']) ?></span><span class="pf-time"><?= date('d M Y',strtotime($resp['fecha'])) ?></span></div>
            </div>
            <div class="pf-post-body"><div class="pf-content"><?= htmlspecialchars($resp['contenido']) ?></div></div>
        </div>
        <?php endforeach; endif; ?>
    </div>

    <div class="tab-content" id="tab-multimedia">
        <?php if (empty($misMedia)): ?>
        <div class="empty-tab"><div class="empty-icon">🖼️</div><p data-i18n="empty_media">No has subido imágenes aún.</p></div>
        <?php else: ?>
        <div class="media-grid">
            <?php foreach ($misMedia as $m): ?>
            <div class="media-item"><img src="uploads/posts/<?= htmlspecialchars($m['imagen']) ?>" alt="media"></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="tab-content" id="tab-megusta">
        <?php if (empty($misLikes)): ?>
        <div class="empty-tab"><div class="empty-icon">❤️</div><p data-i18n="empty_likes">Aún no has dado me gusta a ningún post.</p></div>
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

    <div class="tab-content" id="tab-cursos">
        <?php if (empty($misCursos)): ?>
        <div class="empty-tab"><div class="empty-icon">🎓</div><p data-i18n="empty_cursos">Aún no tienes cursos desbloqueados.</p><a href="cursos.php" class="btn-ir-blog" data-i18n="ver_cursos">Ver cursos disponibles</a></div>
        <?php else: ?>
        <div class="cursos-tab-grid">
            <?php foreach ($misCursos as $curso):
                $icon = $CATEGORIA_ICONS[$curso['categoria']] ?? '📚';
                $fechaDesbloqueo = date('d M Y', strtotime($curso['fecha_desbloqueo']));
            ?>
            <div class="curso-tab-card">
                <div class="curso-tab-icon"><?= $icon ?></div>
                <span class="curso-tab-cat"><?= htmlspecialchars($curso['categoria']) ?></span>
                <p class="curso-tab-titulo"><?= htmlspecialchars($curso['titulo']) ?></p>
                <p class="curso-tab-desc"><?= htmlspecialchars($curso['descripcion']) ?></p>
                <span class="curso-tab-badge">✓ <span data-i18n="desbloqueado">Desbloqueado</span></span>
                <span class="curso-tab-fecha">📅 <?= $fechaDesbloqueo ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div></div>

<!-- Modal Crop Portada -->
<div id="modalCoverCrop" class="crop-overlay">
    <div class="crop-modal">
        <div class="crop-header">
            <button class="crop-back" onclick="cancelarCrop()"><i class="fas fa-arrow-left"></i></button>
            <span data-i18n="editar_portada">Editar portada</span>
            <button class="crop-apply" onclick="aplicarCrop()" data-i18n="aplicar">Aplicar</button>
        </div>
        <div class="crop-area" id="cropArea">
            <img id="cropImg" src="" alt="portada" draggable="false">
            <div class="crop-frame"></div>
        </div>
        <div class="crop-hint"><i class="fas fa-arrows-alt"></i> <span data-i18n="arrastra">Arrastra la imagen para ajustar</span></div>
    </div>
</div>

<!-- Modal Editar Perfil -->
<div id="modalEditarPerfil" class="edit-modal-overlay">
    <div class="edit-modal-card">
        <div class="edit-modal-header">
            <h2 data-i18n="editar_perfil_title">Editar perfil</h2>
            <button class="edit-modal-close" onclick="cerrarModalEditar()"><i class="fas fa-times"></i></button>
        </div>
        <form id="editPerfilForm" action="/controllers/PerfilController.php" method="POST" enctype="multipart/form-data">
            <div class="edit-portada-wrap" id="editPortadaWrap">
                <?php if ($fotoPortada): ?>
                <img src="<?= $fotoPortada ?>" id="editPortadaImg" class="edit-portada-img" style="object-position:center <?= htmlspecialchars($portadaPosicion) ?>">
                <?php else: ?>
                <div class="edit-portada-default" id="editPortadaImg"></div>
                <?php endif; ?>
                <label for="editPortadaInput" class="edit-portada-btn"><i class="fas fa-camera"></i> <span data-i18n="cambiar_portada">Cambiar portada</span></label>
                <input type="file" id="editPortadaInput" accept="image/jpeg,image/png,image/webp,image/gif" style="display:none;">
                <input type="hidden" name="portada_posicion" id="inputPosicion" value="<?= htmlspecialchars($portadaPosicion) ?>">
                <input type="hidden" name="portada_base64" id="portadaBase64" value="">
            </div>
            <div class="edit-fields">
                <div class="edit-field-group">
                    <label class="edit-label" data-i18n="label_nombre">Nombre</label>
                    <input type="text" name="nombre" class="edit-input" value="<?= htmlspecialchars($user['nombre']) ?>" maxlength="50" required>
                </div>
                <div class="edit-field-group">
                    <label class="edit-label"><span data-i18n="label_bio">Biografía</span> <span class="edit-chars" id="bioChars"><?= strlen($biografia) ?>/160</span></label>
                    <textarea name="biografia" class="edit-textarea" maxlength="160" rows="3" id="bioTextarea"><?= htmlspecialchars($biografia) ?></textarea>
                </div>
            </div>
            <div class="edit-modal-footer">
                <button type="button" class="edit-btn-cancel" onclick="cerrarModalEditar()" data-i18n="cancelar">Cancelar</button>
                <button type="submit" class="edit-btn-save" data-i18n="guardar">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal logout -->
<div id="logoutModal" class="modal-overlay">
    <div class="modal-card">
        <div class="modal-icon">🚪</div>
        <h2 data-i18n="logout_title">¿Deseas cerrar sesión?</h2>
        <p data-i18n="logout_msg">Se perderán los progresos no guardados.</p>
        <div class="modal-btns">
            <button class="btn-confirmar" onclick="confirmarLogout()" data-i18n="logout_si">Sí, Salir</button>
            <button class="btn-cancelar" onclick="cerrarModal()" data-i18n="logout_no">No, Cancelar</button>
        </div>
    </div>
</div>

<script>
// ══════════════════════════════════════════════
// TRADUCCIONES ESTÁTICAS ES ↔ EN
// ══════════════════════════════════════════════
const i18n = {
    es: {
        nav_inicio:'🏠 Inicio', nav_blog:'📝 Blog', nav_consejos:'🔍 Consejos',
        nav_salud:'💚 Salud', nav_bienestar:'❤️ Bienestar', nav_cursos:'🎓 Cursos',
        nav_juegos:'🎮 Juegos Bienestar', nav_perfil:'👤 Mi Perfil',
        nav_notif:'🔔 Notificaciones', nav_admin:'⚙️ Panel Admin',
        btn_editar:'Editar perfil', se_unio:'Se unió en',
        stat_posts:'Posts', stat_resp:'Respuestas', stat_likes:'Me gusta dados', stat_cursos:'Cursos',
        tab_posts:'Posts', tab_resp:'Respuestas', tab_media:'Multimedia', tab_likes:'Me gusta', tab_cursos:'🎓 Cursos',
        empty_posts:'Aún no has publicado nada.', ir_blog:'Ir al Blog',
        empty_resp:'Aún no has respondido ningún post.',
        respondiste:'Respondiste a',
        empty_media:'No has subido imágenes aún.',
        empty_likes:'Aún no has dado me gusta a ningún post.',
        empty_cursos:'Aún no tienes cursos desbloqueados.', ver_cursos:'Ver cursos disponibles',
        desbloqueado:'Desbloqueado',
        editar_portada:'Editar portada', aplicar:'Aplicar', arrastra:'Arrastra la imagen para ajustar',
        editar_perfil_title:'Editar perfil', cambiar_portada:'Cambiar portada',
        label_nombre:'Nombre', label_bio:'Biografía', cancelar:'Cancelar', guardar:'Guardar cambios',
        logout_title:'¿Deseas cerrar sesión?', logout_msg:'Se perderán los progresos no guardados.',
        logout_si:'Sí, Salir', logout_no:'No, Cancelar',
    },
    en: {
        nav_inicio:'🏠 Home', nav_blog:'📝 Blog', nav_consejos:'🔍 Tips',
        nav_salud:'💚 Health', nav_bienestar:'❤️ Wellness', nav_cursos:'🎓 Courses',
        nav_juegos:'🎮 Wellness Games', nav_perfil:'👤 My Profile',
        nav_notif:'🔔 Notifications', nav_admin:'⚙️ Admin Panel',
        btn_editar:'Edit profile', se_unio:'Joined in',
        stat_posts:'Posts', stat_resp:'Replies', stat_likes:'Likes given', stat_cursos:'Courses',
        tab_posts:'Posts', tab_resp:'Replies', tab_media:'Media', tab_likes:'Likes', tab_cursos:'🎓 Courses',
        empty_posts:"You haven't published anything yet.", ir_blog:'Go to Blog',
        empty_resp:"You haven't replied to any post yet.",
        respondiste:'Replied to',
        empty_media:"You haven't uploaded any images yet.",
        empty_likes:"You haven't liked any post yet.",
        empty_cursos:"You don't have unlocked courses yet.", ver_cursos:'View available courses',
        desbloqueado:'Unlocked',
        editar_portada:'Edit cover', aplicar:'Apply', arrastra:'Drag the image to adjust',
        editar_perfil_title:'Edit profile', cambiar_portada:'Change cover',
        label_nombre:'Name', label_bio:'Biography', cancelar:'Cancel', guardar:'Save changes',
        logout_title:'Do you want to log out?', logout_msg:'Unsaved progress will be lost.',
        logout_si:'Yes, Log out', logout_no:'No, Cancel',
    }
};

let idiomaActual = 'es';

// Aplica traducciones estáticas
function aplicarIdioma(lang) {
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if (i18n[lang][key]) el.textContent = i18n[lang][key];
    });
    document.getElementById('html-root').setAttribute('lang', lang === 'es' ? 'es' : 'en');
}

// ══════════════════════════════════════════════
// TRADUCCIÓN DINÁMICA CON MYMEMORY API
// ══════════════════════════════════════════════
const cacheTraduccion = {};

async function traducirTexto(texto, destLang) {
    const clave = texto + '_' + destLang;
    if (cacheTraduccion[clave]) return cacheTraduccion[clave];
    if (!texto.trim()) return texto;
    try {
        const srcLang = destLang === 'en' ? 'es' : 'en';
        const url = `https://api.mymemory.translated.net/get?q=${encodeURIComponent(texto)}&langpair=${srcLang}|${destLang}`;
        const res = await fetch(url);
        const data = await res.json();
        const traducido = data.responseData?.translatedText || texto;
        cacheTraduccion[clave] = traducido;
        return traducido;
    } catch(e) {
        return texto;
    }
}

async function traducirContenidoDinamico(lang) {
    // Traducir biografía
    const bio = document.getElementById('profile-bio');
    if (bio && bio.dataset.original === undefined) bio.dataset.original = bio.textContent;
    if (bio) {
        if (lang === 'en') {
            bio.textContent = await traducirTexto(bio.dataset.original, 'en');
        } else {
            bio.textContent = bio.dataset.original;
        }
    }

    // Traducir contenido de posts
    const contenidos = document.querySelectorAll('.pf-content');
    for (const el of contenidos) {
        if (el.dataset.original === undefined) el.dataset.original = el.innerHTML;
        if (lang === 'en') {
            const textoPlano = el.dataset.original.replace(/<[^>]+>/g, ' ').trim();
            const traducido = await traducirTexto(textoPlano, 'en');
            el.textContent = traducido;
        } else {
            el.innerHTML = el.dataset.original;
        }
    }
}

async function toggleIdioma() {
    const btn = document.getElementById('btn-traducir');
    const label = document.getElementById('btn-traducir-label');

    // Mostrar spinner
    btn.classList.add('traduciendo');
    btn.querySelector('i').className = 'fas fa-spinner spin';

    const nuevoIdioma = idiomaActual === 'es' ? 'en' : 'es';

    // Aplicar traducciones estáticas
    aplicarIdioma(nuevoIdioma);

    // Aplicar traducciones dinámicas (MyMemory API)
    await traducirContenidoDinamico(nuevoIdioma);

    idiomaActual = nuevoIdioma;
    label.textContent = nuevoIdioma === 'es' ? 'EN' : 'ES';
    btn.querySelector('i').className = 'fas fa-globe';
    btn.classList.remove('traduciendo');
}

// ── Logout ──
function abrirModal()     { document.getElementById('logoutModal').style.display = 'flex'; }
function cerrarModal()    { document.getElementById('logoutModal').style.display = 'none'; }
function confirmarLogout(){ window.location.href = '/controllers/LogoutController.php'; }

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
let cropImgSrc = null, isDragging = false, startY = 0, currentOffsetY = 0, imgDisplayH = 0, containerH = 0, minOffset = 0, maxOffset = 0;

document.getElementById('editPortadaInput').addEventListener('change', function() {
    const file = this.files[0]; if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { cropImgSrc = e.target.result; abrirCrop(cropImgSrc); };
    reader.readAsDataURL(file);
});

function abrirCrop(src) {
    const modal = document.getElementById('modalCoverCrop');
    const img   = document.getElementById('cropImg');
    img.src = src; modal.style.display = 'flex';
    img.onload = () => {
        const area = document.getElementById('cropArea');
        containerH = area.clientHeight;
        const ratio = img.naturalHeight / img.naturalWidth;
        imgDisplayH = area.clientWidth * ratio;
        if (imgDisplayH <= containerH) {
            img.style.top = ((containerH - imgDisplayH) / 2) + 'px';
            currentOffsetY = (containerH - imgDisplayH) / 2;
            minOffset = maxOffset = currentOffsetY;
        } else {
            currentOffsetY = -(imgDisplayH - containerH) / 2;
            minOffset = -(imgDisplayH - containerH); maxOffset = 0;
            img.style.top = currentOffsetY + 'px';
        }
    };
}

function cancelarCrop() { document.getElementById('modalCoverCrop').style.display = 'none'; document.getElementById('editPortadaInput').value = ''; }

function aplicarCrop() {
    const cropImgEl = document.getElementById('cropImg');
    const area = document.getElementById('cropArea');
    const imgRect = cropImgEl.getBoundingClientRect(), areaRect = area.getBoundingClientRect();
    const visibleTop = areaRect.top - imgRect.top, visibleHeight = areaRect.height;
    const scale = cropImgEl.naturalHeight / imgRect.height;
    const srcY = visibleTop * scale, srcH = visibleHeight * scale, srcW = cropImgEl.naturalWidth;
    const canvas = document.createElement('canvas');
    canvas.width = srcW; canvas.height = Math.round(srcH);
    canvas.getContext('2d').drawImage(cropImgEl, 0, srcY, srcW, srcH, 0, 0, srcW, Math.round(srcH));
    const base64 = canvas.toDataURL('image/jpeg', 0.92);
    document.getElementById('portadaBase64').value = base64;
    document.getElementById('inputPosicion').value = '50%';
    const wrap = document.getElementById('editPortadaWrap');
    let oldImg = document.getElementById('editPortadaImg'); if (oldImg) oldImg.remove();
    const newImg = document.createElement('img');
    newImg.id = 'editPortadaImg'; newImg.src = base64; newImg.className = 'edit-portada-img';
    wrap.insertBefore(newImg, wrap.firstChild);
    document.getElementById('modalCoverCrop').style.display = 'none';
}

const cropArea = document.getElementById('cropArea');
const cropImg  = document.getElementById('cropImg');
cropArea.addEventListener('mousedown', e => { isDragging = true; startY = e.clientY; e.preventDefault(); });
window.addEventListener('mousemove', e => { if (!isDragging) return; const dy = e.clientY - startY; startY = e.clientY; currentOffsetY = Math.max(minOffset, Math.min(maxOffset, currentOffsetY + dy)); cropImg.style.top = currentOffsetY + 'px'; });
window.addEventListener('mouseup', () => { isDragging = false; });
cropArea.addEventListener('touchstart', e => { isDragging = true; startY = e.touches[0].clientY; e.preventDefault(); }, {passive:false});
cropArea.addEventListener('touchmove', e => { if (!isDragging) return; const dy = e.touches[0].clientY - startY; startY = e.touches[0].clientY; currentOffsetY = Math.max(minOffset, Math.min(maxOffset, currentOffsetY + dy)); cropImg.style.top = currentOffsetY + 'px'; e.preventDefault(); }, {passive:false});
cropArea.addEventListener('touchend', () => { isDragging = false; });
</script>
</body>
</html>