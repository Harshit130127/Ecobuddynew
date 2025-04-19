<?php
// Enable full error reporting
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Critical facility check at top
$facilityId = $_SESSION['facilityId'] ?? null;
if (!$facilityId) {
    $_SESSION['error'] = "No facility selected. Please select a facility first.";
    header("Location: public.php");
    exit();
}

require_once __DIR__ . '/../../controllers/ecofacilitycontroller.php';

// Debug session state
error_log("Session data at start: " . print_r($_SESSION, true));

// Check if user is logged in
// Check if user is properly logged in with ID
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    $_SESSION['error'] = "Please login first";
    header("Location: userlogin.php");
    exit();
}

// Instantiate the EcoFacilityController
$ecoFacilityController = new EcoFacilityController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("Raw POST data: " . print_r($_POST, true));

    // Capture and sanitize review text
    $reviewText = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $userId = $_SESSION['user_id'] ?? null;
    $date = date('Y-m-d H:i:s');
    $facilityId = $_SESSION['facilityId'] ?? null;

    error_log("Processed review data - Facility: $facilityId, UserID: $userId, Date: $date");

    // Validate critical data
    if (empty($facilityId)) {
        error_log("Critical error: facilityId not set in session");
        $_SESSION['error'] = "Facility not selected properly. Please try again.";
        header("Location: review.php");
        exit();
    }

    if (!$userId) {
        error_log("Critical error: user_id not set in session");
        $_SESSION['error'] = "Authentication error. Please login again.";
        header("Location: userlogin.php");
        exit();
    }

    if (empty($reviewText)) {
        error_log("Validation error: Empty review text");
        $_SESSION['error'] = "Review text cannot be empty";
        header("Location: review.php");
        exit();
    }

    // Store the review in the database
    try {
        error_log("Attempting to add review to database...");
        $success = $ecoFacilityController->addReview(
            $facilityId, 
            $userId,
            $reviewText,
            $date
        );
        
        if ($success) {
            error_log("Review submission successful");
            $_SESSION['message'] = "Review submitted successfully!";
        } else {
            error_log("Database operation failed without exception");
            $_SESSION['error'] = "Failed to submit review. Please try again later.";
        }
    } catch (Exception $e) {
        error_log("Database exception: " . $e->getMessage());
        $_SESSION['error'] = "A system error occurred. Our team has been notified.";
    }

    header("Location: review.php");
    exit();
}

// Fetch existing reviews
error_log("Fetching reviews for facility ID: " . ($_SESSION['facilityId'] ?? 'NOT_SET'));
$reviews = [];
try {
    $reviews = $ecoFacilityController->getReviews($_SESSION['facilityId'] ?? 0);
    error_log("Retrieved " . count($reviews) . " reviews");
} catch (Exception $e) {
    error_log("Error fetching reviews: " . $e->getMessage());
    $_SESSION['error'] = "Error loading existing reviews";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/review.css">
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h5>Submit Your Review</h5>
        <form action="" method="POST">
            <div class="form-group">
                <label for="review">Your Review</label>
                <textarea class="form-control" id="review" name="review" rows="5" required></textarea>
                <div class="text-right mt-2">
                    <small><span id="char-count">0</span>/500 characters</small>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>

        <h5 class="mt-4">Existing Reviews</h5>
        <div class="reviews">
            <?php if (empty($reviews)): ?>
                <p>No reviews found for this facility.</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): 
                    // Get username from users table using user_id
                    try {
                        $user = $ecoFacilityController->getUserById($review['user_id']);
                    } catch (Exception $e) {
                        error_log("Error fetching user: " . $e->getMessage());
                        $user = ['username' => 'Unknown User'];
                    }
                ?>
                    <div class="review">
                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                        <em><?= htmlspecialchars($review['date']) ?></em>
                        <p><?= htmlspecialchars($review['review_text']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Enhanced character counter with validation
        const textarea = document.getElementById('review');
        const charCount = document.getElementById('char-count');
        const MAX_CHARS = 500;

        textarea.addEventListener('input', function() {
            const length = textarea.value.length;
            charCount.textContent = `${length}/${MAX_CHARS}`;
            charCount.style.color = length > MAX_CHARS ? 'red' : 'inherit';
        });

        document.querySelector('form').addEventListener('submit', function(e) {
            if (textarea.value.length > MAX_CHARS) {
                e.preventDefault();
                alert(`Review cannot exceed ${MAX_CHARS} characters`);
            }
        });
    </script>
</body>
</html>
