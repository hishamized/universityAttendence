<?php
session_start();
require_once('../config.php');
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    echo '<div class="alert alert-danger" role="alert">You are not authorized to view this page.</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addNewCourse'])) {
    // Retrieve form data
    $courseName = $_POST['courseName'];
    $slug = $_POST['slug'];
    $duration = $_POST['duration'];
    $subjectCount = $_POST['subjectCount'];
    $creditsCount = $_POST['creditsCount'];
    $semestersCount = $_POST['semestersCount'];
    $intakeCapacity = $_POST['intakeCapacity'];
    $projectsCount = $_POST['projectsCount'];

    // Check if the course already exists
    $query = "SELECT COUNT(*) FROM courses WHERE slug = ? OR course_name = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "ss", $slug, $courseName);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $count);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);

    if ($count > 0) {
        echo '<div class="alert alert-danger" role="alert">Course with the same slug or name already exists</div>';
        exit();
    }

    // Prepare the SQL statement with parameter binding
    $query = "INSERT INTO courses (course_name, slug, duration, subject_count, credits_count, semesters_count, intake_capacity, projects_count) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $statement = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($statement, "ssiiiiii", $courseName, $slug, $duration, $subjectCount, $creditsCount, $semestersCount, $intakeCapacity, $projectsCount);

    // Execute the statement
    $result = mysqli_stmt_execute($statement);

    // Check for errors
    if ($result) {
        echo '<div class="alert alert-success" role="alert">New course added successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error adding new course: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['addNewCourse']);

    // Close the statement
    mysqli_stmt_close($statement);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editCourse'])) {
    // Retrieve form data
    $courseId = $_POST['courseId'];
    $courseName = $_POST['editCourseName'];
    $slug = $_POST['editSlug'];
    $duration = $_POST['editDuration'];
    $subjectCount = $_POST['editSubjectCount'];
    $creditsCount = $_POST['editCreditsCount'];
    $semestersCount = $_POST['editSemestersCount'];
    $intakeCapacity = $_POST['editIntakeCapacity'];
    $projectsCount = $_POST['editProjectsCount'];

    // Check if the course already exists
    $query = "SELECT COUNT(*) FROM courses WHERE slug = ? OR course_name = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "ss", $slug, $courseName);
    mysqli_stmt_execute($statement);
    mysqli_stmt_bind_result($statement, $count);
    mysqli_stmt_fetch($statement);
    mysqli_stmt_close($statement);

    if ($count > 0) {
        echo '<div class="alert alert-danger" role="alert">Course with the same slug or name already exists</div>';
        exit();
    }

    // Prepare the SQL statement with parameter binding
    $query = "UPDATE courses SET course_name = ?, slug = ?, duration = ?, subject_count = ?, credits_count = ?, semesters_count = ?, intake_capacity = ?, projects_count = ? WHERE id = ?";
    $statement = mysqli_prepare($conn, $query);

    // Bind parameters
    mysqli_stmt_bind_param($statement, "ssiiiiiii", $courseName, $slug, $duration, $subjectCount, $creditsCount, $semestersCount, $intakeCapacity, $projectsCount, $courseId);

    // Execute the statement
    $result = mysqli_stmt_execute($statement);

    // Check for errors
    if ($result) {
        echo '<div class="alert alert-success" role="alert">Course updated successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error updating course: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['editCourse']);

    // Close the statement
    mysqli_stmt_close($statement);
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteCourseId'])) {
    $courseId = $_POST['deleteCourseId'];
    $query = "DELETE FROM courses WHERE id = ?";
    $statement = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($statement, "i", $courseId);
    $result = mysqli_stmt_execute($statement);
    if($result) {
        echo '<div class="alert alert-success" role="alert">Course deleted successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting course: ' . mysqli_error($conn) . '</div>';
    }
    unset($_POST['deleteCourseId']);
    mysqli_stmt_close($statement);
}

// Fetch data from the courses table
$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);

// Close the database connection
mysqli_close($conn);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/1bc2765d38.js" crossorigin="anonymous"></script>
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
                        <a class="btn btn-danger" href="admin_logout.php">Logout Admin ( <?php echo $_SESSION['admin_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container m-5">
        <!-- Button to toggle the form -->
        <button type="button" class="btn btn-primary" onclick="toggleForm()">Add a new course</button>

        <!-- Hidden Bootstrap form -->
        <form id="courseForm" style="display: none;" method="post" action="manage_courses.php">
            <div class="mb-3">
                <label for="courseName" class="form-label">Course Name</label>
                <input type="text" class="form-control" id="courseName" name="courseName" required>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control" id="slug" name="slug" required>
            </div>
            <div class="mb-3">
                <label for="duration" class="form-label">Duration</label>
                <input type="number" class="form-control" id="duration" name="duration" required>
            </div>
            <div class="mb-3">
                <label for="subjectCount" class="form-label">Subject Count</label>
                <input type="number" class="form-control" id="subjectCount" name="subjectCount" required>
            </div>
            <div class="mb-3">
                <label for="creditsCount" class="form-label">Credits Count</label>
                <input type="number" class="form-control" id="creditsCount" name="creditsCount" required>
            </div>
            <div class="mb-3">
                <label for="semestersCount" class="form-label">Semesters Count</label>
                <input type="number" class="form-control" id="semestersCount" name="semestersCount" required>
            </div>
            <div class="mb-3">
                <label for="intakeCapacity" class="form-label">Intake Capacity</label>
                <input type="number" class="form-control" id="intakeCapacity" name="intakeCapacity" required>
            </div>
            <div class="mb-3">
                <label for="projectsCount" class="form-label">Projects Count</label>
                <input type="number" class="form-control" id="projectsCount" name="projectsCount" required>
            </div>

            <button type="submit" name="addNewCourse" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container mt-5">
        <h1>Manage Courses</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Slug</th>
                    <th>Duration</th>
                    <th>Subject Count</th>
                    <th>Credits Count</th>
                    <th>Semesters Count</th>
                    <th>Intake Capacity</th>
                    <th>Projects Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr id="row-<?= $row['id'] ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['slug']); ?></td>
                        <td><?php echo $row['duration']; ?></td>
                        <td><?php echo $row['subject_count']; ?></td>
                        <td><?php echo $row['credits_count']; ?></td>
                        <td><?php echo $row['semesters_count']; ?></td>
                        <td><?php echo $row['intake_capacity']; ?></td>
                        <td><?php echo $row['projects_count']; ?></td>
                        <td>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#editModal" data-course-id="<?php echo $row['id']; ?>" onclick="editCourseModal(<?php echo $row['id']; ?>)">Edit</button>
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
                    <h5 class="modal-title" id="editModalLabel">Edit Course</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close" onclick="closeEditModal()"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" method="post" action="manage_courses.php">
                        <div class="mb-3">
                            <input class="form-control" type="hidden" id="courseId" name="courseId">
                        </div>
                        <div class="mb-3">
                            <label for="editCourseName" class="form-label">Course Name</label>
                            <input type="text" class="form-control" id="editCourseName" name="editCourseName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSlug" class="form-label">Slug</label>
                            <input type="text" class="form-control" id="editSlug" name="editSlug" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDuration" class="form-label">Duration</label>
                            <input type="number" class="form-control" id="editDuration" name="editDuration" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSubjectCount" class="form-label">Subject Count</label>
                            <input type="number" class="form-control" id="editSubjectCount" name="editSubjectCount" required>
                        </div>
                        <div class="mb-3">
                            <label for="editCreditsCount" class="form-label">Credits Count</label>
                            <input type="number" class="form-control" id="editCreditsCount" name="editCreditsCount" required>
                        </div>
                        <div class="mb-3">
                            <label for="editSemestersCount" class="form-label">Semesters Count</label>
                            <input type="number" class="form-control" id="editSemestersCount" name="editSemestersCount" required>
                        </div>
                        <div class="mb-3">
                            <label for="editIntakeCapacity" class="form-label">Intake Capacity</label>
                            <input type="number" class="form-control" id="editIntakeCapacity" name="editIntakeCapacity" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProjectsCount" class="form-label">Projects Count</label>
                            <input type="number" class="form-control" id="editProjectsCount" name="editProjectsCount" required>
                        </div>
                        <button type="submit" name="editCourse" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
        <!-- Delete Batch Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this course?</p>
                        <!-- Form for deletion confirmation -->
                        <form id="deleteForm" method="post" action="manage_courses.php">
                            <input type="hidden" name="deleteCourseId" id="deleteCourseId">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    <script>
        // Function to toggle the form visibility
        function toggleForm() {
            var form = document.getElementById('courseForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>


    <script>
        function closeEditModal() {
            $('#editModal').modal('hide');
        }

        function editCourseModal(id) {
            const modal = document.getElementById('editModal');
            const isModalVisible = modal.classList.contains('show');

            // Check if the modal is not already visible
            if (!isModalVisible) {
                // Show the modal
                $('#editModal').modal('show');

                // Find the table row corresponding to the provided ID
                const tableRow = document.getElementById(`row-${id}`);

                // Extract data from table cells
                const courseId = tableRow.cells[0].innerText; // Assuming course ID is in the first column
                const courseName = tableRow.cells[1].innerText; // Assuming course name is in the second column
                const slug = tableRow.cells[2].innerText; // Assuming slug is in the third column
                const duration = tableRow.cells[3].innerText; // Assuming duration is in the fourth column
                const subjectCount = tableRow.cells[4].innerText; // Assuming subject count is in the fifth column
                const creditsCount = tableRow.cells[5].innerText; // Assuming credits count is in the sixth column
                const semestersCount = tableRow.cells[6].innerText; // Assuming semesters count is in the seventh column
                const intakeCapacity = tableRow.cells[7].innerText; // Assuming intake capacity is in the eighth column
                const projectsCount = tableRow.cells[8].innerText; // Assuming projects count is in the ninth column


                // Populate form fields with extracted data
                document.getElementById('courseId').value = courseId;
                document.getElementById('editCourseName').value = courseName;
                document.getElementById('editSlug').value = slug;
                document.getElementById('editDuration').value = duration;
                document.getElementById('editSubjectCount').value = subjectCount;
                document.getElementById('editCreditsCount').value = creditsCount;
                document.getElementById('editSemestersCount').value = semestersCount;
                document.getElementById('editIntakeCapacity').value = intakeCapacity;
                document.getElementById('editProjectsCount').value = projectsCount;

            } else {
                // Hide the modal if it's already visible
                $('#editModal').modal('hide');
            }
        }
        function openDeleteModal(batchId) {
            console.log(batchId);
            
            document.getElementById('deleteCourseId').value = batchId;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        
        function confirmDelete() {
            
            document.getElementById('deleteForm').submit();
        }
    </script>




    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>