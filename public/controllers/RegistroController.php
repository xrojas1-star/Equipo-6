<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre          = trim($_POST['fullName']         ?? '');
    $correo          = trim($_POST['email']            ?? '');
    $password        = $_POST['password']              ?? '';
    $confirmPassword = $_POST['confirmPassword']       ?? '';

    // ── 1. Validar nombre ──
    if (strlen($nombre) < 2) {
        echo "<script>alert('El nombre es demasiado corto.'); window.history.back();</script>";
        exit;
    }

    // ── 2. Validar formato de correo ──
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('El correo electrónico no es válido.'); window.history.back();</script>";
        exit;
    }

    // ── 3. Validar dominio permitido ──
    $dominiosPermitidos = [
        'gmail.com',
        'hotmail.com',
        'outlook.com',
        'yahoo.com',
        'ucol.mx',
        'live.com',
        'icloud.com',
    ];

    $dominio = strtolower(substr($correo, strpos($correo, '@') + 1));

    if (!in_array($dominio, $dominiosPermitidos)) {
        $lista = implode(', ', $dominiosPermitidos);
        echo "<script>alert('El dominio del correo no está permitido.\\nSolo se aceptan: $lista'); window.history.back();</script>";
        exit;
    }

    // ── 4. Validar contraseña segura ──
    $erroresPassword = [];
    if (strlen($password) < 8)                   $erroresPassword[] = 'mínimo 8 caracteres';
    if (!preg_match('/[A-Z]/', $password))        $erroresPassword[] = 'al menos una mayúscula';
    if (!preg_match('/[0-9]/', $password))        $erroresPassword[] = 'al menos un número';
    if (!preg_match('/[@$!%*?&#.]/', $password))  $erroresPassword[] = 'al menos un carácter especial';

    if (!empty($erroresPassword)) {
        $msg = 'La contraseña debe tener: ' . implode(', ', $erroresPassword) . '.';
        echo "<script>alert(" . json_encode($msg) . "); window.history.back();</script>";
        exit;
    }

    // ── 5. Confirmar contraseñas ──
    if ($password !== $confirmPassword) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.history.back();</script>";
        exit;
    }

    // ── 6. Registrar en BD ──
    $database = new Database();
    $db = $database->getConnection();

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        $query = "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
        $stmt  = $db->prepare($query);
        $stmt->bindParam(':nombre',   $nombre);
        $stmt->bindParam(':correo',   $correo);
        $stmt->bindParam(':password', $passwordHash);

        if ($stmt->execute()) {
            session_unset();
            session_regenerate_id(true);
            $_SESSION['user_id']   = $db->lastInsertId();
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_role'] = 'autor';
            unset($_SESSION['es_visitante']);
            echo "<script>
                    alert('¡Registro exitoso! Bienvenido a Vitalis.');
                    window.location.href = '/home.php';
                  </script>";
        } else {
            echo "<script>alert('Hubo un problema al registrar. Intenta de nuevo.'); window.history.back();</script>";
        }

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo "<script>alert('Este correo ya está registrado. Por favor, inicia sesión.'); window.history.back();</script>";
        } else {
            echo "<script>alert('Error al registrar. Intenta más tarde.'); window.history.back();</script>";
        }
    }
}
?>