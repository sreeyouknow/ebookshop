<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../base/login.php");
    exit;
}

require '../Classes/database-control.php'; 
require '../Classes/user-control.php';
require '../Classes/search-pagination-controller.php';

$db = new database();
$conn = $db->getConnection();

$user = new User($conn);

$pagesearch = new searchPagination($conn);

$c_user_role = $_SESSION['role'] ?? null;
$c_user_name = $_SESSION['name'] ?? 'Guest';
$c_user_id   = $_SESSION['id'] ?? null;

$count_cart = $conn->prepare("SELECT COUNT(book_id) AS total_book FROM cart");
$count_cart->execute();
$result = $count_cart->get_result();
$row =$result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Buy Website</title>
    <link rel="stylesheet" href="include/c-style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
    <div id="header">
        <div>
            <a href="dashboard.php"><h2>Books here</h2></a>
        </div>
        <div>
            <a href="javascript:void(0)" class="sidebar-link" data-page="wishlist.php"><span id = "wishlist"> ♡ </span></a>
            <a href="javascript:void(0)" class="sidebar-link" data-page="cart.php">
                <strong>🛒</strong>
                <span id="count"><?php echo $row['total_book']; ?></span>
            </a>
            <a href="javascript:void(0)" class="sidebar-link" data-page="messages.php"><strong> 🖂 </strong> </a>
            
            <span><a href="../base/logout.php">| Logout</a></span>
        </div>
    </div>


</header>
<?php include '../client/include/sidebar.php';?>