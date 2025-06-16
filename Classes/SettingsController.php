<?php
class SettingsController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function getAdminById($admin_id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getSMTPSettings() {
        $result = $this->conn->query("SELECT * FROM settings LIMIT 1");
        return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function saveSMTPSettings($data) {
        $response = ['success' => '', 'error' => ''];

        if (!hash_equals($_SESSION['csrf_token'], $data['csrf_token'])) {
            $response['error'] = 'Invalid CSRF token.';
            return $response;
        }

        $host = trim($data['smtp_host']);
        $user = trim($data['smtp_user']);
        $pass = trim($data['smtp_pass']);
        $port = (int) $data['smtp_port'];

        if ($host && $user && $pass && $port > 0) {
            $existing = $this->conn->query("SELECT id FROM settings LIMIT 1");

            if ($existing->num_rows > 0) {
                $id = $existing->fetch_assoc()['id'];
                $stmt = $this->conn->prepare("UPDATE settings SET smtp_host=?, smtp_user=?, smtp_pass=?, smtp_port=? WHERE id=?");
                $stmt->bind_param("sssii", $host, $user, $pass, $port, $id);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO settings (smtp_host, smtp_user, smtp_pass, smtp_port) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $host, $user, $pass, $port);
            }

            $stmt->execute();
            $response['success'] = "SMTP settings saved.";
        } else {
            $response['error'] = "Please fill all SMTP fields properly.";
        }

        return $response;
    }
}
?>
