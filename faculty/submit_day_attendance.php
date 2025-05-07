<?php
session_start();
include('../config/db.php');

// Check if the user is faculty
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use the session-stored username
} 

// When the attendance form is submitted
if (isset($_POST['mark_day_attendance'])) {
    $faculty_name = $_POST['faculty_name']; // Changed to use faculty_name
    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $date = $_POST['date'];
    $batch = $_POST['batch'];
    $semester_no = $_POST['semester_no'];

    $total_students = 0;
    $total_present = 0;
    $total_absent = 0;


    foreach ($_POST as $key => $value) {
        if (strpos($key, 'status_') === 0) {
            $student_regno = substr($key, 7); // Extract student regno from key
            $status = $value; // Present or Absent

            // Fetch student name from the database
            $student_query = $conn->prepare("SELECT student_name FROM students WHERE student_regno = ?");
            $student_query->bind_param("s", $student_regno);
            $student_query->execute();
            $student_result = $student_query->get_result();
            $student_data = $student_result->fetch_assoc();
            $student_name = $student_data['student_name'];

            // Check if student name was successfully fetched
            if ($student_name) {
                // Insert or update attendance record
                $query = $conn->prepare("INSERT INTO dayattendance 
                    (student_regno, student_name, faculty_name, department, class_name, section,  date, status, batch, semester_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE status = VALUES(status)");
                
                $query->bind_param("ssssssssis", $student_regno, $student_name, $faculty_name, $department, $class_name, $section, $date, $status, $batch, $semester_no);
                
                if (!$query->execute()) {
                    echo "Error: " . $query->error;
                }
            $total_students++;
            if ($status == 'Present') {
                $total_present++;
            } else {
                $total_absent++;
            }
        } else {
            echo "Error: Student with register number '$student_regno' does not exist.";
        }
    }
 
}
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Attendance</title>
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        .message-container {
            text-align: center;
            margin-top: 20px;
        }

    </style>
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
        <li><a href="../dashboard/faculty_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt" class="sidebar-link"></i><span> Dashboard</span></a></li>
    <li><a href="../faculty/manage_students.php"class="sidebar-link"><i class="fas fa-user-graduate"class="sidebar-link"></i><span> Manage Students</span></a></li>
    <li><a href="../faculty/student_profile.php" class="sidebar-link"><i class="fas fa-id-card"></i><span> Student Profile</span></a></li>
    <li><a href="../faculty/mark_attendance.php"class="sidebar-link"><i class="fas fa-check-circle"class="sidebar-link"></i><span> Mark Attendance</span></a></li>
    <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"class="sidebar-link"></i><span> View Attendance</span></a></li>
    <li><a href="../faculty/reports.php"class="sidebar-link"><i class="fas fa-file-alt"class="sidebar-link"></i><span> Generate Reports</span></a></li>
    
        </ul>
    </div>

    <div class="content">
        <table class="attendance-table">
            <!-- Your student list table goes here -->
        </table>
        
        

        <div class='message-container'>
            <p>Day Attendance marked successfully!</p>
            <p>Total Students: <?php echo $total_students; ?></p>
            <p>No of Present: <?php echo $total_present; ?></p>
            <p>No of Absent: <?php echo $total_absent; ?></p>
        </div>
    </div>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
