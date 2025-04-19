<?php
require_once __DIR__ . '/../config/config.php'; // Include config file

class usermodel {
    private $db;

    public function __construct() {
        $this->db = getDB(); // Get database connection
    }

    // Get user by username
    public function getUser($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    // Create new user
    public function createUser($username, $password, $userType) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            INSERT INTO users 
            (username, password, user_type) 
            VALUES 
            (:username, :password, :user_type)
        ");
        
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
        $stmt->bindValue(':user_type', $userType, SQLITE3_TEXT);
        
        return $stmt->execute();
    }

    // Update user password
    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password = :password 
            WHERE id = :id
        ");
        
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
        
        return $stmt->execute();
    }

    // Get user by ID
    public function getUserById($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM users 
            WHERE id = :id
        ");
        $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }
}
?>