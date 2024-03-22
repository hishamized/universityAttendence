<?php
require_once('../config.php');

if(isset($_GET['class_id']) && $_GET['action'] === "fetch_students"){
    $class_id = intval($_GET['class_id']);
    $query = "SELECT students.id, students.full_name, students.university_enroll , students.class_roll_number FROM students WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $class_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($result){
        $students = array();
        while($row = mysqli_fetch_assoc($result)){
            $students[] = array(
                'id' => $row['id'],
                'name' => $row['full_name'],
                'roll_number' => $row['class_roll_number'],
                'university_enroll' => $row['university_enroll']
            );
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        header('Content-Type: application/json');
        echo json_encode($students);
    }else{
        echo "Error fetching students: " . mysqli_error($conn);
    }
}

?>