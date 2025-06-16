<?php 
include '../includes/header.php'; 


if($_SERVER['REQUEST_METHOD'] == 'POST'){
 $name = trim($_POST['name']);
 $email = trim($_POST['email']);
 $password = trim($_POST['password']);
 $role     = trim($_POST['role']);

    if(empty($name) || empty($email) || empty($password) || empty($role)){
        echo "all fields required";
        exit;
    }
    if($user->emailExists($email)){
        echo "Email already exists";
        exit;
    }
    if(!$user->emailValidate($email)){
        echo "Invalid email Format";
        exit;
    }
    if(!$user->passwordValidate($password)){
        echo "Password must be at least 8 characters with 1 capital letter, 1 number, and 1 symbol.";
        exit;
    }
    if(!empty($name) && !empty($email) && !empty($password) && !empty($role)){
        $registered = $user->register($name, $email, $password, $role);
        header("Location:login.php");
        exit;
    }
    exit;
}
?>
<style>
    #container{
        margin:6.3% auto;
    }
</style>
<h2 style="text-align:center;">Register here</h2>
<form action="register.php" method="POST">
    <input type="text" name="name" placeholder="Enter your Full name" required>
    <input type="email" name="email" placeholder="Enter your Email" required>
    <input type="password" name="password" placeholder="Enter your password" required>
    
    <select name="role" id="role" required>
        <option value="client">Client</option>
    </select>

    <button type="submit">Register</button>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</form>

<?php include '../includes/footer.php'; ?>
