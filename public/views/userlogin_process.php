<?php
session_start();
require_once __DIR__ . '/../../config/config.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$facilityId = $_POST['facility_id'] ?? '';

// Validate user credentials
$db = getDB();
$stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindValue(':username', $username, SQLITE3_TEXT);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['facilityId'] = $facilityId; // Store facility ID in session
    header('Location: review.php');
    exit();
} else {
    $_SESSION['error'] = 'Invalid username or password';
    header('Location: userlogin.php');
    exit();
}
?>