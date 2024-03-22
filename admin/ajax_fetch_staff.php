<?php
require_once('../config.php');
if( isset($_GET['subject_id']) && $_GET['action'] === "fetch_staff"){
    $subject_id = intval($_GET['subject_id']);
    $query = "SELECT staff.id, staff.full_name FROM staff INNER JOIN subject_staff ON staff.id = subject_staff.staff_id WHERE subject_staff.subject_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $subject_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result){
        $staff = array();
        while($row = mysqli_fetch_assoc($result)){
            $staff[] = array(
                'id' => $row['id'],
                'name' => $row['full_name']
            );
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header('Content-Type: application/json');
        echo json_encode($staff);
    }else{
        echo "Error fetching staff: " . mysqli_error($conn);
    }
}
?>