<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/usermodel.php';

class usercontroller {
    private $userModel;

    public function __construct() {
        $this->userModel = new usermodel();
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
            $_SESSION['user_type'] = $user['user_type'];
            return true;
        }
        return false;
    }
}
?>