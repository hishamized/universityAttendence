<?php

date_default_timezone_set('Asia/Kolkata');

define( 'ROOT_PATH', dirname(__FILE__) );
define( 'BASE_URL', 'http://localhost/kashmirUniversity' );
$host = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "attendance"; 


$conn = mysqli_connect($host, $username, $password, $database);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>
