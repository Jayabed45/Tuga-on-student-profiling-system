<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'Admin') {
    header("location: login.php");
    exit;
}

$host = '127.0.0.1';
$db = 'tugaon_db';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $postId = $_GET['id'];

    // Fetch post details
    $selectPostSql = "SELECT * FROM posts WHERE id = ?";
    $stmt = $conn->prepare($selectPostSql);
    $stmt->bind_param('i', $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['update_post'])) {
    $content = $_POST['content'];

    // Handle image upload
    $imagePath = $post['image_path']; // Retain old image path if no new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmp = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        // Validate image type
        $validExts = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($imageExt, $validExts)) {
            $imagePath = 'uploads/' . uniqid() . '.' . $imageExt;
            move_uploaded_file($imageTmp, $imagePath);

            // Delete the old image if it exists
            if ($post['image_path'] && file_exists($post['image_path'])) {
                unlink($post['image_path']);
            }
        } else {
            echo "Invalid image type. Please upload jpg, jpeg, png, or gif.";
        }
    }

    // Update post content in the database
    $updatePostSql = "UPDATE posts SET content = ?, image_path = ? WHERE id = ?";
    $stmt = $conn->prepare($updatePostSql);
    $stmt->bind_param('ssi', $content, $imagePath, $postId);
    if ($stmt->execute()) {
        echo "Post updated successfully!";
        header("Location: post.php"); // Redirect back to the post page
    } else {
        echo "Error updating post.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Edit Post - Admin Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .main-content {
            margin-left: 250px;
            padding: 30px;
        }

        .post-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .post-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-name {
            font-weight: bold;
            font-size: 18px;
        }

        .post-body {
            width: 100%;
            height: 150px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 20px;
            resize: none;
            outline: none;
            box-sizing: border-box;
        }

        .post-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .post-footer .icons {
            display: flex;
            align-items: center;
        }

        .post-footer .icons i {
            margin-right: 20px;
            cursor: pointer;
            color: #ccc;
        }

        .post-footer .icons i:hover {
            color: #1da1f2;
        }

        .edit-delete-buttons {
            display: flex;
            gap: 10px;
        }

        .edit-delete-buttons a {
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #1da1f2;
            color: white;
            text-decoration: none;
            cursor: pointer;
        }

        .edit-delete-buttons a:hover {
            background-color: #0d8bce;
        }

        .post-footer button {
            padding: 10px 20px;
            background-color: #1da1f2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .post-footer button:hover {
            background-color: #0d8bce;
        }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="logo-details">
        <img src="assets/img/bsitlogo.png" alt="Logo" class="logo-image">
            <span class="logo_name">Admin</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="admin.php" class="active">
                    <i class='bx bx-grid-alt'></i>
                    <span class="links_name">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="managestudent.php">
                    <i class='bx bx-list-ul'></i>
                    <span class="links_name">Manage Students</span>
                </a>
            </li>
            <li>
                <a href="post.php">
                    <i class='bx bxs-note'></i>
                    <span class="links_name">Post Something</span>
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
    <div class="main-content">
        <h1>Edit Post</h1>
        <div class="post-container">
            <div class="post-header">
                <img src="assets/img/bsitlogo.png" alt="Profile Image">
                <span class="user-name">Admin</span>
            </div>
            <form action="edit_post.php?id=<?php echo $post['id']; ?>" method="POST" enctype="multipart/form-data">
                <textarea name="content" class="post-body" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>

                <!-- Image upload (optional) -->
                <div class="post-footer">
                    <div class="icons">
                        <label for="image">
                            <i class="bx bx-image"></i>
                        </label>
                        <input type="file" name="image" id="image" accept="image/*" style="display:none;">
                    </div>
                    <button type="submit" name="update_post">Update Post</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
