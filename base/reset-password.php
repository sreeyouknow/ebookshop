<?php
include '../includes/header.php';
require '../vendor/autoload.php';
require '../classes/PasswordReset.php';


$reset = new PasswordReset($conn);

if (!isset($_GET['token'])) {
    exit("Invalid token.");
}

$token = htmlspecialchars($_GET['token']);
$user = $reset->validateToken($token);
if (!is_array($user)) {
    exit($user); // Shows token error
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = trim($_POST['password']);
    $confirm = trim($_POST['password_c']);
    $response = $reset->resetPassword($token, $pass, $confirm);
    echo $response === true ? "Password reset successful. <a href='login.php'>Login</a>" : $response;
}
?>
<form method="POST">
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="password_c" placeholder="Confirm Password" required>
    <button type="submit">Reset Password</button>
</form>
