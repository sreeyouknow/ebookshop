<?php
class searchPagination {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Approve a book request
   public function approve($id) {
    $stmt = $this->conn->prepare("UPDATE book_requests SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

public function reject($id) {
    $stmt = $this->conn->prepare("UPDATE book_requests SET status = 'rejected' WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

public function countBookRequests($search) {
    $searchParam = "%" . $search . "%";
    $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM book_requests WHERE title LIKE ?");
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

public function getBookRequests($search, $start, $limit) {
    $searchParam = "%" . $search . "%";

    // Cast $start and $limit as integers to prevent SQL injection
    $start = (int) $start;
    $limit = (int) $limit;

    // Since MySQL does not allow binding LIMIT as parameter in all versions,
    // include $start and $limit directly after casting
    $sql = "
        SELECT br.*, u.name AS client_name
        FROM book_requests br
        LEFT JOIN users u ON br.client_id = u.id
        WHERE br.title LIKE ?
        ORDER BY br.requested_at DESC
        LIMIT $start, $limit
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("s", $searchParam);
    $stmt->execute();
    return $stmt->get_result();
}

    //option 2: get book request for client
    public function getBookRequestsByClient($client_id, $search, $start, $limit) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("
            SELECT * FROM book_requests 
            WHERE client_id = ? AND title LIKE ? 
            ORDER BY requested_at DESC 
            LIMIT ?, ?
        ");
        $stmt->bind_param("issi", $client_id, $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Count Agents
    public function countAgent($search) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'agent' AND name LIKE ?");
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Get Agents
    public function getAgent($search, $start, $limit) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'agent' AND name LIKE ? LIMIT ?, ?");
        $stmt->bind_param("sii", $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    // Count Agents
    public function countClient($search) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'client' AND name LIKE ?");
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Get Agents
    public function getClient($search, $start, $limit) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE role = 'client' AND name LIKE ? LIMIT ?, ?");
        $stmt->bind_param("sii", $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    // Count Users
    public function countUsers($search) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE name LIKE ?");
        $stmt->bind_param("s", $searchParam);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }

    // Get Users
    public function getUsers($search, $start, $limit) {
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE name LIKE ? LIMIT ?, ?");
        $stmt->bind_param("sii", $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    //  Count Books 
    public function countBooks($search){
        $searchParam = "%{$search}%";
        $count_stmt = $this->conn->prepare("SELECT COUNT(*) AS total FROM books WHERE title LIKE ?");
        $count_stmt->bind_param("s", $searchParam);
        $count_stmt->execute();
        return $count_stmt->get_result()->fetch_assoc(); 
    }
    // $countBook = $pagesearch->countBooks($search);

    // Get Books 
    public function getBooks($search, $start, $limit){
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("SELECT b.*, u.name AS uploader 
        FROM books b 
        JOIN users u ON b.uploaded_by = u.id 
        WHERE b.title LIKE ? 
        ORDER BY b.uploaded_at DESC 
        LIMIT ?, ?");
        $stmt->bind_param("sii", $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    // $result = $pagesearch->getBooks($search, $start, $limit);

    public function countReviews($agent_id, $search){
        $searchParam = "%{$search}%";
        $count_stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total
            FROM reviews r
            JOIN users u ON r.client_id = u.id
            WHERE r.agent_id = ? AND u.name LIKE ?
        ");
        $count_stmt->bind_param("is", $agent_id, $searchParam);
        $count_stmt->execute();
        return $count_stmt->get_result()->fetch_assoc()['total'];
    }

    public function getReviews($agent_id, $search, $start, $limit){
        $searchParam = "%{$search}%";
        $review_stmt = $this->conn->prepare("
            SELECT r.id, r.message, r.sent_at, r.reply, u.name AS client_name
            FROM reviews r
            JOIN users u ON r.client_id = u.id
            WHERE r.agent_id = ? AND u.name LIKE ?
            ORDER BY r.sent_at DESC
            LIMIT ?, ?
        ");
        $review_stmt->bind_param("isii", $agent_id, $searchParam, $start, $limit);
        $review_stmt->execute();
        return $review_stmt->get_result();
    }

    public function countWishlist($client_id, $search){
        $searchParam = "%{$search}%";
        $count_stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total 
            FROM wishlist 
            JOIN books ON wishlist.book_id = books.id 
            WHERE wishlist.client_id = ? AND books.title LIKE ?
        ");
        $count_stmt->bind_param("is", $client_id, $searchParam);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result()->fetch_assoc();
        return $count_result['total'];
    }
    public function getWishlist($client_id, $search, $start, $limit){
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("
            SELECT wishlist.id, books.title, books.author, books.price 
            FROM wishlist 
            JOIN books ON wishlist.book_id = books.id 
            WHERE wishlist.client_id = ? AND books.title LIKE ?
            ORDER BY wishlist.added_at DESC
            LIMIT ?, ?
        ");
        $stmt->bind_param("isii", $client_id, $searchParam, $start, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function countPurchase($client_id, $search){
        $searchParam = "%{$search}%";
        $count_stmt = $this->conn->prepare("SELECT COUNT(*) AS total
            FROM purchases
            JOIN books ON purchases.book_id = books.id
            WHERE purchases.client_id = ? AND books.title LIKE ?
        ");
        $count_stmt->bind_param("is", $client_id, $searchParam);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result()->fetch_assoc();
        return $count_result['total'];
    }
    public function getPurchase($client_id, $search, $start, $limit){
        $searchParam = "%{$search}%";
        $stmt = $this->conn->prepare("
            SELECT purchases.purchase_date, books.title, books.author, books.price 
            FROM purchases
            JOIN books ON purchases.book_id = books.id 
            WHERE purchases.client_id = ? AND books.title LIKE ?
            ORDER BY purchases.purchase_date DESC LIMIT ? OFFSET ?
        ");
        $stmt->bind_param("isii", $client_id, $searchParam, $limit, $start);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
