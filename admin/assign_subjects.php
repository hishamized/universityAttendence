<?php
session_start();


if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
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


$query = "SELECT id, course_name FROM courses";
$courses = mysqli_prepare($conn, $query);
mysqli_stmt_execute($courses);
mysqli_stmt_store_result($courses);
mysqli_stmt_bind_result($courses, $course_id, $course_name);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['findSubjects'])) {
    
    $course_id = trim($_POST['course']);
    $branch_id = trim($_POST['branch']);
    $semester = trim($_POST['semester']);

    
    $query = "SELECT s.id, s.name, ss.staff_id 
              FROM subjects s 
              LEFT JOIN subject_staff ss ON s.id = ss.subject_id
              WHERE s.course_id = ? AND s.branch_id = ? AND s.semester = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iii", $course_id, $branch_id, $semester);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    
    mysqli_stmt_bind_result($stmt, $subject_id, $subject_name, $staff_id);

    
    $subjects = array();
    while (mysqli_stmt_fetch($stmt)) {
        
        if (!isset($subjects[$subject_id])) {
            $subjects[$subject_id] = array(
                'name' => $subject_name,
                'staff' => array(),
            );
        }
        
        if ($staff_id !== null) {
            $subjects[$subject_id]['staff'][] = $staff_id;
        }
    }

    
    mysqli_stmt_close($stmt);

    
    $staff_query = "SELECT id, full_name FROM staff";
    $staff_result = mysqli_query($conn, $staff_query);

    
    $staff_members = array();
    while ($row = mysqli_fetch_assoc($staff_result)) {
        $staff_members[$row['id']] = $row['full_name'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assignStaff'])) {
    
    $subject_id = trim($_POST['subject_id']);
    $staff_id = trim($_POST['staff_id']);

    
    $query = "SELECT COUNT(*) FROM subject_staff WHERE subject_id = ? AND staff_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $subject_id, $staff_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    
    if ($count > 0) {
        $_SESSION['error'] = "Assignment already exists for the selected staff and subject.";
        header("Location: assign_subjects.php");
        exit();
    }

    
    $query = "INSERT INTO subject_staff (subject_id, staff_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $subject_id, $staff_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    
    $_SESSION['success'] = "Staff assigned to the subject successfully.";
    header("Location: assign_subjects.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['dismissStaff'])){
    
    $subject_id = trim($_POST['subject_id']);
    $staff_id = trim($_POST['staff_id']);

    
    $query = "DELETE FROM subject_staff WHERE subject_id = ? AND staff_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $subject_id, $staff_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    
    $_SESSION['success'] = "Staff dismissed from the subject successfully.";
    header("Location: assign_subjects.php");
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

    <script type="text/javascript" src="../js/admin/assign_subjects.js"></script>
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
                        <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="admin_logout.php">Logout Admin ( <?php echo $_SESSION['admin_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container m-5">
        <h1>Assign Subjects</h1>
        <form id="searchForm" method="post" action="assign_subjects.php">
            <div class="mb-3">
                <label for="selectCourse" class="form-label">Course:</label>
                <select id="selectCourse" onchange="enableBranches()" name="course" class="form-select" required>
                    <option value="default">Select Course</option>
                    <?php while (mysqli_stmt_fetch($courses)) { ?>
                        <option data-course-id="<?= $course_id ?>" value="<?= $course_id ?>"><?= $course_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="selectBranch" class="form-label">Branch:</label>
                <select id="selectBranch" onchange="enableSemesters()" name="branch" class="form-select" disabled required>
                    <option value="default">Select Branch</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="selectSemester" class="form-label">Semester:</label>
                <select id="selectSemester" name="semester" class="form-select" disabled required>
                    <option value="default">Select Semester</option>
                    <option value="1">First</option>
                    <option value="2">Second</option>
                    <option value="3">Third</option>
                    <option value="4">Fourth</option>
                    <option value="5">Fifth</option>
                    <option value="6">Sixth</option>
                    <option value="7">Seventh</option>
                    <option value="8">Eighth</option>
                    <option value="9">Ninth</option>
                    <option value="10">Tenth</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" name="findSubjects">Find Subjects</button>
        </form>
        <div id="subjectTable">
            <!-- Subject table will be populated dynamically using JavaScript -->
        </div>
    </div>


    <div class="container m-5">
        <div id="subjectTable">
            <table class="table">
                <thead>
                    <tr>
                        <th>Subject Name</th>
                        <th>Assigned Staff Members</th>
                        <th>Assign New Staff Member</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($subjects)) : ?>
                        <?php foreach ($subjects as $subject_id => $subject) : ?>
                            <tr>
                                <td><?= $subject['name'] ?></td>
                                <td>
                                    <?php foreach ($subject['staff'] as $staff_id) : ?>
                                        <div class="d-flex flex-row align-items-center">
                                            <?= $staff_members[$staff_id] ?>
                                            <form method="POST" action="assign_subjects.php">
                                                <input type="hidden" name="staff_id" value="<?= $staff_id ?>">
                                                <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                                            <button type="submit" name="dismissStaff" class="btn btn-danger m-1" data-staff="<?= $staff_id ?>" data-subject="<?= $subject_id ?>">Dismiss</button>
                                            </form>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <form id="assignForm_<?= $subject_id ?>" class="assign-form" method="post" action="assign_subjects.php">
                                        <!-- Dropdown to assign new staff member -->
                                        <select name="staff_id" class="form-select assign-dropdown">
                                            <option value="">Select Staff Member</option>
                                            <?php foreach ($staff_members as $staff_id => $staff_name) : ?>
                                                <option value="<?= $staff_id ?>"><?= $staff_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <!-- Hidden input to store subject ID -->
                                        <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                                        <!-- Add button to trigger assignment -->
                                        <button name="assignStaff" type="submit" class="btn btn-primary assign-button my-3">Assign</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>

<?php
mysqli_stmt_close($courses);
?>