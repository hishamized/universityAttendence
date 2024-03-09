<?php
require_once('../config.php');
session_start();
require_once('../config.php');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    echo '<div class="alert alert-danger" role="alert">You are not authorized to view this page.</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addNewClass'])) {

    $className = $_POST['className'];
    $semester = $_POST['semester'];
    $courseId = $_POST['courseId'];
    $branchId = $_POST['branchId'];
    $batchId = $_POST['batchId'];
    $duration = $_POST['duration'];
    $studentCount = $_POST['student_count'];



    $query = "INSERT INTO classes (name, semester, course_id, branch_id, batch_id, duration, student_count) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $statement = mysqli_prepare($conn, $query);


    mysqli_stmt_bind_param($statement, "ssiiiii", $className, $semester,  $courseId,  $branchId, $batchId, $duration, $studentCount);


    $result = mysqli_stmt_execute($statement);


    if ($result) {
        echo '<div class="alert alert-success" role="alert">New class added successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error adding new class: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['addNewClass']);


    mysqli_stmt_close($statement);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editClass'])) {

    $classId = $_POST['classId'];
    $className = $_POST['editClassName'];
    $semester = $_POST['editSemester'];
    $courseId = $_POST['editCourseId'];
    $branchId = $_POST['editBranchId'];
    $batchId = $_POST['editBatchId'];
    $duration = $_POST['editDuration'];
    $studentCount = $_POST['editStudentCount'];


    $query = "UPDATE classes SET name = ?, semester = ?, course_id = ?, branch_id = ?, duration = ?, student_count = ? WHERE id = ?";
    $statement = mysqli_prepare($conn, $query);


    mysqli_stmt_bind_param($statement, "ssiiiii", $className, $semester, $courseId, $branchId, $duration, $studentCount, $classId);


    $result = mysqli_stmt_execute($statement);


    if ($result) {
        echo '<div class="alert alert-success" role="alert">Class updated successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating class: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['editClass']);


    mysqli_stmt_close($statement);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteClassId'])) {
    $classId = $_POST['deleteClassId'];
    $query = "DELETE FROM classes WHERE id = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "i", $classId);
    $result = mysqli_stmt_execute($statement);
    if ($result) {
        echo '<div class="alert alert-success" role="alert">Class deleted successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting class: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['deleteClassId']);
    mysqli_stmt_close($statement);
}


$courseFetchSql = "SELECT * FROM courses";
$courseFetchResult = mysqli_query($conn, $courseFetchSql);

$branchFetchSql = "SELECT * FROM branches";
$branchFetchResult = mysqli_query($conn, $branchFetchSql);

$batchFetchSql = "SELECT * FROM batches";
$batchFetchResult = mysqli_query($conn, $batchFetchSql);

$query = "SELECT classes.*, courses.course_name, branches.branch_name, batches.year 
          FROM classes 
          LEFT JOIN courses ON classes.course_id = courses.id 
          LEFT JOIN branches ON classes.branch_id = branches.id 
          LEFT JOIN batches ON classes.batch_id = batches.id";
$result = mysqli_query($conn, $query);




mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/1bc2765d38.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script type="text/javascript" src="../js/admin/manage_classes.js"></script>
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

    <div class="container m-5">
        <!-- Button to toggle the form -->
        <button type="button" class="btn btn-primary" onclick="toggleClassForm()">Add a new class</button>

        <!-- Hidden Bootstrap form -->
        <form id="classForm" style="display: none;" method="post" action="manage_classes.php">
            <div class="mb-3">
                <label for="className" class="form-label">Class Name</label>
                <input type="text" class="form-control" id="className" name="className" required>
            </div>
            <div class="mb-3">
                <label for="semester" class="form-label">Semester</label>
                <input type="text" class="form-control" id="semester" name="semester" required>
            </div>
            <div class="mb-3">
                <label for="courseId" class="form-label">Course</label>
                <select class="form-select" id="courseId" name="courseId" required>
                    <?php while ($row = mysqli_fetch_assoc($courseFetchResult)) : ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['course_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="branchId" class="form-label">Branch</label>
                <select class="form-select" id="branchId" name="branchId" required>
                    <?php while ($row = mysqli_fetch_assoc($branchFetchResult)) : ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['branch_name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="batchId" class="form-label">Batch</label>
                <select class="form-select" id="batchId" name="batchId" required>
                    <?php while ($row = mysqli_fetch_assoc($batchFetchResult)) : ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['year']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>

            </div>

            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="number" class="form-control" id="duration" name="duration" required>
            </div>

            <div class="mb-3">
                <label for="student_count" class="form-label">Student Count</label>
                <input type="number" class="form-control" id="student_count" name="student_count" required>
            </div>
            <button type="submit" name="addNewClass" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container mt-5">
        <h1>Manage Classes</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                    <th>Semester</th>
                    <th>Course</th>
                    <th>Branch</th>
                    <th>Batch Year</th>
                    <th>Duration</th>
                    <th>Student Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) === 0) {
                    echo "<tr><td colspan='9'>No classes found</td></tr>";
                }
                ?>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr id="row-<?= $row['id'] ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['semester']); ?></td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['branch_name']; ?></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['duration']; ?></td>
                        <td><?php echo $row['student_count']; ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editModal" data-class-id="<?php echo $row['id']; ?>" onclick="editClassModal(<?php echo $row['id']; ?>)">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $row['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Class</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" onclick="closeEditModal()"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="post" action="manage_classes.php">
                        <div class="mb-3">
                            <input class="form-control" type="hidden" id="classId" name="classId">
                        </div>
                        <div class="mb-3">
                            <label for="editClassName" class="form-label">Class Name</label>
                            <input type="text" class="form-control" id="editClassName" name="editClassName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSemester" class="form-label">Semester</label>
                            <input type="text" class="form-control" id="editSemester" name="editSemester" required>
                        </div>
                        <div class="mb-3">
                            <label for="courseId" class="form-label">Course</label>
                            <select class="form-select" id="editCourseId" name="editCourseId" required>
                                <?php mysqli_data_seek($courseFetchResult, 0); ?>
                                <?php while ($rowE = mysqli_fetch_assoc($courseFetchResult)) : ?>
                                    <option value="<?php echo $rowE['id']; ?>"><?php echo $rowE['course_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="branchId" class="form-label">Branch</label>
                            <select class="form-select" id="editBranchId" name="editBranchId" required>
                                <?php mysqli_data_seek($branchFetchResult, 0); ?>
                                <?php while ($rowE = mysqli_fetch_assoc($branchFetchResult)) : ?>
                                    <option value="<?php echo $rowE['id']; ?>"><?php echo $rowE['branch_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="batchId" class="form-label">Batch</label>
                            <select class="form-select" id="editBatchId" name="editBatchId" required>
                                <?php mysqli_data_seek($batchFetchResult, 0); ?>
                                <?php while ($rowE = mysqli_fetch_assoc($batchFetchResult)) : ?>
                                    <option value="<?php echo $rowE['id']; ?>"><?php echo $rowE['year']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editDuration" class="form-label">Duration</label>
                            <input type="number" class="form-control" id="editDuration" name="editDuration" required>
                        </div>
                        <div class="mb-3">
                            <label for="editStudentCount" class="form-label">Student Count</label>
                            <input type="number" class="form-control" id="editStudentCount" name="editStudentCount" required>
                        </div>
                        <button type="submit" name="editClass" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <!-- Delete Class Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">


            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this class?</p>
                    <!-- Form for deletion confirmation -->
                    <form id="deleteForm" method="post" action="manage_classes.php">
                        <input type="hidden" name="deleteClassId" id="deleteClassId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>