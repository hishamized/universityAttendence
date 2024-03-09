<?php
session_start();


if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    
    echo '<div class="alert alert-danger" role="alert">You are not authorized to view this page.</div>';
    exit();
}

require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addNewBatch'])) {
    
    if (isset($_POST['year']) && !empty($_POST['year'])) {
        $year = $_POST['year'];

        
        $sql = "SELECT * FROM batches WHERE year = $year";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            
            echo '<div class="alert alert-danger" role="alert">Batch for year ' . $year . ' already exists.</div>';
        } else {
            
            $insertSql = "INSERT INTO batches (year) VALUES ($year)";
            if (mysqli_query($conn, $insertSql)) {
                
                echo '<div class="alert alert-success" role="alert">Batch added successfully.</div>';
            } else {
                
                echo "Error adding batch: " . mysqli_error($conn);
            }
        }
    } else {
        
        echo '<div class="alert alert-danger" role="alert">Please provide a year for the batch.</div>';
    }
    unset($_POST['addNewBatch']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editBatch'])) {
        $batchId = $_POST['batchId_edit'];
        $year = $_POST['year_edit'];

        $updateSql = "UPDATE batches SET year = $year WHERE id = $batchId";
        if (mysqli_query($conn, $updateSql)) {
            echo '<div class="alert alert-success" role="alert">Batch updated successfully.</div>';
        } else {
            echo "Error updating batch: " . mysqli_error($conn);
        }
    }
    unset($_POST['editBatch']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteBatchId'])) {
    $batchId = $_POST['deleteBatchId'];
    $deleteSql = "DELETE FROM batches WHERE id = $batchId";
    if (mysqli_query($conn, $deleteSql)) {
        echo '<div class="alert alert-success" role="alert">Batch deleted successfully.</div>';
    } else {
        echo "Error deleting batch: " . mysqli_error($conn);
    }
    unset($_POST['deleteBatchId']);
}



$sql = "SELECT * FROM batches";
$result = mysqli_query($conn, $sql);

$batches = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $batches[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Batches</title>
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

    <!-- Edit batch form -->

    <div class="container mt-5">
        <h2 class="mb-4">Manage Batches</h2>

        <div id="editBatchForm" style="display: none;">
            <form method="POST" action="manage_batches.php">
                <div class="mb-3">
                    <label for="batchId" class="form-label">Batch ID</label>
                    <input type="text" class="form-control" id="batchId" name="batchId_edit" readonly>
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <input type="text" class="form-control" id="year" name="year_edit">
                </div>
                <button type="submit" class="btn btn-primary" name="editBatch">Save Changes</button>
                <a href="#" class="btn btn-secondary" onclick="closeEditBatchForm()">Cancel</a>
            </form>
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
                        <p>Are you sure you want to delete this batch?</p>
                        <!-- Form for deletion confirmation -->
                        <form id="deleteForm" method="post" action="manage_batches.php">
                            <input type="hidden" name="deleteBatchId" id="deleteBatchId">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                    </div>
                </div>
            </div>
        </div>



        <?php if (empty($batches)) : ?>
            <div class="alert alert-info" role="alert">
                No batches found.
            </div>
        <?php else : ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Year</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($batches as $batch) : ?>
                        <tr data-batch-id="<?= $batch['id'] ?>">
                            <td class="batch-id"><?= $batch['id'] ?></td>
                            <td class="batch-year"><?= $batch['year'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="editBatch(<?= $batch['id'] ?>)">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $batch['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <button class="btn btn-success mb-3" onclick="showAddBatchForm()">Add Batch</button>

        <div id="addBatchForm" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Batch</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addBatchForm" method="post" action="manage_batches.php">
                            <div class="mb-3">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="year" name="year" required>
                            </div>
                            <!-- Add more fields as needed -->
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" name="addNewBatch">Add Batch</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


    </div>


    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <!-- JavaScript to handle interactions -->
    <script>
        function editBatch(batchId) {
            
            var row = document.querySelector('tr[data-batch-id="' + batchId + '"]');

            
            var id = row.querySelector('.batch-id').innerText; 
            var year = row.querySelector('.batch-year').innerText; 

            
            document.getElementById('batchId').value = id;
            document.getElementById('year').value = year;

            
            document.getElementById('editBatchForm').style.display = 'block';
        }

        function closeEditBatchForm() {
            document.getElementById('editBatchForm').style.display = 'none';
        }

        function showAddBatchForm() {
            
            var modal = new bootstrap.Modal(document.getElementById('addBatchForm'));
            modal.show();
        }

        function toggleAddBatchForm() {
            
            var modal = new bootstrap.Modal(document.getElementById('addBatchForm'));
            modal.hide();
        }


        
        function openDeleteModal(batchId) {
            console.log(batchId);
            
            document.getElementById('deleteBatchId').value = batchId;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        
        function confirmDelete() {
            
            document.getElementById('deleteForm').submit();
        }
    </script>
</body>

</html>