<?php
include('../config/db.php');
session_start();
if (!isset($_SESSION['user_type']) || ($_SESSION['user_type'] !== 'admin' && $_SESSION['user_type'] !== 'faculty' && $_SESSION['user_type'] !== 'student')) {
    header('Location: ../login.php');
    exit();
}

// Fetch the logged-in user's username from the session
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username']; // Use the session-stored username
} else {
    $username = 'Guest'; // Fallback in case the username isn't set
}; // include your DB connection

// Success message handler
$success = "";
if (isset($_POST['delete_student'])) {
    $regno = $_POST['regno'];
    
    // Remove the image file if it exists
    $getQuery = mysqli_query($conn, "SELECT photo FROM students WHERE student_regno='$regno'");
    if ($row = mysqli_fetch_assoc($getQuery)) {
        $photoPath = 'uploads/' . $row['photo'];
        if (file_exists($photoPath)) {
            unlink($photoPath);
        }
    }

    // Delete the student record
    $deleteQuery = mysqli_query($conn, "DELETE FROM students WHERE student_regno='$regno'");

    if ($deleteQuery) {
        echo "<script>alert('Student deleted successfully.'); window.location.href='updatedel_profile.php';</script>";
        exit;
    } else {
        echo "<script>alert('Failed to delete student.');</script>";
    }
}

// Handle deletion
if (isset($_GET['delete'])) {
    $reg_no = $_GET['delete'];
    
    // Get photo path to remove image file
    $photo_query = $conn->prepare("SELECT photo FROM students WHERE student_regno = ?");
    $photo_query->bind_param("s", $reg_no);
    $photo_query->execute();
    $photo_result = $photo_query->get_result();
    if ($photo_row = $photo_result->fetch_assoc()) {
        $photo_path = 'uploads/' . $photo_row['photo'];
        if (file_exists($photo_path)) {
            unlink($photo_path);
        }
    }

    $del_stmt = $conn->prepare("DELETE FROM students WHERE student_regno = ?");
    $del_stmt->bind_param("s", $reg_no);
    if ($del_stmt->execute()) {
        header("Location: updatedel_profile.php?success=deleted");
    }
    if (isset($_GET['success']) && $_GET['success'] == 'deleted') {
        $success = "Student deleted successfully.";
    }
    
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_student'])) {
    $reg_no = $_POST['student_regno'];
    $name = $_POST['student_name'];
    $class = $_POST['class_name'];
    $section = $_POST['section'];
    $dept = $_POST['department'];
    $batch = $_POST['batch'];
    $email = $_POST['email'];
    $contact = $_POST['contact_number'];
    $father = $_POST['father_name'];
    $father_contact = $_POST['father_contact'];
    $mother = $_POST['mother_name'];
    $mother_contact = $_POST['mother_contact'];
    $address = $_POST['address'];

    // Handle photo upload
    $photo = '';
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        $photo = basename($_FILES['photo']['name']);
        $targetFile = $targetDir . $photo;
        move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
    }

    $query = "UPDATE students SET student_name=?, class_name=?, section=?, department=?, batch=?, email=?, contact_number=?, father_name=?, father_contact=?, mother_name=?, mother_contact=?, address=?";
    if ($photo) {
        $query .= ", photo=?";
    }
    $query .= " WHERE student_regno=?";

    if ($photo) {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssssssssss", $name, $class, $section, $dept,$batch, $email, $contact, $father, $father_contact, $mother, $mother_contact, $address, $photo, $reg_no);
    } else {
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssssssssss", $name, $class, $section, $dept,$batch, $email, $contact, $father, $father_contact, $mother, $mother_contact, $address, $reg_no);
    }

    if ($stmt->execute()) {
        $success = "Student details updated successfully.";
    }
}

// Load selected student info if accessed via update link
$student = [];
if (isset($_GET['reg_no'])) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_regno = ?");
    $stmt->bind_param("s", $_GET['reg_no']);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update/Delete Student Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <h2>Update Profile</h2>
    <style>
       label { display: block; margin-top: 10px; }
        input, select, textarea { width: 100%; padding: 8px; margin-top: 5px; }
        .form-container { max-width: 600px; margin: auto;margin-top:40px; border: 1px solid #ccc; padding: 20px; border-radius: 8px; box-shadow: 2px 2px 12px rgba(0,0,0,0.1); }
        .button { margin-top: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .success { color: green; text-align: center; margin-bottom: 15px; font-weight: bold; }
    
        input[type="text"],input[type="number"],input[type="email"],input[type="file"], select {
            width: 60%;
            padding: 10px;
            margin-top: 2px 0;
            margin-bottom: 2px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="button"] {
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

        input[type="button"]:hover {
            background-color:dodgerblue; 
            transition: background-color 0.3s ease;
        }
    </style>
    <script>
        function showImage(event) {
            const [file] = event.target.files;
            const preview = document.getElementById('preview');
            if (file) {
                preview.src = URL.createObjectURL(file);
                preview.style.display = 'block';
            }
        }
        function validateContact(input) {
            if (input.value.length > 10) input.value = input.value.slice(0,10);
        }
    </script>
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
        <li><a href="../dashboard/faculty_dashboard.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="../faculty/manage_students.php" class="sidebar-link"><i class="fas fa-user-graduate"></i> Manage Students</a></li>
        <li><a href="../faculty/student_profile.php" class="sidebar-link"><i class="fas fa-id-card"></i> Student Profile</a></li>
        <li><a href="../faculty/mark_attendance.php" class="sidebar-link"><i class="fas fa-check-circle"></i> Mark Attendance</a></li>
        <li><a href="../faculty/view_attendance.php" class="sidebar-link"><i class="fas fa-eye"></i> View Attendance</a></li>
        <li><a href="../faculty/reports.php" class="sidebar-link"><i class="fas fa-file-alt"></i> Generate Reports</a></li>
    </ul>
</div>
<form method="post" action="updatedel_profile.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
    
    <input type="submit" name="delete_student" value="Delete" class="button">
</form>
    <div class="form-container">
        <?php if ($success) echo "<div class='success'>$success</div>"; ?>

        <?php if (!empty($student)) { ?>
            <form method="POST" enctype="multipart/form-data">
            
                <label>Upload Picture:</label>
                <input type="file" name="photo" accept="image/*" onchange="showImage(event)">
                <img id="preview" src="<?php echo !empty($student['photo']) ? 'uploads/'.$student['photo'] : '#'; ?>" alt="Student Image" style="<?php echo !empty($student['photo']) ? 'display:block' : 'display:none'; ?>; max-width:300px;">

                <label>Student Reg No:</label>
                <input type="text" name="student_regno" value="<?php echo htmlspecialchars($student['student_regno']); ?>" readonly>

                <label>Student Name:</label>
                <input type="text" name="student_name" value="<?php echo htmlspecialchars($student['student_name']); ?>" required>
                
                <label>Class Name:</label>
                <select name="class_name" required>
                    <option value="">Select Class</option>
                    <?php
                    $q1 = $conn->query("SELECT DISTINCT class_name FROM classes");
                    while ($row = $q1->fetch_assoc()) {
                        $selected = ($student['class_name'] == $row['class_name']) ? 'selected' : '';
                        echo "<option value='{$row['class_name']}' $selected>{$row['class_name']}</option>";
                    }
                    ?>
                </select>
                
                <label>Section:</label>
                <select name="section" required>
                    <?php
                    foreach (["A", "B", "C", "D"] as $sec) {
                        $selected = ($student['section'] == $sec) ? 'selected' : '';
                        echo "<option value='$sec' $selected>$sec</option>";
                    }
                    ?>
                </select>

                <label>Department:</label>
                <select name="department" required>
                    <option value="">Select Department</option>
                    <?php
                    $q2 = $conn->query("SELECT DISTINCT department FROM classes");
                    while ($row = $q2->fetch_assoc()) {
                        $selected = ($student['department'] == $row['department']) ? 'selected' : '';
                        echo "<option value='{$row['department']}' $selected>{$row['department']}</option>";
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

                <label>Email ID:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
               
                <label>Contact Number:</label>
                <input type="number" name="contact_number" value="<?php echo htmlspecialchars($student['contact_number']); ?>" maxlength="10" oninput="validateContact(this)" required>

                <label>Father's Name:</label>
                <input type="text" name="father_name" value="<?php echo htmlspecialchars($student['father_name']); ?>" required>

                <label>Father's Contact:</label>
                <input type="number" name="father_contact" value="<?php echo htmlspecialchars($student['father_contact']); ?>" maxlength="10" oninput="validateContact(this)" required>

                <label>Mother's Name:</label>
                <input type="text" name="mother_name" value="<?php echo htmlspecialchars($student['mother_name']); ?>" required>

                <label>Mother's Contact:</label>
                <input type="number" name="mother_contact" value="<?php echo htmlspecialchars($student['mother_contact']); ?>" maxlength="10" oninput="validateContact(this)" required>

                <label>Address:</label>
                <textarea name="address" required><?php echo htmlspecialchars($student['address']); ?></textarea>
                
                <button type="submit" name="update_student" value="Update" class="button">Update Profile</button>
                <button type="button" onclick="window.location.href='add_profile.php'" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Back</button>
            </form>
            
        <?php } ?>
    </div>
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
