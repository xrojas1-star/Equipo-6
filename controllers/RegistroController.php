<?php
// Incluimos nuestro archivo de conexión a la base de datos
require_once '../config/Database.php';

// Verificamos que los datos vengan del formulario por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recibimos los datos del formulario (usamos los 'name' del HTML)
    $nombre = $_POST['fullName'];
    $correo = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // 2. Validar que las contraseñas sean idénticas
    if ($password !== $confirmPassword) {
        // Si no son iguales, mostramos una alerta y lo regresamos
        echo "<script>
                alert('Error: Las contraseñas no coinciden.');
                window.history.back();
              </script>";
        exit;
    }

    // 3. Conectamos a la base de datos instanciando el objeto Database
    $database = new Database();
    $db = $database->getConnection();

    // 4. Encriptamos la contraseña por seguridad (NUNCA se guardan en texto plano)
    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    try {
        // 5. Preparamos la instrucción SQL para insertar el usuario
        $query = "INSERT INTO usuarios (nombre, correo, password) VALUES (:nombre, :correo, :password)";
        $stmt = $db->prepare($query);

        // 6. Asignamos los valores (esto evita ataques de inyección SQL)
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':password', $passwordHash);

        // 7. Ejecutamos la consulta
        if ($stmt->execute()) {
            echo "<script>
                    alert('¡Registro exitoso! Bienvenido a Vitalis.');
                    window.location.href = '../public/home.php'; 
                  </script>";
        } else {
            echo "<script>
                    alert('Hubo un problema al registrar. Intenta de nuevo.');
                    window.history.back();
                  </script>";
        }
    } catch (PDOException $e) {
        // Si el correo ya existe en la base de datos, lanzará el error 23000
        if ($e->getCode() == 23000) {
            echo "<script>
                    alert('Este correo ya está registrado. Por favor, inicia sesión.');
                    window.history.back();
                  </script>";
        } else {
            // Otro tipo de error de base de datos
            echo "Error fatal: " . $e->getMessage();
        }
    }
}
?>