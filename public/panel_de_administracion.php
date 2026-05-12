<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: home.php"); exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();

// ── Estadísticas reales ──────────────────────────────
$totalUsuarios   = $db->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalPosts      = $db->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalComentarios= $db->query("SELECT COUNT(*) FROM comentarios")->fetchColumn();
$totalMultimedia = $db->query("SELECT COUNT(*) FROM posts WHERE imagen IS NOT NULL")->fetchColumn();

// ── Sección activa ────────────────────────────────────
$seccion = $_GET['s'] ?? 'inicio';

// ── Usuarios ──────────────────────────────────────────
$usuarios = $db->query("
    SELECT id, nombre, correo, rol, creado_en,
           foto_perfil
    FROM usuarios ORDER BY creado_en DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Comentarios con su publicación ────────────────────
$comentarios = $db->query("
    SELECT c.id, c.contenido, c.fecha,
           u.nombre AS autor,
           p.id     AS post_id,
           SUBSTRING(p.contenido,1,60) AS post_resumen
    FROM comentarios c
    JOIN usuarios u ON u.id = c.usuario_id
    JOIN posts    p ON p.id = c.post_id
    ORDER BY c.fecha DESC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Categorías más populares por likes ────────────────
$categorias = $db->query("
    SELECT p.hashtags,
           COUNT(l.post_id) as total_likes
    FROM posts p
    LEFT JOIN post_likes l ON l.post_id = p.id AND l.tipo = 'like'
    WHERE p.hashtags IS NOT NULL AND p.hashtags != ''
    GROUP BY p.hashtags
    ORDER BY total_likes DESC
    LIMIT 8
")->fetchAll(PDO::FETCH_ASSOC);

// ── Acción: cambiar rol ───────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_rol'])) {
    $uid  = intval($_POST['uid']);
    $rol  = $_POST['rol'];
    $roles = ['visitante','autor','editor','admin'];
    if (in_array($rol, $roles) && $uid !== (int)$_SESSION['user_id']) {
        $db->prepare("UPDATE usuarios SET rol=? WHERE id=?")->execute([$rol, $uid]);
    }
    header("Location: panel_de_administracion.php?s=usuarios"); exit();
}

// ── Acción: eliminar usuario ──────────────────────────
if (isset($_GET['del_user'])) {
    $uid = intval($_GET['del_user']);
    if ($uid !== (int)$_SESSION['user_id']) {
        $db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$uid]);
    }
    header("Location: panel_de_administracion.php?s=usuarios"); exit();
}

// ── Acción: eliminar comentario ───────────────────────
if (isset($_GET['del_com'])) {
    $cid = intval($_GET['del_com']);
    $db->prepare("DELETE FROM comentarios WHERE id=?")->execute([$cid]);
    header("Location: panel_de_administracion.php?s=comentarios"); exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vitalis - Panel de Administración</title>
    <link rel="stylesheet" href="assets/css/panel_de_administracion.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<nav class="sidebar">
    <div class="logo-container">
        <img src="assets/img/logovitalis.png" alt="Logo Vitalis" class="vitalis-logo">
    </div>
    <ul class="nav-menu">
        <li class="<?= $seccion==='inicio'      ? 'active':'' ?>"><a href="?s=inicio">      <i class="fas fa-home"></i>       Inicio</a></li>
        <li class="<?= $seccion==='usuarios'    ? 'active':'' ?>"><a href="?s=usuarios">    <i class="fas fa-users"></i>      Usuarios</a></li>
        <li class="<?= $seccion==='comentarios' ? 'active':'' ?>"><a href="?s=comentarios"> <i class="fas fa-comments"></i>   Comentarios</a></li>
        <li class="<?= $seccion==='categorias'  ? 'active':'' ?>"><a href="?s=categorias">  <i class="fas fa-tags"></i>       Categorías</a></li>
        <li class="<?= $seccion==='estadisticas'? 'active':'' ?>"><a href="?s=estadisticas"><i class="fas fa-chart-line"></i> Estadísticas</a></li>
        <li style="margin-top:20px;border-top:1px solid #eee;padding-top:10px;">
            <a href="home.php" style="color:#2bbbad;">
                <i class="fas fa-arrow-left" style="color:#2bbbad;"></i> Volver a la Página
            </a>
        </li>
    </ul>
    <div class="user-profile">
        <div style="width:40px;height:40px;border-radius:50%;background:#e0f2f1;display:flex;align-items:center;justify-content:center;margin-right:12px;font-weight:bold;color:#00796b;">
            <?= strtoupper(substr($_SESSION['user_name'],0,1)) ?>
        </div>
        <div class="user-info">
            <span class="user-name"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <span class="user-role">Admin</span>
        </div>
        <a href="../controllers/LogoutController.php">
            <i class="fas fa-sign-out-alt logout-icon"></i>
        </a>
    </div>
</nav>

<main class="main-content">
    <div class="section-title">
        <h1>Administrador</h1>
    </div>

    <!-- ── TARJETAS ESTADÍSTICAS ── -->
    <div class="stats-cards">
        <div class="card">
            <div class="card-icon-bg"><i class="fas fa-user-friends"></i></div>
            <div class="card-details">
                <span class="card-title">Total de Usuarios</span>
                <span class="card-number"><?= $totalUsuarios ?></span>
            </div>
        </div>
        <div class="card">
            <div class="card-icon-bg"><i class="fas fa-file-medical"></i></div>
            <div class="card-details">
                <span class="card-title">Publicaciones</span>
                <span class="card-number green"><?= $totalPosts ?></span>
            </div>
        </div>
        <div class="card">
            <div class="card-icon-bg"><i class="fas fa-comment-dots"></i></div>
            <div class="card-details">
                <span class="card-title">Comentarios</span>
                <span class="card-number"><?= $totalComentarios ?></span>
            </div>
        </div>
        <div class="card">
            <div class="card-icon-bg"><i class="fas fa-camera-retro"></i></div>
            <div class="card-details">
                <span class="card-title">Con Imagen</span>
                <span class="card-number blue"><?= $totalMultimedia ?></span>
            </div>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECCIÓN: INICIO                                -->
    <!-- ══════════════════════════════════════════════ -->
    <?php if ($seccion === 'inicio'): ?>
    <div class="bottom-container">
        <div class="user-management">
            <div class="management-header">
                <h2>Gestión de Usuarios</h2>
            </div>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u):
                    $inicial = strtoupper(substr($u['nombre'],0,1));
                    $colorClass = match($u['rol']) {
                        'admin'    => 'm-cell',
                        'editor'   => 'c-cell',
                        default    => 'g-cell'
                    };
                ?>
                <tr>
                    <td class="user-name-cell">
                        <?php if (!empty($u['foto_perfil'])): ?>
                        <img src="uploads/fotos/<?= htmlspecialchars($u['foto_perfil']) ?>"
                             class="avatar-foto" alt="<?= htmlspecialchars($u['nombre']) ?>"
                             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                        <div class="avatar-cell <?= $colorClass ?>" style="display:none;"><?= $inicial ?></div>
                        <?php else: ?>
                        <div class="avatar-cell <?= $colorClass ?>"><?= $inicial ?></div>
                        <?php endif; ?>
                        <?= htmlspecialchars($u['nombre']) ?>
                    </td>
                    <td><?= htmlspecialchars($u['correo']) ?></td>
                    <td><?= ucfirst($u['rol']) ?></td>
                    <td><span class="status active">Activo</span></td>
                    <td class="actions-cell">
                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                        <a href="?s=usuarios&edit=<?= $u['id'] ?>"><i class="fas fa-pencil-alt edit"></i></a>
                        <a href="?s=inicio&del_user=<?= $u['id'] ?>"
                           onclick="return confirm('¿Eliminar usuario?')">
                            <i class="fas fa-trash-alt delete"></i>
                        </a>
                        <?php else: ?>
                        <span style="font-size:11px;color:#aaa;">Tú</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <aside class="right-panels">
            <div class="quick-admin">
                <h3>Acceso Rápido</h3>
                <div class="quick-grid">
                    <a href="?s=usuarios" class="quick-card qc-users">
                        <div class="qc-icon"><i class="fas fa-users"></i></div>
                        <div class="qc-info">
                            <span class="qc-title">Usuarios</span>
                            <span class="qc-sub"><?= $totalUsuarios ?> registrados</span>
                        </div>
                        <i class="fas fa-chevron-right qc-arrow"></i>
                    </a>
                    <a href="?s=comentarios" class="quick-card qc-comments">
                        <div class="qc-icon"><i class="fas fa-comments"></i></div>
                        <div class="qc-info">
                            <span class="qc-title">Comentarios</span>
                            <span class="qc-sub"><?= $totalComentarios ?> en total</span>
                        </div>
                        <i class="fas fa-chevron-right qc-arrow"></i>
                    </a>
                    <a href="?s=categorias" class="quick-card qc-cats">
                        <div class="qc-icon"><i class="fas fa-tags"></i></div>
                        <div class="qc-info">
                            <span class="qc-title">Categorías</span>
                            <span class="qc-sub">Top por likes</span>
                        </div>
                        <i class="fas fa-chevron-right qc-arrow"></i>
                    </a>
                    <a href="?s=estadisticas" class="quick-card qc-stats">
                        <div class="qc-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="qc-info">
                            <span class="qc-title">Estadísticas</span>
                            <span class="qc-sub"><?= $totalPosts ?> publicaciones</span>
                        </div>
                        <i class="fas fa-chevron-right qc-arrow"></i>
                    </a>
                </div>
            </div>
            <div class="system-infra">
                <h3>Infraestructura del Sistema</h3>
                <p style="font-size:13px;color:#666;padding:10px 0;">PHP 8 · MySQL · Apache · XAMPP</p>
            </div>
        </aside>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECCIÓN: USUARIOS                              -->
    <!-- ══════════════════════════════════════════════ -->
    <?php elseif ($seccion === 'usuarios'): ?>
    <?php
    // Editar usuario
    $editUser = null;
    if (isset($_GET['edit'])) {
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE id=?");
        $stmt->execute([intval($_GET['edit'])]);
        $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <div class="seccion-panel">
        <h2><i class="fas fa-users"></i> Todos los Usuarios</h2>

        <?php if ($editUser): ?>
        <div class="edit-box">
            <h3>Cambiar rol de: <strong><?= htmlspecialchars($editUser['nombre']) ?></strong></h3>
            <form method="POST" action="?s=usuarios" class="edit-form">
                <input type="hidden" name="cambiar_rol" value="1">
                <input type="hidden" name="uid" value="<?= $editUser['id'] ?>">
                <select name="rol" class="rol-select">
                    <option value="visitante" <?= $editUser['rol']==='visitante'?'selected':'' ?>>Visitante</option>
                    <option value="autor"     <?= $editUser['rol']==='autor'    ?'selected':'' ?>>Autor</option>
                    <option value="editor"    <?= $editUser['rol']==='editor'   ?'selected':'' ?>>Editor</option>
                    <option value="admin"     <?= $editUser['rol']==='admin'    ?'selected':'' ?>>Admin</option>
                </select>
                <button type="submit" class="btn-save">Guardar</button>
                <a href="?s=usuarios" class="btn-cancel">Cancelar</a>
            </form>
        </div>
        <?php endif; ?>

        <table class="user-table full-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($usuarios as $u):
                $inicial = strtoupper(substr($u['nombre'],0,1));
                $colorClass = match($u['rol']) { 'admin'=>'m-cell','editor'=>'c-cell',default=>'g-cell' };
                $rolBadge   = match($u['rol']) { 'admin'=>'badge-admin','editor'=>'badge-editor','autor'=>'badge-autor',default=>'badge-visitante' };
            ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td class="user-name-cell">
                    <?php if (!empty($u['foto_perfil'])): ?>
                    <img src="uploads/fotos/<?= htmlspecialchars($u['foto_perfil']) ?>"
                         class="avatar-foto" alt=""
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <div class="avatar-cell <?= $colorClass ?>" style="display:none;"><?= $inicial ?></div>
                    <?php else: ?>
                    <div class="avatar-cell <?= $colorClass ?>"><?= $inicial ?></div>
                    <?php endif; ?>
                    <?= htmlspecialchars($u['nombre']) ?>
                </td>
                <td><?= htmlspecialchars($u['correo']) ?></td>
                <td><span class="rol-badge <?= $rolBadge ?>"><?= ucfirst($u['rol']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($u['creado_en'])) ?></td>
                <td class="actions-cell">
                    <?php if ($u['id'] != $_SESSION['user_id']): ?>
                    <a href="?s=usuarios&edit=<?= $u['id'] ?>"><i class="fas fa-pencil-alt edit"></i></a>
                    <a href="?s=usuarios&del_user=<?= $u['id'] ?>"
                       onclick="return confirm('¿Eliminar a <?= htmlspecialchars($u['nombre']) ?>?')">
                        <i class="fas fa-trash-alt delete"></i>
                    </a>
                    <?php else: ?>
                    <span class="tu-label">Tú</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECCIÓN: COMENTARIOS                           -->
    <!-- ══════════════════════════════════════════════ -->
    <?php elseif ($seccion === 'comentarios'): ?>
    <div class="seccion-panel">
        <h2><i class="fas fa-comments"></i> Todos los Comentarios</h2>
        <?php if (empty($comentarios)): ?>
        <p class="empty-msg">No hay comentarios aún.</p>
        <?php else: ?>
        <table class="user-table full-table">
            <thead>
                <tr>
                    <th>Autor</th>
                    <th>Comentario</th>
                    <th>Publicación</th>
                    <th>Fecha</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($comentarios as $c): ?>
            <tr>
                <td><strong><?= htmlspecialchars($c['autor']) ?></strong></td>
                <td><?= htmlspecialchars(substr($c['contenido'],0,80)) ?>...</td>
                <td class="post-ref">
                    <i class="fas fa-file-alt"></i>
                    "<?= htmlspecialchars($c['post_resumen']) ?>..."
                </td>
                <td><?= date('d/m/Y H:i', strtotime($c['fecha'])) ?></td>
                <td>
                    <a href="?s=comentarios&del_com=<?= $c['id'] ?>"
                       onclick="return confirm('¿Eliminar este comentario?')">
                        <i class="fas fa-trash-alt delete"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECCIÓN: CATEGORÍAS                            -->
    <!-- ══════════════════════════════════════════════ -->
    <?php elseif ($seccion === 'categorias'): ?>
    <div class="seccion-panel">
        <h2><i class="fas fa-tags"></i> Categorías más Populares (por likes)</h2>
        <?php if (empty($categorias)): ?>
        <p class="empty-msg">Aún no hay publicaciones con hashtags.</p>
        <?php else: ?>
        <div class="cat-grid">
            <?php
            $maxLikes = max(array_column($categorias, 'total_likes')) ?: 1;
            foreach ($categorias as $idx => $cat):
                $tags = array_filter(explode(' ', $cat['hashtags']));
                $tag  = $tags ? reset($tags) : 'Sin etiqueta';
                $pct  = round(($cat['total_likes'] / $maxLikes) * 100);
                $colors = ['#2bbbad','#00897b','#26c6da','#0097a7','#4db6ac','#80cbc4','#26a69a','#00796b'];
                $color  = $colors[$idx % count($colors)];
            ?>
            <div class="cat-card">
                <div class="cat-header" style="background:<?= $color ?>;">
                    <span class="cat-tag"><?= htmlspecialchars($tag) ?></span>
                    <span class="cat-likes"><i class="fas fa-thumbs-up"></i> <?= $cat['total_likes'] ?></span>
                </div>
                <div class="cat-bar-wrap">
                    <div class="cat-bar" style="width:<?= $pct ?>%;background:<?= $color ?>;"></div>
                </div>
                <p class="cat-label"><?= $cat['total_likes'] ?> likes · <?= $pct ?>% del top</p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ══════════════════════════════════════════════ -->
    <!-- SECCIÓN: ESTADÍSTICAS                          -->
    <!-- ══════════════════════════════════════════════ -->
    <?php elseif ($seccion === 'estadisticas'): ?>
    <?php
    $postsRecientes = $db->query("
        SELECT p.contenido, p.fecha_creacion, u.nombre,
               COUNT(l.post_id) as likes
        FROM posts p
        JOIN usuarios u ON u.id = p.usuario_id
        LEFT JOIN post_likes l ON l.post_id = p.id AND l.tipo='like'
        GROUP BY p.id ORDER BY p.fecha_creacion DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    $topUsuarios = $db->query("
        SELECT u.nombre, COUNT(p.id) as total_posts
        FROM usuarios u
        LEFT JOIN posts p ON p.usuario_id = u.id
        GROUP BY u.id ORDER BY total_posts DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="seccion-panel">
        <h2><i class="fas fa-chart-line"></i> Estadísticas</h2>
        <div class="stats-grid">
            <div class="stat-box">
                <h3>Posts más recientes</h3>
                <table class="user-table">
                    <thead><tr><th>Usuario</th><th>Post</th><th>Likes</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($postsRecientes as $pr): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($pr['nombre']) ?></strong></td>
                        <td><?= htmlspecialchars(substr($pr['contenido'],0,50)) ?>...</td>
                        <td><i class="fas fa-thumbs-up" style="color:#2bbbad"></i> <?= $pr['likes'] ?></td>
                        <td><?= date('d/m H:i', strtotime($pr['fecha_creacion'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="stat-box">
                <h3>Usuarios más activos</h3>
                <table class="user-table">
                    <thead><tr><th>Usuario</th><th>Posts</th></tr></thead>
                    <tbody>
                    <?php foreach ($topUsuarios as $tu): ?>
                    <tr>
                        <td><?= htmlspecialchars($tu['nombre']) ?></td>
                        <td><strong style="color:#2bbbad"><?= $tu['total_posts'] ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</main>
</body>
</html>