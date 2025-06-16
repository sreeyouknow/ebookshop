<?php
class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Register a new user
    public function register($name, $email, $password, $role) {
        $stmt = $this->conn->prepare("INSERT INTO users(name, email, password, role) VALUES (?, ?, ?, ?)");
        if (!$stmt) return false;
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bind_param("ssss", $name, $email, $hash, $role);
        return $stmt->execute();
        // Usage: $register = $user->register($name, $email, $password, $role);
    }

    // Check if an email exists
    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
        // Usage: $emailExists = $user->emailExists($email);
    }

    // Validate email format
    public function emailValidate($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
        // Usage: $isValidEmail = $user->emailValidate($email);
    }

    // Validate password strength
    public function passwordValidate($password) {
        return preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $password);
        // Usage: $isValidPassword = $user->passwordValidate($password);
    }

    // Login user
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        return ($user && password_verify($password, $user['password'])) ? $user : false;
        // Usage: $loginUser = $user->login($email, $password);
    }

    // Update reset token for forgot password
    public function updateResetToken($email, $token, $expiry) {
        $stmt = $this->conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
        $stmt->bind_param('sss', $token, $expiry, $email);
        return $stmt->execute();
        // Usage: $updateToken = $user->updateResetToken($email, $token, $expiry);
    }

    // Find user by reset token
    public function findByToken($token) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
        // Usage: $foundUser = $user->findByToken($token);
    }

    // Update password using token
    public function updatePassword($token, $password) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->bind_param('ss', $hashed, $token);
        return $stmt->execute();
        // Usage: $passwordUpdated = $user->updatePassword($token, $newPassword);
    }

    // Get agent by ID
    public function agents($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
        // Usage: $agent = $user->agents($id);
    }

    // Get book by ID
    public function books($id) {
        $stmt = $this->conn->prepare("SELECT * FROM books WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result();
        // Usage:
        // $bookResult = $user->books($edit_id);
        // $book = $bookResult->fetch_assoc();
    }

    // Insert a new book
    public function insertBooks($id, $title, $author, $description, $price, $uploaded_by, $uploaded_at) {
        $stmt = $this->conn->prepare("INSERT INTO books (id, title, author, description, price, uploaded_by, uploaded_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $id, $title, $author, $description, $price, $uploaded_by, $uploaded_at);
        return $stmt->execute();
        // Usage: $insertBook = $user->insertBooks($title, $author, $description, $price, $uploaded_by, $uploaded_at);
    }
    
    //select conversation_status
    public function selectConversation($client_id, $agent_id){
        $check = $this->conn->prepare("SELECT conversation_status FROM messages WHERE client_id = ? AND agent_id = ? ORDER BY id DESC LIMIT 1");
        $check->bind_param("ii", $client_id, $agent_id);
        $check->execute();
        return $check->get_result()->fetch_assoc();
    }
    public function insertConversation($agent_id, $client_id, $reply){
        $stmt = $this->conn->prepare("INSERT INTO messages (agent_id, client_id, sender, message) VALUES (?, ?, 'agent', ?)");
        $stmt->bind_param("iis", $agent_id, $client_id, $reply);
        return $stmt->execute();
    }
    //for client
    public function insertclientConversation($agent_id, $client_id, $reply){
        $stmt = $this->conn->prepare("INSERT INTO messages (agent_id, client_id, sender, message) VALUES (?, ?, 'client', ?)");
        $stmt->bind_param("iis", $agent_id, $client_id, $reply);
        return $stmt->execute();
    }
    // $insertCConversation = $user->insertclientConversation($agent_id, $client_id, $reply);

    //reviews
    public function insertReviews($client_id, $agent_id, $message, $rating){
        $stmt = $this->conn->prepare("INSERT INTO reviews (client_id, agent_id, message, rating) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iisi", $client_id, $agent_id, $message, $rating);
        return $stmt->execute();
    }


    //update profile for agent
    public function profileUpdate($agent_id) {
        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $agent_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }
    // $profileUpdate = $user->profileUpdate($agent_id);

    // Get book_id from wishlist
    public function getBookFWishlist($wishlist_id, $client_id){
        $stmt = $this->conn->prepare("SELECT book_id FROM wishlist WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $wishlist_id, $client_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    // $result = $user->getBookFWishlist();

    //get book for place order
    public function getBookOrder($cart_id, $client_id){
        $stmt = $this->conn->prepare("SELECT book_id FROM cart WHERE id = ? AND client_id = ?");
        $stmt->bind_param("ii", $cart_id, $client_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    // $result = $user->getBookOrder($cart_id, $client_id);

    // insert book place order 
    public function insertBookOrder($client_id, $book_id){
        $insert = $this->conn->prepare("INSERT INTO purchases (client_id, book_id, purchase_date) VALUES (?, ?, NOW())");
        $insert->bind_param("ii", $client_id, $book_id);
        return $insert->execute();
    }
    // $insertBookOrder = $user->insertBookOrder($client_id, $book_id);

    public function insertBookRequest($book_request_id, $client_id, $title, $author, $note){
        $stmt = $this->conn->prepare("INSERT INTO book_requests (id, client_id, title, author, note, requested_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisss",$book_request_id, $client_id, $title, $author, $note);
        $stmt->execute();
    }

}
?>
