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
}

// When the form is submitted, display the student list
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_student"])) {
    $student_regno = $_POST["student_regno"];
    $student_name = $_POST["student_name"];
    $class_name = $_POST["class_name"];
    $section = $_POST["section"];
    $department = $_POST["department"];
    $batch = $_POST["batch"];
    $email = $_POST["email"];
    $contact_number = $_POST["contact_number"];
    $father_name = $_POST["father_name"];
    $father_contact = $_POST["father_contact"];
    $mother_name = $_POST["mother_name"];
    $mother_contact = $_POST["mother_contact"];
    $address = $_POST["address"];

    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploadDir = "uploads/";
        $filename = basename($_FILES['photo']['name']);
        
        // Check if the file already exists and increment the filename
        $fileExt = pathinfo($filename, PATHINFO_EXTENSION);
        $baseName = "photo"; // Base name for the photo

        // Check for the highest existing photo number
        $existingFiles = glob($uploadDir . $baseName . "*.{$fileExt}");
        $fileCount = count($existingFiles);
        
        // New filename will be based on the count (e.g., photo1.jpg, photo2.jpg)
        $newFilename = $baseName . ($fileCount + 1) . "." . $fileExt;
        $targetFile = $uploadDir . $newFilename;

        // Move the uploaded file
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
            $photo = $newFilename; // Store the new filename in DB
        } else {
            $photo = null;
        }
    }

    // Check if student already exists
    $check = $conn->prepare("SELECT student_regno FROM students WHERE student_regno = ?");
    $check->bind_param("s", $student_regno);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error_message = "Student with Reg No '$student_regno' already exists.";
        $check->close();
    } else {
        $check->close();

        // Insert new student into the database
        $stmt = $conn->prepare("INSERT INTO students (student_regno, student_name, class_name, section, department,batch ,email, contact_number, father_name, father_contact, mother_name, mother_contact, address, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssss", $student_regno, $student_name, $class_name, $section, $department,$batch ,$email, $contact_number, $father_name, $father_contact, $mother_name, $mother_contact, $address, $photo);

        if ($stmt->execute()) {
            $success_message = "Student profile added successfully.";
        } else {
            $error_message = "Error adding profile: " . $stmt->error;
        }

        $stmt->close();
    }
    
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Student Profile</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/forms.css">
    <link rel="stylesheet" href="../css/topbar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    
    <style>
        
        input[type="text"],input[type="number"],input[type="email"],input[type="file"], select {
            width: 60%;
            padding: 10px;
            margin-bottom: 2px 0;
            display: block;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            
            
        }
        
</style>
    <script>
    function validateContact(input) {
        if (input.value.length > 10) input.value = input.value.slice(0, 10);
    }
    function showImage(event) {
        const image = document.getElementById("preview");
        image.src = URL.createObjectURL(event.target.files[0]);
        image.style.display = 'block';
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

<div class="content">
    <h2>Add Student Profile</h2>
    <?php if (!empty($success_message)) echo "<p style='color:green;'>$success_message</p>"; ?>
    <?php if (!empty($error_message)) echo "<p style='color:red;'>$error_message</p>"; ?>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-grid">
            <div>
            <label>Upload Picture:</label>
                <input type="file" name="photo" accept="image/*" onchange="showImage(event)" required>
                <img id="preview" src="#" alt="Student Image" style="display:none; max-width:300px;">
                <label>Student Reg No:</label>
                <input type="text" name="student_regno" required>
                <label>Student Name:</label>
                <input type="text" name="student_name" required>
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
                <label>Section:</label>
                <select name="section" id="section" required>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <label>Department:</label>
                <select name="department" id="department" required>
                    <option value="">Select Department</option>
                    <?php
                    // Populate class names from database
                    $class_query = $conn->query("SELECT DISTINCT department FROM classes");
                    while ($class_row = $class_query->fetch_assoc()) {
                        $selected = (isset($_POST['department']) && $_POST['department'] === $class_row['department']) ? 'selected' : '';
                        echo "<option value='{$class_row['department']}' $selected>{$class_row['department']}</option>";
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
                </div>
                <div>
                
                <label>Email ID:</label>
                <input type="email" name="email" required>
                
                <label>Contact Number:</label>
                <input type="number" name="contact_number" maxlength="10" oninput="validateContact(this)" required>
                <label>Father's Name:</label>
                <input type="text" name="father_name" required>
                <label>Father's Contact:</label>
                <input type="number" name="father_contact" maxlength="10" oninput="validateContact(this)" required>
                <label>Mother's Name:</label>
                <input type="text" name="mother_name" required>
                <label>Mother's Contact:</label>
                <input type="number" name="mother_contact" maxlength="10" oninput="validateContact(this)" required>
                <label>Address:</label>
                <textarea name="address" required></textarea>
                
                <button type="submit" name="upload_student" class="button">Add Profile</button>
            </div>
        </div>
        
    </form>

    <!-- Display Existing Student Profiles -->
    <!-- Display Existing Student Profiles with Search Bar -->
<div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: center;">
    <h2>Existing Student Profiles</h2>
    <form method="GET" style="display: flex; gap: 8px;">
        <input type="text" name="search" placeholder="Search by Reg No, Class, or Dept" style="padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
        <button type="submit" class="button">Search</button>
    </form>
</div>

<div class="student-profile" style="display: flex; flex-wrap: wrap; gap: 20px;">
<?php
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    $stmt = $conn->prepare("SELECT * FROM students 
        WHERE student_regno LIKE ? OR class_name LIKE ? OR department LIKE ?
        ORDER BY 
            CASE 
                WHEN photo IS NOT NULL AND photo != '' THEN 0 
                ELSE 1 
            END");
    $param = "%$search%";
    $stmt->bind_param("sss", $param, $param, $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT * FROM students 
        ORDER BY 
            CASE 
                WHEN photo IS NOT NULL AND photo != '' THEN 0 
                ELSE 1 
            END");
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) 
    {
        echo '<div class="student-card" style="width: 200px; border: 1px solid #ccc; border-radius: 8px; padding: 10px; text-align: center; box-shadow: 2px 2px 10px rgba(0,0,0,0.1);">';

        // ✅ Check if photo exists
        $photoPath = 'uploads/' . $row['photo'];

        if (!empty($row['photo']) && file_exists('uploads/' . $row['photo'])) {
            echo '<img src="uploads/' . $row['photo'] . '" alt="Student Photo" style="width: 100%; height: 180px; object-fit: cover; border-radius: 5px;">';
        } else {
            echo '<div style="width: 100%; height: 180px; display: flex; align-items: center; justify-content: center; background-color: #f2f2f2; border-radius: 5px; color: #888;">No Profile Photo</div>';
        }
        

        echo '<div class="student-info" style="margin-top: 10px;">';
        echo '<div><strong>' . htmlspecialchars($row['student_regno']) . '</strong></div>';
        echo '<div>' . htmlspecialchars($row['student_name']) . '</div>';
        echo '<div>' . htmlspecialchars($row['class_name']) . ' - ' . htmlspecialchars($row['section']) . '</div>';
        echo '</div>';
        echo '<div class="action-buttons" style="margin-top: 10px;">';
        echo '<a class="update-btn" href="updatedel_profile.php?reg_no=' . urlencode($row['student_regno']) . '" style="margin: 3px; padding: 5px 10px; background-color: #9b59b6; color: white; text-decoration: none; border-radius: 4px;"" onmouseover="this.style.backgroundColor=\'dodgerblue\'" onmouseout="this.style.backgroundColor=\'#9b59b6\'">Update</a>';
        echo '<a class="delete-btn" href="updatedel_profile.php?reg_no=' . urlencode($row['student_regno']) . '" onclick="return confirm(\'Are you sure to delete this student?\')" style="margin: 3px; padding: 5px 10px; background-color: #9b59b6; color: white; text-decoration: none; border-radius: 4px;" onmouseover="this.style.backgroundColor=\'dodgerblue\'" onmouseout="this.style.backgroundColor=\'#9b59b6\'">Delete</a>';

        echo '</div>';
        echo '</div>';
    }
    
} else {
    echo '<p>No student profiles found.</p>';
}

?>

</div>

</form>
</div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include your script -->
<script src="../js/cls_dept_link.js"></script>
<script src="../js/sidebar.js"></script>
<script src="../js/topbar.js"></script>
</body>
</html>