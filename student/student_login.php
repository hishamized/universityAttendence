<?php
session_start();



// Include database configuration
require_once '../config.php';

// Check if student is already logged in, redirect to dashboard if true
if (isset($_SESSION['student_id'])) {
    // header("Location: student_dashboard.php");
    header("Location: student_dashboard.php");
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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = htmlspecialchars(trim($_POST['identifier'])); // Can be either username or email
    $password = htmlspecialchars(trim($_POST['password']));

    if (strlen($identifier) == 0 || strlen($password) == 0) {
        $error_message[] = "Either username/email or password field was empty. Please fill the login form completely.";
    }

    // Validate student credentials
    $query = "SELECT * FROM students WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $identifier, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $student = $result->fetch_assoc();
        if (password_verify($password, $student['password'])) {
            // student authenticated, create session and redirect to dashboard
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_username'] = $student['username'];
            $_SESSION['student_name'] = $student['full_name'];
            $_SESSION['student_logged_in'] = true;
            header("Location: ../student/student_dashboard.php");
            exit();
        } else {
            $error_message[] = "Invalid password";
        }
    } else {
        $error_message[] = "student not found. Check login credentials.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
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
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Student Login</h3>
                        <?php if (isset($error_message)) : ?>
                            <div class="alert alert-danger">
                                <?php foreach($error_message as $error){
                                     echo($error);
                                     echo("<br>");
                                      } ?>
                                      </div>
                        <?php endif; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="identifier" class="form-label">Username or Email</label>
                                <input type="text" class="form-control" id="identifier" name="identifier" >
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" >
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>