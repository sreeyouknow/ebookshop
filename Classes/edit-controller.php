<?php
class edit{
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    //with password and without role edit user 
    public function editUsers($id, $name, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $passwordHash, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    //$editUsers = $edit->editUsers($id, $name, $email, $password);
    
    //without password and role edit user
    public function editUser($name, $email, $id) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    //$editUser = $edit->editUser($name, $email, $id);

    //update books
    public function editBooks($title, $author, $description, $price, $uploaded_by, $id){
        $stmt = $this->conn->prepare("UPDATE books SET title=?, author=?, description=?, price=?, uploaded_by=? WHERE id=?");
        $stmt->bind_param("sssiii", $title, $author, $description, $price, $uploaded_by, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    //$editBooks = $edit->editBooks($title, $author, $description, $uploaded_by, $id);
    
    //with password and without role edit user 
    public function editUsersr($id, $name, $email, $password, $role) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $passwordHash, $role, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    //$editUsers = $edit->editUsersr($id, $name, $email, $password, $role);
    
    //without password and role edit user
    public function editUserr($id, $name, $email, $password, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }
    //$editUserr = $edit->editUserr($id, $name, $email, $password, $role);

    //change password for admin and agent
    public function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function changePassword($user_id, $old, $new) {
        $response = ['success' => '', 'error' => ''];

        if (strlen($new) < 6) {
            $response['error'] = "Password must be at least 6 characters.";
            return $response;
        }

        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if (!$data || !password_verify($old, $data['password'])) {
            $response['error'] = "Incorrect old password.";
            return $response;
        }

        $hashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hashed, $user_id);
        $stmt->execute();

        $response['success'] = "Password changed successfully.";
        return $response;
    }


    
    //end conversation
    public function endConversation($agent_id, $client_to_end){
        $stmt = $this->conn->prepare("UPDATE messages SET conversation_status = 'ended' WHERE agent_id = ? AND client_id = ?");
        $stmt->bind_param("ii", $agent_id, $client_to_end);
        return $stmt->execute();
    }

    //use current password then chagne user data
    public function updateProfileWithPassword($user_id, $name, $role, $current_password) {
        $stmt = $this->conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return ['status' => false, 'message' => 'User not found.'];
        }

        // Verify password
        if (!password_verify($current_password, $user['password'])) {
            return ['status' => false, 'message' => 'Incorrect current password.'];
        }

        // Update name and role
        $update = $this->conn->prepare("UPDATE users SET name = ?, role = ? WHERE id = ?");
        $update->bind_param("ssi", $name, $role, $user_id);
        if ($update->execute()) {
            // Update session too
            $_SESSION['name'] = $name;
            $_SESSION['role'] = $role;
            return ['status' => true, 'message' => 'Profile updated successfully.'];
        } else {
            return ['status' => false, 'message' => 'Failed to update profile.'];
        }
    }
    //$UpCurrentPassword = $edit->updateProfileCurrentPassword();

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
}

}


?>