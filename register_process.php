<?php
// Include the database connection file
include('config/db.php');

// Initialize message variable
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user type from the form
    $user_type = $_POST['user_type'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Prepare the SQL query based on the user type
    if ($user_type == 'student') {
        $student_regno = $_POST['student_regno'];
        $student_name = $_POST['student_name'];
        $department = $_POST['department'];
        $class_name = $_POST['class_name'];
        $section = $_POST['section'];
        $email = $_POST['email'];

        // Check if student_regno already exists
        $check = "SELECT * FROM students WHERE student_regno = '$student_regno'";
        $result = $conn->query($check);
        if ($result->num_rows > 0) {
            $message = "Error: Student with Reg.No $student_regno already exists.";
        } else {
            // Insert the student's details into the students table
            $sql = "INSERT INTO students (student_regno, student_name, department, class_name, section, email, password)
                    VALUES ('$student_regno', '$student_name', '$department', '$class_name', '$section', '$email', '$password')";
        }

    } elseif ($user_type == 'faculty') {
        $username = $_POST['username'];

        // Check if username already exists in faculty table
        $check = "SELECT * FROM faculty WHERE faculty_name = '$username'";
        $result = $conn->query($check);
        if ($result->num_rows > 0) {
            $message = "Error: Faculty with username $username already exists.";
        } else {
            // Insert faculty details into the faculty table
            $sql = "INSERT INTO faculty (faculty_name, password) VALUES ('$username', '$password')";
        }

    } elseif ($user_type == 'admin') {
        $username = $_POST['username'];

        // Check if username already exists in admins table
        $check = "SELECT * FROM admins WHERE username = '$username'";
        $result = $conn->query($check);
        if ($result->num_rows > 0) {
            $message = "Error: Admin with username $username already exists.";
        } else {
            // Insert admin details into the admins table
            $sql = "INSERT INTO admins (username, password) VALUES ('$username', '$password')";
        }
    }

    // Execute the query and check for success
    if (isset($sql)) {
        if ($conn->query($sql) === TRUE) {
            $message = "<h3>Registered successfully as " . ucfirst($user_type) . "!</h3>";
            $message .= "<p><a href='index.php'>Go back to home</a></p>";
        } else {
            $message = "Error: " . $conn->error;
        }
    } else {
        $message = "No SQL query executed.";
    }

    // Redirect with the message
    header("Location: register.php?message=" . urlencode($message));
    exit();
}

// Close the database connection
$conn->close();
?>
