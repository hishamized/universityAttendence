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


$query = "SELECT id, name FROM classes";
$result = mysqli_query($conn, $query);
$classes = mysqli_fetch_all($result, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addTimeTable'])) {
    
    $day = $_POST['day'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $classId = $_POST['classId'];
    $subjectId = $_POST['subjectId'];
    $duration = $_POST['duration'];
    $subjectName = "";
    $firstQuery = "SELECT name FROM subjects WHERE id = $subjectId";
    $firsrQueryResult = mysqli_query($conn, $firstQuery);
    $subjectName = mysqli_fetch_assoc($firsrQueryResult)['name'];

    
    $query = "INSERT INTO time_table (day, start_time, end_time, subject_name, class_id, subject_id, duration) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssiid", $day, $startTime, $endTime, $subjectName, $classId, $subjectId, $duration);
    $result = mysqli_stmt_execute($stmt);

    
    if ($result) {
        $_SESSION['success'] = "Time Table added successfully";
        header("Location: manage_timeTable.php");
    } else {
        $_SESSION['error'] = "Failed to add Time Table" . mysqli_error($conn);
        header("Location: manage_timeTable.php");
    }

    
    mysqli_stmt_close($stmt);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['findData'])) {
    $classId = $_POST['classId'];

    
    $query = "SELECT * FROM time_table WHERE class_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $classId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    
    if (mysqli_num_rows($result) > 0) {
        
        $TableRows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        
        $_SESSION['error'] = "No data found for the selected class.";
    }

    
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deleteTimeTable'])) {
    $timeTableId = $_POST['timeTableId'];

    
    $query = "DELETE FROM time_table WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $timeTableId);
    mysqli_stmt_execute($stmt);

    
    if (mysqli_affected_rows($conn) > 0) {
        
        $_SESSION['success'] = "Entry deleted successfully";
        header("Location: manage_timeTable.php");
        unset($_POST['deleteTimeTable']);
    } else {
        
        $_SESSION['error'] = "Failed to delete entry";
    }

    
    mysqli_stmt_close($stmt);

    
    header("Location: manage_timeTable.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editTimeTable'])) {
    $timeTableId = $_POST['editTimeTableId'];
    $editDay = $_POST['editDay'];
    $editStartTime = $_POST['editStartTime'];
    $editEndTime = $_POST['editEndTime'];
    $editDuration = $_POST['editDuration'];

    
    
    $query = "UPDATE time_table SET day = ?, start_time = ?, end_time = ?, duration = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $editDay, $editStartTime, $editEndTime, $editDuration, $timeTableId);
    mysqli_stmt_execute($stmt);

    
    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['success'] = "Entry updated successfully";
        header("Location: manage_timeTable.php");
    } else {
        $_SESSION['error'] = "Failed to update entry";
        header("Location: manage_timeTable.php");
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

    <script type="text/javascript" src="../js/admin/manage_timeTable.js"></script>
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
        <button class="btn btn-primary" onclick="toggleNewTimeTableForm()">Add New Time Table</button>

        <div class="form-container" id="newTimeTableForm" style="display: none;">
            <h1>Time Table Form</h1>
            <form action="manage_timeTable.php" method="POST">
                <div class="mb-3">
                    <label for="day" class="form-label">Day</label>
                    <select id="day" name="day" class="form-select" required>
                        <option value="">Select Day</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="startTime" class="form-label">Start Time</label>
                    <input type="time" id="startTime" name="startTime" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="endTime" class="form-label">End Time</label>
                    <input onchange="calculateDuration(this.id, 'startTime', 'duration')" type="time" id="endTime" name="endTime" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="classId" class="form-label">Class</label>
                    <select id="classId" name="classId" class="form-select" required onchange="enableSubjects()">
                        <option value="default">Select Class</option>
                        <?php foreach ($classes as $class) : ?>
                            <option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="subjectId" class="form-label">Subject Name</label>
                    <select name="subjectId" id="subjectId" class="form-select" required disabled>

                    </select>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration</label>
                    <input type="number" id="duration" name="duration" class="form-control" required>
                </div>
                <button type="submit" name="addTimeTable" class="btn btn-primary">Submit</button>
                <button class="btn btn-danger" onclick="toggleNewTimeTableForm()">Cancel</button>
            </form>
        </div>
    </div>

    <div class="container m-5">
        <form action="manage_timeTable.php" method="POST">
            <div class="mb-3">
                <label for="classId" class="form-label">Class</label>
                <select id="classId" name="classId" class="form-select" required onchange="enableSubjects()">
                    <option value="default">Select Class</option>
                    <?php reset($classes); 
                    ?>
                    <?php foreach ($classes as $class) : ?>
                        <option value="<?php echo $class['id'] ?>"><?php echo $class['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="findData" class="btn btn-primary">Find Data</button>
        </form>
        <table class="table m-5">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">Day</th>
                    <th scope="col">Start Time</th>
                    <th scope="col">End Time</th>
                    <th scope="col">Subject Name</th>
                    <th scope="col">Class Id</th>
                    <th scope="col">Subject Id</th>
                    <th scope="col">Duration (minutes)</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (isset($TableRows)) : ?>
                    <?php foreach ($TableRows as $TableRow) : ?>
                        <tr data-rowId="<?php echo $TableRow['id']; ?>">
                            <td><?php echo $TableRow['day'] ?></td>
                            <td><?php echo $TableRow['start_time'] ?></td>
                            <td><?php echo $TableRow['end_time'] ?></td>
                            <td><?php echo $TableRow['subject_name'] ?></td>
                            <td><?php echo $TableRow['class_id'] ?></td>
                            <td><?php echo $TableRow['subject_id'] ?></td>
                            <td><?php echo $TableRow['duration'] ?></td>
                            <td class="flex flex-col">
                                <button onclick="editTimeTable(this.id)" id="<?= $TableRow['id'] ?>" class="btn btn-primary" data-editTimeTableId="<?= $TableRow['id'] ?>"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                                <button class="btn btn-danger" data-timeTableId="<?= $TableRow['id'] ?>"><i class="fa-solid fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif (isset($_SESSION['error'])) : ?>
                    <tr>
                        <td colspan="6"><?php
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Pop up modals -->
    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this entry?</p>
                    <form id="deleteForm" method="post" action="manage_timeTable.php">
                        <input type="hidden" id="timeTableIdToDelete" name="timeTableId">
                        <button type="submit" name="deleteTimeTable" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="post" action="manage_timeTable.php">
                    <input type="hidden" id="editTimeTableId" name="editTimeTableId">
                    <div class="mb-3">
                        <label for="editDay" class="form-label">Day</label>
                        <select id="editDay" name="editDay" class="form-select">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editStartTime" class="form-label">Start Time</label>
                        <input type="time" class="form-control" id="editStartTime" name="editStartTime">
                    </div>
                    <div class="mb-3">
                        <label for="editEndTime" class="form-label">End Time</label>
                        <input type="time" class="form-control" id="editEndTime" name="editEndTime">
                    </div>
                    <div class="mb-3">
                        <label for="editDuration" class="form-label">Duration</label>
                        <input type="number" class="form-control" id="editDuration" name="editDuration">
                    </div>
                    <button type="submit" name="editTimeTable" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
<?php $conn->close(); ?>