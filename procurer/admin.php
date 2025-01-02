<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['user_type'] !== 'Admin') {
    header("location: login.php"); // Redirect to login page if not logged in or not an admin
    exit;
}

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
$adminSql = "SELECT profile_picture FROM admin WHERE id = 1"; // Assuming admin ID is 1
$adminResult = $conn->query($adminSql);
$admin = $adminResult->fetch_assoc();

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $deleteSql = "DELETE FROM students WHERE id = $id"; // Adjust if your table has a different primary key
    $conn->query($deleteSql);
}

// Fetch all student data
$sql = "SELECT id, firstname, last_name, age, gender, student_year, section_of_student, address, email, profile_picture FROM students";
$result = $conn->query($sql);

// Fetch totals
$totalStudents = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$totalMale = $conn->query("SELECT COUNT(*) as count FROM students WHERE gender = 'Male'")->fetch_assoc()['count'];
$totalFemale = $conn->query("SELECT COUNT(*) as count FROM students WHERE gender = 'Female'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/table.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <?php
            $profile_picture_path = htmlspecialchars($admin['profile_picture']);
            if (!empty($profile_picture_path) && file_exists($profile_picture_path)) {
                echo "<img src='$profile_picture_path' alt='Profile Picture' class='logo-image' style='height: 50px; width: 50px; border-radius: 50%;'>";
            } else {
                echo "<img src='assets/img/default-profile.png' alt='Default Profile' class='logo-image' style='height: 50px; width: 50px; border-radius: 50%;'>";
            }
            ?>
            <span class="logo_name">Admin</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="admin_profile.php"> 
                    <i class='bx bx-user'></i>
                    <span class="links_name">Admin Profile</span>
                </a>
            </li>
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

    <section class="home-section">
        <nav>
            <div class="sidebar-button">
                <i class='#'></i>
                <span class="dashboard">Dashboard</span>
            </div>
        </nav>

        <div class="home-content">
            <div class="overview-boxes">
                <div class="box">
                    <i class='bx bx-user' style="font-size: 2em;"></i>
                    <div class="right-side">
                        <div class="box-topic">Total Students</div>
                        <div class="number"><?php echo $totalStudents; ?></div>
                    </div>
                </div>
                <div class="box">
                    <i class='bx bx-male' style="font-size: 2em;"></i>
                    <div class="right-side">
                        <div class="box-topic">Total Male</div>
                        <div class="number"><?php echo $totalMale; ?></div>
                    </div>
                </div>
                <div class="box">
                    <i class='bx bx-female' style="font-size: 2em;"></i>
                    <div class="right-side">
                        <div class="box-topic">Total Female</div>
                        <div class="number"><?php echo $totalFemale; ?></div>
                    </div>
                </div>
            </div>

            <h2>Students</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Year</th>
                            <th>Section</th>
                            <th>Address</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                $profile_picture_path = htmlspecialchars($row['profile_picture']);
                                if (!empty($profile_picture_path) && file_exists($profile_picture_path)) {
                                    echo "<td><img src='$profile_picture_path' alt='Profile Picture' style='height: 50px;'></td>";
                                } else {
                                    echo "<td>No Image</td>";
                                }
                                echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['age']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['student_year']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['section_of_student']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                                echo "</tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>
</html>

<?php
$conn->close(); // Close the database connection
?>