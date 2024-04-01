<?php
session_start();

// Check if staff is not logged in, redirect to login page
if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
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
$query = "SELECT id, name FROM classes WHERE status = 'active' ORDER BY name ASC;";
$result = mysqli_query($conn, $query);
$classes = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['findAttendance'])) {
    $class_id = htmlspecialchars(trim($_POST['class_id']));
    $subject_id = htmlspecialchars(trim($_POST['subject_id']));
    $date = htmlspecialchars(trim($_POST['date']));


    $query = "SELECT attendance.*, students.full_name, subjects.name AS subject_name 
              FROM attendance 
              JOIN students ON attendance.student_id = students.id 
              JOIN subjects ON attendance.subject_id = subjects.id 
              WHERE attendance.class_id = ? AND attendance.subject_id = ? AND attendance.date = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $class_id, $subject_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($attendance)) {
        $_SESSION['error'] = "No records found";
        header("Location: extract_attendance.php");
        exit();
    } else {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("printAttendanceBtn").style.display = "block";
        });
    </script>';
        $_SESSION['success'] = "Records found";
    }
    unset($_POST['findAttendance']);
    $stmt->close();
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
    <script type="text/javascript" src="../js/staff/extract_attendance.js"></script>
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
                        <a class="nav-link" href="staff_dashboard.php">staff Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="staff_logout.php">Logout staff ( <?php echo $_SESSION['staff_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container m-5">
        <h1>Extract Attendance</h1>
        <form action="extract_attendance.php" method="POST">
            <input type="hidden" value="<?= $_SESSION['staff_id'] ?>" id="staffId" name="staffId">
            <div class="my-4">
                <label for="selectClass">Select Class</label>
                <select onchange="findSubjects(this.id, 'selectSubject', 'staffId');" class="form-select" id="selectClass" name="class_id">
                    <option value="default" selected>Select Class</option>
                    <?php foreach ($classes as $class) : ?>
                        <option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="my-4">
                <label for="selectSubject">Select Subject</label>
                <select class="form-select" id="selectSubject" name="subject_id" disabled>
                    <option value="default">Select Subject</option>
                    <!-- More options will be populated using javascript -->
                </select>
            </div>
            <div class="my-4">
                <label for="selectDate">Select Date</label>
                <input type="date" class="form-control" id="selectDate" name="date" value="<?php echo date('Y-m-d') ?>">
            </div>

            <div class="my-4">
                <button type="submit" name="findAttendance" class="btn btn-primary">Retrieve Records</button>
            </div>

        </form>
    </div>

    <!-- Show fetched records with edit option -->
    <div class="container m-5">
        <h3>Attendance Records</h3>
        <?php
        $printData = array();
        if(isset($attendance)){
            $printData = array(
                'subject' => $attendance[0]['subject_name'],
                'date' => $attendance[0]['date'],
                'time' => date("h:i A", strtotime($attendance[0]['time'])),
            );
        }
        $printDataJson = json_encode($printData);
        ?>
        <button id="printAttendanceBtn" style="display: none;" onclick="printAttendanceTable(<?php echo htmlspecialchars($printDataJson); ?>)" class="btn btn-primary my-3">Export As PDF</button>
        <table class="table table-bordered" id="attendance_container">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Date/Time</th>
                    <th>Attendance</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($attendance)) : ?>
                    <?php foreach ($attendance as $record) : ?>
                        <tr>
                            <td><?php echo $record['student_id'] ?></td>
                            <td><?php echo $record['full_name'] ?></td>
                            <td><?php echo $record['subject_name'] ?></td>
                            <td><?php echo $record['date'] . '</br>' . date("h:i A", strtotime($record['time'])); ?></td>
                            <td>
                                <?php if ($record['status'] == "present") {
                                    echo '<span class="badge bg-success" style="font-size:large;">Present</span>';
                                } elseif ($record['status'] == "absent") {
                                    echo '<span class="badge bg-danger" style="font-size:large;">Absent</span>';
                                } elseif ($record['status'] == "on_leave") {
                                    echo '<span class="badge bg-warning" style="font-size:large;">On Leave</span>';
                                } ?>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <button id="printAttendanceBtn" style="display: none;" onclick="printAttendanceTable()" class="btn btn-primary">Export As PDF</button>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>