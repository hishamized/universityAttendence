<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['addNewStaff'])) {
    $username = htmlspecialchars($_POST['username']);
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $phoneNumber = htmlspecialchars($_POST['phoneNumber']);
    $password = htmlspecialchars($_POST['password']);
    $confirmPassword = htmlspecialchars($_POST['confirmPassword']);
    $designation = htmlspecialchars($_POST['designation']);

    if (strlen($password) < 8) {
        echo "Password must be at least 8 characters long";
        exit();
    } else if ($password != $confirmPassword) {
        echo "Passwords do not match";
        exit();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address";
        exit();
    } else if (strlen($phoneNumber) < 10) {
        echo "Invalid phone number";
        exit();
    } else if ($username == "" || $fullName == "") {
        echo "Username and Full Name are required";
        exit();
    }

    // Check if the username already exists
    $sql = "SELECT COUNT(*) FROM staff WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    if ($count > 0) {
        // echo "Username already exists";
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        exit();
    }

    // Hash the password
    $password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new staff member into the database
    $sql = "INSERT INTO staff (username, full_name, email, phone_number, password, designation) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssss', $username, $fullName, $email, $phoneNumber, $password, $designation);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Staff member added successfully
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        Failed to add staff member
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    }
    unset($_POST['addNewStaff']);
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editStaff'])) {
    $editStaffid = htmlspecialchars(trim($_POST['editStaffId']));
    $editUsername = htmlspecialchars(trim($_POST['editUsername']));
    $editFullName = htmlspecialchars(trim($_POST['editFullName']));
    $editEmail = htmlspecialchars(trim($_POST['editEmail']));
    $editPhoneNumber = htmlspecialchars(trim($_POST['editPhoneNumber']));
    $editDesignation = htmlspecialchars(trim($_POST['editDesignation']));

    $sql =  "SELECT COUNT(*) FROM staff WHERE username = ?";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $editUsername);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);


    if ($count > 0) {
        // echo "Username already exists";
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
        exit();
    }
    $sql = "UPDATE staff SET username = ?, full_name = ?, email = ?, phone_number = ?, designation = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssi', $editUsername, $editFullName, $editEmail, $editPhoneNumber, $editDesignation, $editStaffid);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
        Staff member updated successfully
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>';
    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
        Failed to update staff member
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteStaffId'])){
    $staffId = $_POST['deleteStaffId'];

    $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->bind_param("i", $staffId);

    if($stmt->execute()){
        echo '<div class="alert alert-success" role="alert">Subject deleted successfully.</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error deleting subject: ' . $conn->error . '</div>';
    }

    unset($_POST['deleteStaffId']);
    $stmt->close();
}

// Fetch all staff members from the database
$sql = "SELECT  id, username, full_name, email, phone_number, designation FROM staff";
$result = mysqli_query($conn, $sql);
$staffData = mysqli_fetch_all($result, MYSQLI_ASSOC);

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
    <script src="../js/admin/manage_staff.js" type="text/javascript"></script>
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
        <button class="btn btn-primary" onclick="toggleAddStaffForm('staffFormContainer')">Add New Staff Member</button>
        <div id="staffFormContainer" class="form-container" style="display: none;">
            <h2>Add Staff</h2>
            <form id="staffForm" method="post" action="manage_staff.php">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phoneNumber" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                </div>
                <div class="mb-3">
                    <label for="designation" class="form-label">Designation</label>
                    <select class="form-select" id="designation" name="designation" required>
                        <option value="teaching">Teaching</option>
                        <option value="non_teaching">Non-Teaching</option>
                        <option value="dignitary">Dignitary</option>
                    </select>
                </div>
                <button type="submit" name="addNewStaff" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <div class="container m-5">

        <div class="container mt-5">
            <h2>Staff Data</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Designation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffData as $staff) : ?>
                        <tr data-staff-id="<?= $staff['id'] ?>">
                            <td><?= $staff['id'] ?></td>
                            <td class="username"><?= $staff['username'] ?></td>
                            <td class="full_name"><?= $staff['full_name'] ?></td>
                            <td class="email"><?= $staff['email'] ?></td>
                            <td class="phone_number"><?= $staff['phone_number'] ?></td>
                            <td class="designation"><?= $staff['designation'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-edit">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $staff['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>


    <!-- Bootstrap modal for edit staff -->
    <div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">Edit Staff</h5>
                    <button onclick="closeStaffEditModal()" type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editStaffForm" method="post" action="manage_staff.php">
                        <!-- Input fields for editing staff details -->
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="editUsername" name="editUsername" required>
                        </div>
                        <div class="mb-3">
                            <label for="editFullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="editFullName" name="editFullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="editEmail" name="editEmail" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPhoneNumber" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="editPhoneNumber" name="editPhoneNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDesignation" class="form-label">Designation</label>
                            <select class="form-select" id="editDesignation" name="editDesignation" required>
                                <option value="teaching">Teaching</option>
                                <option value="non_teaching">Non-Teaching</option>
                                <option value="dignitary">Dignitary</option>
                            </select>
                        </div>
                        <!-- Hidden input field for staff ID -->
                        <input type="hidden" id="editStaffId" name="editStaffId">
                        <button type="submit" name="editStaff" class="btn btn-primary">Update</button>
                        <button onclick="closeStaffEditModal()" type="button" class="btn btn-danger">Cancel</button>
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
                    <p>Are you sure you want to delete this Staff Member Account?</p>
                    <!-- Form for deletion confirmation -->
                    <form id="deleteForm" method="post" action="manage_staff.php">
                        <input type="hidden" name="deleteStaffId" id="deleteStaffId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <?php $conn->close(); ?>
</body>

</html>