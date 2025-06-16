<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../base/login.php");
    exit;
}

require '../Classes/database-control.php'; 
require '../Classes/user-control.php';
require '../classes/search-pagination-controller.php';

$db = new database();
$conn = $db->getConnection();

$user = new User($conn);

$pagesearch = new searchPagination($conn);

$c_user_role = $_SESSION['role'] ?? null;
$c_user_name = $_SESSION['name'] ?? 'Guest';
$c_user_id   = $_SESSION['id'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Buy Website</title>
    <link rel="stylesheet" href="include/ag-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
    <div id="header">
        <div>
            <a href="dashboard.php"><h2>Books here</h2></a>
        </div>
        <div>
            <a href="javascript:void(0)" class="sidebar-link" data-page="upload_book.php"><strong> 📚 </strong></a>
            <a href="javascript:void(0)" class="sidebar-link" data-page="client-messages.php"><strong> 🖂 </strong> </a>
            <a href="javascript:void(0)" class="sidebar-link" data-page="profile.php"><strong> 👤 </strong></a>
            
            <span><a href="../base/logout.php">| Logout</a></span>
        </div>
    </div>
</header>
<?php include '../agent/include/sidebar.php';?>