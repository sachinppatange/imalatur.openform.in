<?php
session_start();
include_once "../config/config.php";

if (empty($_SESSION['id'])) {
    header("location: ../index.php");
    exit;
}

// Fetch user data from `user` table based on session id
$user_id = intval($_SESSION['id']);
$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    exit('Database connection error. Check config.php');
}

$sql = "SELECT id, ticket, email, whatsappno, participant_name, gender, dob, address, area, city, state, tshirt_size, emergency_name, emergency_number, blood_group, ticket_amount, amount, status, createdby, createdon
        FROM `user` WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    exit('Database prepare error: ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (empty($user)) {
    exit('User not found.');
}

// Helper: safe output
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Convert number to words (simple, supports up to lakhs)
function convertNumberToWords($number) {
    $number = intval(round($number));
    $words = array(
        0 => 'Zero',1 => 'One',2 => 'Two',3 => 'Three',4 => 'Four',5 => 'Five',6 => 'Six',7 => 'Seven',8 => 'Eight',9 => 'Nine',
        10 => 'Ten',11 => 'Eleven',12 => 'Twelve',13 => 'Thirteen',14 => 'Fourteen',15 => 'Fifteen',16 => 'Sixteen',17 => 'Seventeen',
        18 => 'Eighteen',19 => 'Nineteen',20 => 'Twenty',30 => 'Thirty',40 => 'Forty',50 => 'Fifty',60 => 'Sixty',70 => 'Seventy',
        80 => 'Eighty',90 => 'Ninety'
    );
    if ($number < 21) return $words[$number];
    if ($number < 100) {
        $t = intval($number / 10) * 10;
        $r = $number % 10;
        return $words[$t] . ($r ? ' ' . $words[$r] : '');
    }
    if ($number < 1000) {
        $h = intval($number / 100);
        $rem = $number % 100;
        return $words[$h] . ' Hundred' . ($rem ? ' ' . convertNumberToWords($rem) : '');
    }
    if ($number < 100000) {
        $th = intval($number / 1000);
        $rem = $number % 1000;
        return convertNumberToWords($th) . ' Thousand' . ($rem ? ' ' . convertNumberToWords($rem) : '');
    }
    if ($number < 10000000) {
        $l = intval($number / 100000);
        $rem = $number % 100000;
        return convertNumberToWords($l) . ' Lakh' . ($rem ? ' ' . convertNumberToWords($rem) : '');
    }
    return (string)$number;
}

// Prepare display values
$amount = is_numeric($user['amount']) ? floatval($user['amount']) : 0;
$amount_int = intval(floor($amount));
$amount_words = convertNumberToWords($amount_int);
$ticket_amount = is_numeric($user['ticket_amount']) ? number_format((float)$user['ticket_amount'], 2) : '';
$display_dob = '';
if (!empty($user['dob'])) {
    // if stored as YYYY-MM-DD convert to dd/mm/YYYY else show raw
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $user['dob'])) {
        $dt = DateTime::createFromFormat('Y-m-d', $user['dob']);
        if ($dt) $display_dob = $dt->format('d/m/Y');
        else $display_dob = $user['dob'];
    } else {
        $display_dob = $user['dob'];
    }
}
$display_createdon = '';
if (!empty($user['createdon'])) {
    $dtc = DateTime::createFromFormat('Y-m-d H:i:s', $user['createdon']);
    if ($dtc) $display_createdon = $dtc->format('d/m/Y H:i:s');
    else $display_createdon = $user['createdon'];
}

// Build simple HTML (keeps layout minimal and only lists allowed user fields)
$html = '<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, Arial, sans-serif; font-size:14px; }
.table { width:100%; border-collapse: collapse; }
.table th, .table td { border: 1px solid #000; padding: 8px; vertical-align: top; text-align: left; }
.table th { background: #f2f2f2; width:35%; }
.header { text-align:center; margin-bottom:10px; }
.section { border: 2px solid #000; padding:10px; }
</style>
</head>
<body>
<div class="header">
  <h2>Registration & Payment Receipt</h2>
</div>
<div class="section">
<table class="table">
  <tr><th>User ID</th><td>' . h('SS96' . (30000 + intval($user['id']))) . '</td></tr>
  <tr><th>Ticket</th><td>' . h($user['ticket']) . '</td></tr>
  <tr><th>Participant Name</th><td>' . h($user['participant_name']) . '</td></tr>
  <tr><th>Gender</th><td>' . h($user['gender']) . '</td></tr>
  <tr><th>Date of Birth</th><td>' . h($display_dob) . '</td></tr>
  <tr><th>Email</th><td>' . h($user['email']) . '</td></tr>
  <tr><th>WhatsApp / Mobile</th><td>' . h($user['whatsappno']) . '</td></tr>
  <tr><th>Address</th><td>' . nl2br(h($user['address'])) . '</td></tr>
  <tr><th>Area</th><td>' . h($user['area']) . '</td></tr>
  <tr><th>City</th><td>' . h($user['city']) . '</td></tr>
  <tr><th>State</th><td>' . h($user['state']) . '</td></tr>
  <tr><th>T-shirt Size</th><td>' . h($user['tshirt_size']) . '</td></tr>
  <tr><th>Emergency Contact Name</th><td>' . h($user['emergency_name']) . '</td></tr>
  <tr><th>Emergency Contact Number</th><td>' . h($user['emergency_number']) . '</td></tr>
  <tr><th>Blood Group</th><td>' . h($user['blood_group']) . '</td></tr>
  <tr><th>Ticket Amount (INR)</th><td>' . ($ticket_amount !== '' ? '₹ ' . h($ticket_amount) : '') . '</td></tr>
  <tr><th>Amount Paid (INR)</th><td>₹ ' . h(number_format($amount, 2)) . '</td></tr>
  <tr><th>Payment (in words)</th><td>' . h($amount_words) . ' only</td></tr>
  <tr><th>Status</th><td>' . h($user['status']) . '</td></tr>
  <tr><th>Created By</th><td>' . h($user['createdby']) . '</td></tr>
  <tr><th>Created On</th><td>' . h($display_createdon) . '</td></tr>
</table>
</div>
</body>
</html>';

// Generate PDF using mPDF (same as original)
require_once __DIR__ . '/../vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
$mpdf->WriteHTML($html);
$mpdf->Output();
exit;
?>