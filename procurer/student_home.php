<?php
session_start();
if (!isset($_SESSION['loggedin'])) {
    header("location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Database connection
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

// Fetch posts from the database, including admin's name
$getPostsSql = "
    SELECT posts.*, admin.firstname, admin.lastname, admin.profile_picture 
    FROM posts 
    JOIN admin ON posts.admin_id = admin.id 
    ORDER BY posts.created_at DESC
";
$result = $conn->query($getPostsSql);
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/posts.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <img src="assets/img/bsitlogo.png" alt="Logo" class="logo-image">
            <span class="logo_name">HOME</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="home.php" class="active">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Home</span>
                </a>
            </li>
            <li>
                <a href="student.php">
                    <i class='bx bx-user'></i>
                    <span class="links_name">View Profile</span>
                </a>
            </li>
            <li>
                <a href="edit_profile.php">
                    <i class='bx bxs-edit-alt'></i>
                    <span class="links_name">Manage Profile</span>
                </a>
            </li>
            <li class="log_out">
                <a href="../logout.php">
                    <i class='bx bx-log-out'></i>
                    <span class="links_name">Log out</span>
                </a>
            </li>
        </ul>
    </div>

    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='#'></i>
                <span class="dashboard">News Feed</span>
            </div>
        </nav>
        <div class="home-content">

            <!-- Posts Section -->
            <div class="posts">
                <?php
                if ($result->num_rows > 0) {
                    while ($post = $result->fetch_assoc()) {
                        echo '<div class="post">';
                        echo '<h3>' . htmlspecialchars($post['content']) . '</h3>';
                        if ($post['image_path']) {
                            echo '<img src="' . htmlspecialchars($post['image_path']) . '" alt="Post Image" style="max-width:100%; border-radius:8px;">';
                        }
                        // Display the admin who posted with their profile picture
                        echo '<p class="admin">';
                        echo '<img src="' . htmlspecialchars($post['profile_picture']) . '" alt="Admin Image" class="admin-img">';
                        echo 'Posted by: ' . htmlspecialchars($post['firstname']) . ' ' . htmlspecialchars($post['lastname']) . '</p>';
                        echo '</div>'; // End post
                    }
                } else {
                    echo '<p>No posts available.</p>';
                }
                ?>
            </div>
        </div>
    </section>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>