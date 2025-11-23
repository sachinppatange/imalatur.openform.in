<?php
session_start();
include_once "../config/config.php";
include_once '../controller/ctrlgetStudDetails.php';

if (empty($_SESSION['id'])) {
    header("location: ../index.php");
}

require_once __DIR__ . '/../vendor/autoload.php';

$studid = $_SESSION['id'];


// Fetch student details
$student = getStudentRegById($studid);

function convertNumberToWords($number)
{
    $words = array(
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
        20 => 'Twenty',
        30 => 'Thirty',
        40 => 'Forty',
        50 => 'Fifty',
        60 => 'Sixty',
        70 => 'Seventy',
        80 => 'Eighty',
        90 => 'Ninety'
    );

    if ($number == 0)
        return 'Zero';

    if ($number < 21)
        return $words[$number];

    if ($number < 100) {
        return $words[$number - ($number % 10)] . ($number % 10 ? " " . $words[$number % 10] : "");
    }

    if ($number < 1000) {
        return $words[intval($number / 100)] . " Hundred " . convertNumberToWords($number % 100);
    }

    if ($number < 1000000) {
        return convertNumberToWords(intval($number / 1000)) . " Thousand " . convertNumberToWords($number % 1000);
    }

    return "Number too large";
}

$amount = (int) $student['amount'];
$amountInWords = convertNumberToWords($amount);
$json = $student['centre'];

$data = json_decode($json, true); // Decode JSON as an associative array

// Correct way to access values
// print_r($data['centre1']); // Outputs: (null, meaning empty)
// print_r($data['centre2']); // Outputs: Latur (लातूर)
// print_r($data['centre3']); // Outputs: Solapur (सोलापूर)

// Print the decoded array

$html = '<html>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ccc;
    text-align: left;
}

th {
    background-color: #f2f2f2;
    width: 30%;
}

td {
    width: 70%;
}
</style>
<body>
<h2 align="center"> Shri Tuljabhavani Temple Trusts, <bR>
Shri Tuljabhavani Sainiki Sec. & Higher Sec. School,Tuljapur </h2>
    <h3 style="text-align: center;"><u>Students Registration and Payment Receipt</u></h3>
    <section class="section" style="border:4px solid black;">
       <table border="2" cellspacing="2" cellpadding="5" class="table table-bordered"
       style="border:1px solid #000; width:100%; margin:-8px; page-break-inside: avoid;">
    <tr>
        <th>Student ID</th>
        <td>' . htmlspecialchars($student['stud_id ']) . '</td>
    </tr>
    <tr>
        <th>Surname</th>
        <td>' . htmlspecialchars($student['surname']) . '</td>
    </tr>
    <tr>
        <th>First Name</th>
        <td>' . htmlspecialchars($student['firstname']) . '</td>
    </tr>
    <tr>
        <th>Father’s Name</th>
        <td>' . htmlspecialchars($student['fathername']) . '</td>
    </tr>
    <tr>
        <th>Mother’s Name</th>
        <td>' . htmlspecialchars($student['mothername'] ?? '') . '</td>
    </tr>
    <tr>
        <th>Email ID</th>
        <td>' . htmlspecialchars($student['email']) . '</td>
    </tr>
    <tr>
        <th>WhatsApp No.</th>
        <td>' . htmlspecialchars($student['whatsappno']) . '</td>
    </tr>
    <tr>
        <th>Alternate Number</th>
        <td>' . htmlspecialchars($student['alternateno']) . '</td>
    </tr>
    <tr>
        <th>Aadhar No.</th>
        <td>' . htmlspecialchars($student['aadhar']) . '</td>
    </tr>
    <tr>
        <th>Course</th>
        <td>' . htmlspecialchars($student['course']) . '</td>
    </tr>
    <tr>
        <th>Date of Birth</th>
        <td>' . htmlspecialchars($student['dob']) . '</td>
    </tr>
    <tr>
        <th>Category</th>
        <td>' . htmlspecialchars($student['category']) . '</td>
    </tr>
	<tr>
        <th>Admission Category</th>
        <td>' . htmlspecialchars($student['adcategory']) . '</td>
    </tr>
    <tr>
        <th>Address</th>
        <td>' . htmlspecialchars($student['address']) . '</td>
    </tr>
    <tr>
        <th>Present School Name</th>
        <td>' . htmlspecialchars($student['schoolname']) . '</td>
    </tr>
    <tr>
        <th>Previous Std.</th>
        <td>' . htmlspecialchars($student['previousstd']) . '</td>
    </tr>
    <tr>
        <th>Grade Obtained in Previous Std.</th>
        <td>' . htmlspecialchars($student['grade']) . '</td>
    </tr>
    <tr>
        <th>Board</th>
        <td>' . htmlspecialchars(ucfirst($student['board'])) . '</td>
    </tr>
    <tr>
        <th>Preferred Language for Exam</th>
        <td>' . htmlspecialchars($student['language']) . '</td>
    </tr>
    <!--<tr>
        <th>Exam Centre Choice</th>
        <td>';

foreach ($data as $key => $value) {
    if ($value !== null) {
        $html .= htmlspecialchars($value) . '<br>';
    }
}

$html .= '      </td>
    </tr>-->
    <tr>
        <th>Fee Paid</th>
        <td>₹ ' . htmlspecialchars($student['amount']) . '/-</td>
    </tr>
</table>

<!-- Amount in Words and Authorized Signatory section -->
<div style="margin-top: 20px; page-break-inside: avoid; page-break-before: auto;">
    <p style="font-size:15px; padding-bottom:10px;">Amount in Words: ₹ ' . htmlspecialchars($amountInWords) . ' only</p>
    <p style="font-size:15px; text-align: right;">STBSV Tuljapur &nbsp;&nbsp;</p>
</div>

    </section>
</body>
</html>';

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output();
?>