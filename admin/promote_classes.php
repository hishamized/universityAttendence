<?php
session_start();

// Check if admin is not logged in, redirect to login page
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
        <div class="card-body">
            <h2 class="card-title">Promote Students</h2>
            <form id="promoteForm">
                <div class="mb-3">
                    <label for="fromClass" class="form-label">From Class:</label>
                    <select id="fromClass" class="form-select" onchange="populateToClasses()">
                    <option value="">Select Class</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>
                <div class="mb-3">
                    <label for="toClass" class="form-label">To Class:</label>
                    <select id="toClass" class="form-select">
                        <!-- Options will be populated based on selected 'fromClass' -->
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Promote Students</button>
            </form>
        </div>
    </div>
</div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        function populateFromClassOptions() {
            var fromClassSelect = document.getElementById("fromClass");
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "ajax_get_classes.php", true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var classes = JSON.parse(xhr.responseText);
                    classes.forEach(function(classObj) {
                        var option = document.createElement("option");
                        option.text = classObj.name;
                        option.value = classObj.id;
                        fromClassSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }

        function populateToClasses() {
            var fromClassId = document.getElementById("fromClass").value;
            var toClassSelect = document.getElementById("toClass");
            toClassSelect.innerHTML = ""; // Clear previous options

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "ajax_get_classes_by_course_branch_batch.php?class_id=" + fromClassId, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var classes = JSON.parse(xhr.responseText);
                    classes.forEach(function(classObj) {
                        var option = document.createElement("option");
                        option.text = classObj.name;
                        option.value = classObj.id;
                        toClassSelect.appendChild(option);
                    });
                }
            };
            xhr.send();
        }

        document.getElementById("promoteForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var fromClassId = document.getElementById("fromClass").value;
            var toClassId = document.getElementById("toClass").value;

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax_promote_students.php", true);
            xhr.setRequestHeader("Content-Type", "application/json");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    alert(xhr.responseText);
                }
            };
            xhr.send(JSON.stringify({
                from_class_id: fromClassId,
                to_class_id: toClassId
            }));
        });

        // Populate 'From Class' options on page load
        populateFromClassOptions();
    </script>
</body>

</html>