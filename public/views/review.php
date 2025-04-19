<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php"); // Redirect to login if not logged in
    exit();
}

// Get facility ID from query string
$facilityId = isset($_GET['facility_id']) ? intval($_GET['facility_id']) : 0;

// Include database connection and model if needed
require_once '../controllers/ecofacilitycontroller.php';
$ecoFacilityController = new EcoFacilityController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure facility ID is set and valid
    if ($facilityId <= 0) {
        die("Invalid facility ID.");
    }
    
    // Capture and sanitize review text
    $reviewText = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);

    // Update the facility's review in the eco_facilities table
    if ($ecoFacilityController->updateFacilityReview($facilityId, $reviewText)) {
        // Redirect with a success message in the query string
        header("Location: public.php?message=success");
        exit();
    } else {
        $_SESSION['error'] = "Failed to submit review.";
        echo $_SESSION['error']; // For debugging purposes
    }
}
?>

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/review.css"> 
</head>
<body>
    <!-- Background Elements -->
    <div class="background-image"></div>
    <div class="background-overlay"></div>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle mr-2"></i>
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h5>
            <i class="fas fa-comment-dots text-success mr-2"></i>
            Submitting Review for Facility
        </h5>

        <form action="" method="POST">
            <div class="form-group">
                <label for="review">
                    <i class="fas fa-edit text-success mr-2"></i>
                    Your Review
                </label>
                <textarea 
                    class="form-control" 
                    id="review" 
                    name="review" 
                    rows="5" 
                    maxlength="500" 
                    required
                    placeholder="Share your thoughts about this eco facility..."
                ></textarea>
                <div class="char-count" id="char-count">0 / 500</div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane mr-2"></i>
                Submit Review
            </button>
        </form>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https:// code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Character count functionality
        const textarea = document.getElementById('review');
        const charCount = document.getElementById('char-count');

        textarea.addEventListener('input', function() {
            const length = textarea.value.length;
            charCount.textContent = `${length} / 500`;
        });
    </script>
</body>
</html>