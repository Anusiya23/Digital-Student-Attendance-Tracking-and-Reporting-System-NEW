<?php
session_start();
include('../config/db.php');

// Check if admin is logged in
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

// Add, Edit, and Delete functionality
$edit_mode = false; // Flag for edit mode
$error_message = "";
$success_message = ""; // Initialize success message variable
// Check if there's a message stored in the session
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying it
}
// Check if we're editing
if (isset($_GET['edit']) && isset($_GET['student_regno']) && isset($_GET['student_name']) && isset($_GET['class_name']) && isset($_GET['section']) && isset($_GET['department'])) {
    $edit_mode = true;
    $student_regno = $_GET['student_regno'];
    $student_name = $_GET['student_name'];
    $class_name = $_GET['class_name'];
    $section = $_GET['section'];
    $department = $_GET['department'];
}

// Add a new student or update an existing one
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        // Add new student
        $student_regno = $_POST['student_regno'];
        $student_name = $_POST['student_name'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "INSERT INTO students (student_regno, student_name, class_name, section, department) 
                VALUES ('$student_regno', '$student_name', '$class_name', '$section', '$department')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Student added successfully!";
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error adding student: " . $conn->error;
        }
    } // Update student in the database
    elseif (isset($_POST['edit_student'])) {
        // Retrieve the new values from the form
        $student_regno = $_POST['student_regno'];
        $student_name = $_POST['student_name'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];
    
        // Retrieve the original student_regno
        $original_student_regno = $_POST['original_student_regno'];
    
        // Update the student in the database
        $sql = "UPDATE students 
                SET student_regno='$student_regno', student_name='$student_name', 
                    class_name='$class_name', section='$section', department='$department' 
                WHERE student_regno='$original_student_regno'"; // Use original student_regno for WHERE
    
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Student updated successfully!";
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error updating student: " . $conn->error;
            echo "SQL Query: " . $sql;
        }
    }
    
    } elseif (isset($_POST['delete_student'])) { // Delete student
        $student_regno = $_POST['student_regno'];
        $student_name = $_POST['student_name'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "DELETE FROM students 
                WHERE student_regno='$student_regno' 
                  AND student_name='$student_name' 
                  AND class_name='$class_name' 
                  AND section='$section' 
                  AND department='$department'";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Student deleted successfully!";
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error deleting student: " . $conn->error;
        }
    }


// Fetch existing students
$students = $conn->query("SELECT * FROM students");

// Fetch class details for dropdowns
$classes = $conn->query("SELECT DISTINCT class_name, section, department FROM classes");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
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
        <li><a href="../faculty/view_attendance.php"class="sidebar-link"><i class="fas fa-eye"></i><span>View Attendance</span></a></li>
        <li><a href="../faculty/reports.php"class="sidebar-link"><i class="fas fa-file-alt"></i><span> Generate Reports</span></a></li>
    </ul>
    </div>

    <!-- Main content -->
    <div class="content">
        <h2>Manage Students</h2>

        <?php if ($edit_mode): ?>
            <!-- Edit Form -->
            <h3>Edit Student</h3>
            <form method="post" action="">
            <div class="form-grid">
                <div>
                <!-- Hidden fields to store original values -->
                <!-- Hidden fields to store original values -->
                <input type="hidden" name="original_student_regno" value="<?= $student_regno ?>">

                <input type="hidden" name="student_name" value="<?= $student_name ?>">
                <input type="hidden" name="class_name" value="<?= $class_name ?>">
                <input type="hidden" name="section" value="<?= $section ?>">
                <input type="hidden" name="department" value="<?= $department ?>">

                <label>Student Reg No</label>
                <input type="text" name="student_regno" value="<?= $student_regno ?>" required>

                <label>Student Name</label>
                <input type="text" name="student_name" value="<?= $student_name ?>" required>

                <label>Class Name</label>
                <select name="class_name" required>
                    <?php while ($row = $classes->fetch_assoc()): ?>
                        <option value="<?= $row['class_name'] ?>" <?= $row['class_name'] == $class_name ? 'selected' : '' ?>><?= $row['class_name'] ?></option>
                    <?php endwhile; ?>
                </select>
                </div>
                 <div>
                <label>Section</label>
                <select name="section" required>
                    <option value="A" <?= $section == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $section == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $section == 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $section == 'D' ? 'selected' : '' ?>>D</option>
                </select>

                <label>Department</label>
                <select name="department" required>
                    <?php 
                    $classes->data_seek(0);
                    while ($row = $classes->fetch_assoc()): ?>
                        <option value="<?= $row['department'] ?>" <?= $row['department'] == $department ? 'selected' : '' ?>><?= $row['department'] ?></option>
                    <?php endwhile; ?>
                </select>
                 </div>
            </div>

                <button type="submit" name="edit_student" class="button">Update Student</button>
            </form>
        <?php else: ?>
            <!-- Add Form -->
            <h3>Add New Student</h3>
            <form method="post" action="">
            <div class="form-grid">
                <div>
             
                <label>Student Reg No</label>
                <input type="text" name="student_regno" required>

                <label>Student Name</label>
                <input type="text" name="student_name" required>

                <label>Class Name:</label>
                <select name="class_name" id="class_name" class="form-control">
    <option value="">Select Class</option>
    <?php
    $result = mysqli_query($conn, "SELECT DISTINCT class_name FROM classes");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['class_name']}'>{$row['class_name']}</option>";
    }
    ?>
</select>

                </div>
                <div>

                <label>Section</label>
                <select name="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <label>Department:</label>
<select name="department" id="department" class="form-control">
    <option value="">Select Department</option>
    <?php
    $result = mysqli_query($conn, "SELECT DISTINCT department FROM classes");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['department']}'>{$row['department']}</option>";
    }
    ?>
</select>
                </div>
            </div>

                <button type="submit" name="add_student" class="button">Add Student</button>
            </form>
            <?php if (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Display existing students -->
        <h3>Existing Students</h3>
        <table>
            <thead>
                <tr>
                    <th>Student Reg No</th>
                    <th>Student Name</th>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= $student['student_regno'] ?></td>
                        <td><?= $student['student_name'] ?></td>
                        <td><?= $student['class_name'] ?></td>
                        <td><?= $student['section'] ?></td>
                        <td><?= $student['department'] ?></td>
                        <td>
                            <a href="manage_students.php?edit=true&student_regno=<?= $student['student_regno'] ?>&student_name=<?= $student['student_name'] ?>&class_name=<?= $student['class_name'] ?>&section=<?= $student['section'] ?>&department=<?= $student['department'] ?>" class="button">Edit</a>
                            <form method="post" action="" style="display:inline-block;">
                                <input type="hidden" name="student_regno" value="<?= $student['student_regno'] ?>">
                                <input type="hidden" name="student_name" value="<?= $student['student_name'] ?>">
                                <input type="hidden" name="class_name" value="<?= $student['class_name'] ?>">
                                <input type="hidden" name="section" value="<?= $student['section'] ?>">
                                <input type="hidden" name="department" value="<?= $student['department'] ?>">
                                <button type="submit" name="delete_student" class="button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include your script -->
<script src="../js/cls_dept_link.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
