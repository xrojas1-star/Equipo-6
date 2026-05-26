<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /perfil.php");
    exit();
}
require_once __DIR__ . '/../config/Database.php';

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

    // Nudity
    $nudity = $data['nudity'] ?? [];
    if (($nudity['sexual_activity'] ?? 0) > 0.4) return false;
    if (($nudity['very_nudity']     ?? $nudity['nude'] ?? 0) > 0.5) return false;
    if (($nudity['semi_nude']       ?? 0) > 0.6) return false;
    if (($nudity['very_suggestive'] ?? 0) > 0.7) return false;

    // Armas
    $weapon = $data['weapon'] ?? [];
    if (($weapon['classes']['firearm'] ?? $weapon['firearm'] ?? 0) > 0.6) return false;

    // Drogas
    $drug = $data['recreational_drug'] ?? [];
    if (($drug['prob'] ?? 0) > 0.6) return false;

    // Gore
    $gore = $data['gore'] ?? [];
    if (($gore['prob'] ?? 0) > 0.7) return false;

    // Ofensivo
    $offensive = $data['offensive'] ?? [];
    if (($offensive['prob'] ?? 0) > 0.7) return false;

    return true;
}

// =====================================================
// PROCESAR SUBIDA
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId  = $_SESSION['user_id'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024;

    $campo = null;
    $file  = null;

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $campo     = 'foto_perfil';
        $file      = $_FILES['foto_perfil'];
        $uploadDir = __DIR__ . '/../uploads/fotos/';
        $prefix    = 'user_';
    } elseif (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
        $campo     = 'foto_portada';
        $file      = $_FILES['foto_portada'];
        $uploadDir = __DIR__ . '/../uploads/portadas/';
        $prefix    = 'portada_';
    }

    if (!$campo) {
        echo "<script>alert('No se recibió ningún archivo.'); window.history.back();</script>";
        exit();
    }

    if (!in_array($file['type'], $allowed)) {
        echo "<script>alert('Solo se permiten imágenes JPG, PNG, WEBP o GIF.'); window.history.back();</script>";
        exit();
    }

    if ($file['size'] > $maxSize) {
        echo "<script>alert('La imagen no debe superar los 5 MB.'); window.history.back();</script>";
        exit();
    }

    // ── MODERACIÓN ──
    if (!imagenEsSegura($file['tmp_name'])) {
        $tipo = ($campo === 'foto_perfil') ? 'foto de perfil' : 'foto de portada';
        echo "<script>alert('⚠️ Tu $tipo fue rechazada porque contiene contenido inapropiado (adulto, armas, drogas u ofensivo). Por favor sube una imagen diferente.'); window.history.back();</script>";
        exit();
    }

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . $userId . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET {$campo} = :foto WHERE id = :id");
        $stmt->bindParam(':foto', $filename);
        $stmt->bindParam(':id',   $userId);
        $stmt->execute();
        if ($campo === 'foto_perfil') {
            $_SESSION['foto_perfil'] = $filename;
        }
        header("Location: /perfil.php");
        exit();
    } else {
        echo "<script>alert('No se pudo guardar la imagen. Verifica permisos.'); window.history.back();</script>";
    }

} else {
    header("Location: /perfil.php");
    exit();
}
?>