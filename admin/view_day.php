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
$showTable = false;
if (isset($_POST['view_day_attendance'])) {
    // Capture input values
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $department = $_POST['department'];
    $batch = $_POST['batch'];
    $date = $_POST['date'];
    $semester_no = $_POST['semester_no'];

    // Fetch attendance data
    $sql = "SELECT s.student_regno, s.student_name, d.batch, d.status, d.date
            FROM dayattendance d
            JOIN students s ON d.student_regno = s.student_regno
            WHERE d.class_name = '$class_name'
            AND d.section = '$section'
            AND d.department = '$department'
            AND d.date = '$date'
            AND d.semester_no = '$semester_no'
            AND d.batch = '$batch'";
    
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $showTable = true;
        $firstRow = $result->fetch_assoc();

        $date_value = $firstRow['date'];
        $batch = $firstRow['batch'];
        // Start capturing table in a buffer
        ob_start();
        echo "<table>
         <tr>
                        <th colspan='4'> Date: $date_value | Batch: $batch </th>
                    </tr>
                <tr>
                    <th>S.No</th>
                    <th>Student Reg. No</th>
                    <th>Student Name</th>
                    <th>Status</th>
                    
                </tr>";
        
                $sno = 1;
                $row_class = ($firstRow['status'] === 'Absent') ? 'absent-row' : '';
                echo "<tr class='{$row_class}'>
                        <td>{$sno}</td>
                        <td>{$firstRow['student_regno']}</td>
                        <td>{$firstRow['student_name']}</td>
                        <td>{$firstRow['status']}</td>
                    </tr>";
                $sno++;
                while ($row = $result->fetch_assoc()) {
                    $row_class = ($row['status'] === 'Absent') ? 'absent-row' : '';
                    echo "<tr class='{$row_class}'>
                            <td>{$sno}</td>
                            <td>{$row['student_regno']}</td>
                            <td>{$row['student_name']}</td>
                            <td>{$row['status']}</td>
                          </tr>";
                    $sno++;
                }
            
        echo "</table>";
        $attendanceTable = ob_get_clean();
    } else {
        $attendanceTable =  "<p>No attendance records found for the selected day.</p>";
    }
}

?> 



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Day Attendance</title>
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
    input[type="text"], select {
            width: 80%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
    }
    button {
            width: 30%;
            padding: 10px;
            background-color: #9b59b6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        button:hover {
            background-color: dodgerblue;
        }
        table {
            width: 70%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color:white;
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
        <li><a href="../dashboard/admin_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
                <li><a href="../admin/manage_classes.php"class="sidebar-link"><i class="fas fa-chalkboard"></i><span> Manage Classes</span></a></li>
                <li><a href="../admin/manage_subjects.php"class="sidebar-link"><i class="fas fa-book"></i><span> Manage Subjects</span></a></li>
                <li><a href="../admin/manage_faculty.php"class="sidebar-link"><i class="fas fa-user-tie"></i><span> Manage Faculty</span></a></li>
                <li><a href="../admin/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
                
        </ul>
    </div>

    <!-- Main content -->
    <div class="content">
        <h2>View Day Attendance</h2>
        <form method="POST" action="">
        <div class="container">
            <div class="form-grid">
                <div>
                    <label for="class_name">Class Name</label>
                    <select id="class_name" name="class_name" required>
                        <option value="">Select Class</option>
                        <?php
                        $sql = "SELECT DISTINCT class_name FROM classes";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['class_name'] . "'>" . $row['class_name'] . "</option>";
                        }
                        ?>
                    </select>

                    <label>Section</label>
                <select name="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                    <label for="department">Department</label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <?php
                        $sql = "SELECT DISTINCT department FROM classes";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row['department'] . "'>" . $row['department'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                <label for="batch">Batch:</label>
                <select id="batch" name="batch" required>
                            <option value="">Select batch</option>
                            <option value="2022-2025">2022-2025</option>
                            <option value="2023-2026">2023-2026</option>
                            <option value="2024-2027">2024-2027</option>
                            <option value="2023-2025">2023-2025</option>
                            <option value="2022-2024">2022-2024</option>
                            <option value="2024-2026">2024-2026</option>
                        
                        </select>
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required>

                    <label for="semester_no">Semester No</label>
                    <input type="number" id="semester_no" name="semester_no" required>
                </div>
            </div>
            <button type="submit" name="view_day_attendance">View Attendance</button>
        </form>

        <?php if ($showTable): ?>
            <div class="attendance-table-container">
                <?php echo $attendanceTable; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
