<?php

include '../config.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $data = json_decode(file_get_contents("php://input"));

    
    $fromClassId = $data->from_class_id;
    $toClassId = $data->to_class_id;

    
    mysqli_begin_transaction($conn);

    
    $queryUpdateStudents = "UPDATE students SET class_id = $toClassId WHERE class_id = $fromClassId";
    $resultUpdateStudents = mysqli_query($conn, $queryUpdateStudents);

    
    $queryUpdatePrevClassStatus = "UPDATE classes SET status = 'deprecated' WHERE id = $fromClassId";
    $resultUpdatePrevClassStatus = mysqli_query($conn, $queryUpdatePrevClassStatus);

    
    $queryUpdateNextClassStatus = "UPDATE classes SET status = 'active' WHERE id = $toClassId";
    $resultUpdateNextClassStatus = mysqli_query($conn, $queryUpdateNextClassStatus);

    
    if ($resultUpdateStudents && $resultUpdatePrevClassStatus && $resultUpdateNextClassStatus) {
        
        mysqli_commit($conn);
        echo "Students promoted successfully and previous class status updated.";
    } else {
        
        mysqli_rollback($conn);
        echo "Error promoting students: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request method.";
}


mysqli_close($conn);
?>
