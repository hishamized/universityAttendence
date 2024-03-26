<?php


// Start session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the student login page
header("Location: ../student/student_login.php");
exit();
?>
