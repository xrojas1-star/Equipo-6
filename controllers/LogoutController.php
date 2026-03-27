<?php
session_start();
session_unset(); // Borra las variables de sesión
session_destroy(); // Destruye la sesión por completo

// Redirigir de vuelta al login
header("Location: ../public/index.php");
exit();
?>