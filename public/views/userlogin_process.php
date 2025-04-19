<?php
session_start();
require_once __DIR__ . '../controllers/usercontroller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from POST request
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    
    // Get facility ID from hidden input (POST)
    $facilityId = isset($_POST['facility_id']) ? intval($_POST['facility_id']) : 0;

    

    // Create an instance of usercontroller
    $userController = new usercontroller();

    // Attempt to log in the user
    if ($userController->login($username, $password)) {
        // Redirect to review page with facility ID after successful login
        header("Location: review.php?facility_id=$facilityId");
        exit();
    } else {
        // Invalid credentials, redirect back with error message
        
        header("Location: userlogin.php?facility_id=$facilityId");
        $_SESSION['error'] = "Invalid username or password.";
        exit();
    }
}
?>