<?php
session_start();
require_once '../controllers/ecofacilitycontroller.php';

// Check if the user is logged in and is a Manager
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'manager') {
    header("Location: login.php"); // Redirect to login if not a manager
    exit(); // Ensure no further code is executed after redirection
}

$ecoFacilityController = new EcoFacilityController();
$search = isset($_GET['search']) ? $_GET['search'] : '';
$facilities = $ecoFacilityController->getFacilities($search);

// Handle delete operation
if (isset($_GET['delete'])) {
    $facilityId = $_GET['delete'];
    $ecoFacilityController->deleteFacility($facilityId);
    $_SESSION['message'] = "Facility deleted successfully."; // Set success message
    header("Location: facilities.php"); // Redirect after deletion
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Eco Facilities</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/facility.css">
    <style>
        /* Custom styles for the table */
        table {
            width: 100%;
            border-collapse: collapse; /* Ensures borders are collapsed */
        }
        th, td {
            border: 1px solid #ddd; /* Adds border to cells */
            padding: 8px; /* Adds padding for better spacing */
            text-align: left; /* Align text to the left */
        }
        th {
            background-color: #f2f2f2; /* Light gray background for header */
        }
        tr:nth-child(even) {
            background-color: #f9f9f9; /* Alternating row colors */
        }
        tr:hover {
            background-color: #f1f1f1; /* Highlight row on hover */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Manage Eco Facilities</h2>

        <?php if (isset($_SESSION['message']) && isset($_GET['flash'])): ?>
        <div class="alert alert-success" id="flash-message">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); // Clear message after displaying ?>
        </div>

        <script>
            // Hide the message after 4 seconds
            setTimeout(function() {
                document.getElementById('flash-message').style.display = 'none';
            }, 4000); // 4000 milliseconds = 4 seconds
        </script>
    <?php endif; ?>


        <form action="facilities.php" method="GET" class="mb-3">
            <input type="text" name="search" placeholder="Search facilities" class="form-control" value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit" class="btn btn-primary mt-2">Search</button>
        </form>
        
        <a href="add_facilities.php" class="btn btn-success mb-3">Add New Facility</a>

        <?php if (empty($facilities)): ?>
            <div class="alert alert-warning">No facilities found.</div>
        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No.</th> <!-- Sequential Number Column -->
                        <th>Title</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Latitude</th> <!-- Latitude Column -->
                        <th>Longitude</th> <!-- Longitude Column -->
                        <th>Status</th> <!-- Status Column -->
                        <th>Photo</th>
                        <th>Actions</th> <!-- Actions Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facilities as $index => $facility): ?>
                    <tr>
                        <td><?php echo $index + 1; // Display sequential number ?></td> <!-- Sequential Number -->
                        <td><?php echo htmlspecialchars($facility['title']); ?></td>
                        <td><?php echo htmlspecialchars($facility['category']); ?></td>
                        <td><?php echo htmlspecialchars($facility['description']); ?></td>
                        <td><?php echo htmlspecialchars($facility['location']); ?></td>

                        <!-- Display Latitude and Longitude as strings -->
                        <td><?php echo htmlspecialchars($facility['latitude']); ?></td>
                        <td><?php echo htmlspecialchars($facility['longitude']); ?></td>

                        <!-- Display Status -->
                        <td><?php echo htmlspecialchars($facility['status_of_facility']); ?></td>

                        <!-- Photo Display -->
                        <td><img src="<?php echo htmlspecialchars($facility['photo']); ?>" alt="Facility Photo" style="width:100px;height:100px;"></td>

                        <!-- Actions -->
                        <td>
                            <!-- Pass the facility id to the edit page -->
                            <a href="edit_facility.php?id=<?php echo htmlspecialchars($facility['id']); ?>" class="btn btn-warning">Edit</a>

                            <!-- Delete button -->
                            <a href="?delete=<?php echo htmlspecialchars($facility['id']); ?>" onclick="return confirm('Are you sure you want to delete this facility?');" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Include Bootstrap JS (optional) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script> 
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body> 
</html>