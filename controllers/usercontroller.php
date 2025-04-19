<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/usermodel.php';

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
    }

    public function register($username, $password, $userType) {
        // Sanitize inputs
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $userType = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_STRING);

        return $this->userModel->createUser ($username, $password, $userType);
    }

    public function login($username, $password) {
        // Sanitize inputs
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        
        $user = $this->userModel->getUser ($username);
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['user_type'] = $user['user_type'];
            return true;
        }
        return false;
    }

    // Method to validate user credentials
    public function validateUser ($username, $password) {
        return $this->login($username, $password);
    }
}
?>