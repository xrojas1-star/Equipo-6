<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/login.php");
    exit();
}
require_once '../config/Database.php';

$database = new Database();
$db = $database->getConnection();

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

// =====================================================
// CREAR POST
// =====================================================
if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenido = trim($_POST['contenido'] ?? '');
    $videoUrl  = trim($_POST['video_url'] ?? '');
    $userId    = $_SESSION['user_id'];
    $imagen    = null;

    if (empty($contenido)) {
        echo "<script>alert('El contenido no puede estar vacío.'); window.history.back();</script>";
        exit();
    }

    // Extraer hashtags automáticamente del contenido
    preg_match_all('/#(\w+)/u', $contenido, $matches);
    $hashtags = !empty($matches[0]) ? implode(' ', $matches[0]) : null;

    // Subir imagen opcional
    if (!empty($_FILES['imagen']['name'])) {
        $file    = $_FILES['imagen'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
        if (in_array($file['type'], $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $uploadDir = '../public/uploads/posts/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'post_' . $userId . '_' . time() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $imagen = $filename;
            }
        }
    }

    $stmt = $db->prepare("INSERT INTO posts (usuario_id, contenido, imagen, video_url, hashtags)
                          VALUES (:uid, :contenido, :imagen, :video, :hashtags)");
    $stmt->bindParam(':uid',      $userId);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':imagen',   $imagen);
    $stmt->bindParam(':video',    $videoUrl);
    $stmt->bindParam(':hashtags', $hashtags);

    if ($stmt->execute()) {
        header("Location: ../public/blog.php");
    } else {
        echo "<script>alert('Error al publicar.'); window.history.back();</script>";
    }
    exit();
}

// =====================================================
// ELIMINAR POST (solo el autor o admin)
// =====================================================
if ($accion === 'eliminar') {
    $postId = intval($_GET['id'] ?? 0);
    $userId = $_SESSION['user_id'];
    $rol    = $_SESSION['user_role'] ?? 'autor';

    if ($rol === 'admin') {
        $stmt = $db->prepare("DELETE FROM posts WHERE id = :id");
    } else {
        $stmt = $db->prepare("DELETE FROM posts WHERE id = :id AND usuario_id = :uid");
        $stmt->bindParam(':uid', $userId);
    }
    $stmt->bindParam(':id', $postId);
    $stmt->execute();
    header("Location: ../public/blog.php");
    exit();
}

header("Location: ../public/blog.php");
exit();
?>
