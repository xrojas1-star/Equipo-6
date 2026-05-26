<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        $query = "SELECT id, nombre, password, rol FROM usuarios WHERE correo = :correo";
        $stmt  = $db->prepare($query);
        $stmt->bindParam(':correo', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $row['password'])) {
                // Limpiar sesión visitante si existía
                session_unset();
                session_regenerate_id(true);
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['nombre'];
                $_SESSION['user_role'] = $row['rol'];
                unset($_SESSION['es_visitante']);

                // Todos van a home.php — el admin verá su botón desde ahí
                echo "<script>
                        alert('¡Bienvenido de nuevo, " . $row['nombre'] . "!');
                        window.location.href = '/home.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Error: Contraseña incorrecta.');
                        window.history.back();
                      </script>";
            }
        } else {
            echo "<script>
                    alert('No existe ninguna cuenta con este correo.');
                    window.history.back();
                  </script>";
        }
    } catch (PDOException $e) {
        echo "Error de base de datos: " . $e->getMessage();
    }
}
?>