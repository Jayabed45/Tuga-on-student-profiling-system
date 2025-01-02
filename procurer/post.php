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

// Fetch admin data
$adminId = 1;
$adminSql = "SELECT profile_picture FROM admin WHERE id = ?";
$stmt = $conn->prepare($adminSql);
$stmt->bind_param('i', $adminId);
$stmt->execute();
$adminResult = $stmt->get_result();
$admin = $adminResult->fetch_assoc();
$stmt->close();

$adminProfilePicture = htmlspecialchars($admin['profile_picture']);
$validExts = ['jpg', 'jpeg', 'png', 'gif']; // Define valid extensions

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = trim($_POST['content']);
    $imagePath = '';

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $profileTmp = $_FILES['profile_picture']['tmp_name'];
        $profileName = $_FILES['profile_picture']['name'];
        $profileExt = strtolower(pathinfo($profileName, PATHINFO_EXTENSION));

        if (in_array($profileExt, $validExts)) {
            $profilePicturePath = 'uploads/profile_' . uniqid() . '.' . $profileExt;
            if (move_uploaded_file($profileTmp, $profilePicturePath)) {
                $updateProfileSql = "UPDATE admin SET profile_picture = ? WHERE id = ?";
                $stmt = $conn->prepare($updateProfileSql);
                $stmt->bind_param('si', $profilePicturePath, $adminId);
                $stmt->execute();
                $stmt->close();
                $adminProfilePicture = $profilePicturePath;
            } else {
                echo "Failed to upload profile picture.";
            }
        } else {
            echo "Invalid profile picture type.";
        }
    }

    // Handle post image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        if (in_array($imageExt, $validExts)) {
            $imagePath = 'uploads/' . uniqid() . '.' . $imageExt;
            if (!move_uploaded_file($imageTmp, $imagePath)) {
                echo "Failed to upload post image.";
            }
        } else {
            echo "Invalid post image type.";
        }
    }

    // Insert post into the database
    if (!empty($content)) {
        $insertPostSql = "INSERT INTO posts (content, image_path, admin_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insertPostSql);
        $stmt->bind_param('ssi', $content, $imagePath, $adminId);
        if ($stmt->execute()) {
            echo "<script>alert('Post created successfully!');</script>";
        } else {
            echo "<script>alert('Error creating post.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Post content cannot be empty.');</script>";
    }
}

// Fetch posts from the database
$getPostsSql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($getPostsSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Post Something</title>
    <link href='https://unpkg.com/boxicons@2.0.0/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/post.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <?php
            echo !empty($adminProfilePicture) ? "<img src='$adminProfilePicture' alt='Profile Picture' class='logo-image' style='height: 50px; width: 50px; border-radius: 50%;'>" : "<img src='assets/img/default-profile.png' alt='Default Profile' class='logo-image' style='height: 50px; width: 50px; border-radius: 50%;'>";
            ?>
            <span class="logo_name">Admin</span>
        </div>
        <ul class="nav-links">
            <li><a href="admin_profile.php"><i class='bx bx-user'></i><span class="links_name">Admin Profile</span></a></li>
            <li><a href="admin.php"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
            <li><a href="managestudent.php"><i class='bx bx-list-ul'></i><span class="links_name">Manage Students</span></a></li>
            <li><a href="post.php" class="active"><i class='bx bxs-note'></i><span class="links_name">Post Something</span></a></li>
            <li class="log_out"><a href="../logout.php"><i class='bx bx-log-out'></i><span class="links_name">Log out</span></a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="post-container">
            <div class="post-header">
                <?php
                echo !empty($adminProfilePicture) ? "<img src='$adminProfilePicture' alt='Profile Image' style='height: 40px; width: 40px; border-radius: 50%;'>" : "<img src='assets/img/default-profile.png' alt='Default Profile' style='height: 40px; width: 40px; border-radius: 50%;'>";
                ?>
                <span class="user-name">Post Something</span>
            </div>
            <form action="post.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" style="display:none;">
                <label for="profile_picture" style="cursor: pointer;">Change Profile Picture</label>
                
                <textarea class="post-body" name="content" id="content" placeholder="What's happening?" required></textarea>

                <div class="post-footer">
                    <div class="icons">
                        <label for="image">
                            <i class="bx bx-image"></i>
                        </label>
                        <input type="file" name="image" id="image" accept="image/*" style="display:none;">
                    </div>
                    <button type="submit" name="submit_post">Post</button>
                </div>
            </form>
        </div>

        <div class="posts">
            <h2>Recent Posts</h2>
            <?php
            if ($result->num_rows > 0) {
                while ($post = $result->fetch_assoc()) {
                    echo '<div class="post-item">';
                    echo '<div class="post-header">';
                    echo !empty($adminProfilePicture) ? '<img src="' . $adminProfilePicture . '" alt="Profile Image" style="height: 40px; width: 40px; border-radius: 50%;">' : '<img src="assets/img/default-profile.png" alt="Default Profile" style="height: 40px; width: 40px; border-radius: 50%;">';
                    echo '<span class="user-name">Admin</span>';
                    echo '<div class="dropdown">';
                    echo '<button class="dropdown-button" onclick="toggleDropdown(' . $post['id'] . ')">Options</button>';
                    echo '<div id="dropdown-' . $post['id'] . '" class="dropdown-content">';
                    echo '<a href="edit_post.php?id=' . $post['id'] . '">Edit</a>';
                    echo '<a href="delete_post.php?id=' . $post['id'] . '" onclick="return confirm(\'Are you sure you want to delete this post?\')">Delete</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>'; // End post header
                    echo '<div class="post-content">' . nl2br(htmlspecialchars($post['content'])) . '</div>';
                    if ($post['image_path']) {
                        echo '<img src="' . htmlspecialchars($post['image_path']) . '" alt="Post Image">';
                    }
                    echo '</div>'; // End post item
                }
            } else {
                echo '<p>No posts available.</p>';
            }
            ?>
        </div>
    </div>

    <script src="assets/js/post.js"></script>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>