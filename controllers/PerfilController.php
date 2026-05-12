<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/perfil.php");
    exit();
}

require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();
$userId = $_SESSION['user_id'];

$nombre    = trim($_POST['nombre']           ?? '');
$biografia = trim($_POST['biografia']        ?? '');
$posicion  = trim($_POST['portada_posicion'] ?? '50%');
$base64    = trim($_POST['portada_base64']   ?? '');

if (empty($nombre)) {
    echo "<script>alert('El nombre no puede estar vacío.'); window.history.back();</script>";
    exit();
}
if (!preg_match('/^\d+(\.\d+)?%$/', $posicion)) {
    $posicion = '50%';
}

$uploadDir   = '../public/uploads/portadas/';
$fotoPortada = null;

// ── Opción 1: imagen recortada como base64 (viene del canvas) ──
if (!empty($base64) && preg_match('/^data:image\/(jpeg|png|webp);base64,/', $base64, $m)) {
    $imgData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));
    if ($imgData !== false && strlen($imgData) > 1000) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = ($m[1] === 'png') ? 'png' : 'jpg';
        $filename = 'portada_' . $userId . '_' . time() . '.' . $ext;
        if (file_put_contents($uploadDir . $filename, $imgData) !== false) {
            $fotoPortada = $filename;
        }
    }
}

// ── Opción 2: fallback — archivo subido directamente ──
if (!$fotoPortada && isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['foto_portada'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (in_array($file['type'], $allowed) && $file['size'] <= 8 * 1024 * 1024) {
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'portada_' . $userId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $fotoPortada = $filename;
        }
    }
}

// ── Actualizar BD ──
if ($fotoPortada) {
    $stmt = $db->prepare("UPDATE usuarios SET nombre=:nombre, biografia=:bio, foto_portada=:portada, portada_posicion=:pos WHERE id=:id");
    $stmt->bindParam(':portada', $fotoPortada);
    $stmt->bindParam(':pos',     $posicion);
} else {
    $stmt = $db->prepare("UPDATE usuarios SET nombre=:nombre, biografia=:bio, portada_posicion=:pos WHERE id=:id");
    $stmt->bindParam(':pos', $posicion);
}

$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':bio',    $biografia);
$stmt->bindParam(':id',     $userId);
$stmt->execute();

$_SESSION['user_name'] = $nombre;

header("Location: ../public/perfil.php");
exit();
?>