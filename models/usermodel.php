<?php
require_once '../config/config.php';

class usermodel {
    private $conn;

    public function __construct() {
        global $mysqli; // Use the global mysqli connection
        $this->conn = $mysqli; // Assign the global mysqli connection to the class property

        // Check if the db connection is valid
        if ($this->conn === null) {
            die("Database connection not established.");
        }
    }
    
    public function getUser($username) {
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->conn->prepare($query); // Prepare the statement
        
        if ($stmt === false) {
            die("Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error);
        }
    
        $stmt->bind_param("s", $username); // Bind the parameter
        if (!$stmt->execute()) {
            die("Execute failed: (" . $this->conn->errno . ") " . $this->conn->error);
        }
    
        $result = $stmt->get_result(); // Get the result set
        return $result->fetch_assoc(); // Fetch the associative array
    }
}
?>