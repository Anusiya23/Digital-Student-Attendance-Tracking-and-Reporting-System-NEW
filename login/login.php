<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    if ($user_type == 'admin') {
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password'";
    } elseif ($user_type == 'faculty') {
        $sql = "SELECT * FROM faculty WHERE faculty_name='$username' AND password='$password'";
    } else {
        $sql = "SELECT * FROM students WHERE student_name='$username' AND password='$password'";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['user'] = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $_SESSION['user_type'] = $user_type;

        if ($user_type == 'admin') {
            header("Location: ../dashboard/admin_dashboard.php");
        } elseif ($user_type == 'faculty') {
            header("Location: ../dashboard/faculty_dashboard.php");
        } else {
            header("Location: ../dashboard/student_dashboard.php");
        }
    } else {
        echo "Invalid credentials";
    }
}
// Assuming you have already validated the login credentials...
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <!-- Add Font Awesome for the eye icon -->
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
            window.location.href = "../index.php";
        }

    </script>
</head>
<body>
    <div class="login-container">
    <img src="../img/login.jpg" alt="Login Image" style="width: 100px; height: 100px; display: block; margin: 0 auto 10px;">
        <h2>Login</h2>
        
        <form method="post" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">Password:</label>
            <div class="password-container">
                <input type="password" id="password" name="password" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i class="fa fa-eye"></i>
                </span>
            </div>

            <!-- Forgot password link -->
            <p><a href="../forgot_password.php" class="forgot-password">Forgot your password?</a></p>
           
            <div class="user-type-container">
                <label>User Type:</label>
                <label><input type="radio" name="user_type" value="student" checked> Student</label>
                <label><input type="radio" name="user_type" value="faculty"> Faculty</label>
                <label><input type="radio" name="user_type" value="admin"> Admin</label>
            </div>

            <input type="submit" value="Login">
            
            <input type="submit" onclick="goBack()" value="Back" style="margin-top: 5px;">
        </form>
    </div>
</body>
</html>
