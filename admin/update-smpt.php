<?php
include '../admin/include/header.php';
require '../Classes/SettingsController.php';

$admin_id = $_SESSION['user_id'];
$settings = new SettingsController($conn);

// Generate CSRF token
$csrf_token = $settings->generateCSRFToken();

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $result = $settings->saveSMTPSettings($_POST);
    $success_msg = $result['success'];
    $error_msg = $result['error'];
}

$admin = $settings->getAdminById($admin_id);
$smtp = $settings->getSMTPSettings();
?>
<div id="main-container">
<section>
    <form method="POST" id="myForm">
        <h3>Email / SMTP Settings</h3>

        <?php if ($success_msg): ?>
            <p style="color:green;"><?= htmlspecialchars($success_msg) ?></p>
        <?php elseif ($error_msg): ?>
            <p style="color:red;"><?= htmlspecialchars($error_msg) ?></p>
        <?php endif; ?>
         <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <input type="text" name="smtp_host" value="<?= $smtp ? htmlspecialchars($smtp['smtp_host']) : '' ?>" placeholder="SMTP Host" required>
        <input type="text" name="smtp_user" value="<?= $smtp ? htmlspecialchars($smtp['smtp_user']) : '' ?>" placeholder="SMTP User" required>
        <input type="password" name="smtp_pass" value="<?= $smtp ? htmlspecialchars($smtp['smtp_pass']) : '' ?>" placeholder="SMTP Password" required>
        <input type="number" name="smtp_port" value="<?= $smtp ? htmlspecialchars($smtp['smtp_port']) : '' ?>" placeholder="SMTP Port" required>
        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
        <button type="submit">Save SMTP</button>
    </form>
    <p><a href="../base/logout.php" onclick="return confirm('Are you sure you want to logout?')">Logout</a></p>
</section>
<?php include '../includes/footer.php'; ?>
