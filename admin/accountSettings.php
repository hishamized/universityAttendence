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


$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);


if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['editProfile'])) {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $status = htmlspecialchars(trim($_POST['status']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));

    $query = "UPDATE admins SET full_name = '$full_name', status = '$status', email = '$email', phone_number = '$phone_number' WHERE id = '$admin_id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $_SESSION['success'] = 'Profile updated successfully';
        header('Location: accountSettings.php');
        exit();
    } else {
        $_SESSION['error'] = 'Failed to update profile';
        header('Location: accountSettings.php');
        exit();
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['changePassword'])) {
    $current_password = htmlspecialchars(trim($_POST['current_password']));
    $new_password = htmlspecialchars(trim($_POST['new_password']));
    $confirm_new_password = htmlspecialchars(trim($_POST['confirm_new_password']));

    if ($new_password !== $confirm_new_password) {
        $_SESSION['error'] = 'New password and confirm new password do not match';
        header('Location: accountSettings.php');
        exit();
    }

    if (!preg_match('/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/', $new_password)) {
        $_SESSION['error'] = 'Password should be Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character';
        header('Location: accountSettings.php');
        exit();
    }

    $query = "SELECT password FROM admins WHERE id = '$admin_id'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);

    if (password_verify($current_password, $admin['password'])) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE admins SET password = '$new_password' WHERE id = '$admin_id'";
        $result = mysqli_query($conn, $query);

        if ($result) {
            $_SESSION['success'] = 'Password changed successfully';
            header('Location: accountSettings.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to change password';
            header('Location: accountSettings.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'Current password is incorrect';
        header('Location: accountSettings.php');
        exit();
    }
}




if (isset($_POST['deleteAdminId']) && isset($_POST['adminPassword'])) {
    
    $countQuery = "SELECT COUNT(*) AS adminCount FROM admins";
    $countResult = $conn->query($countQuery);

    if ($countResult && $countResult->num_rows > 0) {
        $row = $countResult->fetch_assoc();
        $adminCount = $row['adminCount'];

        
        if ($adminCount <= 1) {
            
            $_SESSION['error'] = "Cannot delete the last admin.";
            header("Location: accountSettings.php");
            exit();
        } else {
            
            $adminId = $_POST['deleteAdminId'];
            $adminPassword = $_POST['adminPassword'];

            $fetchAdminQuery = "SELECT password FROM admins WHERE id = '$adminId'";
            $fetchAdminResult = $conn->query($fetchAdminQuery);
            $adminDbPassword = $fetchAdminResult->fetch_assoc()['password'];

            $validPassword = password_verify($adminPassword, $adminDbPassword);

            if ($validPassword) {
                $_SESSION['success'] = "Admin account deleted successfully.";
                header("Location: admin_logout.php");
                
                $deleteQuery = "DELETE FROM admins WHERE id = ?";
                $stmt = $conn->prepare($deleteQuery);
                $stmt->bind_param("i", $adminId);
                $stmt->execute();

                
                
                exit();
            } else {
                
                $_SESSION['error'] = "Invalid admin password.";
                header("Location: accountSettings.php");
                exit();
            }
        }
    } else {
        
        $_SESSION['error'] = "Error occurred while fetching admin count.";
        header("Location: accountSettings.php");
        exit();
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

    <script type="text/javascript" src="../js/admin/accountSettings.js"></script>
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

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                Admin Profile
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Username: <?php echo $admin['username']; ?></h5>
                        <p class="card-text">Full Name: <?php echo $admin['full_name']; ?></p>
                        <p class="card-text">Email: <?php echo $admin['email']; ?></p>
                        <p class="card-text">Phone Number: <?php echo $admin['phone_number']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">Status: <?php echo ucfirst($admin['status']); ?></p>
                        <p class="card-text">Privilege: <?php echo ucfirst($admin['privilege']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <button class="btn btn-primary mb-3" id="editProfileBtn">Edit Profile</button>

        <form action="accountSettings.php" method="POST" id="editProfileForm" style="display: none;">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo $admin['full_name']; ?>">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="active" <?php if ($admin['status'] === 'active') echo 'selected'; ?>>Active</option>
                    <option value="inactive" <?php if ($admin['status'] === 'inactive') echo 'selected'; ?>>Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $admin['email']; ?>">
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="<?php echo $admin['phone_number']; ?>">
            </div>
            <button type="submit" name="editProfile" class="btn btn-primary">Submit</button>
            <button onclick="hideEditForm(editProfileForm)" class="btn btn-danger">Cancel</button>
        </form>
    </div>

    <div class="container mt-5">
        <button class="btn btn-primary mb-3" id="changePasswordBtn">Change Password</button>

        <!-- Change Password Form -->
        <form action="accountSettings.php" method="POST" style="display: none;" id="changePasswordForm">
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input type="password" class="form-control" id="currentPassword" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" class="form-control" id="newPassword" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirmNewPassword">Confirm New Password</label>
                <input type="password" class="form-control" id="confirmNewPassword" name="confirm_new_password" required>
            </div>
            <button type="submit" name="changePassword" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container mt-5">
    <button type="button" class="btn btn-danger" onclick="openDeleteModal()">
        Delete Account
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>
            <div class="modal-body">
                <form id="deleteForm" method="POST" action="accountSettings.php">
                    <div class="form-group">
                        <label for="adminPassword">Enter Admin Password:</label>
                        <input type="password" class="form-control" id="adminPassword" name="adminPassword" required>
                    </div>
                    <!-- Hidden input for admin ID -->
                    <input type="hidden" name="deleteAdminId" id="deleteAdminId" value="<?php echo $admin['id'] ?>">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitForm()">Confirm Delete</button>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>