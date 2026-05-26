<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/filtro_grocerias.php';

$database = new Database();
$db = $database->getConnection();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// Obtener comentarios — permitido para todos (visitante también puede ver)
if ($accion === 'obtener') {
    $postId = intval($_GET['post_id'] ?? 0);
    if (!$postId) { echo json_encode([]); exit(); }
    $stmt = $db->prepare("
        SELECT c.id, c.contenido, c.fecha, u.nombre, u.foto_perfil, c.usuario_id
        FROM comentarios c
        JOIN usuarios u ON u.id = c.usuario_id
        WHERE c.post_id = :pid
        ORDER BY c.fecha ASC
    ");
    $stmt->bindParam(':pid', $postId);
    $stmt->execute();
    $coms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($coms);
    exit();
}

// Las demás acciones requieren sesión real
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'no auth']);
    exit();
}

// ---- CREAR COMENTARIO ----
if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId    = intval($_POST['post_id'] ?? 0);
    $contenido = trim($_POST['contenido'] ?? '');
    $userId    = $_SESSION['user_id'];

    if (!$postId || empty($contenido)) {
        echo json_encode(['error' => 'datos incompletos']); exit();
    }

    // ── FILTRO DE GROSERÍAS ──
    if (!textoEsLimpio($contenido)) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'groseria', 'mensaje' => '⚠️ Tu comentario contiene lenguaje inapropiado. Por favor usa un lenguaje respetuoso.']);
        exit();
    }

    $stmt = $db->prepare("INSERT INTO comentarios (post_id, usuario_id, contenido) VALUES (:pid, :uid, :con)");
    $stmt->execute([':pid' => $postId, ':uid' => $userId, ':con' => $contenido]);
    $comId = $db->lastInsertId();

    $get = $db->prepare("
        SELECT c.id, c.contenido, c.fecha, u.nombre, u.foto_perfil, c.usuario_id
        FROM comentarios c JOIN usuarios u ON u.id = c.usuario_id
        WHERE c.id = :id
    ");
    $get->bindParam(':id', $comId);
    $get->execute();
    $com = $get->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'comentario' => $com]);
    exit();
}

// ---- ELIMINAR COMENTARIO ----
if ($accion === 'eliminar') {
    $comId  = intval($_GET['id'] ?? 0);
    $userId = $_SESSION['user_id'];
    $rol    = $_SESSION['user_role'] ?? 'autor';

    if ($rol === 'admin') {
        $stmt = $db->prepare("DELETE FROM comentarios WHERE id=:id");
    } else {
        $stmt = $db->prepare("DELETE FROM comentarios WHERE id=:id AND usuario_id=:uid");
        $stmt->bindParam(':uid', $userId);
    }
    $stmt->bindParam(':id', $comId);
    $stmt->execute();
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit();
}

echo json_encode(['error' => 'acción no válida']);
?>