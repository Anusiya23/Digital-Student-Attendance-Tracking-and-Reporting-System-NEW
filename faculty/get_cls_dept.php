<?php
include('../config/db.php'); // Adjust path to your DB connection

if (isset($_POST['class_name'])) {
    $class_name = $_POST['class_name'];
    $result = mysqli_query($conn, "SELECT department FROM classes WHERE class_name='$class_name' LIMIT 1");
    $row = mysqli_fetch_assoc($result);
    echo $row['department'];
}

if (isset($_POST['department'])) {
    $department = $_POST['department'];
    $result = mysqli_query($conn, "SELECT class_name FROM classes WHERE department='$department'");
    echo "<option value=''>Select Class </option>";
    $options = "";
    while ($row = mysqli_fetch_assoc($result)) {
        $options .= "<option value='{$row['class_name']}'>{$row['class_name']}</option>";
    }
    echo $options;
}
?>
