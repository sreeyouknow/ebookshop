<?php
include '../client/include/header.php';
require '../Classes/edit-controller.php';

$edit = new edit($conn);

$user_id = $_SESSION['user_id'] ?? null;
$success = '';
$error = '';

// Fetch current user info
$user = $user->agents($user_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $role = trim($_POST['role']);
    $current_password = $_POST['c_password'];

    $result = $edit->updateProfileWithPassword($user_id, $name, $role, $current_password);

    if ($result['status']) {
        echo "<p style='color:green;'>{$result['message']}</p>";
    } else {
        echo "<p style='color:red;'>{$result['message']}</p>";
    }
}

?>
<div id="main-container">
<section>
    <h1>My Profile</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php elseif ($success): ?>
        <p style="color: green;"><?= $success ?></p>
    <?php endif; ?>

    <div>
        <h2>Account Information</h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <form method="post" id="myForm">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user_id) ?>">
            <label>Full Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label>Current Role: <?= htmlspecialchars($user['role']) ?></label>
            <select name="role" required>
                <option value="client" <?= $user['role'] === 'client' ? 'selected' : '' ?>>Client</option>
            </select>

            <label>Current Password (to confirm changes)</label>
            <input type="password" name="c_password" required>

            <button type="submit">Save Profile Changes</button>
        </form>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
