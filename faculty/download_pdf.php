<?php
require_once('C:/xampp/htdocs/Digital-Student-Attendance-Tracking and Reporting-System-main/TCPDF-main/tcpdf.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $department = $_POST['department'];
    $class_name = $_POST['class_name'];
    $section = $_POST['section'];
    $semester = $_POST['semester_no'];
    $batch = $_POST['batch'];

    // Convert dates to Y-m-d for database query
    $from_date = date('Y-m-d', strtotime($_POST['from_date']));
    $to_date = date('Y-m-d', strtotime($_POST['to_date']));

    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'attendanceproject');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch attendance records
    $query = "SELECT student_regno, student_name, status, date FROM dayattendance 
              WHERE department = '$department' 
              AND class_name = '$class_name' 
              AND section = '$section' 
              AND semester_no = '$semester' 
              AND batch = '$batch'
              AND date BETWEEN '$from_date' AND '$to_date'
              ORDER BY student_regno, date";

    $result = $conn->query($query);
    $attendanceData = [];
    $studentTotals = [];


    if ($result->num_rows > 0) {
        // Create new PDF document
        $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);

        $pdf->SetMargins(10, 5, 10);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();
        $imagePath = 'C:/xampp/htdocs/Digital-Student-Attendance-Tracking and Reporting-System-main/img/vicas_logo.jpg';
        $header_html = '

        <table cellspacing="0" cellpadding="2" style="width: 90%; border-bottom:4px solid #ddd;">
            <tr>
                <td width="30%" align="right" style="padding-top: 40px;">
                <br><br>
                    <img src="' . $imagePath . '" height="130" />
                </td>
                <td width="70%" align="center" valign="top" style="line-height:1.5; text-align: justify;">
                 <br><br>
                <span style="color: #e91e63; font-size: 26px; font-weight: bold;font-family: Times New Roman; letter-spacing: 4px; margin: -10px 0 0 0;">VIVEKANANDHA</span><br>
                    <span style="font-size: 15px; margin: 2px 0 0 0;">College of Arts and Sciences for Women (Autonomous)</span><br>
                    <span style="font-size: 13px; color: midnightblue; margin: 2px 0 0 0;">
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

        // Then add the title as HTML
        $title_html = '
        <h2 style="text-align: center;">Attendance Report</h2>
        <p style="text-align: center;">
            Department: ' . $department . ' | Class: ' . $class_name . ' | Section: ' . $section . ' | Semester: ' . $semester . ' | Batch: ' . $batch . '<br>
            From: ' . $from_date . ' To: ' . $to_date . '
        </p><br>
        ';

        $pdf->writeHTML($title_html, true, false, false, false, '');
        $sundayPrinted = [];

        // Table header
        $start = strtotime($from_date);
        $end = strtotime($to_date);
        $date_columns = [];
        $html = '<table border="1" cellpadding="3">
        <thead>
            <tr style="background-color:#f2f2f2;">
                <th>S.No</th>
                <th>Student Reg. No</th>
                <th>Student Name</th>';
                for ($i = $start; $i <= $end; $i += 86400) {
                    if (date('w', $i) != 0) {
                        // Display date header only if not Sunday
                        $html .= '<th>' . date('d/m/Y', $i) . '</th>';
                    }
                }
        $html .= '<th>Attendance %</th>';

        $html .= '</tr></thead><tbody>';


        // Fetch attendance data
        $sno = 1;
        $attendanceData = [];
        while ($row = $result->fetch_assoc()) {
            $regno = $row['student_regno'];
            $name = $row['student_name'];
            $date = strtotime($row['date']);
            $status = $row['status'];
            $dayOfWeek = date('w', $date); // 0 = Sunday

            // Initialize student
            if (!isset($attendanceData[$regno])) {
                $attendanceData[$regno] = [
                    'student_name' => $name,
                    'attendance' => []
                ];
                $studentTotals[$regno] = ['present' => 0, 'total' => 0];
            }

            // Record status for date
            $attendanceData[$regno]['attendance'][date('Y-m-d', $date)] = $status; // Store with Y-m-d format

            // Skip Sunday for percentage calculation
            if ($dayOfWeek != 0) {
                $studentTotals[$regno]['total']++;
                if ($status === 'Present') {
                    $studentTotals[$regno]['present']++;
                }
            }
        }

        // Add rows to table
        $sno = 1;
        $totalRows = count($attendanceData);

$sundayCols = [];
        foreach ($attendanceData as $regno => $data) {
            $name = $data['student_name'];
            $present = $studentTotals[$regno]['present'];
            $total = $studentTotals[$regno]['total'];
            $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

            $html .= "<tr><td>$sno</td><td>$regno</td><td>$name</td>";
            
            for ($i = strtotime($from_date); $i <= strtotime($to_date); $i += 86400) {
                if (date('w', $i) == 0) {
                    // Handle Sunday column
                    if (!isset($sundayCols[$i])) {
                        $sundayCols[$i] = true;
                    }
            
            
                    continue;
                }
            
                $date_str = date('Y-m-d', $i); // Get date in Y-m-d format
                $status = isset($data['attendance'][$date_str]) ? $data['attendance'][$date_str] : 'N/A'; // Use Y-m-d format to retrieve
                $symbol = ($status === 'Present') ? 'P' : (($status === 'Absent') ? 'A' : '-');
               
                $html .= "<td >$symbol</td>";
            }
            $html .= "<td>{$percentage}%</td></tr>";
            $sno++;
        }
        $html .= "</tbody></table>";


        // Add signature section
        $html .= '<br><br><table style="width:100%; border:none;">
            <tr>
                <td style="width: 50%; border: none;">
            <table style="width:100%;"><tr><td style="text-align: left; padding-left: 100px;font-size:15px;"><br><br><br><br><br>Class Incharge</td></tr></table>
        </td>
        <td style="width: 40%; text-align: right; padding-right: 30px; border: none;font-size:15px;"><br><br><br><br><br>HOD</td>
            </tr>
          </table>';


        $pdf->writeHTML($html, true, false, false, false, '');
        $pdf->Output('attendance_report.pdf', 'D');
        exit;
    } else {
        echo "<script>alert('No attendance records found!');</script>";
    }

    $conn->close();
}
?>