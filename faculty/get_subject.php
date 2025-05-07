<?php
include('../config/db.php');
// Your DB connection file

// Get subjects for a class
if (isset($_POST['class_name']) && !isset($_POST['subject_name'])) {
    $class_name = $_POST['class_name'];
    $result = mysqli_query($conn, "SELECT DISTINCT subject_name FROM subjects WHERE class_name='$class_name'");
    echo "<option value=''>Select Subject Name</option>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='{$row['subject_name']}'>{$row['subject_name']}</option>";
    }
}

// Get paper code for subject and class
if (isset($_POST['subject_name']) && isset($_POST['class_name'])) {
    $subject_name = $_POST['subject_name'];
    $class_name = $_POST['class_name'];
    $result = mysqli_query($conn, "SELECT subject_papercode FROM subjects WHERE subject_name='$subject_name' AND class_name='$class_name' LIMIT 1");
    if ($row = mysqli_fetch_assoc($result)) {
        echo $row['subject_papercode']; // ✅ only the value, no <option>
    } else {
        echo '';
    }
}
?>
