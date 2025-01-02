<?php
// Database connection
$host = '127.0.0.1';
$db = 'tugaon_db'; // Ensure this matches your database name
$user = 'root';
$pass = '';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin data
$sql = "SELECT * FROM admin WHERE id = 1";
$result = $conn->query($sql);
$admin = $result->fetch_assoc();

// Update admin data if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = isset($_POST['password']) ? $conn->real_escape_string($_POST['password']) : '';

    // Handle file upload for profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);

        // Update the database with the new profile picture path
        $update_sql = "UPDATE admin SET firstname='$firstname', lastname='$lastname', email='$email', profile_picture='$target_file', password='$password' WHERE id=1";
    } else {
        // Update without changing the profile picture
        $update_sql = "UPDATE admin SET firstname='$firstname', lastname='$lastname', email='$email', password='$password' WHERE id=1";
    }

    if ($conn->query($update_sql) === TRUE) {
        echo "<p>Profile updated successfully.</p>";
        $admin = array_merge($admin, $_POST); // Update local admin array
        if (isset($target_file)) {
            $admin['profile_picture'] = $target_file; // Update the local picture path
        }
    } else {
        echo "<p>Error updating profile: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin_profile.css">
    <title>Admin Profile</title>

</head>
<body>

<div class="container">
    <h1>Admin Profile</h1>
    <?php if ($admin): ?>
        <img class="profile-picture" src="<?php echo htmlspecialchars($admin['profile_picture']); ?>" alt="Profile Picture">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label for="firstname">First Name:</label>
                <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($admin['firstname']); ?>" required>
            </div>
            <div class="form-row">
                <label for="lastname">Last Name:</label>
                <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($admin['lastname']); ?>" required>
            </div>
            <div class="form-row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>
            </div>
            <div class="form-row">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter new password">
            </div>
            <div class="form-row">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" id="profile_picture" name="profile_picture">
            </div>
            <div class="form-row">
                <input type="submit" class="update-button" value="Update Profile">
            </div>
        </form>
        <a class="cancel-link" href="admin.php">Cancel</a>
    <?php else: ?>
        <p>No admin found.</p>
    <?php endif; ?>
</div>

</body>
</html>