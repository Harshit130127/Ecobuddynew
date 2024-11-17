<?php
require_once '../models/ecofacilitymodel.php';

class EcoFacilityController {
    private $ecoFacilityModel;

    public function __construct() {
        $this->ecoFacilityModel = new ecofacilitymodel();
    }

    public function addFacility($title, $category, $description, $location, $latitude, $longitude,  $photo,$status_of_facility) {
        // Sanitize inputs
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_STRING);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_STRING);
        $photo = filter_input(INPUT_POST, 'photo', FILTER_SANITIZE_STRING); // Assuming photo is a string path
        $status_of_facility = filter_input(INPUT_POST, 'status_of_facility', FILTER_SANITIZE_STRING);

        return $this->ecoFacilityModel->createFacility($title, $category, $description, $location, $latitude, $longitude, $photo,$status_of_facility) ;
        }

        public function updateFacility($id, $title, $category, $description, $location, $latitude, $longitude, $photo,$status_of_facility) {
        // Sanitize inputs
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
        $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
        $location = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_STRING);
        $latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_STRING);
        $longitude = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_STRING);
        
        $photo = filter_input(INPUT_POST, 'photo', FILTER_SANITIZE_STRING);
        $status_of_facility  = filter_input(INPUT_POST, 'status_of_facility', FILTER_SANITIZE_STRING);


        return $this->ecoFacilityModel->updateFacility($id, $title, $category, $description, $location, $latitude, $longitude,  $photo,$status_of_facility);
    }
    public function deleteFacility($id) {
        // Sanitize input
        $id = filter_var($id, FILTER_VALIDATE_INT); // Ensure it's an integer

        if ($id === false) {
            return false; // Invalid ID
        }

        return $this->ecoFacilityModel->deleteFacility($id); // Call the model's delete method
    }
    
    public function getFacilities($search = '') {
        global $mysqli;
    
        if ($search) {
            $stmt = $mysqli->prepare("SELECT * FROM ecofacilities WHERE title LIKE ? OR category LIKE ?");
            $searchParam = "%$search%";
            $stmt->bind_param("ss", $searchParam, $searchParam);
        } else {
            $stmt = $mysqli->prepare("SELECT * FROM ecofacilities");
        }
    
        if (!$stmt->execute()) {
            return [];
        }
    
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // Ensure IDs are included
    }

    public function getFacilityById($id) {
        return $this->ecoFacilityModel->getFacilityById($id); // Call the model's method
    }

    public function updateFacilityReview($facilityId, $reviewText) {
        global $mysqli; 
    
        // Prepare and execute the update statement
        $stmt = $mysqli->prepare("UPDATE ecofacilities SET reviews = ? WHERE id = ?");
        
        if ($stmt === false) {
            die("Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error);
        }
    
        // Bind parameters
        if (!$stmt->bind_param("si", $reviewText, $facilityId)) {
            die("Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error);
        }
    
        // Execute the statement
        if (!$stmt->execute()) {
            die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        }
    
        return true; 
    }
    public function updateFacilityReviewByTitle($reviewText,$facilityTitle) {
        return $this->ecoFacilityModel->updateFacilityReviewByTitle($reviewText,$facilityTitle);
    }
}
?>