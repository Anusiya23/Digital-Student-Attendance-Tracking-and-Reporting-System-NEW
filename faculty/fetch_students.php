<?php
session_start();
include('../config/db.php');

// Check if the user is faculty or admin
if ($_SESSION['user_type'] !== 'faculty') {
    header('Location: ../login.php');
    exit();
}

// Get username from session
$username = $_SESSION['username'] ?? 'Guest';// Replace with your DB connection script

$department = $_GET['department'] ?? '';
$class_name = $_GET['class_name'] ?? '';
$section = $_GET['section'] ?? '';

if ($department && $class_name && $section) {
    $stmt = $conn->prepare("SELECT student_regno FROM students WHERE department = ? AND class_name = ? AND section = ?");
    $stmt->bind_param("sss", $department, $class_name, $section);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    echo json_encode($students);
}
?>
