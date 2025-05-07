
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
if (isset($_GET['edit']) && isset($_GET['subject_name']) && isset($_GET['subject_papercode']) && isset($_GET['class_name']) && isset($_GET['section']) && isset($_GET['department'])) {
    $edit_mode = true;
    $subject_name = $_GET['subject_name'];
    $subject_papercode = $_GET['subject_papercode'];
    $class_name = $_GET['class_name'];
    $section = $_GET['section'];
    $department = $_GET['department'];
}

// Add a new subject or update an existing one
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_subject'])) {
        // Add new subject
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "INSERT INTO subjects (subject_name, subject_papercode, class_name, section, department) 
                VALUES ('$subject_name', '$subject_papercode', '$class_name', '$section', '$department')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Subject added successfully!";
            header('Location: manage_subjects.php');
            exit();
        } else {
            echo "Error adding subject: " . $conn->error;
        }
    } elseif (isset($_POST['edit_subject'])) { // Update subject in the database
        // Retrieve the new values from the form
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        // Retrieve the original values from hidden inputs
       

        // Update the subject in the database
        $sql = "UPDATE subjects 
                SET subject_name='$subject_name', subject_papercode='$subject_papercode', 
                    class_name='$class_name', section='$section', department='$department' 
                WHERE class_name='$class_name' ";

        if ($conn->query($sql)) {
            $_SESSION['message'] = "Subject updated successfully!";
            header('Location: manage_subjects.php');
            exit();
        } else {
            echo "Error updating subject: " . $conn->error . "<br>";
            echo "SQL Query: " . $sql;
        }
    } elseif (isset($_POST['delete_subject'])) { // Delete subject
        $subject_name = $_POST['subject_name'];
        $subject_papercode = $_POST['subject_papercode'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "DELETE FROM subjects 
                WHERE subject_name='$subject_name' 
                  AND subject_papercode='$subject_papercode' 
                  AND class_name='$class_name' 
                  AND section='$section' 
                  AND department='$department'";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Subject deleted successfully!";
            header('Location: manage_subjects.php');
            exit();
        } else {
            echo "Error deleting subject: " . $conn->error;
        }
    }
}

// Fetch existing subjects
$subjects = $conn->query("SELECT * FROM subjects");

// Fetch class details for dropdowns
$classes = $conn->query("SELECT DISTINCT class_name, section, department FROM classes");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects</title>
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
        <li><a href="../dashboard/admin_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span> Dashboard</span></a></li>
            <li><a href="../admin/manage_classes.php" class="sidebar-link"><i class="fas fa-chalkboard"></i><span> Manage Classes</span></a></li>
            <li><a href="../admin/manage_subjects.php" class="sidebar-link"><i class="fas fa-book"></i><span> Manage Subjects</span></a></li>
            <li><a href="../admin/manage_faculty.php" class="sidebar-link"><i class="fas fa-user-tie"></i><span> Manage Faculty</span></a></li>
            <li><a href="../admin/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i><span> View Attendance</span></a></li>
            
        </ul>
    </div>

    <!-- Main content -->
    <div class="content">
        <h2>Manage Subjects</h2>

        <?php if ($edit_mode): ?>
            <!-- Edit Form -->
            <h3>Edit Subject</h3>
            <form method="post" action="">
            <div class="form-grid">
                <div>
                <!-- Hidden fields to store original values -->
                <input type="hidden" name="original_subject_name" value="<?= $subject_name ?>">
                <input type="hidden" name="original_subject_papercode" value="<?= $subject_papercode ?>">
                <input type="hidden" name="original_class_name" value="<?= $class_name ?>">
                <input type="hidden" name="original_section" value="<?= $section ?>">
                <input type="hidden" name="original_department" value="<?= $department ?>">

                <label>Subject Name</label>
                <input type="text" name="subject_name" value="<?= $subject_name ?>" required>

                <label>Paper Code</label>
                <input type="text" name="subject_papercode" value="<?= $subject_papercode ?>" required>

                <label>Class Name</label>
                <select name="class_name" required>
                <option value="">Select Class</option>
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
                <button type="submit" name="edit_subject" class="button">Update Subject</button>
            </form>
        <?php else: ?>
            <!-- Add Form -->
            <h3>Add New Subject</h3>
            <form method="post" action="">
            <div class="form-grid">
                <div>
                <label>Subject Name</label>
                <input type="text" name="subject_name" required>

                <label>Paper Code</label>
                <input type="text" name="subject_papercode" required>

                <label>Class Name</label>
                <select name="class_name" required>
                <option value="">Select Class</option>
                    <?php while ($row = $classes->fetch_assoc()): ?>
                        <option value="<?= $row['class_name'] ?>"><?= $row['class_name'] ?></option>
                    <?php endwhile; ?>
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
                <button type="submit" name="add_subject" class="button">Add Subject</button>
            </form>
            <?php if (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Display existing subjects -->
        <h3>Existing Subjects</h3>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Paper Code</th>
                    <th>Class Name</th>
                    <th>Section</th>
                    <th>Department</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($subject = $subjects->fetch_assoc()): ?>
                    <tr>
                        <td><?= $subject['subject_name'] ?></td>
                        <td><?= $subject['subject_papercode'] ?></td>
                        <td><?= $subject['class_name'] ?></td>
                        <td><?= $subject['section'] ?></td>
                        <td><?= $subject['department'] ?></td>
                        <td>
                            <a href="manage_subjects.php?edit=true&subject_name=<?= $subject['subject_name'] ?>&subject_papercode=<?= $subject['subject_papercode'] ?>&class_name=<?= $subject['class_name'] ?>&section=<?= $subject['section'] ?>&department=<?= $subject['department'] ?>" class="button">Edit</a>
                            <form method="post" action="" style="display:inline-block;">
                                <input type="hidden" name="subject_name" value="<?= $subject['subject_name'] ?>">
                                <input type="hidden" name="subject_papercode" value="<?= $subject['subject_papercode'] ?>">
                                <input type="hidden" name="class_name" value="<?= $subject['class_name'] ?>">
                                <input type="hidden" name="section" value="<?= $subject['section'] ?>">
                                <input type="hidden" name="department" value="<?= $subject['department'] ?>">
                                <button type="submit" name="delete_subject" class="button">Delete</button>
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
