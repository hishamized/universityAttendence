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

// Fetch the class_id of the student
$studentId = $_SESSION['student_id'];
$query = "SELECT class_id FROM students WHERE id = $studentId";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$classId = $row['class_id'];

// Fetch the course_id of the class
$query = "SELECT course_id FROM classes WHERE id = $classId";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$courseId = $row['course_id'];

// Fetch the subjects matching the course_id
$query = "SELECT id, name FROM subjects WHERE course_id = $courseId";
$result = mysqli_query($conn, $query);
$subjects = array();
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $subjects[] = $row;
    }
} else {
    echo "<p>No subjects found</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['getAttendance'])) {
    $studentId = intval($_SESSION['student_id']);
    $subjectId = intval($_POST['subject']);

    $query = "SELECT *, subjects.name FROM attendance
     INNER JOIN subjects ON attendance.subject_id = subjects.id
     WHERE student_id = ? AND subject_id = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $studentId, $subjectId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $attendance = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $attendance[] = $row;
            }
        } else {
            $_SESSION['error'] = "No attendance found";
            header("Location: my_attendance.php");
            exit();
        }
    } else {
        // Error handling for failed statement preparation
        $_SESSION['error'] = "Failed to prepare statement";
        header("Location: my_attendance.php");
        exit();
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

    <form action="process_attendance.php" method="POST"></form>
    <div class="container">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <form action="my_attendance.php" method="POST">
                    <div class="form-group">
                        <label class="my-4" for="subject">Select Subject:</label>
                        <select class="form-control" id="subject" name="subject">
                            <option value="">Select Subject</option>
                            <?php foreach ($subjects as $subject) { ?>
                                <option value="<?php echo $subject['id']; ?>"><?php echo $subject['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" name="getAttendance" class="btn btn-primary my-4">Submit</button>
            </div>
            </form>
        </div>
    </div>
    </form>

    <div class="container mt-5">
        <?php
        if (isset($attendance)) {
            $total = 0;
            $present = 0;
            $absent = 0;
            $onLeave = 0;
        ?>
            <h6>Student Name: <?php echo $_SESSION['student_name']; ?></h6>
            <h6>Subject: <?= $attendance[0]['name'] ?></h6>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendance as $record) { ?>
                        <tr>
                            <td><?php echo $record['date']; ?></td>
                            <td><?php echo date("h:i A", strtotime($record['time'])); ?></td>
                            <td>
                                <?php 
                                if($record['status'] == "present"){
                                    $total = $total + 1;
                                    $present = $present + 1;
                                    echo '<span class="badge bg-success" style="font-size: 16px;">Present</span>';
                                } else if($record['status'] == "absent"){
                                    $total = $total + 1;
                                    $absent = $absent + 1;
                                    echo '<span class="badge bg-danger" style="font-size: 16px;>Absent</span>';
                                } else {
                                    $total = $total + 1;
                                    $onLeave = $onLeave + 1;
                                    echo '<span class="badge bg-warning" style="font-size: 16px;>On Leave</span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <h6>Total Classes: <?= $total ?></h6>
            <h6>Present: <?= $present ?></h6>
            <h6>Absent: <?= $absent ?></h6>
            <h6>On Leave: <?= $onLeave ?></h6>
            <h6>Attendance Percentage: <?php echo (($present + $onLeave) / $total)*100; ?> %</h6>
        <?php } else {
            echo "<p>No attendance found</p>";
        } ?>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>