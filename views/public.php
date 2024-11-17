<?php
session_start();
require_once '../controllers/ecofacilitycontroller.php';
require_once '../config/config.php';

$ecoFacilityController = new EcoFacilityController();

// Check if a search term is provided
$searchTerm = isset($_POST['search']) ? $_POST['search'] : '';
$facilities = $ecoFacilityController->getFacilities($searchTerm);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Eco Facilities</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles/public.css">
</head>
<body>
    <div class="container">
        <h2>Public Eco Facilities</h2>

        <!-- Search Form -->
        <form method="POST" class="mb-3">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search by title or category" value="<?php echo htmlspecialchars($searchTerm); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
        </form>

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
                        <th>Reviews</th> <!-- New Reviews Column -->
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($facilities as $index => $facility): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td> <!-- Sequential Number -->
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

                        <!-- Reviews Section -->
                        <td>
                            <?php 
                            if (!empty($facility['reviews'])) {
                                foreach ($facility['reviews'] as $review) {
                                    echo "<p>" . htmlspecialchars($review) . "</p>";
                                }
                            } else {
                                echo "No reviews";
                            }
                            ?>
                            <!-- Give Review Button -->
                            <a href="userlogin.php?facility_title=<?php echo urlencode($facility['title']); ?>" class="btn btn-info mt-2">Give Review</a>             
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

    <!-- JavaScript function for giving a review -->
   
   

</body> 
</html>
