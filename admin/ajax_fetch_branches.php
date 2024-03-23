<?php
require_once('../config.php');

if (isset($_GET['course_id']) && $_GET['action'] === 'fetch_branches') {
    // Sanitize and validate the input
    $course_id = $_GET['course_id'];

    // Prepare and execute the SQL query to fetch branches based on the selected course_id
    $query = "SELECT id, branch_name FROM branches WHERE course_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $course_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    // Bind the result variables
    mysqli_stmt_bind_result($stmt, $branch_id, $branch_name);

    // Fetch branches into an array
    $branches = array();
    while (mysqli_stmt_fetch($stmt)) {
        $branches[] = array(
            'id' => $branch_id,
            'branch_name' => $branch_name
        );
    }

    // Close the statement
    mysqli_stmt_close($stmt);

    // Return the branches as a JSON response
    header('Content-Type: application/json');
    echo json_encode($branches);
} 


$conn->close();

?>