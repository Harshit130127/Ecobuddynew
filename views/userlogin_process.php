<?php
session_start();
require_once '../controllers/usercontroller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get username and password from POST request
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Create an instance of usercontroller
    $userController = new usercontroller();

    // Attempt to log in the user
    if ($userController->login($username, $password)) {
        // Check if there is a facility_title in the query string
        $facilityTitle = isset($_GET['facility_title']) ? $_GET['facility_title'] : '';

        // Redirect to review page with facility title after successful login
        header("Location: review.php?facility_title=" . urlencode($facilityTitle));
        exit();
    } else {
        // Invalid credentials, redirect back with error message
        $_SESSION['error'] = "Invalid username or password.";
        header("Location: userlogin.php");
        exit();
    }
}
?>