<?php
session_start();
require_once __DIR__ . '/../../controllers/ecofacilitycontroller.php';

// Check if the user is logged in and is a Manager
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$ecoFacilityController = new EcoFacilityController();
$facilityToEdit = null;
$errors = [];

// Check if we're editing an existing facility
if (isset($_GET['id'])) {
    $facilityToEdit = $ecoFacilityController->getFacilityById($_GET['id']);
    if (!$facilityToEdit) {
        $_SESSION['error'] = "Facility not found.";
        header("Location: facilities.php?flash=true"); //Redirect with flash
        exit();
    }
} else {
    // Redirect if no ID is provided
    $_SESSION['error'] = "No facility ID provided.";
    header("Location: facilities.php?flash=true"); //Redirect with flash
    exit();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $photo = trim($_POST['photo']);
    $status = trim($_POST['status_of_facility']);

    // Validate inputs
    if (empty($title)) $errors[] = "Title is required";
    if (empty($category)) $errors[] = "Category is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($location)) $errors[] = "Location is required";
    if (empty($latitude)) $errors[] = "Latitude is required";
    if (empty($longitude)) $errors[] = "Longitude is required";
    if (empty($photo)) $errors[] = "Photo URL is required";
    if (empty($status)) $errors[] = "Facility status is required";

    if (empty($errors)) {
        try {
            // Update facility
            $result = $ecoFacilityController->updateFacility(
                $_POST['id'],
                $title,
                $category,
                $description,
                $location,
                $latitude,
                $longitude,
                $photo,
                $status
            );

            if ($result) {
                $_SESSION['message'] = "Facility updated successfully.";
                header("Location: facilities.php?flash=true");
                exit();
            } else {
                $errors[] = "Failed to update facility. Please try again.";
            }
        } catch (Exception $e) {
            $errors[] = "An error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Eco Facility</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/edit.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">
                    <i class="fas fa-edit text-warning mr-2"></i>
                    Edit Eco Facility
                </h2>
            </div>

            <div class="card-body">
                <!-- Error Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <!-- Edit Facility Form -->
                <form action="edit_facility.php?id=<?php echo htmlspecialchars($facilityToEdit['id']); ?>"
                      method="POST"
                      id="editFacilityForm">

                    <!-- Hidden ID Field -->
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($facilityToEdit['id']); ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="title">
                                    <i class="fas fa-heading text-success mr-2"></i>Title
                                </label>
                                <input type="text"
                                       name="title"
                                       class="form-control <?php echo (!empty($errors) && empty($title)) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo htmlspecialchars($facilityToEdit['title']); ?>"
                                       required
                                       maxlength="200">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="category">
                                    <i class="fas fa-tags text-success mr-2"></i>Category
                                </label>
                                <input type="text"
                                       name="category"
                                       class="form-control <?php echo (!empty($errors) && empty($category)) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo htmlspecialchars($facilityToEdit['category']); ?>"
                                       required
                                       maxlength="300">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">
                            <i class="fas fa-comment-alt text-success mr-2"></i>Description
                        </label>
                        <textarea
                            name="description"
                            class="form-control <?php echo (!empty($errors) && empty($description)) ? 'is-invalid' : ''; ?>"
                            required
                            maxlength="300"
                            rows="3"><?php echo htmlspecialchars($facilityToEdit['description']); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">
                                    <i class="fas fa-map-marker-alt text-success mr-2"></i>Location
                                </label>
                                <input type="text"
                                       name="location"
                                       class="form-control <?php echo (!empty($errors) && empty($location)) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo htmlspecialchars($facilityToEdit['location']); ?>"
                                       required
                                       maxlength="300">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status_of_facility">
                                    <i class="fas fa-check-circle text-success mr-2"></i>Facility Status
                                </label>
                                <input type="text"
                                       name="status_of_facility"
                                       class="form-control <?php echo (!empty($errors) && empty($status)) ? 'is-invalid' : ''; ?>"
                                       value="<?php echo htmlspecialchars($facilityToEdit['status_of_facility']); ?>"
                                       required
                                       maxlength="300">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Latitude Input -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latitude">
                                    <i class="fas fa-map text-success mr-2"></i>Latitude
                                </label>
                                <input type="text" name="latitude" class="form-control"
                                       value="<?php echo htmlspecialchars($facilityToEdit['latitude']); ?>" required>
                            </div>
                        </div>

                        <!-- Longitude Input -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="longitude">
                                    <i class="fas fa-map text-success mr-2"></i>Longitude
                                </label>
                                <input type="text" name="longitude" class="form-control"
                                       value="<?php echo htmlspecialchars($facilityToEdit['longitude']); ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="photo">
                                <i class="fas fa-image text-success mr-2"></i>Photo URL
                            </label>
                            <input type="text" name="photo"
                                   class="form-control <?php echo (!empty($errors) && empty($photo)) ? 'is-invalid' : ''; ?>"
                                   value="<?php echo htmlspecialchars($facilityToEdit['photo']); ?>" required>
                        </div>

                        <button type="submit" name="update" class="btn btn-warning btn-block">
                            <i class="fas fa-save mr-2"></i>Update Facility
                        </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
