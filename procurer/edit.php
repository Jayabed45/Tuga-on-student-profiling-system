<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'Admin') {
    header("location: login.php");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $student_year = $_POST['student_year'];
    $section_of_student = $_POST['section_of_student']; // New field
    $address = $_POST['address'];
    $email = $_POST['email'];

    $profile_picture = $_FILES['profile_picture']['name'];
    $target_dir = "uploads/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $target_file = $target_dir . basename($profile_picture);

    if (!empty($profile_picture)) {
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
    } else {
        $result = $conn->query("SELECT profile_picture FROM students WHERE id = $id");
        $row = $result->fetch_assoc();
        $target_file = $row['profile_picture'];
    }

    $updateSql = "UPDATE students SET firstname='$firstname', last_name='$lastname', age='$age', gender='$gender', student_year='$student_year', section_of_student='$section_of_student', address='$address', email='$email', profile_picture='$target_file' WHERE id=$id";
    $conn->query($updateSql);
    header("Location: admin.php");
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM students WHERE id = $id";
$result = $conn->query($sql);
$student = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/edit.css">
</head>
<body>
    <h1>Edit Student</h1>
    <div class="form-container">
        <form action="edit.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

            <!-- Section A: Email -->
            <div class="form-row">
                <label for="email"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
            </div>

            <!-- Section B: First Name -->
            <div class="form-row">
                <label for="firstname"><i class="fas fa-user"></i> First Name:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
            </div>

            <!-- Section C: Last Name -->
            <div class="form-row">
                <label for="lastname"><i class="fas fa-user"></i> Last Name:</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <!-- Section D: Year -->
            <div class="form-row">
                <label for="student_year"><i class="fas fa-graduation-cap"></i> Year:</label>
                <select name="student_year" required>
                    <option value="1st Year" <?php if ($student['student_year'] == '1st Year') echo 'selected'; ?>>1st Year</option>
                    <option value="2nd Year" <?php if ($student['student_year'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                    <option value="3rd Year" <?php if ($student['student_year'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                    <option value="4th Year" <?php if ($student['student_year'] == '4th Year') echo 'selected'; ?>>4th Year</option>
                </select>
            </div>
            
            <!-- Section E: Section of Student -->
            <div class="form-row">
                <label for="section_of_student"><i class="fas fa-book"></i> Section:</label>
                <select name="section_of_student" required>
                    <option value="Section A" <?php if ($student['section_of_student'] == 'Section A') echo 'selected'; ?>>Section A</option>
                    <option value="Section B" <?php if ($student['section_of_student'] == 'Section B') echo 'selected'; ?>>Section B</option>
                    <option value="Section C" <?php if ($student['section_of_student'] == 'Section C') echo 'selected'; ?>>Section C</option>
                    <option value="Section D" <?php if ($student['section_of_student'] == 'Section D') echo 'selected'; ?>>Section D</option>
                    <option value="Section E" <?php if ($student['section_of_student'] == 'Section E') echo 'selected'; ?>>Section E</option>
                </select>
            </div>

            <!-- Additional Info -->
            <div class="form-row">
                <label for="age"><i class="fas fa-calendar-alt"></i> Age:</label>
                <input type="number" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
            </div>

            <div class="form-row">
                <label for="gender"><i class="fas fa-venus-mars"></i> Gender:</label>
                <select name="gender" required>
                    <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <div class="form-row">
                <label for="address"><i class="fas fa-home"></i> Address:</label>
                <textarea name="address" required><?php echo htmlspecialchars($student['address']); ?></textarea>
            </div>

            <div class="form-row">
                <label for="profile_picture"><i class="fas fa-image"></i> Profile Picture:</label>
                <input type="file" name="profile_picture">
            </div>
            <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Current Profile Picture" class="profile-picture"><br>

            <input type="submit" value="Update Student">
        </form>
        <a href="admin.php" class="cancel-link"><i class="fas fa-times"></i> Cancel</a>
    </div>
    
</body>
</html>

<?php
$conn->close();
?>