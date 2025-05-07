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
$success_message = ""; // Initialize success message variable
// Check if there's a message stored in the session
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying it
}

// Check if we're editing
if (isset($_GET['edit']) && isset($_GET['class_name']) && isset($_GET['section']) && isset($_GET['department'])) {
    $edit_mode = true;
    $class_name = $_GET['class_name'];
    $section = $_GET['section'];
    $department = $_GET['department'];
}

// Add a new class or update an existing one
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_class'])) {
        // Add new class
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        $sql = "INSERT INTO classes (class_name, section, department) VALUES ('$class_name', '$section', '$department')";
        if ($conn->query($sql)) {
            $_SESSION['message'] = "Class added successfully!";
            header('Location: manage_classes.php');
            exit();
        } else {
            echo "Error adding class: " . $conn->error;
        }
    } elseif (isset($_POST['edit_class'])) { // Update class in the database
        // Retrieve the new values from the form
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        // Update the class in the database
        $sql = "UPDATE classes 
                SET class_name='$class_name', section='$section', department='$department' 
                WHERE class_name='$class_name'";

        if ($conn->query($sql)) {
            $_SESSION['message'] = "Class updated successfully!";
            header('Location: manage_classes.php');
            exit();
        } else {
            // Display the error message
            echo "Error updating class: " . $conn->error . "<br>";
            echo "SQL Query: " . $sql;
        }
    } elseif (isset($_POST['delete_class'])) { // Delete class
        // Check if there are subjects linked to this class
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $department = $_POST['department'];

        // Check for related records in subjects table
        $subject_check_sql = "SELECT * FROM subjects WHERE class_name = '$class_name'";
        $result = $conn->query($subject_check_sql);

        if ($result->num_rows > 0) {
            $error_message = "Error: Cannot delete class as it is associated with subjects.";
        } else {
            // Proceed to delete the class if no associated subjects
            $delete_sql = "DELETE FROM classes WHERE class_name='$class_name' AND section='$section' AND department='$department'";
            if ($conn->query($delete_sql)) {
                $_SESSION['message'] = "Class deleted successfully!";
                header('Location: manage_classes.php');
                exit();
            } else {
                $error_message = "Error deleting class: " . $conn->error;
            }
        }
    }
}

// Fetch existing classes
$classes = $conn->query("SELECT * FROM classes");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
       
    input[type="text"], select {
            width: 40%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
    }
    
        .button {
            background-color:#9b59b6;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display:inline-block;
            font-size: 14px; 
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            width:auto;
        }

        .button:hover {
            background-color: dodgerblue ;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        input[type="submit"] {
            background-color: #9b59b6;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-left:50px;
        }
        td {
            padding: 5px 5px;
        }
    </style>
</head>
<body>
<?php include('../clgeheader.html'); ?>
<div class="topbar" id="topbar">
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
    <div class="side-content" id="sidebar">
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
    
        <h2>Manage Classes</h2>
          
        <?php if ($edit_mode): ?>
            <!-- Edit Form -->
            <h3>Edit Class</h3>
            <form method="post" action="">
                <label>Class Name</label>
                <input type="text" name="class_name" value="<?= $class_name ?>" required>

                <label>Section</label>
                <select name="section" required>
                    <option value="A" <?= $section == 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $section == 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $section == 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $section == 'D' ? 'selected' : '' ?>>D</option>
                </select>

                <label>Department</label>
                <input type="text" name="department" value="<?= $department ?>" required>

                <button type="submit" name="edit_class" class="button">Update Class</button>
            </form>

        <?php else: ?>
            <!-- Add Form -->
            <form method="post" action="">
                <label>Class Name</label>
                <input type="text" name="class_name" placeholder="Class Name" required>

                <label>Section</label>
                <select name="section" required>
                    <option value="A" selected>A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>

                <label>Department</label>
                <input type="text" name="department" placeholder="Department" required>

                <input type="submit" name="add_class" value="Add Class" class="button">
            </form>
            <?php if (!empty($success_message)): ?>
            <p class="success-message"><?= $success_message ?></p>
        <?php endif; ?>
            <!-- Display error message below the Add Class button -->
            <?php if (!empty($error_message)): ?>
                <p style="color: red; font-weight: bold;"><?= $error_message ?></p>
            <?php endif; ?>
        <?php endif; ?>
         
        <h2>Existing Classes</h2>
        <table>
            <tr>
                <th>Class Name</th>
                <th>Section</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $classes->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['class_name'] ?></td>
                    <td><?= $row['section'] ?></td>
                    <td><?= $row['department'] ?></td>
                    <td>
                        <!-- Edit button -->
                        <a href="manage_classes.php?edit=1&class_name=<?= $row['class_name'] ?>&section=<?= $row['section'] ?>&department=<?= $row['department'] ?>" class="button">Edit</a>

                        <!-- Delete form -->
                        <form method="post" action="" style="display:inline-block;">
                            <input type="hidden" name="class_name" value="<?= $row['class_name'] ?>">
                            <input type="hidden" name="section" value="<?= $row['section'] ?>">
                            <input type="hidden" name="department" value="<?= $row['department'] ?>">
                            <input type="submit" name="delete_class" value="Delete" class="button">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
             
            </div>
         </div>       
    <script src="../js/sidebar.js"></script> <!-- Adjust the path based on your folder structure -->
    <script src="../js/topbar.js"></script>
    
</body>
</html>
