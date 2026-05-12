<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../public/perfil.php");
    exit();
}

require_once '../config/Database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId  = $_SESSION['user_id'];
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    // Determinar qué foto se sube
    $campo = null;
    $file  = null;

    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $campo = 'foto_perfil';
        $file  = $_FILES['foto_perfil'];
        $uploadDir = '../public/uploads/fotos/';
        $prefix    = 'user_';
    } elseif (isset($_FILES['foto_portada']) && $_FILES['foto_portada']['error'] === UPLOAD_ERR_OK) {
        $campo = 'foto_portada';
        $file  = $_FILES['foto_portada'];
        $uploadDir = '../public/uploads/portadas/';
        $prefix    = 'portada_';
    }

    if (!$campo) {
        echo "<script>alert('No se recibió ningún archivo.'); window.history.back();</script>";
        exit();
    }

    // Validaciones
    if (!in_array($file['type'], $allowed)) {
        echo "<script>alert('Solo se permiten imágenes JPG, PNG, WEBP o GIF.'); window.history.back();</script>";
        exit();
    }
    if ($file['size'] > $maxSize) {
        echo "<script>alert('La imagen no debe superar los 5 MB.'); window.history.back();</script>";
        exit();
    }

    // Crear carpeta si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Nombre único
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . $userId . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("UPDATE usuarios SET {$campo} = :foto WHERE id = :id");
        $stmt->bindParam(':foto', $filename);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        if ($campo === 'foto_perfil') {
            $_SESSION['foto_perfil'] = $filename;
        }

        header("Location: ../public/perfil.php");
        exit();
    } else {
        echo "<script>alert('No se pudo guardar la imagen. Verifica permisos.'); window.history.back();</script>";
    }
} else {
    header("Location: ../public/perfil.php");
    exit();
}
?>