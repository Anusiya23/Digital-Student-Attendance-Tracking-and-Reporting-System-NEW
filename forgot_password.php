<?php
include('config/db.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type'];

    if ($user_type == 'admin') {
        $sql = "UPDATE admins SET password='$new_password' WHERE username='$username'";
    } elseif ($user_type == 'faculty') {
        $sql = "UPDATE faculty SET password='$new_password' WHERE faculty_name='$username'";
    } else {
        $sql = "UPDATE students SET password='$new_password' WHERE student_regno='$username'";
    }

    if ($conn->query($sql) === TRUE) {
        $message = "Password updated successfully.";
        $message .= "<p><a href='index.php'>Go back to home</a></p>";
    } else {
        $message = "Error updating password: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/forgot_password.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .login-container input[type="button"] {
    padding: 15px;
    background-color: #71b7e6;
    color: #fff;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    text-transform: uppercase;
    font-weight: bold;
    letter-spacing: 1px;
}

.login-container input[type="button"]:hover {
    background-color: #9b59b6;
}
</style>
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
            window.location.href = "login/login.php";
        }
    </script>
</head>

<body>
    <div class="login-container">
        <h2>Reset Password</h2>
        <form method="post" action="forgot_password.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="new_password">New Password:</label>
            <div class="password-container">
                <input type="password" id="new_password" name="new_password" required>
                <span class="toggle-password" onclick="togglePassword('password')">
                    <i class="fa fa-eye"></i>
                </span>
            </div>
            <div class="user-type-container">
                <label>User Type:</label>
                <label><input type="radio" name="user_type" value="student" checked> Student</label>
                <label><input type="radio" name="user_type" value="faculty"> Faculty</label>
                <label><input type="radio" name="user_type" value="admin"> Admin</label>
            </div>

            <input type="submit" value="Reset Password">

            <input type="button" onclick="goBack()" value="Back" style="margin-top: 5px;">
        </form>

        <div class="message-container">
            <?php echo $message; ?>
        </div>
    </div>
</body>
</html>
