<?php
session_start();
require '../Classes/database-control.php'; 
require '../Classes/delete-controller.php';
require '../Classes/user-control.php';
require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id']) && is_numeric($_POST['cart_id'])) {
    $cart_id = (int)$_POST['cart_id'];
    $client_id = $_SESSION['user_id'] ?? 0;

    $db = new database();
    $conn = $db->getConnection();

    $user = new User($conn);
    $delete = new delete($conn);

    // Fetch the cart item details
    $result = $user->getBookOrder($cart_id, $client_id);

    if ($row = $result->fetch_assoc()) {
        $book_id = $row['book_id'];
        $book_title = $row['title'] ?? 'Unknown Title';
        $author = $row['author'] ?? 'Unknown';
        $price = $row['price'] ?? 0;

        // Insert order
        $user->insertBookOrder($client_id, $book_id);

        // Remove from cart
        $delete->deleteCarts($cart_id, $client_id);

        // Generate PDF
        $mpdf = new Mpdf();
        $html = "
            <h1>Order Confirmation</h1>
            <p><strong>Book Title:</strong> {$book_title}</p>
            <p><strong>Author:</strong> {$author}</p>
            <p><strong>Price:</strong> ₹{$price}</p>
            <p><strong>Status:</strong> Confirmed ✅</p>
        ";
        $mpdf->WriteHTML($html);
        $mpdf->Output("order_{$book_title}_{$cart_id}.pdf", 'D'); // Force download
        exit;

    } else {
        echo "Invalid cart item.";
    }
} else {
    echo "Invalid request.";
}
?>
