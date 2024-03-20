<?php
session_start();

// Check if admin is not logged in, redirect to login page
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
require_once('../config.php');

// Fetch classes from the database
$sql = "SELECT 
classes.id AS class_id,
classes.name,
classes.semester,
batches.year AS batch_year,
branches.branch_name,
courses.course_name
FROM 
classes
JOIN 
batches ON classes.batch_id = batches.id
JOIN 
branches ON classes.branch_id = branches.id
JOIN 
courses ON classes.course_id = courses.id;
";
$result = mysqli_query($conn, $sql);
$classOptions = mysqli_fetch_all($result, MYSQLI_ASSOC);


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addStudent'])) {
    // Trim and sanitize input data
    $username = htmlspecialchars(trim($_POST['username']));
    $full_name = htmlspecialchars(trim($_POST['full_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $address = htmlspecialchars(trim($_POST['address']));
    $university_enroll = htmlspecialchars(trim($_POST['university_enroll']));
    $registration_number = htmlspecialchars(trim($_POST['registration_number']));
    $class_roll_number = htmlspecialchars(trim($_POST['class_roll_number']));
    $library_card_number = htmlspecialchars(trim($_POST['library_card_number']));
    $validity = htmlspecialchars(trim($_POST['validity']));
    $class_id = htmlspecialchars(trim($_POST['class_id']));
    $session = htmlspecialchars(trim($_POST['session']));
    $course = htmlspecialchars(trim($_POST['course']));
    $branch = htmlspecialchars(trim($_POST['branch']));

    // Check if username already exists
    $query = "SELECT * FROM students WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo '<div class="alert alert-danger" role="alert">Username already exists</div>';
    } else {
        // Check if email already exists
        $query = "SELECT * FROM students WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo '<div class="alert alert-danger" role="alert">Email already exists</div>';
        } else {
            // Check if password and confirm password match
            if ($password !== $confirm_password) {
                echo '<div class="alert alert-danger" role="alert">Password and confirm password do not match</div>';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Insert the student into the database
                $query = "INSERT INTO students (username, full_name, email, password, phone_number, address, university_enroll, registration_number, class_roll_number, library_card_number, validity, class_id, session, course, branch) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "sssssssssssssss", $username, $full_name, $email, $hashed_password, $phone_number, $address, $university_enroll, $registration_number, $class_roll_number, $library_card_number, $validity, $class_id, $session, $course, $branch);
                if (mysqli_stmt_execute($stmt)) {
                    echo '<div class="alert alert-success" role="alert">Student added successfully</div>';
                } else {
                    echo '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($conn) . '</div>';
                }
            }
        }
    }
    // Close statement
    mysqli_stmt_close($stmt);
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editStudent'])){
    // Trim and sanitize input data
    $id = htmlspecialchars(trim($_POST['editId']));
    $username = htmlspecialchars(trim($_POST['editUsername']));
    $full_name = htmlspecialchars(trim($_POST['editFullName']));
    $email = htmlspecialchars(trim($_POST['editEmail']));
    $phone_number = htmlspecialchars(trim($_POST['editPhoneNumber']));
    $address = htmlspecialchars(trim($_POST['editAddress']));
    $university_enroll = htmlspecialchars(trim($_POST['editUniversityEnroll']));
    $registration_number = htmlspecialchars(trim($_POST['editRegistrationNumber']));
    $class_roll_number = htmlspecialchars(trim($_POST['editClassRollNumber']));
    $library_card_number = htmlspecialchars(trim($_POST['editLibraryCardNumber']));
    $validity = htmlspecialchars(trim($_POST['editValidity']));
   

    // Update the student in the database
    $query = "UPDATE students SET username = ?, full_name = ?, email = ?, phone_number = ?, address = ?, university_enroll = ?, registration_number = ?, class_roll_number = ?, library_card_number = ?, validity = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssssssssi", $username, $full_name, $email, $phone_number, $address, $university_enroll, $registration_number, $class_roll_number, $library_card_number, $validity, $id);
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="alert alert-success" role="alert">Student updated successfully</div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">Error: ' . mysqli_error($conn) . '</div>';
    }
    // Close statement
    mysqli_stmt_close($stmt);

}

//Fetch data about students
$query = "SELECT * FROM students";
$result = mysqli_query($conn, $query);

// Check if any students are found
if (mysqli_num_rows($result) > 0) {
    // Initialize an empty array to store student data
    $students = array();

    // Fetch each row of student data
    while ($row = mysqli_fetch_assoc($result)) {
        // Add the row to the students array
        $students[] = $row;
    }
} else {
    $status = "No students found";
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        .hidden {
            display: none;
        }

        .address-cell {
            max-width: 200px;
            /* Set the maximum width for the cell */
            white-space: nowrap;
            /* Prevent the text from wrapping */
            overflow: hidden;
            /* Hide any overflowing text */
            text-overflow: ellipsis;
            /* Display ellipsis (...) for truncated text */
        }

        .address-cell:hover {
            max-width: none;
            /* Remove the maximum width on hover */
            white-space: normal;
            /* Allow the text to wrap */
            overflow: visible;
            /* Show overflowing text */
            background-color: #f0f0f0;
            /* Add background color on hover */
        }

        .table-container {
            max-width: 90vw;
            overflow-x: scroll;
        }
    </style>
    <script type="text/javascript" src="../js/admin/manage_students.js"></script>
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


    <div class="container mt-5">
        <!-- Button to toggle the form -->
        <button type="button" id="toggleFormBtn" class="btn btn-primary">Add a new student</button>

        <!-- Form to add a new student -->
        <form id="studentForm" class="hidden" method="post" action="manage_students.php">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <!-- phone_number -->
            <div class="mb-3">
                <label for="phone_number" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
            </div>
            <!-- address -->
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <!-- university_enroll -->
            <div class="mb-3">
                <label for="university_enroll" class="form-label">University Enrollment</label>
                <input type="text" class="form-control" id="university_enroll" name="university_enroll" required>
            </div>
            <!-- registration_number -->
            <div class="mb-3">
                <label for="registration_number" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="registration_number" name="registration_number" required>
            </div>
            <!-- class_roll_number -->
            <div class="mb-3">
                <label for="class_roll_number" class="form-label">Class Roll Number</label>
                <input type="text" class="form-control" id="class_roll_number" name="class_roll_number" required>
            </div>
            <!-- library_card_number -->
            <div class="mb-3">
                <label for="library_card_number" class="form-label">Library Card Number</label>
                <input type="text" class="form-control" id="library_card_number" name="library_card_number" required>
            </div>
            <!-- validity  (date)-->
            <div class="mb-3">
                <label for="validity" class="form-label">Validity</label>
                <input type="date" class="form-control" id="validity" name="validity" required>
            </div>
            <!-- Fetch class options through PHP script -->
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select class="form-select" id="class_id" name="class_id" required>
                    <option value="" selected disabled>Select Class</option>
                    <?php
                    foreach ($classOptions as $class) {
                        $optionText = $class['name'] . " - " . $class['course_name'] . " (" . $class['branch_name'] . " - " . "SEMESTER: " . $class['semester'] . "-" . "Batch: " . $class['batch_year'] . ")";
                        echo "<option value='" . $class['class_id'] . "' data-batch-year='" . $class['batch_year'] . "' data-course-name='" . $class['course_name'] . "' data-branch-name='" . $class['branch_name'] . "'>" . $optionText . "</option>";
                    }

                    ?>
                </select>
            </div>
            <!-- session (year) -->
            <div class="mb-3">
                <label for="session" class="form-label">Session (Year)</label>
                <input type="text" class="form-control" id="session" name="session" required readonly>
            </div>
            <!-- course -->
            <div class="mb-3">
                <label for="course" class="form-label">Course</label>
                <input type="text" class="form-control" id="course" name="course" required readonly>
            </div>
            <!-- branch -->
            <div class="mb-3">
                <label for="branch" class="form-label">Branch</label>
                <input type="text" class="form-control" id="branch" name="branch" required readonly>
            </div>
            <button type="submit" name="addStudent" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <div class="container m-5 table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Actions</th>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Address</th>
                    <th>Session</th>
                    <th>University Enrollment</th>
                    <th>Registration Number</th>
                    <th>Class Roll Number</th>
                    <th>Library Card Number</th>
                    <th>Validity</th>
                    <th>Course</th>
                    <th>Branch</th>
                    <th>Class ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $student) : ?>
                    <tr id="studentRow<?= $student['id'] ?>">
                        <td>
                            <button class="btn btn-primary my-2" onclick="editStudent(<?= $student['id'] ?>)">Edit</button>
                            <button class="btn btn-danger my-2">Delete</button>
                        </td>
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo $student['username']; ?></td>
                        <td><?php echo $student['full_name']; ?></td>
                        <td><?php echo $student['email']; ?></td>
                        <td><?php echo $student['phone_number']; ?></td>
                        <td class="address-cell"><?php echo $student['address']; ?></td>
                        <td><?php echo $student['session']; ?></td>
                        <td><?php echo $student['university_enroll']; ?></td>
                        <td><?php echo $student['registration_number']; ?></td>
                        <td><?php echo $student['class_roll_number']; ?></td>
                        <td><?php echo $student['library_card_number']; ?></td>
                        <td><?php echo $student['validity']; ?></td>
                        <td><?php echo $student['course']; ?></td>
                        <td><?php echo $student['branch']; ?></td>
                        <td><?php echo $student['class_id']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit student Form -->
    <div id="editStudentFormContainer" class="container m-5 hidden">
        <form id="editStudentForm" method="post" action="manage_students.php">
            <input type="hidden" id="editId" name="editId" value="">
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
                <label for="editAddress" class="form-label">Address</label>
                <input type="text" class="form-control" id="editAddress" name="editAddress" required>
            </div>
            <div class="mb-3">
                <label for="editUniversityEnroll" class="form-label">University Enrollment</label>
                <input type="text" class="form-control" id="editUniversityEnroll" name="editUniversityEnroll" required>
            </div>
            <div class="mb-3">
                <label for="editRegistrationNumber" class="form-label">Registration Number</label>
                <input type="text" class="form-control" id="editRegistrationNumber" name="editRegistrationNumber" required>
            </div>
            <div class="mb-3">
                <label for="editClassRollNumber" class="form-label">Class Roll Number</label>
                <input type="text" class="form-control" id="editClassRollNumber" name="editClassRollNumber" required>
            </div>
            <div class="mb-3">
                <label for="editLibraryCardNumber" class="form-label">Library Card Number</label>
                <input type="text" class="form-control" id="editLibraryCardNumber" name="editLibraryCardNumber" required>
            </div>
            <div class="mb-3">
                <label for="editValidity" class="form-label">Validity</label>
                <input type="date" class="form-control" id="editValidity" name="editValidity" required>
            </div>
            <div class="mb-3">
                <label for="editClassId" class="form-label">Class</label>
                <input type="text" class="form-control" id="editClassId" name="editClassId" disabled readonly>
            </div>
            <div class="mb-3">
                <label for="editSession" class="form-label">Session</label>
                <input type="text" class="form-control" id="editSession" name="editSession" disabled readonly>
            </div>
            <div class="mb-3">
                <label for="editCourse" class="form-label">Course</label>
                <input type="text" class="form-control" id="editCourse" name="editCourse" disabled readonly>
            </div>
            <div class="mb-3">
                <label for="editBranch" class="form-label">Branch</label>
                <input type="text" class="form-control" id="editBranch" name="editBranch" disabled readonly>
            </div>
            <button type="submit" name="editStudent" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-danger" onclick="closeEditForm()">Cancel</button>
        </form>
    </div>


</body>

</html>
<?php $conn->close(); ?>