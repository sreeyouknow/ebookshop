<?php
include '../client/include/header.php';
require '../Classes/edit-controller.php';

$db = new database();
$conn = $db->getConnection();

$user = new User($conn);

$edit = new edit($conn);

$user_id = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match.";
    } else {
        $result = $edit->changePassword($user_id, $current_password, $new_password);
        if (!empty($result['error'])) {
            $error = $result['error'];
        } else {
            $success = $result['success'];
        }
    }
}?>

<div id="main-container">
<section>
    <?php if ($success): ?>
    <p style="color:green;"><?= $success ?></p>
<?php elseif ($error): ?>
    <p style="color:red;"><?= $error ?></p>
<?php endif; ?>

        <h2>Change Password</h2>
        <form method="post" id="myForm">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user_id) ?>">
            <label>Current Password</label>
            <input type="password" name="current_password" required>

            <label>New Password</label>
            <input type="password" name="new_password" required>
            <small>Must be at least 6 characters.</small><br>

            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" name="change_password">Change Password</button>
            <a href="../base/forgot-password.php">Forgot Password?</a>
        </form>
</section>

<?php include '../includes/footer.php'; ?>