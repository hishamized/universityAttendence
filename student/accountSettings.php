<?php
session_start();


if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
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


$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM students WHERE id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);


if ($_SERVER['REQUEST_METHOD'] == 'POST'  && isset($_POST['editProfile'])) {
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));

    $query = "UPDATE students SET full_name = '$full_name', email = '$email', phone_number = '$phone_number' WHERE id = '$student_id'";
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

    $query = "SELECT password FROM students WHERE id = '$student_id'";
    $result = mysqli_query($conn, $query);
    $student = mysqli_fetch_assoc($result);

    if (password_verify($current_password, $student['password'])) {
        $new_password = password_hash($new_password, PASSWORD_DEFAULT);
        $query = "UPDATE students SET password = '$new_password' WHERE id = '$student_id'";
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

    <script type="text/javascript" src="../js/student/accountSettings.js"></script>
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
                        <a class="nav-link" href="student_dashboard.php">student Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="student_logout.php">Logout student ( <?php echo $_SESSION['student_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- End Navbar -->

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                student Profile
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="card-title">Username: <?php echo $student['username']; ?></h5>
                        <p class="card-text">Full Name: <?php echo $student['full_name']; ?></p>
                        <p class="card-text">Email: <?php echo $student['email']; ?></p>
                        <p class="card-text">Phone Number: <?php echo $student['phone_number']; ?></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text">Address: <?php echo $student['address']; ?></p>
                        <p class="card-text">Session: <?php echo $student['session']; ?></p>
                        <p class="card-text">University Enroll: <?php echo $student['university_enroll']; ?></p>
                        <p class="card-text">Registration Number: <?php echo $student['registration_number']; ?></p>
                        <p class="card-text">Class Roll Number: <?php echo $student['class_roll_number']; ?></p>
                        <p class="card-text">Library Card Number: <?php echo $student['library_card_number']; ?></p>
                        <p class="card-text">Course: <?php echo $student['course']; ?></p>
                        <p class="card-text">Branch: <?php echo $student['branch']; ?></p>
                        <p class="card-text">Validity: <?php echo $student['validity']; ?></p>
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
                <input type="text" class="form-control" id="fullName" name="full_name" value="<?php echo $student['full_name']; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $student['email']; ?>">
            </div>
            <div class="form-group">
                <label for="phoneNumber">Phone Number</label>
                <input type="text" class="form-control" id="phoneNumber" name="phone_number" value="<?php echo $student['phone_number']; ?>">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>