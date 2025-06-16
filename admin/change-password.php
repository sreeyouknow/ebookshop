<?php
session_start();
require '../Classes/database-control.php';
require '../Classes/edit-controller.php';

$db = new database();
$conn = $db->getConnection();
$edit = new edit($conn);

$c_user_role = $_SESSION['role'] ?? null;
$c_user_name = $_SESSION['name'] ?? 'Guest';
$c_user_id   = $_SESSION['id'] ?? null;
$admin_id    = $_SESSION['user_id'] ?? null;

// Handle AJAX password change request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    header('Content-Type: application/json');

    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';

    if (!$admin_id || !$old || !$new) {
        echo json_encode(['error' => 'Missing required data.']);
        exit;
    }

    $result = $edit->changePassword($admin_id, $old, $new);
    echo json_encode($result);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Buy Website</title>
  <link rel="stylesheet" href="include/ad-style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<header>
  <div id="header">
    <div>
      <a href="dashboard.php"><h2>Books here</h2></a>
    </div>
    <div>
      <a href="profile.php"><strong> 👤 </strong></a>
      <span><a href="../base/logout.php">| Logout</a></span>
    </div>
  </div>
</header>

<?php include '../admin/include/sidebar.php'; ?>

<div id="main-container">
  <section>
    <h3>Change Password</h3>
    <div id="passwordMessage"></div>
    <form method="POST" id="changePasswordForm">
      <input type="password" name="old_password" placeholder="Old Password" required>
      <input type="password" name="new_password" placeholder="New Password" required>
      <button type="submit">Change</button>
      <input type="hidden" name="change_password" value="1">
    </form>
    <p><a href="../base/logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a></p>
  <br><br><br><br>
  </section>

<script>
$(function () {
  $('#changePasswordForm').on('submit', function (e) {
    e.preventDefault();

    $.ajax({
      url: "", // Same page
      method: "POST",
      data: $(this).serialize(),
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $('#passwordMessage').html("<p style='color:green;'>" + response.success + "</p>");
          $("#changePasswordForm")[0].reset();
        } else if (response.error) {
          $('#passwordMessage').html("<p style='color:red;'>" + response.error + "</p>");
        }
      },
      error: function (xhr, status, error) {
        $('#passwordMessage').html(
          "<p style='color:red;'>AJAX Error: " + xhr.responseText + "</p>"
        );
        console.error("AJAX failed:", xhr, status, error);
      }
    });
  });
});
</script>

<?php include '../includes/footer.php'; ?>

