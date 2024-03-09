<?php
require_once('../config.php');
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {

    echo '<div class="alert alert-danger" role="alert">You are not authorized to view this page.</div>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['addNewBranch'])) {
    $branchName = $_POST['branchName'];
    $branchSlug = $_POST['branchSlug'];
    $courseId = $_POST['courseId'];

    $query = "INSERT INTO branches (branch_name, branch_slug, course_id) VALUES ('$branchName', '$branchSlug', '$courseId')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo '<div class="alert alert-success" role="alert">New branch added successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error adding new branch</div>';
    }
    unset($_POST['addNewBranch']);
}

if($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['deleteBranchId'])) {
    $branchId = $_POST['deleteBranchId'];
    $query = "DELETE FROM branches WHERE id = $branchId";
    $result = mysqli_query($conn, $query);
    if($result) {
        echo '<div class="alert alert-success" role="alert">Branch deleted successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting branch</div>';
    }
    unset($_POST['deleteBranchId']);
}


//fetch branches data
$sqlQuery = "SELECT branches.id, branches.branch_name, branches.branch_slug, courses.course_name FROM branches JOIN courses ON branches.course_id = courses.id";
$result = mysqli_query($conn, $sqlQuery);
$branches = mysqli_fetch_all($result, MYSQLI_ASSOC);


//Fetch course data
$query = "SELECT * FROM courses";
$result = mysqli_query($conn, $query);
$courses = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
        <!-- Button to toggle form visibility -->
        <button type="button" id="toggleFormButton" class="btn btn-primary">Add a new branch</button>

        <!-- Form to add data to branches table -->
        <div id="branchForm" style="display: none;">
            <h4>Add New Branch</h4>
            <form id="addBranchForm" method="post" action="manage_branches.php">
                <div class="mb-3">
                    <label for="branchName" class="form-label">Branch Name</label>
                    <input type="text" class="form-control" id="branchName" name="branchName" required>
                </div>
                <div class="mb-3">
                    <label for="branchSlug" class="form-label">Branch Slug</label>
                    <input type="text" class="form-control" id="branchSlug" name="branchSlug" required>
                </div>
                <div class="mb-3">
                    <label for="courseId" class="form-label">Course</label>
                    <select class="form-select" id="courseId" name="courseId" required>
                        <?php
                        foreach ($courses as $course) {
                            echo "<option value='" . $course['id'] . "'>" . $course['course_name'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="addNewBranch" class="btn btn-primary">Add Branch</button>
            </form>
        </div>
    </div>

    <div class="container m-5">
        <h3>Manage Branches</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">Branch ID</th>
                    <th scope="col">Branch Name</th>
                    <th scope="col">Branch Slug</th>
                    <th scope="col">Course</th>
                    <th scope="col">Actions</th> <!-- Edit and Delete buttons column -->
                </tr>
            </thead>
            <tbody>
                <?php if(empty($branches)){
                    echo "<tr><td colspan='5'>No branches found</td></tr>";
                } ?>
                <?php foreach ($branches as $branch) : ?>
                    <tr>
                        <td><?php echo $branch['id']; ?></td>
                        <td><?php echo $branch['branch_name']; ?></td>
                        <td><?php echo $branch['branch_slug']; ?></td>
                        <td><?php echo $branch['course_name']; ?></td>
                        <td>
                        <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $branch['id'] ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
                        <p>Are you sure you want to delete this branch?</p>
                        <!-- Form for deletion confirmation -->
                        <form id="deleteForm" method="post" action="manage_branches.php">
                            <input type="hidden" name="deleteBranchId" id="deleteBranchId">
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
        // Function to toggle form visibility
        function toggleForm() {
            const form = document.getElementById('branchForm');
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }

        // Add click event listener to the toggle button
        document.getElementById('toggleFormButton').addEventListener('click', toggleForm);
    </script>
    <script>
         
         function openDeleteModal(branchId) {
            console.log(branchId);
            
            document.getElementById('deleteBranchId').value = branchId;
            
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