<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'no auth']);
    exit();
}
require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();

header('Content-Type: application/json');

$postId = intval($_POST['post_id'] ?? 0);
$tipo   = ($_POST['tipo'] ?? 'like') === 'dislike' ? 'dislike' : 'like';
$userId = $_SESSION['user_id'];

if (!$postId) { echo json_encode(['error' => 'invalid']); exit(); }

// Ver si ya existe reacción
$check = $db->prepare("SELECT tipo FROM post_likes WHERE usuario_id = ? AND post_id = ?");
$check->execute([$userId, $postId]);
$existing = $check->fetch(PDO::FETCH_ASSOC);

if ($existing) {
    if ($existing['tipo'] === $tipo) {
        // Quitar reacción
        $db->prepare("DELETE FROM post_likes WHERE usuario_id = ? AND post_id = ?")
           ->execute([$userId, $postId]);
    } else {
        // Cambiar reacción
        $db->prepare("UPDATE post_likes SET tipo = ? WHERE usuario_id = ? AND post_id = ?")
           ->execute([$tipo, $userId, $postId]);
    }
} else {
    // Nueva reacción
    $db->prepare("INSERT INTO post_likes (usuario_id, post_id, tipo) VALUES (?, ?, ?)")
       ->execute([$userId, $postId, $tipo]);
}

// Contar por separado
$likes = $db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND tipo = 'like'");
$likes->execute([$postId]);
$totalLikes = (int)$likes->fetchColumn();

$dislikes = $db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND tipo = 'dislike'");
$dislikes->execute([$postId]);
$totalDislikes = (int)$dislikes->fetchColumn();

// Reacción actual del usuario
$curr = $db->prepare("SELECT tipo FROM post_likes WHERE usuario_id = ? AND post_id = ?");
$curr->execute([$userId, $postId]);
$miReaccion = $curr->fetchColumn();

echo json_encode([
    'likes'       => $totalLikes,
    'dislikes'    => $totalDislikes,
    'mi_reaccion' => $miReaccion ?: null
]);