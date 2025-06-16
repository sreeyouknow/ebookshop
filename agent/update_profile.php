<?php
include '../agent/include/header.php';
require '../Classes/edit-controller.php';

$edit = new edit($conn);

// --- Get agent_id from session
$agent_id = $_SESSION['user_id'];
$admin = $agent = $user->agents($agent_id);

// Handle AJAX POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $id = intval($_POST['id']);

    if ($email && !empty($name) && $id === $agent_id) {
        $edit->editUser($name, $email, $agent_id);
    }
}
?>

<div id="main-container">
    <section>
        <h2>Agent Profile</h2>
        <form id="myForm" method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($agent_id) ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($agent['name']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($agent['email']) ?>" required>
            <button type="submit">Update</button>
        </form>

        <div id="messageBox"></div>
        <script src="../includes/update-profile.js"></script>
        <br><br><br><br><br><br>
    </section>
<?php include '../includes/footer.php'; ?>
