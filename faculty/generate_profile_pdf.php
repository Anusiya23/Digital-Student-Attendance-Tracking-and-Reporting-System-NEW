<?php
require_once('C:/xampp/htdocs/Digital-Student-Attendance-Tracking and Reporting-System-main/TCPDF-main/tcpdf.php');
include('../config/db.php');

if (isset($_POST['student_regno']) && !empty($_POST['student_regno'])) {
    $regno = mysqli_real_escape_string($conn, $_POST['student_regno']);
    $query = mysqli_query($conn, "SELECT * FROM students WHERE student_regno = '$regno'");
    $student = mysqli_fetch_assoc($query);

    if ($student) {
        $pdf = new TCPDF();
        $pdf->AddPage();
        $imagePath = 'C:/xampp/htdocs/Digital-Student-Attendance-Tracking and Reporting-System-main/img/vicas_logo.jpg';
        $header_html = '

        <table cellspacing="0" cellpadding="2" style="width: 90%; border-bottom:4px solid #ddd;">
            <tr>
                <td width="30%" align="right" style="padding-top: 40px;">
                <br><br>
                    <img src="' . $imagePath . '" height="110" />
                </td>
                <td width="70%" align="center" valign="top" style="line-height:1.5; text-align: justify;">
                 <br><br>
                <span style="color: #e91e63; font-size: 23px; font-weight: bold;font-family: Times New Roman; letter-spacing: 4px; margin: -10px 0 0 0;">VIVEKANANDHA</span><br>
                    <span style="font-size: 12px; margin: 2px 0 0 0;">College of Arts and Sciences for Women (Autonomous)</span><br>
                    <span style="font-size: 10px; color: midnightblue; margin: 2px 0 0 0;">
                        \'A+\' Grade by NAAC || ISO 9001:2015 Certified<br/>
                        DST-FIST & DST-PG CURIE Sponsored<br/>
                        Approved by UGC Act 1956 under Section 2(f)&12(B) and AICTE<br/>
                        Affiliated to Periyar University, Salem
                    </span>
                </td>
            </tr>
        </table><br><br>
        ';
        

// Print the college header
$pdf->writeHTML($header_html, true, false, false, false, '');
        // Add Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Student Profile', 0, 1, 'C');

        // Add Student Photo if available
        $photoPath = '../faculty/uploads/' . $student['photo'];
        if (!empty($student['photo']) && file_exists($photoPath)) {
            $pdf->Image($photoPath, 80, 80, 40, 50, '', '', '', true);
            $pdf->Ln(60); // Add space after the image
        } else {
            $pdf->Ln(10); // Add some space if no photo
        }

        // Set font for table
        $pdf->SetFont('helvetica', '', 12);

        // Student Details Table
        $html = '<table border="1" cellpadding="5">
                    <tr><th width="35%">Reg No</th><td>' . $student['student_regno'] . '</td></tr>
                    <tr><th>Name</th><td>' . $student['student_name'] . '</td></tr>
                    <tr><th>Class</th><td>' . $student['class_name'] . '</td></tr>
                    <tr><th>Section</th><td>' . $student['section'] . '</td></tr>
                    <tr><th>Department</th><td>' . $student['department'] . '</td></tr>
                    <tr><th>Batch</th><td>' . $student['batch'] . '</td></tr>
                    <tr><th>Contact</th><td>' . $student['contact_number'] . '</td></tr>
                    <tr><th>Father Name</th><td>' . $student['father_name'] . '</td></tr>
                    <tr><th>Father Contact</th><td>' . $student['father_contact'] . '</td></tr>
                    <tr><th>Mother Name</th><td>' . $student['mother_name'] . '</td></tr>
                    <tr><th>Mother Contact</th><td>' . $student['mother_contact'] . '</td></tr>
                    <tr><th>Address</th><td>' . $student['address'] . '</td></tr>
                </table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output("Student_Profile_{$student['student_regno']}.pdf", 'D');
    } else {
        echo "Student not found!";
    }
} else {
    echo "Invalid request!";
}
?>
