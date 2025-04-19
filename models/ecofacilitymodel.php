<?php
class EcoFacilityModel {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    public function createFacility($title, $category, $description, $location, $latitude, $longitude, $photo, $status) {
        $stmt = $this->db->prepare("
            INSERT INTO ecofacilities 
            (title, category, description, location, latitude, longitude, photo, status_of_facility) 
            VALUES 
            (:title, :category, :desc, :location, :lat, :lng, :photo, :status)
        ");
        
        $stmt->bindValue(':title', $title, SQLITE3_TEXT);
        $stmt->bindValue(':category', $category, SQLITE3_TEXT);
        $stmt->bindValue(':desc', $description, SQLITE3_TEXT);
        $stmt->bindValue(':location', $location, SQLITE3_TEXT);
        $stmt->bindValue(':lat', $latitude, SQLITE3_TEXT);  // Change to SQLITE3_FLOAT
        $stmt->bindValue(':lng', $longitude, SQLITE3_TEXT); // Change to SQLITE3_FLOAT
        $stmt->bindValue(':photo', $photo, SQLITE3_TEXT);
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
        
        return $stmt->execute();
    }

    // Get facilities
    public function getFacilities($search = '') {
        $searchTerm = "%$search%";
        
        $stmt = $this->db->prepare("
            SELECT * FROM ecofacilities 
            WHERE title LIKE :search OR category LIKE :search
        ");
        $stmt->bindValue(':search', $searchTerm, SQLITE3_TEXT);
        
        $result = $stmt->execute();
        $facilities = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $facilities[] = $row;
        }
        
        return $facilities;
    }

    // Update facility
    public function updateFacility($id, $title, $category, $description, $location, $latitude, $longitude, $photo, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE ecofacilities SET
                    title = :title,
                    category = :category,
                    description = :desc,
                    location = :location,
                    latitude = :lat,
                    longitude = :lng,
                    photo = :photo,
                    status_of_facility = :status
                WHERE id = :id
            ");
            
            // Bind all parameters
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $stmt->bindValue(':title', $title, SQLITE3_TEXT);
            $stmt->bindValue(':category', $category, SQLITE3_TEXT);
            $stmt->bindValue(':desc', $description, SQLITE3_TEXT);
            $stmt->bindValue(':location', $location, SQLITE3_TEXT);
            $stmt->bindValue(':lat', $latitude, SQLITE3_TEXT);  // Change to SQLITE3_FLOAT
            $stmt->bindValue(':lng', $longitude, SQLITE3_TEXT); // Change to SQLITE3_FLOAT
            $stmt->bindValue(':photo', $photo, SQLITE3_TEXT);
            $stmt->bindValue(':status', $status, SQLITE3_TEXT);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("SQL Error: " . $this->db->lastErrorMsg());
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Update failed: " . $e->getMessage());
            return false;
        }
    }

    // Delete facility
    public function deleteFacility($id) {
        $stmt = $this->db->prepare("DELETE FROM ecofacilities WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        return $stmt->execute();
    }

    // Get single facility
    public function getFacilityById($id) {
        $stmt = $this->db->prepare("SELECT * FROM ecofacilities WHERE id = :id");
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        return $result->fetchArray(SQLITE3_ASSOC);
    }

    // Update reviews
    public function updateFacilityReview($id, $review) {
        $stmt = $this->db->prepare("
            UPDATE ecofacilities 
            SET reviews = :review 
            WHERE id = :id
        ");
        
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $stmt->bindValue(':review', $review, SQLITE3_TEXT);
        
        return $stmt->execute();
    }

     // Add a review
     public function addReview($facilityId, $userId, $reviewText, $date) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO reviews 
                (facility_id, user_id, review_text, date) 
                VALUES 
                (:facility_id, :user_id, :review_text, :date)
            ");
            
            $stmt->bindValue(':facility_id', $facilityId, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $userId, SQLITE3_INTEGER);
            $stmt->bindValue(':review_text', $reviewText, SQLITE3_TEXT);
            $stmt->bindValue(':date', $date, SQLITE3_TEXT);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Database error in addReview: " . $e->getMessage());
            return false;
        }
    }

    // Fetch reviews for a specific facility
    public function getReviews($facilityId) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE facility_id = :facility_id");
        $stmt->bindValue(':facility_id', $facilityId, SQLITE3_INTEGER);
        $result = $stmt->execute();

        if ($result === false) {
            echo "Error executing query: " . $this->db->lastErrorMsg();
            return [];
        }

        $reviews = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $reviews[] = $row;
        }

        return $reviews;
    }
    // In EcoFacilityModel.php
        public function getUserById($userId) {
            $stmt = $this->db->prepare("SELECT username FROM users WHERE id = :id");
            $stmt->bindValue(':id', $userId, SQLITE3_INTEGER);
            $result = $stmt->execute();
            return $result->fetchArray(SQLITE3_ASSOC);
}

}
?>
