<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'class_project';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                                $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            // Check if database doesn't exist
            if ($exception->getCode() == 1049) {
                die("Database 'class_project' does not exist. Please run setup_database.php first to create the database and tables.");
            } elseif ($exception->getCode() == 1146) {
                die("Tables don't exist in the database. Please run setup_database.php to create the required tables.");
            } else {
                die("Connection error: " . $exception->getMessage() . "<br>Please check your database configuration.");
            }
        }

        return $this->conn;
    }
}
?>
