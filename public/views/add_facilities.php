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
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $rawLat = trim($_POST['latitude']); // Original input like "53.4808° N"
    $rawLng = trim($_POST['longitude']); // Original input like "2.2426° W"
    $photo = trim($_POST['photo']);
    $status = trim($_POST['status_of_facility']);

    // Validate inputs
    if (empty($title)) $errors[] = "Title is required";
    if (empty($category)) $errors[] = "Category is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($location)) $errors[] = "Location is required";
    if (empty($rawLat)) $errors[] = "Latitude is required";
    if (empty($rawLng)) $errors[] = "Longitude is required";
    if (empty($photo)) $errors[] = "Photo URL is required";
    if (empty($status)) $errors[] = "Facility status is required";

    // Extract numeric value and direction for latitude
    preg_match('/([-+]?\d+\.?\d*)[°]?\s*([NS]?)/i', $rawLat, $latMatches);
    preg_match('/([-+]?\d+\.?\d*)[°]?\s*([EW]?)/i', $rawLng, $lngMatches);

    $latValue = isset($latMatches[1]) ? (float)$latMatches[1] : null;
    $lngValue = isset($lngMatches[1]) ? (float)$lngMatches[1] : null;

    // Validate latitude (should be between -90 and 90)
    if ($latValue === null || $latValue < -90 || $latValue > 90) {
        $errors[] = "Invalid latitude. Use format like 53.4808° N";
    }

    // Validate longitude (should be between -180 and 180)
    if ($lngValue === null || $lngValue < -180 || $lngValue > 180) {
        $errors[] = "Invalid longitude. Use format like 2.2426° W";
    }

    // Convert to decimal if direction is provided
    if (isset($latMatches[2])) {
        $latValue = strtoupper($latMatches[2]) === 'S' ? -$latValue : $latValue;
    }

    if (isset($lngMatches[2])) {
        $lngValue = strtoupper($lngMatches[2]) === 'W' ? -$lngValue : $lngValue;
    }

    // Store cleaned values
    $cleanLat = $rawLat;
    $cleanLng = $rawLng;

    if (empty($errors)) {
        try {
            if (isset($_POST['update'])) {
                // Update facility
                $result = $ecoFacilityController->updateFacility(
                    $_POST['id'],
                    $title,
                    $category,
                    $description,
                    $location,
                    $cleanLat,
                    $cleanLng,
                    $photo,
                    $status
                );

                if ($result) {
                    $_SESSION['message'] = "Facility updated successfully.";
                    header("Location: facilities.php?flash=true");
                    exit();
                }
            } else {
                // Add new facility
                $result = $ecoFacilityController->addFacility(
                    $title,
                    $category,
                    $description,
                    $location,
                    $cleanLat,
                    $cleanLng,
                    $photo,
                    $status
                );

                if ($result) {
                    $_SESSION['message'] = "Facility added successfully.";
                    header("Location: facilities.php?flash=true");
                    exit();
                }
            }
        } catch (InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($facilityToEdit) ? 'Edit Facility' : 'Add Facility'; ?>
    </title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>
            <?php echo isset($facilityToEdit) ? 'Edit Facility' : 'Add Facility'; ?>
        </h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?php echo htmlspecialchars($error); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="id"
                value="<?php echo isset($facilityToEdit) ? htmlspecialchars($facilityToEdit['id']) : ''; ?>">

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" class="form-control" required
                    value="<?= htmlspecialchars($facilityToEdit['title'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" name="category" class="form-control" required
                    value="<?= htmlspecialchars($facilityToEdit['category'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control"
                    required><?= htmlspecialchars($facilityToEdit['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" name="location" class="form-control" required
                    value="<?= htmlspecialchars($facilityToEdit['location'] ?? '') ?>">
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="latitude">
                            <i class="fas fa-map text-success mr-2"></i>Latitude (e.g., 53.4808° N)
                        </label>
                        <input type="text" name="latitude" class="form-control" required
                            pattern="[-+]?\d+\.?\d*°?\s*[NS]?" title="Enter latitude like 53.4808° N"
                            value="<?= htmlspecialchars($rawLat ?? '') ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="longitude">
                            <i class="fas fa-map text-success mr-2"></i>Longitude (e.g., 2.2426° W)
                        </label>
                        <input type="text" name="longitude" class="form-control" required
                            pattern="[-+]?\d+\.?\d*°?\s*[EW]?" title="Enter longitude like 2.2426° W"
                            value="<?= htmlspecialchars($rawLng ?? '') ?>">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="photo">Photo URL</label>
                <input type="text" name="photo" class="form-control"
                    value="<?= htmlspecialchars($facilityToEdit['photo'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="status_of_facility">Status</label>
                <select name="status_of_facility" class="form-control">
                    <option value="active"
                        <?= (isset($facilityToEdit) && $facilityToEdit['status_of_facility'] === 'active') ? 'selected' : ''; ?>>
                        Active</option>
                    <option value="inactive"
                        <?= (isset($facilityToEdit) && $facilityToEdit['status_of_facility'] === 'inactive') ? 'selected' : ''; ?>>
                        Inactive</option>
                </select>
            </div>

            <button type="submit" name="<?= isset($facilityToEdit) ? 'update' : 'add' ?>" class="btn btn-primary">
                <?= isset($facilityToEdit) ? 'Update Facility' : 'Add Facility' ?>
            </button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
