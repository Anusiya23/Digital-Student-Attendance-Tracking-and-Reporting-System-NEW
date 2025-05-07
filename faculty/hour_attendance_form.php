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

$showTable = false; // Flag to show table after form submission

if (isset($_POST['fetch_students'])) {
    // Fetch form data
    $faculty_name = $_POST['faculty_name'];
    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $hour = $_POST['hour'];
    $subject_name = $_POST['subject_name'];
    $subject_papercode = $_POST['subject_papercode'];
    $date = $_POST['date'];
    $day_order = $_POST['day_order'];
    $batch = $_POST['batch'];
    $semester_no = $_POST['semester_no'];

    // Query to get students in the class and section
    $query = $conn->prepare("SELECT student_regno, student_name FROM students WHERE class_name = ? AND section = ?");
    $query->bind_param("ss", $class_name, $section);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $showTable = true;

        // Start capturing table in a buffer
        ob_start();

        echo "<form method='POST' action='submit_attendance.php'>";
        echo "<table class='attendance-table'>
                <tr>
                    <th>S. No.</th>
                    <th>Register Number</th>
                    <th>Student Name</th>
                    <th>Status (Present/Absent)</th>
                </tr>";

        $serial_number = 1; // Initialize serial number

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$serial_number}</td>
                    <td>{$row['student_regno']}</td>
                    <td>{$row['student_name']}</td>
                    <td>
                        <input type='hidden' name='status_{$row['student_regno']}' value='Absent' checked>
                        <input type='checkbox' name='status_{$row['student_regno']}' value='Present' checked> 
                         
                    </td>
                </tr>";
            $serial_number++; // Increment serial number for each student
        }
        echo "</table>";
        // Hidden fields to pass form data
        echo "<input type='hidden' name='faculty_name' value='$faculty_name'>";
        echo "<input type='hidden' name='department' value='$department'>";
        echo "<input type='hidden' name='class_name' value='$class_name'>";
        echo "<input type='hidden' name='section' value='$section'>";
        echo "<input type='hidden' name='hour' value='$hour'>";
        echo "<input type='hidden' name='subject_name' value='$subject_name'>";
        echo "<input type='hidden' name='subject_papercode' value='$subject_papercode'>";
        echo "<input type='hidden' name='date' value='$date'>";
        echo "<input type='hidden' name='day_order' value='$day_order'>";
        echo "<input type='hidden' name='batch' value='$batch'>";
        echo "<input type='hidden' name='semester_no' value='$semester_no'>";
        echo "<input type='submit' name='mark_attendance' value='Submit Attendance'>";
        echo "</form>";

        // Capture the table HTML
        $attendanceTable = ob_get_clean();
    } else {
        $attendanceTable = "<p>No students found in the selected class and section.</p>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mark Attendance</title>
    
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px 0;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.sidebar ul li a {
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    transition: color 0.3s ease;
}
  .sidebar ul li a i {
            margin-right: 10px;
        }

        .sidebar ul li:hover {
            background-color: #8e44ad;
        }

        /* Active class styling */
        .sidebar ul li.active {
            background-color: #6c3483;
            font-weight: bold;
        }

        .sidebar ul li.active a {
            color: #eef2f6;
        }
        .content {
            padding: 50px;
            flex-grow: 1;
            font-weight: bold;
            margin-left: 250px; /* Ensure content is shifted to the right of the sidebar */
            transition: margin-left 0.3s;
            font-size:20px;
        }

        .hidden-sidebar {
            width: 60px; /* Set width for hidden sidebar (to display only icons) */
            overflow: hidden;
        }

        .hidden-sidebar .sidebar-link span {
            display: none; /* Hide text when sidebar is collapsed */
        }

        .hidden-sidebar + .content {
            margin-left: 60px; /* Adjust content width when sidebar is hidden */
        }
h2 {
    color: #333;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

select, input[type="text"], input[type="date"], input[type="number"] {
    width: 80%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type="submit"] {
    background-color: #9b59b6;
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
    background-color: dodgerblue;
}

/* CSS for attendance table */
.attendance-table {
    margin-top:30px;
    margin-bottom:30px;
    margin-left: 50px;
    border-collapse: collapse;
    width: 90%;
    align-items:center;
    position:relative;
}

.attendance-table th, .attendance-table td {
    border: 2px solid #333;
    padding: 7px 5px;
    text-align: center;
}

.attendance-table th {
    background-color: #9b59b6;
    color:white;
    padding: 10px ;
}
input[type="submit"] {
    background-color: #9b59b6;
    border: none;
    color: white;
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    border-radius: 4px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    margin-left: 400px;
    margin-top: 5px;
}
.message-container {
    margin-right : 10px; /* Space between the form and the message */
    text-align:end;
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
        <li><a href="../dashboard/faculty_dashboard.php"class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
    <li><a href="../faculty/manage_students.php"class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
    <li><a href="../faculty/student_profile.php" class="sidebar-link"><i class="fas fa-id-card"></i><span> Student Profile</span></a></li>
    <li><a href="../faculty/mark_attendance.php"class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
    <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
    <li><a href="../faculty/reports.php"class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
    </ul>
    </div>
    
    <div class="content">
        <h2>Hour Attendance</h2>
        <form method="POST" action="">
            <div class="form-grid">
                <div>
                    <label for="faculty_name">Faculty Name:</label>
                    <input type="text" name="faculty_name" id="faculty_name" value="<?php echo isset($_POST['faculty_name']) ? htmlspecialchars($_POST['faculty_name']) : ''; ?>" required>
                    
                    <label for="department">Department:</label>
                    <select name="department" id="department" required>
                        <option value="">Select Department</option>
                        <?php
                        // Populate department names from database
                        $class_query = $conn->query("SELECT DISTINCT department FROM classes");
                        while ($class_row = $class_query->fetch_assoc()) {
                            $selected = (isset($_POST['department']) && $_POST['department'] === $class_row['department']) ? 'selected' : '';
                            echo "<option value='{$class_row['department']}' $selected>{$class_row['department']}</option>";
                        }
                        ?>
                    </select>

                    <label for="class_name">Class Name:</label>
                    <select name="class_name" id="class_name" class="form-control" required>
                        <option value="">Select Class</option>
                        <?php
                        $class_query = $conn->query("SELECT DISTINCT class_name FROM classes");
                        while ($class_row = $class_query->fetch_assoc()) {
                            $selected = (isset($_POST['class_name']) && $_POST['class_name'] === $class_row['class_name']) ? 'selected' : '';
                            echo "<option value='{$class_row['class_name']}'>{$class_row['class_name']}</option>";
                        }
                        ?>
                    </select>

                    <label>Section:</label>
                <select name="section" id="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                 
                <label for="hour">Hour</label>
                        <select id="hour" name="hour" required>
                            <option value="">Select hour</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                        </select>

                    <label for="subject_name">Subject Name:</label>
                    <select name="subject_name" id="subject_name" class="form-control" required>
                        <option value="">Select Subject Name</option>
                        <?php 
                        $subject_query = $conn->query("SELECT DISTINCT subject_name FROM subjects");
                        while ($subject_row = $subject_query->fetch_assoc()) {
                            $selected = (isset($_POST['subject_name']) && $_POST['subject_name'] === $subject_row['subject_name']) ? 'selected' : '';
                            echo "<option value='{$subject_row['subject_name']}'>{$subject_row['subject_name']}</option>";
                        } ?>
                    </select>
                </div>

                <div>
                    <label for="subject_papercode">Subject Paper Code:</label>
                    <input type="text" id="subject_papercode" name="subject_papercode" class="form-control" readonly>

                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" required>

                    <label for="day_order">Day Order:</label>
                    <input type="number" name="day_order" id="day_order" required>

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



                    <label for="semester_no">Semester Number:</label>
                    <input type="number" name="semester_no" id="semester_no" required>
                </div>
            </div>

            <input type="submit" name="fetch_students" value="Student List">
        </form>

        <!-- Display the student list table after form submission -->
        <?php if ($showTable): ?>
            <div class="attendance-table-container">
                <?php echo $attendanceTable; ?>
            </div>
        <?php endif; ?>
    </div>
</div><script>
    
function setDefaultBatch() {
    const input = document.getElementById('batch');
    if (input.value === '') {
        input.value = '20-20';
        // Move cursor to the end
        setTimeout(() => input.setSelectionRange(2, 2), 0);
    }
}

function restrictBatchFormat(input) {
    // Allow only digits and a hyphen, and restrict to format like 2023-2025
    input.value = input.value.replace(/[^0-9\-]/g, '').slice(0, 9);
}
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include your script -->
<script src="../js/cls_dept_link.js"></script>
<script src="../js/subject_link.js"></script>
        

    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
