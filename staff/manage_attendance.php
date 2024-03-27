<?php
session_start();


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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newAttendence'])) {
    
    $date = $_POST['date'];
    $time = $_POST['time'];
    $subject_id = $_POST['subject_id'];
    $class_id = $_POST['class_id'];
    $teacher_id = $_POST['teacher_id'];
    $studentCount = $_POST['studentCount'];
    $student_ids = $_POST['student_ids'];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE date = ? AND subject_id = ? AND class_id = ?");
    $stmt->bind_param("sii", $date, $subject_id, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->fetch_row()[0] > 0) {
        $_SESSION['error'] = "Attendance already marked for this subject and class on this date";
        header("Location: manage_attendance.php");
        exit();
    }


    
    $stmt = $conn->prepare("INSERT INTO attendance (date, time, subject_id, class_id, staff_id, student_id, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiiss", $date, $time, $subject_id, $class_id, $teacher_id, $student_id, $status);

    
     
     for ($i = 0; $i < $studentCount; $i++) {
        
        $student_id = $student_ids[$i];
        $status = $_POST['student' . ($i + 1)]; 
        
        $stmt->execute();

        $_SESSION['success'] = "Attendance marked successfully";
    }


    
    $stmt->close();

    
    $conn->close();

    
    header("Location: manage_attendance.php");
    exit();
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

    <script type="text/javascript" src="../js/staff/manage_attendance.js"></script>
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
        <h3>Mark Attendance</h3>
        <form action="manage_attendance.php" method="POST">
            <input type="hidden" id="staffId" name="staffId" value="<?= $_SESSION['staff_id']?>">
            <div class="m-b">
                <label for="selectDate">Select Date</label>
                <input type="date" class="form-control" id="selectDate" name="date" value="<?php echo date('Y-m-d') ?>">
            </div>
            <div class="mb-3">
                <!-- Time -->
                <label for="selectTime">Select Time</label>
                <input type="time" class="form-control" id="selectTime" name="time" value="<?php echo date('H:i') ?>">
            </div>
            <div class="mb-">
                <label for="selectClass">Select Class</label>
                <select onchange="findSubjects(this.id, 'selectSubject', 'staffId'); fetchStudents(this.id);" class="form-select" id="selectClass" name="class_id">
                    <option value="default" selected>Select Class</option>
                    <?php foreach ($classes as $class) : ?>
                        <option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="selectSubject">Select Subject</label>
                <select class="form-select" id="selectSubject" name="subject_id" disabled>
                    <option value="default">Select Subject</option>
                    <!-- More options will be populated using javascript -->
                </select>
            </div>

            <input type="hidden" value="<?= $_SESSION['staff_id']?>" id="selectTeacher" name="teacher_id" required>

            <div id="attendanceContainer">
                <!-- Attendance will be populated using javascript -->
            </div>
            <input type="hidden" class="form-control" id="studentCount" name="studentCount" value="0">
            <div class="mb-3">
                <button class="btn btn-primary" type="submit" name="newAttendence">Submit</button>
            </div>
        </form>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>