<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/ecofacilitymodel.php';

class EcoFacilityController {
    private $ecoFacilityModel;

    public function __construct() {
        $this->ecoFacilityModel = new EcoFacilityModel();
    }

    // Add new facility
    public function addFacility($title, $category, $description, $location, $latitude, $longitude, $photo, $status) {
        $title = filter_var($title, FILTER_SANITIZE_STRING);
        $category = filter_var($category, FILTER_SANITIZE_STRING);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        $location = filter_var($location, FILTER_SANITIZE_STRING);
        $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);
        $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);
        $photo = filter_var($photo, FILTER_SANITIZE_URL);
        $status = filter_var($status, FILTER_SANITIZE_STRING);

        return $this->ecoFacilityModel->createFacility(
            $title, $category, $description, $location, 
            $latitude, $longitude, $photo, $status
        );
    }

    // Update facility
    public function updateFacility($id, $title, $category, $description, $location, $latitude, $longitude, $photo, $status) {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        $title = filter_var($title, FILTER_SANITIZE_STRING);
        $category = filter_var($category, FILTER_SANITIZE_STRING);
        $description = filter_var($description, FILTER_SANITIZE_STRING);
        $location = filter_var($location, FILTER_SANITIZE_STRING);
        $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);
        $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);
        $photo = filter_var($photo, FILTER_SANITIZE_URL);
        $status = filter_var($status, FILTER_SANITIZE_STRING);

        if (!$id) {
            return false;
        }

        return $this->ecoFacilityModel->updateFacility(
            $id, $title, $category, $description, $location, 
            $latitude, $longitude, $photo, $status
        );
    }

    // Delete facility
    public function deleteFacility($id) {
        $cleanId = filter_var($id, FILTER_VALIDATE_INT);
        return $cleanId ? $this->ecoFacilityModel->deleteFacility($cleanId) : false;
    }

    // Get facilities with optional filter
    public function getFacilities($filter = '') {
        return $this->ecoFacilityModel->getFacilities(
            filter_var($filter, FILTER_SANITIZE_STRING)
        );
    }

    // Get single facility
    public function getFacilityById($id) {
        $cleanId = filter_var($id, FILTER_VALIDATE_INT);
        return $cleanId ? $this->ecoFacilityModel->getFacilityById($cleanId) : null;
    }

    // Update reviews
    public function updateFacilityReview($facilityId, $reviewText) {
        $cleanId = filter_var($facilityId, FILTER_VALIDATE_INT);
        $cleanReview = filter_var($reviewText, FILTER_SANITIZE_STRING);
        
        return ($cleanId && $cleanReview) 
            ? $this->ecoFacilityModel->updateFacilityReview($cleanId, $cleanReview) 
            : false;
    }

    // Get reviews
    public function getReviews($id) {
        return $this->ecoFacilityModel->getReviews($id);
    }

    // Add review
    
    public function addReview($facilityId, $userId, $reviewText, $date) {
        $cleanFacilityId = filter_var($facilityId, FILTER_VALIDATE_INT);
        $cleanUserId = filter_var($userId, FILTER_VALIDATE_INT);
        $cleanReviewText = filter_var($reviewText, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $cleanDate = filter_var($date, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        if (!$cleanFacilityId || !$cleanUserId || !$cleanReviewText || !$cleanDate) {
            error_log("Validation failed in addReview");
            return false;
        }
    
        return $this->ecoFacilityModel->addReview(
            $cleanFacilityId,
            $cleanUserId,  // Now using user ID instead of username
            $cleanReviewText,
            $cleanDate
        );
    }

        // In EcoFacilityController.php
        public function getUserById($userId) {
            return $this->ecoFacilityModel->getUserById($userId);
        }

// Remove the old getUserById() method from the controller


}
?>