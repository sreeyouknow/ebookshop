<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PasswordReset {
    private $conn;
    private $user;

    public function __construct($db) {
        $this->conn = $db;
        $this->user = new User($db);
    }

    public function sendResetEmail($email) {
        $user = $this->user->findByEmail($email);
        if (!$user) return "Email not found.";

        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $this->user->updateResetToken($email, $token, $expiry);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'sreeranganathan03@gmail.com';
            $mail->Password = 'dwvg qoje lsww dpdq'; // Use env var in real apps
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('sreeranganathan03@gmail.com', 'Book Can');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $reset_link = "http://localhost/book-can/base/reset-password.php?token=$token";
            $mail->Body = "Click <a href='$reset_link'>here</a> to reset your password.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function validateToken($token) {
        $user = $this->user->emailExists($token);
        if (!$user) return "Invalid token.";
        if (strtotime($user['reset_token_expiry']) < time()) return "Token expired.";
        return $user;
    }

    public function resetPassword($token, $newPassword, $confirmPassword) {
        if ($newPassword !== $confirmPassword) return "Passwords do not match.";
        if (!preg_match("/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/", $newPassword)) {
            return "Password must be 8+ characters with capital, number, symbol.";
        }
        return $this->user->updatePassword($token, $newPassword);
    }
}
?>
