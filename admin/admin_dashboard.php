<?php
// Start session to check if admin is logged in
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Include database configuration
require_once '../config.php';

// Retrieve admin information from the database
$admin_id = $_SESSION['admin_id'];
$query = "SELECT * FROM admins WHERE id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>
        /* Set the width of the sidebar */
        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #f8f9fa;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }


        /* Toggle button icon */
        .sidebar-button .fas {
            font-size: 24px;
        }

        /* Toggle button inside sidebar */
        .close-sidebar {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        /* Content area */
        .content-area {
            margin-left: 250px;
            transition: margin-left 0.5s;
            padding: 16px;
        }

        #main-content {
            margin-left: 0px;
        }
    </style>
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
                        <a class="btn btn-danger" href="admin_logout.php">Admin Logout ( <?php echo $_SESSION['admin_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container my-5" id="main-content">
        <h1>Welcome, <?php echo $admin['username']; ?></h1>
        <p>This is the Admin Dashboard!</p>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p>Welcome to the Admin Dashboard. As an administrator, you have access to a wide range of features to manage the institution's operations effectively. You can add, remove, and edit student and staff member profiles, as well as manage other administrators. Additionally, you can oversee attendance tracking by marking and editing attendances, and exporting attendance data as PDF for analysis.

                        Furthermore, you have the ability to manage batches, courses, classes, and subjects, allowing you to organize and structure the educational curriculum efficiently. Whether it's adding new batches, removing outdated courses, or editing class schedules, you have the tools to streamline administrative tasks and ensure smooth operations.

                        Should you require any assistance or encounter any issues while using the dashboard, please don't hesitate to reach out for support. Our team is here to assist you and ensure that your administrative tasks are carried out seamlessly.</p>
                </div>
            </div>
        </div>

        <a href="admin_logout.php" class="btn btn-danger">Logout</a>
        <button class="btn btn-success" id="toggleSidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i>&nbsp Sidebar</button>
    </div>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="#" class="close-sidebar" onclick="toggleSidebar()"><i class="fas fa-times"></i></a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="manage_attendance">Manage Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="edit_attendance">Edit Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL ?>/admin/manage_batches">Manage Batches</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL ?>/admin/manage_courses">Manage Courses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_branches">Manage Branches</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_classes">Manage Classes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="promote_classes">Promote Classes</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_subjects">Manage Subjects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="assign_subjects">Assign Subjects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_staff">Manage Staff</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_students">Manage Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manage_timeTable">Manage Time Table</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="accountSettings">Account Settings</a>
            </li>
        </ul>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            sidebar.style.left = '-250px'; // Ensure sidebar is initially hidden
            mainContent.style.marginLeft = '0'; // Ensure main content is initially positioned correctly
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');

            if (sidebar.style.left === '-250px') {
                sidebar.style.left = '0';
                mainContent.style.marginLeft = '250px';
            } else {
                sidebar.style.left = '-250px';
                mainContent.style.marginLeft = '0';
            }
        }
    </script>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>


    <!-- Footer -->
    <?php include ROOT_PATH . '/includes/footer.php'; ?>

    <!-- Include Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>