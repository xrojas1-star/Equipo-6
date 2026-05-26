<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$correo = trim($_POST['correo'] ?? '');

if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo inválido.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Verificar que el correo exista
$stmt = $db->prepare("SELECT id FROM usuarios WHERE correo = :correo");
$stmt->bindParam(':correo', $correo);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'No existe ninguna cuenta con ese correo.']);
    exit;
}

$userId = $user['id'];

// Invalidar tokens anteriores del usuario
$stmt = $db->prepare("UPDATE password_resets SET usado = 1 WHERE usuario_id = :uid");
$stmt->bindParam(':uid', $userId);
$stmt->execute();

// Generar token seguro de 64 caracteres
$token   = bin2hex(random_bytes(32)); // 64 chars hex
$expira  = date('Y-m-d H:i:s', strtotime('+30 minutes'));

// Guardar token en BD
$stmt = $db->prepare("INSERT INTO password_resets (usuario_id, token, expira_en) VALUES (:uid, :token, :expira)");
$stmt->bindParam(':uid',    $userId);
$stmt->bindParam(':token',  $token);
$stmt->bindParam(':expira', $expira);
$stmt->execute();

echo json_encode(['success' => true, 'token' => $token]);
exit;
?>
