<?php
session_start();
include_once __DIR__ . '/../config/config.php'; // सुनिश्चित करा की यात $conn (mysqli) initialize होत आहे

// फक्त POST स्वीकारा
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

function clean($v) {
    return trim((string)$v);
}

// POST डेटा वाचा — सर्व फील्ड ओन्शनल (कोणतीही field required नाही)
$studid           = clean($_POST['studid'] ?? '');
$ticket           = clean($_POST['ticket'] ?? '');               // optional
$email            = clean($_POST['email'] ?? '');                // optional
$whatsapp         = clean($_POST['whatsapp'] ?? '');             // optional
$participant_name = clean($_POST['participant_name'] ?? '');     // optional
$gender           = clean($_POST['gender'] ?? '');               // optional
$dob              = clean($_POST['dob'] ?? '');                  // expected dd/mm/yyyy if provided
$address          = clean($_POST['address'] ?? '');              // optional
$area             = clean($_POST['area'] ?? '');                 // optional
$city             = clean($_POST['city'] ?? '');                 // optional
$state            = clean($_POST['state'] ?? '');                // optional
$tshirt_size      = clean($_POST['tshirt_size'] ?? '');          // optional
$emergency_name   = clean($_POST['emergency_name'] ?? '');       // optional
$emergency_number = clean($_POST['emergency_number'] ?? '');     // optional
$blood_group      = clean($_POST['blood_group'] ?? '');          // optional

// Ticket amount should be auto = 1.00 (as requested)
$ticket_amount = 1.00;

// policies / disclaimer checkboxes not required anymore (do not enforce)
$accept_policies  = isset($_POST['accept_policies']) ? true : false;
$agree_disclaimer = isset($_POST['agree_disclaimer']) ? true : false;

$createdby = $_SESSION['id'] ?? 0;
$createdon = date('Y-m-d H:i:s');

// हलकी सर्व्हर-साईड व्हॅलिडेशन (फक्त जर फील्ड पुरवले असतील तर तपासा)
// कोणतीही फील्ड बंधनकारक नाही — म्हणजे कधीही रिकामी असू शकतात.
$errors = [];

// जर whatsapp भरले असेल तर 10 digit तपासा (नसेल तर फक्त जारी ठेवा)
if ($whatsapp !== '' && !preg_match('/^[0-9]{10}$/', $whatsapp)) {
    $errors[] = 'WhatsApp number must be 10 digits if provided.';
}

// जर emergency_number भरले असेल तर 10 digit तपासा
if ($emergency_number !== '' && !preg_match('/^[0-9]{10}$/', $emergency_number)) {
    $errors[] = 'Emergency Contact number must be 10 digits if provided.';
}

// जर DOB पुरवले असेल तर format तपासा आणि रूपांतरण करा; नसेल तर खालीलप्रमाणे रिक्त ठेवा
$dob_sql = '';
if ($dob !== '') {
    $dt = DateTime::createFromFormat('d/m/Y', $dob);
    $dt_errors = DateTime::getLastErrors();
    if ($dt && empty($dt_errors['warning_count']) && empty($dt_errors['error_count'])) {
        $dob_sql = $dt->format('Y-m-d');
    } else {
        $errors[] = 'Date of Birth must be in dd/mm/yyyy format if provided.';
    }
}

// जर एखादी व्हॅलिडेशन एरर असेल तर JSON मध्ये परत द्या (तसे नको असले तर खालील भाग काढू शकता)
if (!empty($errors)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['status' => 'error', 'errors' => $errors], JSON_UNESCAPED_UNICODE);
    exit;
}

// DB connection
$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection not found. Check config.php');
}

// Duplicate whatsapp तपासणी — फक्त जर whatsapp पुरवला असेल तेव्हा तपासा
if ($whatsapp !== '' && empty($studid)) {
    $stmt = mysqli_prepare($conn, "SELECT id FROM `user` WHERE whatsappno = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $whatsapp);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $num = mysqli_stmt_num_rows($stmt);
    mysqli_stmt_close($stmt);
    if ($num > 0) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'error', 'errors' => ['Mobile Number already exists.']], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// INSERT किंवा UPDATE `user` टेबल मध्ये (prepared statements)
// नोट: अनेक फील्ड optional आहेत — जर रिकामे असतील तर रिकामे स्ट्रिंग '' म्हणून जाईल.
if (empty($studid)) {
    $sql = "INSERT INTO `user`
        (ticket, email, whatsappno, participant_name, gender, dob, address, area, city, state, tshirt_size, emergency_name, emergency_number, blood_group, ticket_amount, studphoto, studaadhar, studsign, createdby, createdon)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        http_response_code(500);
        exit('Prepare failed: ' . mysqli_error($conn));
    }

    // bind params: 14 strings, 1 double, 1 int, 1 string = 's'*14 . 'dis'
    $types = str_repeat('s', 14) . 'dis';
    mysqli_stmt_bind_param($stmt, $types,
        $ticket,            // s
        $email,             // s
        $whatsapp,          // s
        $participant_name,  // s
        $gender,            // s
        $dob_sql,           // s ('' if not provided)
        $address,           // s
        $area,              // s
        $city,              // s
        $state,             // s
        $tshirt_size,       // s
        $emergency_name,    // s
        $emergency_number,  // s
        $blood_group,       // s
        $ticket_amount,     // d
        $createdby,         // i
        $createdon          // s
    );

    $exec = mysqli_stmt_execute($stmt);
    if (!$exec) {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        http_response_code(500);
        exit('Insert failed: ' . $err);
    }
    $insert_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Session set आणि redirect/JSON (तुमचे प्रवाह जसे असतील तसे ठेवा)
    $_SESSION['id'] = $insert_id;
    $_SESSION['username'] = $email;
    $_SESSION['type'] = 'participant';
    $_SESSION['message'] = 'Registration successful. Redirecting for Payment';

    if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => 'success', 'id' => $insert_id]);
        exit;
    } else {
        header("Location: ../web/index.php?userid=$insert_id");
        exit;
    }

} else {
    // UPDATE existing record — optional fields updated as provided
    $sql = "UPDATE `user` SET
        ticket = ?, email = ?, whatsappno = ?, participant_name = ?, gender = ?, dob = ?, address = ?, area = ?, city = ?, state = ?, tshirt_size = ?, emergency_name = ?, emergency_number = ?, blood_group = ?, ticket_amount = ?, createdon = ?
        WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        http_response_code(500);
        exit('Prepare failed: ' . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($stmt, 'ssssssssssssssdsi',
        $ticket,
        $email,
        $whatsapp,
        $participant_name,
        $gender,
        $dob_sql,
        $address,
        $area,
        $city,
        $state,
        $tshirt_size,
        $emergency_name,
        $emergency_number,
        $blood_group,
        $ticket_amount,
        $createdon,
        $studid
    );
    $exec = mysqli_stmt_execute($stmt);
    if (!$exec) {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        http_response_code(500);
        exit('Update failed: ' . $err);
    }
    mysqli_stmt_close($stmt);

    $_SESSION['message'] = 'Record updated';
    header('Location: ../admin/member.php');
    exit;
}
?>