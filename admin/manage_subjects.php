<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    echo '<div class="alert alert-danger" role="alert">You are not authorized to view this page.</div>';
    exit();
}
require_once('../config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addNewSubject'])) {
    function nameToSlug($name)
    {
        $slug = strtolower($name);

        $slug = str_replace(' ', '-', $slug);

        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
        return $slug;
    }
    
    $name = trim(htmlspecialchars($_POST["name"]));
    $slug = nameToSlug($name);
    $courseId = trim(htmlspecialchars($_POST["courseId"]));
    $branchId = trim(htmlspecialchars($_POST["branchId"]));
    $semester = trim(htmlspecialchars($_POST["semester"]));
    $batchId = trim(htmlspecialchars($_POST["batchId"]));
    $creditsCount = trim(htmlspecialchars($_POST["creditsCount"]));
    $assignmentCount = trim(htmlspecialchars($_POST["assignmentCount"]));
    $textbookAssigned = trim(htmlspecialchars($_POST["textbookAssigned"]));

    
    if (empty($name) || empty($courseId) || empty($branchId) || empty($semester) || empty($batchId) || empty($creditsCount) || empty($assignmentCount) || empty($textbookAssigned)) {
        echo '<div class="alert alert-danger" role="alert">All fields are required.</div>';
        exit;
    }
    if ($semester < 1 || $semester > 10) {
        echo '<div class="alert alert-danger" role="alert">Semester should be between 1 and 10.</div>';
        exit;
    }

    
    $stmt = $conn->prepare("INSERT INTO subjects (name, slug, course_id, branch_id, semester, batch_id, credits_count, assignment_count, textbook_assigned) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    
    $stmt->bind_param("ssiiiiiii", $name, $slug, $courseId, $branchId, $semester, $batchId, $creditsCount, $assignmentCount, $textbookAssigned);

    
    if ($stmt->execute()) {
        echo '<div class="alert alert-success" role="alert">Subject added successfully.</div>';
    } else {
        echo "Error: " . $conn->error;
    }

    unset($_POST['addNewSubject']);

    
    $stmt->close();
}


$query = "SELECT subjects.*, courses.course_name, branches.branch_name, batches.year 
          FROM subjects 
          LEFT JOIN courses ON subjects.course_id = courses.id 
          LEFT JOIN branches ON subjects.branch_id = branches.id 
          LEFT JOIN batches ON subjects.batch_id = batches.id";
$result = mysqli_query($conn, $query);


if (!$result) {
    echo '<div class="alert alert-danger" role="alert">Error fetching subjects: ' . mysqli_error($conn) . '</div>';
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

    <script type="text/javascript" src="../js/admin/manage_subjects.js"></script>

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


    <!-- Button to toggle the form -->
    <div class="container m-5">
        <button type="button" class="btn btn-primary" onclick="toggleAddForm()" id="toggleForm">Add Subject</button>
        <!-- Hidden Bootstrap form -->
        <div class="container mt-3" id="subjectForm" style="display: none;">
            <h2>Add Subject</h2>
            <form method="post" action="manage_subjects.php">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="courseId" class="form-label">Course</label>
                    <select class="form-select" id="courseId" name="courseId" required>
                        <?php
                        
                        require_once('../config.php');
                        $courseQuery = "SELECT id, course_name FROM courses";
                        $courseResult = mysqli_query($conn, $courseQuery);
                        while ($row = mysqli_fetch_assoc($courseResult)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['course_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="branchId" class="form-label">Branch</label>
                    <select class="form-select" id="branchId" name="branchId" required>
                        <?php
                        
                        $branchQuery = "SELECT id, branch_name FROM branches";
                        $branchResult = mysqli_query($conn, $branchQuery);
                        while ($row = mysqli_fetch_assoc($branchResult)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['branch_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <input type="number" class="form-control" id="semester" name="semester" required min="1" max="10">
                </div>
                <div class="mb-3">
                    <label for="batchId" class="form-label">Batch</label>
                    <select class="form-select" id="batchId" name="batchId" required>
                        <?php
                        
                        $batchQuery = "SELECT id, year FROM batches";
                        $batchResult = mysqli_query($conn, $batchQuery);
                        while ($row = mysqli_fetch_assoc($batchResult)) {
                            echo "<option value='" . $row['id'] . "'>" . $row['year'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="creditsCount" class="form-label">Credits Count</label>
                    <input type="number" class="form-control" id="creditsCount" name="creditsCount" required>
                </div>
                <div class="mb-3">
                    <label for="assignmentCount" class="form-label">Assignment Count</label>
                    <input type="number" class="form-control" id="assignmentCount" name="assignmentCount" required>
                </div>
                <div class="mb-3">
                    <label for="textbookAssigned" class="form-label">Textbook Assigned</label>
                    <input type="text" id="textbookAssigned" name="textbookAssigned">
                </div>
                <button type="submit" name="addNewSubject" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <div class="container mt-5">
        <h1>Subjects</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Branch</th>
                    <th>Batch Year</th>
                    <th>Semester</th>
                    <th>Credits Count</th>
                    <th>Assignment Count</th>
                    <th>Textbook Assigned</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo $row['course_name']; ?></td>
                        <td><?php echo $row['branch_name']; ?></td>
                        <td><?php echo $row['year']; ?></td>
                        <td><?php echo $row['semester']; ?></td>
                        <td><?php echo $row['credits_count']; ?></td>
                        <td><?php echo $row['assignment_count']; ?></td>
                        <td><?php echo $row['textbook_assigned'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm">Edit</button>
                            <button class="btn btn-danger btn-sm">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>




    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>
<?php $conn->close(); ?>