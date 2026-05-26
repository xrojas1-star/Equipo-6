<?php
class Database {
    private $host = "db5020439652.hosting-data.io";
    private $db_name = "dbs15662179";
    private $username = "dbu3939032";
    private $password = "Vitalis123%";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>