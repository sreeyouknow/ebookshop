<?php
class veiwTotalController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function tusers(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM users");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    //$tusers = $viewTotalCount->tclients()['total'];
    public function tagent(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'agent'");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    public function tclients(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM users WHERE role = 'client'");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    public function tbooks(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM books");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    public function tbookRequest(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM book_requests");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    public function tpurchases(){
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM purchases");
        $stmt->execute();
        return $result = $stmt->get_result()->fetch_assoc();
    }
    public function trecentBooks(){
        $recent_books = $this->conn->prepare("SELECT id, title, uploaded_by, uploaded_at FROM books ORDER BY uploaded_at DESC LIMIT 5");
        $recent_books->execute();
        return $recent_result = $recent_books->get_result();
    }
}
?>