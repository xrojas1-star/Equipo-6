<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /perfil.php");
    exit();
}
require_once __DIR__ . '/../config/Database.php';
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

// =====================================================
// SIGHTENGINE — Moderación de imágenes
// =====================================================
define('SE_USER',   '1528645321');
define('SE_SECRET', 'QJuavJi263bCxuZWxMS4ovwCgUJaF6eF');

function imagenEsSegura($filePath) {
    if (!function_exists('curl_init')) return true;

    $postData = [
        'media'      => new CURLFile($filePath),
        'models'     => 'nudity-2.0,weapon,recreational_drug,gore,offensive-2.0',
        'api_user'   => SE_USER,
        'api_secret' => SE_SECRET,
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.sightengine.com/1.0/check.json');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response  = curl_exec($ch);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError || !$response) return true;

    $data = json_decode($response, true);
    if (!$data || ($data['status'] ?? '') !== 'success') return true;

    $nudity = $data['nudity'] ?? [];
    if (($nudity['sexual_activity'] ?? 0) > 0.4) return false;
    if (($nudity['very_nudity']     ?? $nudity['nude'] ?? 0) > 0.5) return false;
    if (($nudity['semi_nude']       ?? 0) > 0.6) return false;
    if (($nudity['very_suggestive'] ?? 0) > 0.7) return false;

    $weapon = $data['weapon'] ?? [];
    if (($weapon['classes']['firearm'] ?? $weapon['firearm'] ?? 0) > 0.6) return false;

    $drug = $data['recreational_drug'] ?? [];
    if (($drug['prob'] ?? 0) > 0.6) return false;

    $gore = $data['gore'] ?? [];
    if (($gore['prob'] ?? 0) > 0.7) return false;

    $offensive = $data['offensive'] ?? [];
    if (($offensive['prob'] ?? 0) > 0.7) return false;

    return true;
}

// =====================================================
// PROCESAR PORTADA
// =====================================================
$uploadDir   = __DIR__ . '/../uploads/portadas/';
$fotoPortada = null;

// ── Opción 1: base64 desde canvas (drag-to-reposition) ──
if (!empty($base64) && preg_match('/^data:image\/(jpeg|png|webp);base64,/', $base64, $m)) {
    $imgData = base64_decode(preg_replace('/^data:image\/\w+;base64,/', '', $base64));

    if ($imgData !== false && strlen($imgData) > 1000) {
        // Guardar en archivo temporal para analizarlo con Sightengine
        $tmpFile = tempnam(sys_get_temp_dir(), 'vitalis_portada_') . '.jpg';
        file_put_contents($tmpFile, $imgData);

        if (!imagenEsSegura($tmpFile)) {
            @unlink($tmpFile);
            echo "<script>alert('⚠️ Tu foto de portada fue rechazada porque contiene contenido inapropiado. Por favor sube una imagen diferente.'); window.history.back();</script>";
            exit();
        }

        @unlink($tmpFile); // limpiar temporal

        // Guardar imagen final
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = ($m[1] === 'png') ? 'png' : 'jpg';
        $filename = 'portada_' . $userId . '_' . time() . '.' . $ext;
        if (file_put_contents($uploadDir . $filename, $imgData) !== false) {
            $fotoPortada = $filename;
        }
    }
}

// ── Opción 2: archivo directo (fallback) ──
if (!$fotoPortada && isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
    $file    = $_FILES['foto_portada'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    if (in_array($file['type'], $allowed) && $file['size'] <= 8 * 1024 * 1024) {

        if (!imagenEsSegura($file['tmp_name'])) {
            echo "<script>alert('⚠️ Tu foto de portada fue rechazada porque contiene contenido inapropiado. Por favor sube una imagen diferente.'); window.history.back();</script>";
            exit();
        }

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
header("Location: /perfil.php");
exit();
?>