<?php
class add {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function addWishlist($client_id, $book_id){
        $check = $this->conn->prepare("SELECT id FROM wishlist WHERE client_id = ? AND book_id = ?");
        $check->bind_param("ii", $client_id, $book_id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows === 0) {
            $stmt = $this->conn->prepare("INSERT INTO wishlist (client_id, book_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $client_id, $book_id);
            return $stmt->execute();
        }
    }
    // $addWishlist = $add->addWishlist($client_id, $book_id);
    public function addCart($client_id, $book_id){
        $check = $this->conn->prepare("SELECT id, quantity FROM cart WHERE client_id = ? AND book_id = ?");
        $check->bind_param("ii", $client_id, $book_id); 
        $check->execute();
        $result = $check->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $new_qty = $row['quantity'] + 1;
            $stmt = $this->conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $new_qty, $row['id']);
            return $stmt->execute();
        } else {
            $stmt = $this->conn->prepare("INSERT INTO cart (client_id, book_id, quantity) VALUES (?, ?, 1)");
            $stmt->bind_param("ii", $client_id, $book_id);
            return $stmt->execute();
        }
    }
    // $addCart = $add->addCart($client_id, $book_id);
}