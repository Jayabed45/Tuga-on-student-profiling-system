<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'Student') {
    header("Location: login.php");
    exit;
}

// Database connection
$host = '127.0.0.1';
$db = 'tugaon_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch student details
$email = $_SESSION['email'];
$sql = "SELECT id, firstname, last_name, age, gender, student_year, section_of_student, address, profile_picture FROM students WHERE email = '$email'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/student_profile.css">
    <title>My Profile</title>

</head>
<body>
    <h1>View Profile</h1>
    <div class="profile-container">
        <div class="profile-header">
            <!-- Display the current profile picture -->
            <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($student['firstname']) . ' ' . htmlspecialchars($student['last_name']); ?></h2>
            <p><?php echo htmlspecialchars($email); ?></p>
        </div>

        <div class="profile-details">
            <div class="form-row">
                <label>First Name:</label>
                <span><?php echo htmlspecialchars($student['firstname']); ?></span>
            </div>

            <div class="form-row">
                <label>Last Name:</label>
                <span><?php echo htmlspecialchars($student['last_name']); ?></span>
            </div>

            <div class="form-row">
                <label>Age:</label>
                <span><?php echo htmlspecialchars($student['age']); ?></span>
            </div>

            <div class="form-row">
                <label>Gender:</label>
                <span><?php echo htmlspecialchars($student['gender']); ?></span>
            </div>

            <div class="form-row">
                <label>Year:</label>
                <span><?php echo htmlspecialchars($student['student_year']); ?></span>
            </div>

            <div class="form-row">
                <label>Section:</label>
                <span><?php echo htmlspecialchars($student['section_of_student']); ?></span>
            </div>

            <div class="form-row">
                <label>Address:</label>
                <span><?php echo nl2br(htmlspecialchars($student['address'])); ?></span>
            </div>

            <div class="form-row">
                <label>Profile Picture:</label>
                <span><?php echo htmlspecialchars($student['profile_picture']); ?></span>
            </div>
        </div>
        
        <a href="student_home.php" class="cancel-link"><i class="fas fa-times"></i> Back to Home</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>