<?php
session_start();
include('../config/db.php');

// Check if the user is faculty or admin
if ( $_SESSION['user_type'] !== 'faculty') {
    header('Location: ../login.php');
    exit();
}

// Get username from session
$username = $_SESSION['username'] ?? 'Guest';

if (isset($_GET['profile_action'])) {
    $profile_action = $_GET['profile_action'];

    if ($profile_action === 'add') {
        header("Location: add_profile.php");
    } elseif ($profile_action === 'view') {
        header("Location: view_profile.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Profile</title>
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
            <li><a href="../dashboard/faculty_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
            <li><a href="../faculty/manage_students.php" class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
            <li><a href="../faculty/student_profile.php" class="sidebar-link active"><i class="fas fa-id-card"></i><span> Student Profile</span></a></li>
            <li><a href="../faculty/mark_attendance.php" class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
            <li><a href="../faculty/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
            <li><a href="../faculty/reports.php" class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Student Profile</h2>
        <p>Select an option below:</p>
        <div class="attendance-options">
            <form method="GET" action="">
                <button type="submit" name="profile_action" value="add">Add Student Profile</button>
                <button type="submit" name="profile_action" value="view">View Student Profile</button>
            </form>
        </div>

      
    </div>
</div>

<script src="../js/sidebar.js"></script>
<script src="../js/topbar.js"></script>
</body>
</html>
