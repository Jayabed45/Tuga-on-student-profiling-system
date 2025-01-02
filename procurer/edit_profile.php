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
$sql = "SELECT id, firstname, last_name, age, gender, student_year, section_of_student, address, profile_picture, password FROM students WHERE email = '$email'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

// Update student details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile details
    if (isset($_POST['update_profile'])) {
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $age = $_POST['age'];
        $gender = $_POST['gender'];
        $student_year = $_POST['student_year'];
        $section_of_student = $_POST['section_of_student'];
        $address = $_POST['address'];

        // Handle profile picture upload
        $profile_picture = $student['profile_picture'];  // Default to current picture

        if (!empty($_FILES['profile_picture']['name'])) {
            $profile_picture = $_FILES['profile_picture']['name'];
            $target_dir = "uploads/";

            // Check if the directory exists, if not, create it
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $target_file = $target_dir . basename($profile_picture);

            // Check for upload errors
            if ($_FILES['profile_picture']['error'] !== UPLOAD_ERR_OK) {
                echo "Error uploading file: " . $_FILES['profile_picture']['error'];
                exit;
            }

            // Check if file is an image
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
            
            if (!in_array($file_type, $allowed_types)) {
                echo "Invalid file type. Please upload an image (JPEG, PNG, GIF).";
                exit;
            }

            // Move the uploaded file
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                echo "Error uploading file.";
                exit;
            }
        }

        // Update the database with the new information
        $sql = "UPDATE students SET firstname='$firstname', last_name='$lastname', age='$age', gender='$gender', student_year='$student_year', section_of_student='$section_of_student', address='$address', profile_picture='$target_file' WHERE email='$email'";

        if ($conn->query($sql) === TRUE) {
            echo "Profile updated successfully!";
            header("Location: student_home.php");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }

    // Change Password Logic
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Verify current password
        if ($current_password === $student['password']) {  // Assuming password is stored in plain text
            if ($new_password === $confirm_password) {
                // Update the password in the database
                $sql = "UPDATE students SET password='$new_password' WHERE email='$email'";
                if ($conn->query($sql) === TRUE) {
                    echo "Password changed successfully!";
                } else {
                    echo "Error: " . $conn->error;
                }
            } else {
                echo "New passwords do not match!";
            }
        } else {
            echo "Current password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/edit.css">
</head>
<body>
    <h1>Edit Profile</h1>
    <div class="profile-container">
        <div class="profile-header">
            <!-- Display the current profile picture -->
            <img src="<?php echo htmlspecialchars($student['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($student['firstname']) . ' ' . htmlspecialchars($student['last_name']); ?></h2>
            <p><?php echo htmlspecialchars($email); ?></p>
        </div>

        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <!-- Form fields for student details -->
            <div class="form-row">
                <label for="firstname">First Name:</label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($student['firstname']); ?>" required>
            </div>

            <div class="form-row">
                <label for="lastname">Last Name:</label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>

            <div class="form-row">
                <label for="age">Age:</label>
                <input type="number" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
            </div>

            <div class="form-row">
                <label for="gender">Gender:</label>
                <select name="gender" required>
                    <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <div class="form-row">
                <label for="student_year">Year:</label>
                <select name="student_year" required>
                    <option value="1st Year" <?php if ($student['student_year'] == '1st Year') echo 'selected'; ?>>1st Year</option>
                    <option value="2nd Year" <?php if ($student['student_year'] == '2nd Year') echo 'selected'; ?>>2nd Year</option>
                    <option value="3rd Year" <?php if ($student['student_year'] == '3rd Year') echo 'selected'; ?>>3rd Year</option>
                    <option value="4th Year" <?php if ($student['student_year'] == '4th Year') echo 'selected'; ?>>4th Year</option>
                </select>
            </div>

            <div class="form-row">
                <label for="section_of_student">Section:</label>
                <select name="section_of_student" required>
                    <option value="Section A" <?php if ($student['section_of_student'] == 'Section A') echo 'selected'; ?>>Section A</option>
                    <option value="Section B" <?php if ($student['section_of_student'] == 'Section B') echo 'selected'; ?>>Section B</option>
                    <option value="Section C" <?php if ($student['section_of_student'] == 'Section C') echo 'selected'; ?>>Section C</option>
                    <option value="Section D" <?php if ($student['section_of_student'] == 'Section D') echo 'selected'; ?>>Section D</option>
                    <option value="Section E" <?php if ($student['section_of_student'] == 'Section E') echo 'selected'; ?>>Section E</option>
                </select>
            </div>

            <div class="form-row">
                <label for="address">Address:</label>
                <textarea name="address" required><?php echo htmlspecialchars($student['address']); ?></textarea>
            </div>

            <div class="form-row">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture">
            </div>

            <div class="form-row">
                <input type="submit" name="update_profile" value="Update Profile">
            </div>

            <h2>Change Password</h2>
            <div class="form-row">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" required>
            </div>

            <div class="form-row">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" required>
            </div>

            <div class="form-row">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-row">
                <input type="submit" name="change_password" value="Change Password">
            </div>
        </form>

        <a href="student_home.php" class="cancel-link"><i class="fas fa-times"></i> Cancel</a>
    </div>
</body>
</html>

<?php
$conn->close();
?>