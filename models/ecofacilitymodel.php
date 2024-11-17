<?php
require_once '../config/config.php';

class ecofacilitymodel {
    private $db;

    public function __construct() {
        global $mysqli; // Use the global mysqli connection
        $this->db = $mysqli; // Assign the global mysqli connection to the class property

        // Check if the db connection is valid
        if ($this->db === null) {
            die("Database connection not established.");
        }
    }

    public function createFacility($title, $category, $description, $location, $latitude, $longitude, $photo,$status_of_facility) {
        global $mysqli;
        $stmt = $mysqli->prepare("INSERT INTO ecofacilities (title, category, description, location, latitude, longitude,  photo, status_of_facility) VALUES (?, ?,?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $title, $category, $description, $location, $latitude, $longitude,  $photo,$status_of_facility);
        return $stmt->execute();
    }

    public function getFacilities($search = '') {
    global $mysqli; // Assuming $mysqli is your database connection

    if ($search) {
        // Prepare the statement
        $stmt = $mysqli->prepare("SELECT * FROM ecofacilities WHERE title LIKE ? OR category LIKE ?");
        $searchParam = "%$search%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
    } else {
        // Prepare the statement for retrieving all facilities
        $stmt = $mysqli->prepare("SELECT * FROM ecofacilities");
    }

    // Execute the statement
    if (!$stmt->execute()) {
        return []; // Return an empty array on failure
    }

    // Get the result set from the prepared statement
    $result = $stmt->get_result();
    
    // Fetch all results as an associative array
    return $result->fetch_all(MYSQLI_ASSOC);
}

    public function updateFacility($id, $title, $category, $description, $location, $latitude, $longitude,  $photo,$status_of_facility) {
        global $mysqli;
        $stmt = $mysqli->prepare("UPDATE ecofacilities SET title = ?, category = ?, description = ?, location = ?, latitude = ?, longitude = ?,  photo = ?, status_of_facility=? WHERE id = ?");
        $stmt->bind_param("ssssssssi", $title, $category, $description, $location, $latitude, $longitude, $photo,$status_of_facility, $id);
        return $stmt->execute();
    }

    public function deleteFacility($id) {
        global $mysqli;
        $stmt = $mysqli->prepare("DELETE FROM ecofacilities WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    public function getFacilityById($id) {
        // Prepare statement to fetch facility by ID
        $stmt = $this->db->prepare("SELECT * FROM ecofacilities WHERE id=?");
        
        if ($stmt === false) {
            die("Prepare failed: (" . $this->db->errno . ") " . $this->db->error);
        }

        $id = intval($id); // Ensure id is an integer
        if (!$stmt->bind_param('i', $id)) {
            die("Binding parameters failed: (" . $this->db->errno . ") " . $this->db->error);
        }

        if (!$stmt->execute()) {
            die("Execute failed: (" . $this->db->errno . ") " . $this->db->error);
        }

        // Fetch result
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Return associative array of facility data
        } else {
            return null; // No facility found with that ID
        }
        
        // Close statement
        $stmt->close();
    }
    public function updateFacilityReview($facilityId, $reviewText) {
        global $mysqli; // Assuming you have a mysqli connection set up

        // Prepare and execute the update statement
        $stmt = $mysqli->prepare("UPDATE ecofacilities SET reviews = ? WHERE id = ?");
        $stmt->bind_param("si", $reviewText, $facilityId); // "si" means string and integer

        return $stmt->execute(); // Returns true on success or false on failure
    }

    public function updateFacilityReviewByTitle( $reviewText,$facilityTitle) {
        global $mysqli;
    
        // Prepare and execute the update statement using title to find the correct facility
        $stmt = $mysqli->prepare("UPDATE ecofacilities SET reviews = ? WHERE title = ?");
        
        if ($stmt === false) {
            die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
    
        // Bind parameters and execute statement
        if (!$stmt->bind_param("ss", $reviewText, $title)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
    
        return $stmt->execute(); // Returns true on success or false on failure
    }
}
?>