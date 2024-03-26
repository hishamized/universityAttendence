<?php

// Include database configuration
require_once('config.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance App</title>
    <!-- Include any CSS files -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <script src="https://kit.fontawesome.com/1bc2765d38.js" crossorigin="anonymous"></script>

    <style>
        /* Center the text */
        .centered {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
        }
        #imageContainer{
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
            border-radius: 5px;
            min-height: 500px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            margin: 50px;
        }
    </style>
</head>

<body>
    <!-- Include navbar -->
    <?php include 'includes/navbar.php'; ?>

     <!-- Page content -->
     <div class="container">
        <div class="row">
            <div class="col-md-12">
                <!-- Placeholder for the image -->
                <div id="imageContainer" class="position-relative">
                    <div id="imageText" class="centered">
                        <h1>ATTENDANCE MANAGEMENT WEB APPLICATION</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Include footer -->
    <?php include 'includes/footer.php'; ?>


    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script>
    // Function to fetch a random education-related image from Unsplash API
    function fetchRandomImage() {
    var imageUrl = 'https://source.unsplash.com/featured/?education';
    $('#imageContainer').css('background-image', 'linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(' + imageUrl + ')');
}
    // Call the fetchRandomImage function on page load
    $(document).ready(function() {
        fetchRandomImage();
    });
</script>

</body>

</html>