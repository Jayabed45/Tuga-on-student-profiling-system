<?php
session_start();
$host = '127.0.0.1';
$db = 'tugaon_db';
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// For Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $email = $conn->real_escape_string($email);
    $password = $conn->real_escape_string($password);

    $sql = "SELECT email, password, type_of_user FROM admin WHERE email='$email' AND password='$password'
            UNION ALL
            SELECT email, password, type_of_user FROM students WHERE email='$email' AND password='$password'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['loggedin'] = true;
        $_SESSION['email'] = $user['email'];
        $_SESSION['user_type'] = $user['type_of_user'];

        if ($user['type_of_user'] === 'Admin') {
            header("location: admin.php");
        } else {
            header("location: student_home.php");
        }
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

// For Account Creation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_account'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $student_year = $_POST['student_year'];
    $section = $_POST['section'] ?? '';
    $address = $_POST['address'];
    $email = $_POST['new_email'];
    $password = $_POST['new_password'];
    $type_of_user = $_POST['type_of_user'];

    // Handle file upload
    $profile_picture = $_FILES['profile_picture'];
    $target_dir = "uploads/"; // Directory where images will be saved
    $target_file = $target_dir . basename($profile_picture["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($profile_picture["tmp_name"]);
    if ($check === false) {
        $error = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (5MB maximum)
    if ($profile_picture["size"] > 5000000) {
        $error = "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $error = "Sorry, your file was not uploaded.";
    } else {
        // If everything is ok, try to upload file
        if (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
            // File uploaded successfully, insert user data into the database
            $insert_sql = "INSERT INTO students (email, password, firstname, last_name, age, gender, student_year, section_of_student, address, type_of_user, profile_picture) 
                           VALUES ('$email', '$password', '$first_name', '$last_name', '$age', '$gender', '$student_year', '$section', '$address', '$type_of_user', '$target_file')";
            if ($conn->query($insert_sql) === TRUE) {
                $success = "Account created successfully! You can now log in.";
            } else {
                $error = "Error creating account: " . $conn->error;
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <style>
        .btn-primary {
            background-color: #007bff; /* Bootstrap primary color */
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3; /* Darker shade on hover */
            border-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745; /* Green color */
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838; /* Darker green */
            border-color: #218838;
        }

        .navbar-toggler {
            background-color: #007bff; /* Change toggle button color */
        }
    </style>
</head>

<body>
    <header>
        <div id="navbarHeader" class="collapse bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-sm-8 col-md-7 py-4">
                        <h4 class="text-black">About</h4>
                        <p class="text-muted">The Bachelor of Science in Information Technology (BSIT) is a four-year undergraduate degree program designed to equip students with the knowledge and skills needed to design, develop, and manage computer-based systems. This program focuses on the application of technology to solve real-world problems and streamline processes in businesses, organizations, and society.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="navbar navbar-white bg-white  shadow-sm">
            <div class="container d-flex justify-content-between">
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="assets/img/bsitlogo.png" alt="Logo" style="height: 40px; width: auto;">
                </a>
                <button class="btn navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation"><i class="fa fa-navicon"></i></button>
            </div>
        </div>
    </header>

    <main role="main">
        <!-- Video Background Section -->
        <section class="video-background">
            <video autoplay muted loop>
                <source src="assets/img/bsit.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-content">
                <img src="assets/img/bsitlogo.png" alt="BSIT Logo" style="height: 100px; width: auto; margin-bottom: 20px;">
                <h1>BSIT STUDENT PROFILING SYSTEM</h1>
                <p>The BSIT program provides students with both theoretical foundations and practical experiences, ensuring they are ready for the dynamic and challenging IT industry.</p>
                <a class="btn btn-primary my-2" href="#" data-toggle="modal" data-target="#loginModal">Login Now</a>
            </div>
        </section>

        <!-- Album Section -->
        <div class="album py-5 bg-light">
            <div class="container">
                <div class="row">
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="card mb-4 shadow-sm" data-aos="fade-up" data-aos-delay="100">
                            <img class="bd-placeholder-img" width="100%" height="225" src="assets/img/457634202_387188317753016_6904233616961714361_n.jpg" alt="PSIT Organization">
                            <div class="card-body">
                                <p class="card-text">The BSIT PSIT (Philippine Society of Information Technology Students) is a student-led organization dedicated to fostering excellence.<br></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="card mb-4 shadow-sm">
                            <img class="bd-placeholder-img" width="100%" height="225" src="assets/img/457634994_387186944419820_587316084294693066_n.jpg" alt="Faculty Excellence">
                            <div class="card-body">
                                <p class="card-text">The faculty members of our institution exemplify excellence, dedication, and professionalism, providing students with a strong foundation for success. <br></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="card mb-4 shadow-sm">
                            <img class="bd-placeholder-img" width="100%" height="225" src="assets/img/462231956_414364641702050_1064866973580932518_n.jpg" alt="BSIT Events">
                            <div class="card-body">
                                <p class="card-text">With high-quality speakers, interactive activities, and cutting-edge topics, BSIT events create opportunities for students to enhance their technical expertise.<br></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Login and Create Account Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login or Create Account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Login Form -->
                    <div id="loginForm" class="form-container">
                        <h2>Login</h2>
                        <form method="POST" action="">
                            <div class="form-group">
                                <span class="input-icon"><i class="fa fa-envelope"></i></span>
                                <input type="email" name="email" placeholder="Email" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <span class="input-icon"><i class="fa fa-lock"></i></span>
                                <input type="password" name="password" placeholder="Password" class="form-control mb-2" required>
                            </div>
                            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <p class="mt-2">Don't have an account? <a href="javascript:void(0);" id="createAccountLink">Create one</a></p>
                    </div>

                    <!-- Create Account Form -->
                    <div id="createAccountForm" class="form-container mt-4" style="display: none;">
                        <h3>Create Account</h3>
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <input type="text" name="first_name" placeholder="First Name" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="last_name" placeholder="Last Name" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <input type="number" name="age" placeholder="Age" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <select name="gender" class="form-control mb-2" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="type_of_user">Type of User</label>
                                <select name="type_of_user" id="type_of_user" class="form-control mb-2" required>
                                    <option value="Student">Student</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="student_year">Student Year</label>
                                <select name="student_year" id="student_year" class="form-control mb-2" required>
                                    <option value="" disabled selected>Select Year</option>
                                    <option value="1st Year">1st Year</option>
                                    <option value="2nd Year">2nd Year</option>
                                    <option value="3rd Year">3rd Year</option>
                                    <option value="4th Year">4th Year</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="section">Section</label>
                                <select name="section" id="section" class="form-control mb-2" required>
                                    <option value="" disabled selected>Select Section</option>
                                    <option value="Section A">Section A</option>
                                    <option value="Section B">Section B</option>
                                    <option value="Section C">Section C</option>
                                    <option value="Section D">Section D</option>
                                    <option value="Section E">Section E</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="address" placeholder="Address" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="new_email" placeholder="Email" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="new_password" placeholder="Password" class="form-control mb-2" required>
                            </div>
                            <div class="form-group">
                                <input type="file" name="profile_picture" accept="image/*" class="form-control mb-2" required>
                            </div>
                            <button type="submit" name="create_account" class="btn btn-success btn-block">Create Account</button>
                        </form>
                        <p class="mt-2">Already have an account? <a href="javascript:void(0);" id="loginLink">Login</a></p>
                    </div>


                    <!-- JavaScript Libraries -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.1/aos.js"></script>
                    <script src="assets/js/login.js"></script>
                    <script>
                        AOS.init();
                    </script>
                </body>

</html>