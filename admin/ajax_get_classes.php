<?php

include '../config.php';


$query = "SELECT id, name FROM classes;";
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


mysqli_close($conn);
?>
