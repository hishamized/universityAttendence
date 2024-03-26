<?php

include '../config.php';


if (isset($_GET['class_id'])) {
    $fromClassId = $_GET['class_id'];

    
    $query = "SELECT id, name FROM classes WHERE course_id = (SELECT course_id FROM classes WHERE id = $fromClassId) 
              AND branch_id = (SELECT branch_id FROM classes WHERE id = $fromClassId) 
              AND batch_id = (SELECT batch_id FROM classes WHERE id = $fromClassId)";

    $result = mysqli_query($conn, $query);

    
    if ($result) {
        $classes = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $classes[] = $row;
        }
        
        echo json_encode($classes);
    } else {
        echo "Error fetching classes: " . mysqli_error($conn);
    }
} else {
    echo "Class ID parameter is missing.";
}


mysqli_close($conn);
?>
