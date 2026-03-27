<?php
// Iniciamos la sesión para poder "recordar" al usuario después de entrar
session_start();
require_once '../config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        // Buscamos si el correo existe en la tabla de usuarios
        $query = "SELECT id, nombre, password, rol FROM usuarios WHERE correo = :correo";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':correo', $email);
        $stmt->execute();

        // Si encontramos el correo...
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificamos si la contraseña escrita coincide con la encriptada
            if (password_verify($password, $row['password'])) {
                
                // ¡Éxito! Guardamos datos básicos en la sesión
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['nombre'];
                $_SESSION['user_role'] = $row['rol'];

                echo "<script>
                        alert('¡Bienvenido de nuevo, " . $row['nombre'] . "!');
                        window.location.href = '../public/home.php'; // Lo mandamos a la portada
                      </script>";
            } else {
                echo "<script>
                        alert('Error: Contraseña incorrecta.');
                        window.history.back();
                      </script>";
            }
        } else {
            // Si el correo no existe en la BD
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