<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/ecofacilitymodel.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['error' => 'Login required']));
}

$facilityId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$data = json_decode(file_get_contents('php://input'), true);
$comment = filter_var($data['comment'], FILTER_SANITIZE_STRING);

if (!$facilityId || !$comment) {
    http_response_code(400);
    die(json_encode(['error' => 'Invalid input']));
}

$model = new ecofacilitymodel();
if ($model->updateFacilityReview($facilityId, $comment)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Update failed']);
}
?>
