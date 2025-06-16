<?php
class showTable {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function users(){
        $stmt = $this->conn->prepare("SELECT * FROM users");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function agent(){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'agent'");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function clients(){
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'client'");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function Fbooks(){
        $stmt = $this->conn->prepare("SELECT * FROM books");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function bookRequest(){
        $stmt = $this->conn->prepare("SELECT * FROM book_requests");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function purchases(){
        $stmt = $this->conn->prepare("SELECT * FROM purchases");
        $stmt->execute();
        return $result = $stmt->get_result();
    }
    public function wishlist($id){
        $wishlist_result = $this->conn->prepare("
            SELECT b.title FROM wishlist w
            JOIN books b ON w.book_id = b.id
            WHERE w.client_id = ?
        ");
        $wishlist_result->bind_param("i", $id);
        $wishlist_result->execute();
        return $wishlist_result->get_result();
    }
    public function cart($id){
        $cart_result = $this->conn->prepare("
            SELECT b.title, c.quantity FROM cart c
            JOIN books b ON c.book_id = b.id
            WHERE c.client_id = ?
        ");
        $cart_result->bind_param("i", $id);
        $cart_result->execute();
        return $cart_result->get_result();
    }
    public function messages($client_id, $agent_id){
        $stmt = $this->conn->prepare("SELECT * FROM messages WHERE client_id = ? AND agent_id = ? ORDER BY sent_at ASC");
        $stmt->bind_param("ii", $client_id, $agent_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function reviews($id, $client_id){
        $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $id, $client_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>