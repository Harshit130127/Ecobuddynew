<?php
session_start();
require_once __DIR__ . '/../../controllers/ecofacilitycontroller.php';

// Check if the user is logged in and is a Manager
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$ecoFacilityController = new EcoFacilityController();
$search = $_GET['search'] ?? '';
$facilities = $ecoFacilityController->getFacilities($search);

if (isset($_GET['delete'])) {
    $facilityId = (int)$_GET['delete'];
    $ecoFacilityController->deleteFacility($facilityId);
    $_SESSION['message'] = "Facility deleted successfully.";
    header("Location: facilities.php?flash=true"); // Ensure flash message displays
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Eco Buddy</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/facility.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-leaf text-success mr-2"></i>Manage Eco Buddy</h2>
            <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt mr-1"></i>Logout</a>
        </div>

        <?php if (isset($_SESSION['message']) && isset($_GET['flash'])): ?>
            <div class="alert alert-success alert-dismissible fade show" id="flash-message">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="row mb-3">
            <div class="col-md-6">
                <form action="facilities.php" method="GET" class="search-form">
                    <div class="input-group">
                        <input type="text" name="search" placeholder="Search facilities"
                               class="form-control" value="<?= htmlspecialchars($search) ?>">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search mr-1"></i>Search</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6 text-right">
                <a href="add_facilities.php" class="btn btn-success"><i class="fas fa-plus mr-1"></i>Add New Facility</a>
            </div>
        </div>

        <?php if (empty($facilities)): ?>
            <div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>No facilities found.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>No.</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Status</th>
                            <th>Photo</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($facilities as $index => $facility): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($facility['title']) ?></td>
                            <td><?= htmlspecialchars($facility['category']) ?></td>
                            <td><?= htmlspecialchars($facility['description']) ?></td>
                            <td><?= htmlspecialchars($facility['location']) ?></td>
                            <td><?= htmlspecialchars($facility['latitude']) ?></td>
                            <td><?= htmlspecialchars($facility['longitude']) ?></td>
                            <td>
                                <span class="badge <?= $facility['status_of_facility'] === 'active' ? 'badge-success' : 'badge-danger' ?>">
                                    <?= htmlspecialchars($facility['status_of_facility']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($facility['photo'])): ?>
                                    <img src="<?= htmlspecialchars($facility['photo']) ?>"
                                         alt="Facility Photo"
                                         class="facility-thumbnail"
                                         onerror="this.onerror=null;this.src='https://via.placeholder.com/150'">
                                <?php else: ?>
                                    No Photo
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="edit_facility.php?id=<?= (int)$facility['id'] ?>"
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </a>
                                    <a href="?delete=<?= (int)$facility['id'] ?>"
                                       onclick="return confirm('Are you sure?')"
                                       class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            setTimeout(() => $(".alert").alert('close'), 4000);
        });
    </script>
</body>
</html>
