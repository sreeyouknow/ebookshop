<?php
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "book_can";
    private $conn;

    public function __construct() {
        $this->connectDB();
    }

    private function connectDB() {
        $this->conn = mysqli_connect($this->host, $this->username, $this->password, $this->dbname);
        if (!$this->conn) {
            die("Connection failed: " . mysqli_connect_error());
        }

        require_once '../Classes/error_handler.php';
        new ErrorLogger($this->conn);
    }

    public function getConnection() {
        return $this->conn;
    }

    public function is_logged_in() {
        return isset($_SESSION['user_id']);
    }
}
?>
