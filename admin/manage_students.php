<?php
session_start();
include('../config/db.php');

// Check if admin is logged in
if ($_SESSION['user_type'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Add, Edit, and Delete functionality
$edit_mode = false; // Flag for edit mode

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
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error adding student: " . $conn->error;
        }
    } elseif (isset($_POST['edit_student'])) { // Update student in the database
        // Retrieve the new values from the form
        $new_student_regno = $_POST['student_regno'];
        $new_student_name = $_POST['student_name'];
        $new_class_name = $_POST['class_name'];
        $new_section = $_POST['section'];
        $new_department = $_POST['department'];

        // Retrieve the original values from hidden inputs
        $original_student_regno = $_POST['original_student_regno'];
        $original_student_name = $_POST['original_student_name'];
        $original_class_name = $_POST['original_class_name'];
        $original_section = $_POST['original_section'];
        $original_department = $_POST['original_department'];

        // Update the student in the database
        $sql = "UPDATE students 
                SET student_regno='$new_student_regno', student_name='$new_student_name', 
                    class_name='$new_class_name', section='$new_section', department='$new_department' 
                WHERE student_regno='$original_student_regno' 
                  AND student_name='$original_student_name' 
                  AND class_name='$original_class_name' 
                  AND section='$original_section' 
                  AND department='$original_department'";

        if ($conn->query($sql)) {
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error updating student: " . $conn->error;
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
            header('Location: manage_students.php');
            exit();
        } else {
            echo "Error deleting student: " . $conn->error;
        }
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
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <style>
        /* Add your styles here to match the layout of manage_classes */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background-color:#9b59b6;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            padding: 20px;
            font-size:20px
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            padding: 15px 0;
        }

        .sidebar ul li a {
            color: white;
            text-decoration: none;
        }

        .main-content {
            margin-left: 300px;
            font-size :20px;
            font-weight: bold;
            padding: 20px;
            height: 100vh;
        }

        h1 {
            color: white;
        }
        h2 {
            color:#333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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

        .button {
            background-color:#9b59b6;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .button:hover {
            background-color: dodgerblue ;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        input[type="text"], select {
            width: 40%;
            padding: 10px;
            margin: 8px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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
        }

        input[type="submit"]:hover {
            background-color:dodgerblue; 
            transition: background-color 0.3s ease;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h1>Welcome Admin</h1>
        <ul>
        <li><a href="../dashboard/admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="../admin/manage_classes.php"><i class="fas fa-chalkboard"></i> Manage Classes</a></li>
            <li><a href="../admin/manage_subjects.php"><i class="fas fa-book"></i> Manage Subjects</a></li>
            <li><a href="../admin/manage_faculty.php"><i class="fas fa-user-tie"></i> Manage Faculty</a></li>
            <li><a href="../admin/view_attendance.php"><i class="fas fa-eye"></i> View Attendance</a></li>
            <li><a href="../login/login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Manage Students</h2>

        <?php if ($edit_mode): ?>
            <!-- Edit Form -->
            <h3>Edit Student</h3>
            <form method="post" action="">
                <!-- Hidden fields to store original values -->
                <input type="hidden" name="original_student_regno" value="<?= $student_regno ?>">
                <input type="hidden" name="original_student_name" value="<?= $student_name ?>">
                <input type="hidden" name="original_class_name" value="<?= $class_name ?>">
                <input type="hidden" name="original_section" value="<?= $section ?>">
                <input type="hidden" name="original_department" value="<?= $department ?>">

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

                <button type="submit" name="edit_student" class="button">Update Student</button>
            </form>
        <?php else: ?>
            <!-- Add Form -->
            <h3>Add New Student</h3>
            <form method="post" action="">
                <label>Student Reg No</label>
                <input type="text" name="student_regno" required>

                <label>Student Name</label>
                <input type="text" name="student_name" required>

                <label>Class Name</label>
                <select name="class_name" required>
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

                <label>Department</label>
                <select name="department" required>
                    <?php
                    // Reset the result pointer for $classes query
                    $classes->data_seek(0); 
                    while ($row = $classes->fetch_assoc()): ?>
                        <option value="<?= $row['department'] ?>"><?= $row['department'] ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="add_student" class="button">Add Student</button>
            </form>
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
</body>
</html>
