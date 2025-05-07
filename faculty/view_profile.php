<?php
session_start();
include('../config/db.php');

// Check if the user is faculty or admin
if ($_SESSION['user_type'] !== 'faculty') {
    header('Location: ../login.php');
    exit();
}

// Get username from session
$username = $_SESSION['username'] ?? 'Guest';

// Get the parameters from the URL
$department = $_GET['department'] ?? '';
$batch = $_GET['batch'] ?? '';
$class_name = $_GET['class_name'] ?? '';
$section = $_GET['section'] ?? '';

// Query to fetch student registration numbers based on department, class, and section
$query = "SELECT student_regno FROM students WHERE department = '$department' AND batch = '$batch' AND class_name = '$class_name' AND section = '$section'";

// Execute the query
$result = mysqli_query($conn, $query);

// Check if any results are returned
if (mysqli_num_rows($result) > 0) {
    $students = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    // Return the results as JSON
    echo json_encode($students);
} else {
    echo json_encode([]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Student Profile</title>
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
   <style>
     table {
            width: 50%;
            border-collapse: collapse;
            margin-bottom: 20px;
            margin: 30px auto;
            background-color:white;
        }

        table, th, td {
            border: 1px solid #333;
            text-align: center;
            padding: 8px;
        }

        th {
            background-color: #9b59b6;
            color: white;
            padding: 12px 8px;
            font-weight: bold;
        }

        td {
            padding: 12px 8px;
        }

        td + td {
            border-left: 1px solid #333;
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
            <li><a href="../dashboard/faculty_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
            <li><a href="../faculty/manage_students.php" class="sidebar-link"><i class="fas fa-user-graduate"></i><span> Manage Students</span></a></li>
            <li><a href="../faculty/student_profile.php" class="sidebar-link"><i class="fas fa-id-card"></i><span> Student Profile</span></a></li>
            <li><a href="../faculty/mark_attendance.php" class="sidebar-link"><i class="fas fa-check-circle"></i><span> Mark Attendance</span></a></li>
            <li><a href="../faculty/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
            <li><a href="../faculty/reports.php" class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
        </ul>
    </div>

    <div class="content">
        <h2>View Student Profile</h2>

        <!-- Filter Section -->
        <div class="profile-filter">
            <form id="filterForm" method="POST" action="">
            <div class="form-grid">
                <div>
                <label>Department:</label>
                <select name="department" id="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $dept_query = mysqli_query($conn, "SELECT DISTINCT department FROM students");
                    while ($dept = mysqli_fetch_assoc($dept_query)) {
                        echo "<option value='{$dept['department']}'>{$dept['department']}</option>";
                    }
                    ?>
                </select>
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

                <label>Class Name:</label>
                <select name="class_name" id="class_name" required>
                    <option value="">Select Class</option>
                    <?php
                    // Example query to get class names
                    $class_query = $conn->query("SELECT DISTINCT class_name FROM classes");
                    while ($class_row = $class_query->fetch_assoc()) {
                        $selected = (isset($_POST['class_name']) && $_POST['class_name'] === $class_row['class_name']) ? 'selected' : '';
                        echo "<option value='{$class_row['class_name']}' $selected>{$class_row['class_name']}</option>";
                    }
                    ?>
                </select>
                </div>
                <div>
                <label>Section:</label>
                <select name="section" id="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <label>Student Reg No:</label>
                <select name="student_regno" id="student_regno" required>
                    <option value="">Select student regno</option>
                    <?php
                    $student_regno_query = mysqli_query($conn, "SELECT DISTINCT student_regno FROM students");
                    while ($student_regno = mysqli_fetch_assoc($student_regno_query)) {
                        echo "<option value='{$student_regno['student_regno']}'>{$student_regno['student_regno']}</option>";
                    }
                    ?>
                </select>
                

                <button type="submit" name="show_profile"class="button">Show Profile</button>

                </div>
            </form>
            
        </div>
     

        <?php
        // Display Student Profile Details
        if (isset($_POST['show_profile'])) {
            $regno = $_POST['student_regno'];
            $query = mysqli_query($conn, "SELECT * FROM students WHERE student_regno = '$regno'");
            if ($student = mysqli_fetch_assoc($query)) {
                echo "<h3>Student Profile</h3>";
                echo "<table border='1' cellpadding='5'>";
            
                echo "<tr><th>Photo</th><td><img src='uploads/{$student['photo']}' alt='Student Photo' height='200'></td></tr>";
                echo "<tr><th>Reg No</th><td>{$student['student_regno']}</td></tr>";
                echo "<tr><th>Name</th><td>{$student['student_name']}</td></tr>";
                echo "<tr><th>Class</th><td>{$student['class_name']}</td></tr>";
                echo "<tr><th>Section</th><td>{$student['section']}</td></tr>";
                echo "<tr><th>Department</th><td>{$student['department']}</td></tr>";
                echo "<tr><th>Batch</th><td>{$student['batch']}</td></tr>";
                echo "<tr><th>Contact</th><td>{$student['contact_number']}</td></tr>";
                echo "<tr><th>Father Name</th><td>{$student['father_name']}</td></tr>";
                echo "<tr><th>Father Contact</th><td>{$student['father_contact']}</td></tr>";
                echo "<tr><th>Mother Name</th><td>{$student['mother_name']}</td></tr>";
                echo "<tr><th>Mother Contact</th><td>{$student['mother_contact']}</td></tr>";
                echo "<tr><th>Address</th><td>{$student['address']}</td></tr>";
                echo "</table>";
                echo "<form action='generate_profile_pdf.php' method='post' target='_blank' style='text-align: center;'>
                <input type='hidden' name='student_regno' value='{$student['student_regno']}'>
                <button type='submit' name='generate_profile_pdf' class='button'>Generate PDF</button>
            </form>";
            ;
         
            }
        }
        
        ?>
        
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include your script -->
<script src="../js/cls_dept_link.js"></script>

<script src="../js/sidebar.js"></script>
<script src="../js/topbar.js"></script>

<!-- AJAX Script for Dynamic Dropdowns --><script>
    function fetchStudentsIfReady() {
        let dept = document.getElementById("department").value;
        let cls = document.getElementById("class_name").value;
        let sec = document.getElementById("section").value;

        if (dept && cls && sec) {
            fetch(`fetch_students.php?department=${dept}&class_name=${cls}&section=${sec}`)
                .then(res => res.json())
                .then(data => {
                    let regDropdown = document.getElementById("student_regno");
                    regDropdown.innerHTML = "<option value=''>Select Student</option>";
                    if (data.length > 0) {
                        data.forEach(item => {
                            regDropdown.innerHTML += `<option value='${item.student_regno}'>${item.student_regno}</option>`;
                        });
                    } else {
                        regDropdown.innerHTML = "<option value=''>No students found</option>";
                    }
                });
        }
    }

    document.getElementById("department").addEventListener("change", fetchStudentsIfReady);
    document.getElementById("class_name").addEventListener("change", fetchStudentsIfReady);
    document.getElementById("section").addEventListener("change", fetchStudentsIfReady);
</script>
<script>
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
</body>
</html>
