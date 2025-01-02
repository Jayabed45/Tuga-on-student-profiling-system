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

    // Delete post image if it exists
    $selectImageSql = "SELECT image_path FROM posts WHERE id = ?";
    $stmt = $conn->prepare($selectImageSql);
    $stmt->bind_param('i', $postId);
    $stmt->execute();
    $stmt->bind_result($imagePath);
    $stmt->fetch();
    $stmt->close();

    if ($imagePath && file_exists($imagePath)) {
        unlink($imagePath); // Delete the image from the server
    }

    // Delete the post from the database
    $deletePostSql = "DELETE FROM posts WHERE id = ?";
    $stmt = $conn->prepare($deletePostSql);
    $stmt->bind_param('i', $postId);
    if ($stmt->execute()) {
        echo "Post deleted successfully!";
        header("Location: post.php"); // Redirect back to the post page
    } else {
        echo "Error deleting post.";
    }
    $stmt->close();
}

$conn->close();
?>
