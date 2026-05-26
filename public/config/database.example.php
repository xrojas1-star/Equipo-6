<?php
/**
 * ARCHIVO DE EJEMPLO - Copia este archivo como config/database.php
 * y reemplaza los valores con tus credenciales reales.
 * NUNCA subas config/database.php al repositorio.
 */
class Database {
    private $host = "TU_HOST_AQUI";
    private $db_name = "TU_NOMBRE_BD_AQUI";
    private $username = "TU_USUARIO_AQUI";
    private $password = "TU_CONTRASENA_AQUI";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
