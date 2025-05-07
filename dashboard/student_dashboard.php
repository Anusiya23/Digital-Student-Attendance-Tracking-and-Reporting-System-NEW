<?php
session_start();
include('../config/db.php');

// Check if student is logged in
if ($_SESSION['user_type'] !== 'student') {
    header('Location: ../login.php');
    exit();
}
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'faculty' && $_SESSION['user_type'] !== 'student')) {
    header('Location: ../login.php');
    exit();
}

// Fetch the logged-in user's username from the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use the session-stored username
} else {
    $username = 'Guest'; // Fallback in case the username isn't set
}
$class_count = $conn->query("SELECT COUNT(*) AS count FROM classes")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
</head>
<body>
<?php include('../clgeheader.html'); ?>
<div class="topbar">
        <div class="topbar-left">
            <span class="menu-toggle"><i class="fas fa-bars"></i></span>
            <h1>DSATRS</h1>
        </div>
        <div class="topbar-right">
            <i class="fas fa-user-circle"></i>
            <span>Welcome, <?php echo $username; ?></span>
            <div class="welcome-dropdown">
                <a href="../login/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
    <div class="side-content">
<div class="sidebar">
    
    <ul>
        <li><a href="../dashboard/student_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
        <li><a href="../student/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
        
    </ul>
</div>

<div class="content">
    <h1>Student Dashboard</h1>
    <!-- No dashboard statistics here as requested -->
    <div class="dashboard-container">
    <div class="dashboard-card">
                <i class="fas fa-school"></i>
                <h3>Classes</h3>
                <p><?php echo $class_count; ?></p>
            </div>
</div>
</div>
<script src="../js/sidebar.js"></script>
<script src="../js/topbar.js"></script>
</body>
</html>
