<?php
session_start();
require_once '../controllers/ecofacilitycontroller.php';

// Check if the user is logged in and is a Manager
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php"); // Redirect to login if not a manager
    exit(); // Ensure no further code is executed after redirection
}

$ecoFacilityController = new EcoFacilityController();
$facilityToEdit = null;

// Handle edit operation
if (isset($_GET['id'])) { // Change 'index' to 'id' for clarity
    // Fetch facility details by ID
    $facilityToEdit = $ecoFacilityController->getFacilityById($_GET['id']);
    
    if (!$facilityToEdit) {
        $_SESSION['message'] = "Facility not found.";
        header("Location: facilities.php"); // Redirect back to facilities page
        exit();
    }
}

// Handle form submissions for updating facilities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Update facility logic here...
    $ecoFacilityController->updateFacility(
        $_POST['id'], // ID of the facility
        $_POST['title'],
        $_POST['category'],
        $_POST['description'],
        $_POST['location'],
        $_POST['latitude'], // Store as string
        $_POST['longitude'], // Store as string
        $_POST['photo'], // Photo URL input
        $_POST['status_of_facility'] // New status field
    );
    $_SESSION['message'] = "Facility updated successfully."; // Set success message
header("Location: facilities.php?flash=true"); // Redirect with a query parameter
exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Facility</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/edit.css">
</head>
<body>
<div class="container">
    <h2>Edit Facility</h2>

    <form action="edit_facility.php?id=<?php echo htmlspecialchars($facilityToEdit['id']); ?>" method="POST" class="mb-3">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($facilityToEdit['id']); ?>"> <!-- Use ID for updating -->
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required maxlength="200" value="<?php echo htmlspecialchars($facilityToEdit['title']); ?>">
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" class="form-control" required maxlength="300" value="<?php echo htmlspecialchars($facilityToEdit['category']); ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required maxlength="300"><?php echo htmlspecialchars($facilityToEdit['description']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" class="form-control" required maxlength="300" value="<?php echo htmlspecialchars($facilityToEdit['location']); ?>">
        </div>

        <div class="form-group">
            <label for="latitude">Latitude </label>
            <input type="text" name="latitude" class="form-control" required value="<?php echo htmlspecialchars($facilityToEdit['latitude']); ?>">
        </div>

        <div class="form-group">
            <label for="longitude">Longitude </label>
            <input type="text" name="longitude" class="form-control" required value="<?php echo htmlspecialchars($facilityToEdit['longitude']); ?>">
        </div>

        <!-- Photo URL Input -->
        <div class="form-group">
            <label for="photo">Photo URL</label>
            <input type="text" name="photo" class="form-control" required value="<?php echo htmlspecialchars($facilityToEdit['photo']); ?>">
        </div>

        <!-- Status of Facility Input -->
        <div class="form-group">
            <label for="status_of_facility">Status of Facility</label>
            <input type="text" name="status_of_facility" class="form-control" required maxlength="300" value="<?php echo htmlspecialchars($facilityToEdit['status_of_facility']); ?>">
        </div>

        <!-- Update button -->
        <button type="submit" name="update" class='btn btn-warning'>Update Facility</button>
    </form>

</div>

<!-- Include Bootstrap JS (optional) -->
<script src=https://code.jquery.com/jquery-3.5.1.slim.min.js></script> 
<script src=https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js></script> 
<script src=https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js></script>

</body> 
</html>