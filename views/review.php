<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userlogin.php"); // Redirect to login if not logged in
    exit();
}

// Get facility title from query string
$facilityTitle = isset($_GET['facility_title']) ? $_GET['facility_title'] : '';

// Include database connection and model if needed
require_once '../controllers/ecofacilitycontroller.php';
$ecoFacilityController = new EcoFacilityController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture and sanitize review text
    $reviewText = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);

    // Update the facility's review in the eco_facilities table using title
    if ($ecoFacilityController->updateFacilityReviewByTitle( $reviewText,$facilityTitle)) {
        $_SESSION['message'] = "Review submitted successfully!";
        header("Location: public.php"); // Redirect back to public facilities page or wherever you want
        exit();
    } else {
        $_SESSION['error'] = "Failed to submit review.";
        echo $_SESSION['error']; // For debugging purposes
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/review.css">
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

        <!-- Display Facility Title -->
        <h5>Submitting Review for Facility: <strong><?php echo htmlspecialchars($facilityTitle); ?></strong></h5>

        <form action="" method="POST">
            <div class="form-group">
                <label for="review">Your Review:</label>
                <textarea class="form-control" id="review" name="review" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Review</button>
        </form>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
