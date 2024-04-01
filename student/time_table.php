<?php
session_start();

// Check if student is not logged in, redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}


if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success" role="alert">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['getTimeTable'])) {
    $day = $_POST['day'];
    $query = "SELECT 
    time_table.*,
    GROUP_CONCAT(subject_staff.staff_id) AS staff_ids,
    GROUP_CONCAT(staff.username) AS usernames,
    GROUP_CONCAT(staff.full_name) AS full_names
FROM 
    time_table 
JOIN 
    subject_staff ON time_table.subject_id = subject_staff.subject_id
JOIN 
    staff ON subject_staff.staff_id = staff.id
WHERE 
    time_table.day = '$day'
GROUP BY
    time_table.id;
";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $timeTable = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $_SESSION['error'] = "No time table found for " . $day;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/1bc2765d38.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Attendance</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="student_dashboard.php">student Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="student_logout.php">Logout student ( <?php echo $_SESSION['student_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->
    <div class="container mt-5">
        <form action="time_table.php" method="POST">
            <div class="mb-3">
                <label for="day" class="form-label">Select Day</label>
                <select class="form-select" id="day" name="day">
                    <option value="" selected>Select Day</option>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <button type="submit" name="getTimeTable" class="btn btn-primary">Submit</button>
        </form>
        <h1 class="text-center mb-4">Time Table</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Subject</th>
                    <th>Staff Members</th>
                </tr>
            </thead>
            <tbody>
                <?php
               if (isset($timeTable)) {
                foreach ($timeTable as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['day'] . "</td>";
                    echo "<td>" . date('h:i A', strtotime($row['start_time'])) . "</td>";
                    echo "<td>" . date('h:i A', strtotime($row['end_time'])) . "</td>";
                    echo "<td>" . $row['subject_name'] . "</td>";
            
                    $fullNames = explode(',', $row['full_names']);
        
                    echo "<td>";
                    foreach ($fullNames as $fullName) {
                        echo $fullName . "<br>";
                    }
                    echo "</td>";
            
                    echo "</tr>";
                }
            }
            
                ?>
            </tbody>
        </table>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>