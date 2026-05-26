<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");
    exit();
}
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/filtro_grocerias.php';

$database = new Database();
$db = $database->getConnection();
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

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

    // ── FILTRO DE GROSERÍAS ──
    if (!textoEsLimpio($contenido)) {
        echo "<script>alert('⚠️ Tu post contiene lenguaje inapropiado. Por favor usa un lenguaje respetuoso.'); window.history.back();</script>";
        exit();
    }

    preg_match_all('/#(\w+)/u', $contenido, $matches);
    $hashtags = !empty($matches[0]) ? implode(' ', $matches[0]) : null;

    if (!empty($_FILES['imagen']['name'])) {
        $file    = $_FILES['imagen'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif'];

        if (!in_array($file['type'], $allowed)) {
            echo "<script>alert('Solo se permiten imágenes JPG, PNG, WEBP o GIF.'); window.history.back();</script>";
            exit();
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            echo "<script>alert('La imagen no debe superar los 5 MB.'); window.history.back();</script>";
            exit();
        }

        // ── MODERACIÓN DE IMAGEN ──
        if (!imagenEsSegura($file['tmp_name'])) {
            echo "<script>alert('⚠️ Imagen rechazada: contiene contenido inapropiado (adulto, armas, drogas u ofensivo).'); window.history.back();</script>";
            exit();
        }

        $uploadDir = __DIR__ . '/../uploads/posts/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'post_' . $userId . '_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $imagen = $filename;
        }
    }

    $stmt = $db->prepare("INSERT INTO posts (usuario_id, contenido, imagen, video_url, hashtags)
                          VALUES (:uid, :contenido, :imagen, :video, :hashtags)");
    $stmt->bindParam(':uid',       $userId);
    $stmt->bindParam(':contenido', $contenido);
    $stmt->bindParam(':imagen',    $imagen);
    $stmt->bindParam(':video',     $videoUrl);
    $stmt->bindParam(':hashtags',  $hashtags);

    if ($stmt->execute()) {
        header("Location: /blog.php");
    } else {
        echo "<script>alert('Error al publicar.'); window.history.back();</script>";
    }
    exit();
}

// =====================================================
// ELIMINAR POST
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
    header("Location: /blog.php");
    exit();
}

header("Location: /blog.php");
exit();
?>