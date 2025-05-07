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
$error_message = ""; // Initialize error message variable
$success_message = "";
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying it
}
// Check if we're editing
if (isset($_GET['edit']) && isset($_GET['faculty_name']) && isset($_GET['subject_name']) && isset($_GET['subject_papercode']) && isset($_GET['class_name']) && isset($_GET['section']) && isset($_GET['department'])) {
    $edit_mode = true;
    $faculty_name = $_GET['faculty_name'];
    $subject_name = $_GET['subject_name'];
    $subject_papercode = $_GET['subject_papercode'];
    $class_name = $_GET['class_name'];
    $section = $_GET['section'];
    $department = $_GET['department'];
}

// Add new faculty or update existing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_faculty'])) {
        // Add new faculty
        $faculty_name = $_POST['faculty_name'];
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "INSERT INTO faculty (faculty_name, subject_name, subject_papercode, class_name, section, department) 
                VALUES ('$faculty_name', '$subject_name', '$subject_papercode', '$class_name', '$section', '$department')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Faculty added successfully!";
            header('Location: manage_faculty.php');
            exit();
        } else {
            echo "Error adding faculty: " . $conn->error;
        }
    } elseif (isset($_POST['edit_faculty'])) { // Update faculty details
        // New values
        $faculty_name = $_POST['faculty_name'];
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        // Original values
        

        // Update faculty details
        $sql = "UPDATE faculty 
                SET faculty_name='$faculty_name', subject_name='$subject_name', subject_papercode='$subject_papercode', 
                    class_name='$class_name', section='$section', department='$department' 
                WHERE  subject_name='$subject_name'";
                  

        if ($conn->query($sql)) {
            $_SESSION['message'] = "Faculty updated successfully!";
            header('Location: manage_faculty.php');
            exit();
        } else {
            echo "Error updating faculty: " . $conn->error;
        }
    } elseif (isset($_POST['delete_faculty'])) { // Delete faculty
        $faculty_name = $_POST['faculty_name'];
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        // Check for related attendance records
        $check_sql = "SELECT COUNT(*) AS count FROM attendance WHERE faculty_name='$faculty_name'";
        $result = $conn->query($check_sql);
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            // Handle related attendance records (e.g., delete them or set them to NULL)
            $delete_attendance_sql = "DELETE FROM attendance WHERE faculty_name='$faculty_name'";
            $conn->query($delete_attendance_sql);
        }

        // Now delete the faculty member
        $sql = "DELETE FROM faculty 
                WHERE faculty_name='$faculty_name' 
                  AND subject_name='$subject_name' 
                  AND subject_papercode='$subject_papercode' 
                  AND class_name='$class_name' 
                  AND section='$section' 
                  AND department='$department'";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Faculty deleted successfully!";
            header('Location: manage_faculty.php');
            exit();
        } else {
            echo "Error deleting faculty: " . $conn->error;
        }
    }
}

// Fetch existing faculty details from faculty_details table
$faculty = $conn->query("SELECT * FROM faculty");

// Fetch class details for dropdowns
$classes = $conn->query("SELECT DISTINCT class_name, section, department FROM classes");

// Fetch subject details for dropdowns
$subjects = $conn->query("SELECT subject_name, subject_papercode FROM subjects");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Faculty</title>
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
  
    <div class="side-content">
    <div class="sidebar">
        
        <ul>
            <li><a href="../dashboard/admin_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
            <li><a href="../admin/manage_classes.php" class="sidebar-link"><i class="fas fa-chalkboard"></i><span> Manage Classes</span></a></li>
            <li><a href="../admin/manage_subjects.php" class="sidebar-link"><i class="fas fa-book"></i><span> Manage Subjects</span></a></li>
            <li><a href="../admin/manage_faculty.php" class="sidebar-link"><i class="fas fa-user-tie"></i><span> Manage Faculty</span></a></li>
            <li><a href="../admin/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
            
        </ul>
    </div>

    <div class="content">
        <h2>Manage Faculty</h2>

        <?php if ($edit_mode): ?>
            <h3>Edit Faculty</h3>
            <form method="post" action="">
                <div class="form-grid">
                    <div>
                        <label>Faculty Name</label>
                        <input type="text" name="faculty_name" value="<?= $faculty_name ?>" required>

                        <label>Subject Name</label>
                        <select name="subject_name" required>
                        <option value="">Select Subjectname</option>
                            <?php while ($row = $subjects->fetch_assoc()): ?>
                                <option value="<?= $row['subject_name'] ?>" <?= $row['subject_name'] == $subject_name ? 'selected' : '' ?>><?= $row['subject_name'] ?></option>
                            <?php endwhile; ?>
                        </select>

                        <label>Paper Code</label>
                        <select name="subject_papercode" required>
                        <option value="">Select Papercode</option>
                            <?php 
                            $subjects->data_seek(0);
                            while ($row = $subjects->fetch_assoc()): ?>
                                <option value="<?= $row['subject_papercode'] ?>" <?= $row['subject_papercode'] == $subject_papercode ? 'selected' : '' ?>><?= $row['subject_papercode'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label>Class Name</label>
                        <select name="class_name" required>
                        <option value="">Select Class</option>
                            <?php while ($row = $classes->fetch_assoc()): ?>
                                <option value="<?= $row['class_name'] ?>" <?= $row['class_name'] == $class_name ? 'selected' : '' ?>><?= $row['class_name'] ?></option>
                            <?php endwhile; ?>
                        </select>

                        <label>Section</label>
                        <select name="section" required>
                            <option value="A" <?= $section == 'A' ? 'selected' : '' ?>>A</option>
                            <option value="B" <?= $section == 'B' ? 'selected' : '' ?>>B</option>
                            <option value="C" <?= $section == 'C' ? 'selected' : '' ?>>C</option>
                            <option value="D" <?= $section == 'D' ? 'selected' : '' ?>>D</option>
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
                </div>
                <button type="submit" name="edit_faculty" class="button">Update faculty</button>
            </form>
        <?php else: ?>
            <h3>Add Faculty</h3>
            <form method="post" action="">
                <div class="form-grid">
                    <div>
                        <label>Faculty Name</label>
                        <input type="text" name="faculty_name" required>

                        <label>Subject Name</label>
                        <select name="subject_name" id="subject_name" class="form-control" required>
                        <option value="">Select Subject Name</option>
                            <?php while ($row = $subjects->fetch_assoc()): ?>
                                <option value="<?= $row['subject_name'] ?>"><?= $row['subject_name'] ?></option>
                            <?php endwhile; ?>
                        </select>

                        <label for="subject_papercode">Subject Paper Code:</label>
                    <input type="text" id="subject_papercode" name="subject_papercode" required>

                    </div>
                    <div>
                    <label for="class_name">Class Name:</label>
                    <select name="class_name" id="class_name" class="form-control" required>
                        <option value="">Select Class</option>
                            <?php while ($row = $classes->fetch_assoc()): ?>
                                <option value="<?= $row['class_name'] ?>"><?= $row['class_name'] ?></option>
                            <?php endwhile; ?>
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
                </div>
                <button type="submit" name="add_faculty" class="button">Add Faculty</button>
            </form>
            <?php if (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <?php endif; ?>

        <h3> Existing Faculty List</h3>
        <table>
            <thead>
                <tr>
                    <th>Faculty Name</th>
                    <th>Subject Name</th>
                    <th>Paper Code</th>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $faculty->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['faculty_name'] ?></td>
                        <td><?= $row['subject_name'] ?></td>
                        <td><?= $row['subject_papercode'] ?></td>
                        <td><?= $row['class_name'] ?></td>
                        <td><?= $row['section'] ?></td>
                        <td><?= $row['department'] ?></td>
                        <td>
                            <a href="manage_faculty.php?edit=true&faculty_name=<?= $row['faculty_name'] ?>&subject_name=<?= $row['subject_name'] ?>&subject_papercode=<?= $row['subject_papercode'] ?>&class_name=<?= $row['class_name'] ?>&section=<?= $row['section'] ?>&department=<?= $row['department'] ?>" class="button">Edit</a>
                            <form method="post" action="" style="display:inline-block;">
                                <input type="hidden" name="faculty_name" value="<?= $row['faculty_name'] ?>">
                                <input type="hidden" name="subject_name" value="<?= $row['subject_name'] ?>">
                                <input type="hidden" name="subject_papercode" value="<?= $row['subject_papercode'] ?>">
                                <input type="hidden" name="class_name" value="<?= $row['class_name'] ?>">
                                <input type="hidden" name="section" value="<?= $row['section'] ?>">
                                <input type="hidden" name="department" value="<?= $row['department'] ?>">

                                <button type="submit" name="delete_faculty" class="button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include your script -->
<script src="js/cls_dept_link.js"></script>
<script src="js/subject_link.js"></script>
    <script src="../js/sidebar.js"></script>
    <script src="../js/topbar.js"></script>
</body>
</html>
