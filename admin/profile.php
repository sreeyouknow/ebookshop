<?php
include '../admin/include/header.php';
require '../Classes/edit-controller.php';

$edit = new edit($conn);

$admin_id = $_SESSION['user_id'];
$admin = $agent = $user->agents($admin_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    

    if ($email && !empty($name) && $id === $admin_id) {
        $edit->editUser($name, $email, $admin_id);
    }
}
?>

<div id="main-container">
<section>
    <h2>Admin Profile & Settings</h2>

    <!-- Feedback message -->
    <div id="messageBox"></div>
    <h3>Update Profile</h3>
    <!-- Profile update form -->
    <form method="POST" id="myForm">
        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        <button type="submit">Update</button>
    </form><br><br><br>
</section>


<?php include '../includes/footer.php'; ?>
