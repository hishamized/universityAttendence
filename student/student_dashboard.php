<?php
// Start session to check if student is logged in
session_start();

// Check if student is not logged in, redirect to login page
if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

// Include database configuration
require_once '../config.php';

// Retrieve student information from the database
$student_id = $_SESSION['student_id'];
$query = "SELECT * FROM students WHERE id = '$student_id'";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>student Dashboard</title>
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
                        <a class="btn btn-danger" href="student_logout.php">student Logout ( <?php echo $_SESSION['student_username'] ?> )</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container my-5" id="main-content">
        <h1>Welcome, <?php echo $student['username']; ?></h1>
        <p>This is the student Dashboard!</p>
        <a href="student_logout.php" class="btn btn-danger">Logout</a>
        <button class="btn btn-success" id="toggleSidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i>&nbsp Sidebar</button>
        <div class="container my-3">
            <div class="row">
                <div class="col-md-12">
                    <p>Welcome to your Student Dashboard. Here, you can manage your profile information, monitor your attendance records, and stay updated on your academic progress. Use the provided options to view your attendance history, update your personal details, and access any additional resources provided by the institution. If you have any questions or need assistance, feel free to reach out to your teachers or the administrative staff for support.</p>
                </div>
            </div>
        </div>
    </div>


    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <a href="#" class="close-sidebar" onclick="toggleSidebar()"><i class="fas fa-times"></i></a>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="my_attendance.php">My Attendance</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="time_table.php">View Time Table</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="accountSettings.php">Account Settings</a>
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