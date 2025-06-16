<?php
include '../includes/header.php';
require '../vendor/autoload.php';
require '../classes/PasswordReset.php';

$reset = new PasswordReset($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $result = $reset->sendResetEmail($email);
    echo $result === true ? "<p>Reset email sent.</p>" : "<p>$result</p>";
}
?>
<form action="" method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form>
