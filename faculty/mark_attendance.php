<?php
session_start();
include('../config/db.php');

// Check if the user is faculty
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
        // Include the hour attendance form (which is the original mark_attendance.php content)
        header("Location: hour_attendance_form.php");
    } elseif ($attendance_type === 'day') {
        // Redirect to day_attendance.php
        header("Location: day_attendance.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
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
        <li><a href="../dashboard/faculty_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
        <li><a href="../faculty/manage_students.php"class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
        <li><a href="../faculty/student_profile.php" class="sidebar-link"><i class="fas fa-id-card"></i><span> Student Profile</span></a></li>
        <li><a href="../faculty/mark_attendance.php"class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
        <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
        <li><a href="../faculty/reports.php"class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
        
        </ul>
    </div>

    <div class="content">
        <h2>Mark Attendance</h2>
        <p>Select an option to mark attendance:</p>
        <div class="attendance-options">
            <form method="GET" action="">
                <button type="submit" name="attendance_type" value="hour">Hour Attendance</button>
                <button type="submit" name="attendance_type" value="day">Day Attendance</button>
            </form>
        </div>

       
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
