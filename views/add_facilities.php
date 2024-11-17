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

// Handle form submissions for adding or updating facilities
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an update operation
    if (isset($_POST['update'])) {
        // Update facility logic here...
        $ecoFacilityController->updateFacility(
            $_POST['id'], // Ensure this is set correctly in the hidden input below
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_POST['location'],
            $_POST['latitude'], // This should now capture the full string
            $_POST['longitude'], // This should now capture the full string
            $_POST['photo'],
            $_POST['status_of_facility'] // New status field
        );
        $_SESSION['message'] = "Facility updated successfully."; // Set success message
        header("Location: facilities.php"); // Redirect after update
        exit();

    } elseif (isset($_POST['add'])) {
        // Add new facility
        $ecoFacilityController->addFacility(
            $_POST['title'],
            $_POST['category'],
            $_POST['description'],
            $_POST['location'],
            $_POST['latitude'], // Now stored as VARCHAR
            $_POST['longitude'], // Now stored as VARCHAR
            $_POST['photo'],
            $_POST['status_of_facility'] // New status field
        );
        $_SESSION['message'] = "Facility added successfully."; // Set success message
        header("Location: facilities.php"); // Redirect after add
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($facilityToEdit) ? 'Edit Facility' : 'Add Facility'; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/add.css">
</head>
<body>
<div class="container">
    <h2><?php echo isset($facilityToEdit) ? 'Edit Facility' : 'Add Facility'; ?></h2>

    <!-- Form for adding/updating facilities -->
    <form action="add_facilities.php" method="POST" class="mb-3">
        <input type="hidden" name="id" value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['id']) : ''; ?>">
        
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" required maxlength="200" value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['title']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" class="form-control" required maxlength="300" value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['category']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" required maxlength="300"><?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" class="form-control" required maxlength="300" value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['location']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="latitude">Latitude (e.g., 27.175100° N)</label>
            <input type="text" name="latitude" class="form-control" required value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['latitude']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="longitude">Longitude (e.g., 78.042100° E)</label>
            <input type="text" name="longitude" class="form-control" required value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['longitude']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="photo">Photo URL</label>
            <input type="text" name="photo" class="form-control" required value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['photo']) : ''; ?>">
        </div>

        <!-- Status of Facility Input -->
        <div class="form-group">
            <label for="status_of_facility">Status of Facility</label>
            <input type="text" name="status_of_facility" class="form-control" required maxlength="300" value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['status_of_facility']) : ''; ?>">
        </div>

        <!-- Submit Button -->
        <?php if (isset($facilityToEdit)): ?>
            <button type="submit" name="update" class='btn btn-warning'>Update Facility</button>
        <?php else: ?>
            <button type="submit" name="add" class='btn btn-success'>Add Facility</button>
        <?php endif; ?>
    </form>

</div>

<!-- Include Bootstrap JS (optional) -->
<script src=https://code.jquery.com/jquery-3.5.1.slim.min.js></script> 
<script src=https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js></script> 
<script src=https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js></script>

</body> 
</html>