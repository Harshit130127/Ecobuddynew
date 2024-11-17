
<?php
session_start();
require_once '../controllers/usercontroller.php'; // Adjusted path

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    $userController = new UserController();
    
    // Check if login is successful
    if ($userController->login($username, $password)) {
        header("Location: facilities.php"); // Redirect to facilities page (adjusted path)
        exit(); // Ensure no further code is executed after the redirect
    } else {
        echo "Invalid username or password.";
    }
}
?>
