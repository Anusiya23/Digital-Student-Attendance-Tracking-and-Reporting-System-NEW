<?php
session_start();
if ($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'teacher') {
    header("Location: ../login/login.php");
    exit();
}

include('../config/db.php');
?>

<h2>Attendance Reports</h2>
<form method="post" action="">
    Select Class: <select name="class_id">
        <?php
        $sql = "SELECT * FROM classes";
        $result = $conn->query($sql);

        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['class_id'] . "'>" . $row['class_name'] . "</option>";
        }
        ?>
    </select><br>
    <input type="submit" value="Generate Report">
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $class_id = $_POST['class_id'];
    echo "<h3>Attendance Report for Class ID: $class_id</h3>";
    $sql = "SELECT students.name, attendance.date, periods.period_name, attendance.status 
            FROM attendance 
            JOIN students ON attendance.student_id=students.student_id 
            JOIN periods ON attendance.period_id=periods.period_id 
            WHERE students.class_id='$class_id'";
    $result = $conn->query($sql);

    echo "<table border='1'>
        <tr>
            <th>Student Name</th>
            <th>Date</th>
            <th>Period</th>
            <th>Status</th>
        </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['name'] . "</td>
                <td>" . $row['date'] . "</td>
                <td>" . $row['period_name'] . "</td>
                <td>" . $row['status'] . "</td>
            </tr>";
    }
    echo "</table>";
}
?>
  


  