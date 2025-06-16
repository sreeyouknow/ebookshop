<?php
class delete{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function deleteUser($id) {
        $del_user = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $del_user->bind_param("i", $id);
        $del_user->execute();
        return $del_user->affected_rows > 0;
    }
     // $deleteUser = $delete->deleteUser($id);
    public function deleteBook($id){
        $del_book = $this->conn->prepare("DELETE FROM books WHERE id = ?");
        $del_book->bind_param("i", $id);
        $del_book->execute();
        return $del_book->affected_rows > 0;
    }
    // $delete_book = $delete->deleteBook($id);

    public function deleteCard($id){
        $del_cart = $this->conn->prepare("DELETE FROM cart WHERE book_id = ?");
        $del_cart->bind_param("i", $id);
        $del_cart->execute();
        return $del_cart->affected_rows > 0;
    }
    // $delete_cart = $delete->deleteCard($id);
    public function deletePurchases($id){
        $del_purchases = $this->conn->prepare("DELETE FROM purchases WHERE book_id = ?");
        $del_purchases->bind_param("i", $id);
        $del_purchases->execute();
        return $del_cart->affected_rows > 0;
    }
    // $delete_Purchases = $delete->deletePurchases($id);

    public function deleteMessages($id, $agent_id){
        $stmt = $this->conn->prepare("DELETE FROM messages WHERE id = ? AND agent_id = ?");
        $stmt->bind_param("ii", $id, $agent_id);
        return $stmt->execute();
    }
    // $del_message = $delete->deleteMessage($id);

    public function deleteReviews($id, $agent_id){
        $stmt = $this->conn->prepare("DELETE FROM reivews WHERE id = ? AND agent_id = ?");
        $stmt->bind_param("ii", $id, $agent_id);
        return $stmt->execute();
    }
    // $del_reviews = $delete->deleteReviews($id);

    public function deleteReviewsC($id, $client_id){
        $stmt = $this->conn->prepare("DELETE FROM reviews WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $id, $client_id);
        return $stmt->execute();
    }

    public function deleteWishlist($id, $client_id){
        $stmt = $this->conn->prepare("DELETE FROM wishlist WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $id, $client_id);
        return $stmt->execute();
    }

    public function deleteCarts($remove_id, $client_id){
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $remove_id, $client_id);
        return $stmt->execute();
    }
    // $deleteCarts = $delete->deleteCarts($remove_id, $client_id);
}   
?>