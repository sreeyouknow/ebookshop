<?php include '../includes/header.php'; 
?>
<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)){
        echo ' Please fill all the fields.';
        exit;
    } 

    if (!$user->emailExists($email)){
        echo " Email not found";
        exit;
    }

    $loginUser = $user->login($email, $password);

    if(!empty($loginUser)){
        $_SESSION['user_id'] = $loginUser['id'];
        $_SESSION['name']    = $loginUser['name'];
        $_SESSION['email']   = $loginUser['email'];

        switch ($loginUser['role']) {
            case 'admin':
            case 1:
                $_SESSION['role'] = 'admin';
                header("Location: ../admin/dashboard.php");
                exit;
            case 'agent':
            case 2:
                $_SESSION['role'] = 'agent';
                header("Location: ../agent/dashboard.php");
                exit;
            case 'client':
            case 3:
                $_SESSION['role'] = 'client';
                header("Location: ../client/dashboard.php");
                exit;
            default:
                echo "Invalid user role.";
                exit;
        }
    } else {
        echo "Invalid email or password.";
        exit;
    }
}
?>
<style>
    #container{
        margin:7% auto;
    }
</style>
    <h2 style="text-align:center;">Login here</h2>
<form action="login.php" method="POST">
    <input type="text" name="email" placeholder="Enter your Email" required>
    <?php
        if (!empty($e_errors)) {
        foreach ($e_errors as $e_error) {
            echo "<small style='color:red;'>$e_error</small>";
        }
    }
    ?>
    <input type="password" name="password" placeholder="Enter your Password" required>
    <?php
        if (!empty($p_errors)) {
        foreach ($p_errors as $p_error) {
            echo "<small style='color:red;'>$p_error</small>";
        }
    }
    ?>
    <button type="submit">Login</button>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
    <p>Your Password <a href="forgot-password.php"> Forgot Here</a></p>
</form>

<?php include '../includes/footer.php'; ?>
