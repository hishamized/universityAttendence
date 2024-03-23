<?php
require_once '../config.php';

//code for attendance updation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_id = htmlspecialchars(trim($_POST['attendance_id']));
    $attendance = htmlspecialchars(trim($_POST['attendance']));

    $query = "UPDATE attendance SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $attendance, $attendance_id);
    $stmt->execute();
    if($stmt->affected_rows === 0){
        echo json_encode(['status' => 'error', 'message' => 'Failed to update attendance']);
        exit();
    }else{
        echo json_encode(['status' => 'success', 'message' => 'Attendance updated successfully']);
    }
    $stmt->close();
    
    exit();
}

$conn->close();
?>