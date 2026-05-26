<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$token    = trim($_POST['token']    ?? '');
$password = $_POST['password'] ?? '';

// Validar token
if (empty($token) || strlen($token) !== 64) {
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}

// Validar contraseña segura
$errores = [];
if (strlen($password) < 8)              $errores[] = 'mínimo 8 caracteres';
if (!preg_match('/[A-Z]/', $password))  $errores[] = 'al menos una mayúscula';
if (!preg_match('/[0-9]/', $password))  $errores[] = 'al menos un número';
if (!preg_match('/[@$!%*?&#.]/', $password)) $errores[] = 'al menos un carácter especial';

if (!empty($errores)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener: ' . implode(', ', $errores) . '.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();

// Buscar token válido, no usado y no expirado
$stmt = $db->prepare("
    SELECT usuario_id FROM password_resets
    WHERE token = :token
      AND usado = 0
      AND expira_en > NOW()
    LIMIT 1
");
$stmt->bindParam(':token', $token);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'El enlace ha expirado o ya fue usado. Solicita uno nuevo.']);
    exit;
}

$userId = $row['usuario_id'];
$hash   = password_hash($password, PASSWORD_BCRYPT);

// Actualizar contraseña
$stmt = $db->prepare("UPDATE usuarios SET password = :pw WHERE id = :id");
$stmt->bindParam(':pw', $hash);
$stmt->bindParam(':id', $userId);
$stmt->execute();

// Marcar token como usado
$stmt = $db->prepare("UPDATE password_resets SET usado = 1 WHERE token = :token");
$stmt->bindParam(':token', $token);
$stmt->execute();

echo json_encode(['success' => true]);
exit;
?>
