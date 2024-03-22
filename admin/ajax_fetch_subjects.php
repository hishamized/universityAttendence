<?php
require_once('../config.php');

// Check if class_id is set and action is fetch_subjects
if (isset($_GET['class_id']) && $_GET['action'] === "fetch_subjects") {
    // Sanitize input to prevent SQL injection
    $class_id = intval($_GET['class_id']);

    // Fetch the course_id for the given class_id
    $firstQuery = "SELECT course_id FROM classes WHERE id = ?";
    $stmt = mysqli_prepare($conn, $firstQuery);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $firstResult = mysqli_stmt_get_result($stmt);
    
    // Check if the query was successful
    if ($firstResult) {
        $course_id_row = mysqli_fetch_assoc($firstResult);
        $course_id = $course_id_row['course_id'];

        // Fetch subjects based on the course_id
        $query = "SELECT id, name FROM subjects WHERE course_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $course_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Check if the query was successful
        if ($result) {
            $subjects = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $subjects[] = array(
                    'id' => $row['id'],
                    'name' => $row['name']
                    
                );
            }
            
            // Close statement and database connection
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            
            // Send JSON response
            header('Content-Type: application/json');
            echo json_encode($subjects);
        } else {
            // Handle query error
            echo "Error fetching subjects: " . mysqli_error($conn);
        }
    } else {
        // Handle query error
        echo "Error fetching course ID: " . mysqli_error($conn);
    }
}
?>
