<?php
session_start();

include('../config/db.php');

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

        if (isset($_GET['attendance_type'])) {
            $attendance_type = $_GET['attendance_type'];

            if ($attendance_type === 'hour') {
                // Update the path as per your directory structure
                header("Location: ../admin/view_hour.php");
                exit();  // Don't forget to exit after header redirect
            } elseif ($attendance_type === 'day') {
                // Update the path as per your directory structure
                header("Location: ../admin/view_day.php");
                exit();  // Don't forget to exit after header redirect
            }
        }
        ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Attendance</title>
    <link rel="stylesheet" href="../css/forms.css">
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
    <!-- Sidebar -->
    <div class="side-content">
    <div class="sidebar">
        <ul>
        <li><a href="../dashboard/admin_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
                <li><a href="../admin/manage_classes.php"class="sidebar-link"><i class="fas fa-chalkboard"></i><span> Manage Classes</span></a></li>
                <li><a href="../admin/manage_subjects.php"class="sidebar-link"><i class="fas fa-book"></i><span> Manage Subjects</span></a></li>
                <li><a href="../admin/manage_faculty.php"class="sidebar-link"><i class="fas fa-user-tie"></i><span> Manage Faculty</span></a></li>
                <li><a href="../admin/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
                
        </ul>
    </div>

    <!-- Main content -->
    <div class="content">
        <h2>View Attendance</h2>
        <p>Select an option to view attendance:</p>
        <div class="attendance-options">
            <form method="GET" action="">
                <button type="submit" name="attendance_type" value="hour">View Hour Attendance</button>
                <button type="submit" name="attendance_type" value="day">View Day Attendance</button>
            </form>
        </div>

        
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
