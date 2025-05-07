<?php
// Include the database connection file
include('config/db.php');

// Initialize message variable
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['user_type'])) {
    $user_type = $_POST['user_type'];

    // Handle Student registration
    if ($user_type == 'student') {
        $student_regno = $_POST['student_regno'];
        $student_name = $_POST['student_name'];
        $department = $_POST['department'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $email = $_POST['email'];
        $password = password_hash($_POST['student_password'], PASSWORD_DEFAULT);

        $check = "SELECT * FROM students WHERE student_regno = '$student_regno'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            $message = "Error: Student with Reg.No $student_regno already exists.";
        } else {
            $sql = "INSERT INTO students (student_regno, student_name, department, class_name, section, email, password)
                    VALUES ('$student_regno', '$student_name', '$department', '$class_name', '$section', '$email', '$password')";
        }

    // Handle Faculty registration
    } elseif ($user_type == 'faculty') {
        $username = $_POST['username'];
        $password = password_hash($_POST['faculty_password'], PASSWORD_DEFAULT);

        $check = "SELECT * FROM faculty WHERE faculty_name = '$username'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            $message = "Error: Faculty with username $username already exists.";
        } else {
            $sql = "INSERT INTO faculty (faculty_name, password) VALUES ('$username', '$password')";
        }

    // Handle Admin registration
    } elseif ($user_type == 'admin') {
        $username = $_POST['username'];
        $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);

        $check = "SELECT * FROM admins WHERE username = '$username'";
        $result = $conn->query($check);

        if ($result->num_rows > 0) {
            $message = "Error: Admin with username $username already exists.";
        } else {
            $sql = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
        }
    }

    if (isset($sql) && $conn->query($sql) === TRUE) {
        $message = "<h3>Registered successfully as " . ucfirst($user_type) . "!</h3>";
        
    } else {
        if (!isset($sql)) {
            $message = "This user already exist.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
} else {
    $message = "Please select a user type before proceeding.";
}
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <script>
        function togglePassword(fieldId) {
            var passwordField = document.getElementById(fieldId);
            var passwordToggle = passwordField.nextElementSibling.querySelector('i');
            if (passwordField.type === "password") {
                passwordField.type = "text";
                passwordToggle.classList.remove("fa-eye");
                passwordToggle.classList.add("fa-eye-slash");
            } else {
                passwordField.type = "password";
                passwordToggle.classList.remove("fa-eye-slash");
                passwordToggle.classList.add("fa-eye");
            }
        }
           
        function goBack() {
            window.location.href = "index.php";
        }
        
    </script>
</head>
<body>
    
    <div class="register-container">
    <img src="img/register.jpg" alt="Register Image" style="width: 100px; height: 100px; display: block; margin: 0 auto 10px;">
        <h2>REGISTER</h2>
       
        <form action="register.php" method="POST">
            <div class="user-type-container">
                <label for="input1" class="control-label">User Type:</label>
                <label>
                    <input type="radio" name="user_type" value="student" onclick="showFields()"> Student
                </label>
                <label>
                    <input type="radio" name="user_type" value="faculty" onclick="showFields()"> Faculty
                </label>
                <label>
                    <input type="radio" name="user_type" value="admin" onclick="showFields()"> Admin
                </label>
            </div>

            <!-- Admin & Faculty Fields -->
<div id="adminFacultyFields" style="display:none;">
    <label>Username</label>
    <input type="text" name="username" placeholder="Enter Username">
    
    <!-- Faculty Password Field -->
    <label id="facultyPasswordLabel" style="display:none;">Faculty Password</label>
    <div class="password-container" style="display:none;" id="facultyPasswordContainer">
        <input type="password" id="faculty_password" name="faculty_password" placeholder="Enter Faculty Password">
        <span class="toggle-password" onclick="togglePassword('faculty_password')">
            <i class="fa fa-eye"></i>
        </span>
    </div>

    <!-- Admin Password Field -->
    <label id="adminPasswordLabel" style="display:none;">Admin Password</label>
    <div class="password-container" style="display:none;" id="adminPasswordContainer">
        <input type="password" id="admin_password" name="admin_password" placeholder="Enter Admin Password">
        <span class="toggle-password" onclick="togglePassword('admin_password')">
            <i class="fa fa-eye"></i>
        </span>
    </div>
</div>


            <!-- Student Fields -->
            <div id="studentFields" style="display:none;">
                <label>Student Reg.No</label>
                <input type="text" name="student_regno" placeholder="Enter Reg.no">
                <label>Student Name</label>
                <input type="text" name="student_name" placeholder="Enter Student Name">
                <label>Department</label>
                <input type="text" name="department" placeholder="Enter Department">
                <label>Class Name</label>
                <input type="text" name="class_name" placeholder="Enter Class Name">
                <label>Section</label>
                <select name="section">
                    <option value="A" selected>A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter Email">
                <label>Password</label>
                <div class="password-container">
                    <input type="password" id="student_password" name="student_password" placeholder="Enter Password">
                    <span class="toggle-password" onclick="togglePassword('student_password')">
                        <i class="fa fa-eye"></i>
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <input type="submit" value="Register">
            <input type="submit" onclick="goBack()" value="Back" style="margin-top: 5px;">
        </form>
        
    
    <div class="message-container">
        <?php echo $message; ?>
    </div>
    </div>

    <script>
    function showFields() {
    var userType = document.querySelector('input[name="user_type"]:checked').value;
    
    // Hide both sections and reset visibility
    document.getElementById('adminFacultyFields').style.display = 'none';
    document.getElementById('studentFields').style.display = 'none';
    document.getElementById('facultyPasswordLabel').style.display = 'none';
    document.getElementById('facultyPasswordContainer').style.display = 'none';
    document.getElementById('adminPasswordLabel').style.display = 'none';
    document.getElementById('adminPasswordContainer').style.display = 'none';

    // Remove the 'required' attribute from all fields
    document.querySelectorAll('input').forEach(function(input) {
        input.required = false;
    });

    if (userType == 'student') {
        document.getElementById('studentFields').style.display = 'block';
        document.querySelector('input[name="student_regno"]').required = true;
        document.querySelector('input[name="student_name"]').required = true;
        document.querySelector('input[name="department"]').required = true;
        document.querySelector('input[name="class_name"]').required = true;
        document.querySelector('input[name="email"]').required = true;
        document.querySelector('input[name="student_password"]').required = true;

    } else if (userType == 'faculty') {
        document.getElementById('adminFacultyFields').style.display = 'block';
        document.getElementById('facultyPasswordLabel').style.display = 'block';
        document.getElementById('facultyPasswordContainer').style.display = 'block';
        document.querySelector('input[name="username"]').required = true;
        document.querySelector('input[name="faculty_password"]').required = true;

    } else if (userType == 'admin') {
        document.getElementById('adminFacultyFields').style.display = 'block';
        document.getElementById('adminPasswordLabel').style.display = 'block';
        document.getElementById('adminPasswordContainer').style.display = 'block';
        document.querySelector('input[name="username"]').required = true;
        document.querySelector('input[name="admin_password"]').required = true;
    }

    // Hide message if fields are changed
    document.querySelector('.message-container').innerHTML = '';
}


    </script>
</body>
</html>
