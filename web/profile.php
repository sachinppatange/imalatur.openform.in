<?php
session_start();
include_once '../config/config.php';

// Get logged-in user id (fallback to GET id if passed)
$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($user_id <= 0) {
    header('Location: studentlogin.php');
    exit;
}

$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    echo "Database connection error. Check config.php";
    exit;
}

// Fetch only the allowed columns from `user` table
$sql = "SELECT id, ticket, email, whatsappno, participant_name, gender, dob, address, area, city, state,
               tshirt_size, emergency_name, emergency_number, blood_group, ticket_amount, amount, status, createdby, createdon
        FROM `user` WHERE id = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    echo "Database error: " . mysqli_error($conn);
    exit;
}
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = $res ? mysqli_fetch_assoc($res) : null;
mysqli_stmt_close($stmt);

if (empty($user)) {
    echo "<p>User not found. Please login or register.</p>";
    exit;
}

// Helper for safe output
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Format DOB for display (if stored as YYYY-MM-DD)
$display_dob = '';
if (!empty($user['dob'])) {
    // try YYYY-MM-DD -> dd/mm/YYYY
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $user['dob'])) {
        $dt = DateTime::createFromFormat('Y-m-d', $user['dob']);
        if ($dt) $display_dob = $dt->format('d/m/Y');
    } else {
        // display raw if not in expected format
        $display_dob = $user['dob'];
    }
}

// Format createdon
$display_createdon = '';
if (!empty($user['createdon'])) {
    $dtc = DateTime::createFromFormat('Y-m-d H:i:s', $user['createdon']);
    if ($dtc) $display_createdon = $dtc->format('d/m/Y H:i:s');
    else $display_createdon = $user['createdon'];
}

// Amount formatting
$display_ticket_amount = ($user['ticket_amount'] !== null && $user['ticket_amount'] !== '') ? number_format((float)$user['ticket_amount'], 2) : '';
$display_amount = ($user['amount'] !== null && $user['amount'] !== '') ? number_format((float)$user['amount'], 2) : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h(function_exists('getcompany') ? getcompany() : 'Profile'); ?></title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">

    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <?php include_once 'navbar.php'; ?>
    <?php include_once 'sidebar.php'; ?>

    <div class="content-wrapper" style="background-color:#fff;">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6"><h1 class="m-0">Profile</h1></div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile content -->
        <section class="content">
            <div class="container-fluid">
                <div class="card shadow-sm">
                    <div class="card-header bg-transparent border-0">
                        <h3 class="mb-0">User Information</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">User ID</th>
                                <td><?php echo 'SS96' . (30000 + intval($user['id'])); ?></td>
                            </tr>

                            <tr>
                                <th>Ticket</th>
                                <td><?php echo h($user['ticket']); ?></td>
                            </tr>

                            <tr>
                                <th>Participant Name</th>
                                <td><?php echo h($user['participant_name']); ?></td>
                            </tr>

                            <tr>
                                <th>Gender</th>
                                <td><?php echo h($user['gender']); ?></td>
                            </tr>

                            <tr>
                                <th>Date of Birth</th>
                                <td><?php echo h($display_dob); ?></td>
                            </tr>

                            <tr>
                                <th>Email</th>
                                <td><?php echo h($user['email']); ?></td>
                            </tr>

                            <tr>
                                <th>WhatsApp / Mobile</th>
                                <td><?php echo h($user['whatsappno']); ?></td>
                            </tr>

                            <tr>
                                <th>Address</th>
                                <td><?php echo nl2br(h($user['address'])); ?></td>
                            </tr>

                            <tr>
                                <th>Area</th>
                                <td><?php echo h($user['area']); ?></td>
                            </tr>

                            <tr>
                                <th>City</th>
                                <td><?php echo h($user['city']); ?></td>
                            </tr>

                            <tr>
                                <th>State</th>
                                <td><?php echo h($user['state']); ?></td>
                            </tr>

                            <tr>
                                <th>T-shirt Size</th>
                                <td><?php echo h($user['tshirt_size']); ?></td>
                            </tr>

                            <tr>
                                <th>Emergency Contact Name</th>
                                <td><?php echo h($user['emergency_name']); ?></td>
                            </tr>

                            <tr>
                                <th>Emergency Contact Number</th>
                                <td><?php echo h($user['emergency_number']); ?></td>
                            </tr>

                            <tr>
                                <th>Blood Group</th>
                                <td><?php echo h($user['blood_group']); ?></td>
                            </tr>

                            <tr>
                                <th>Ticket Amount (INR)</th>
                                <td><?php echo $display_ticket_amount !== '' ? '₹' . $display_ticket_amount : ''; ?></td>
                            </tr>

                            <tr>
                                <th>Amount (INR)</th>
                                <td><?php echo $display_amount !== '' ? '₹' . $display_amount : ''; ?></td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td><?php echo h($user['status']); ?></td>
                            </tr>

                            <tr>
                                <th>Created By (UserID)</th>
                                <td><?php echo h($user['createdby']); ?></td>
                            </tr>

                            <tr>
                                <th>Created On</th>
                                <td><?php echo h($display_createdon); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <?php include_once 'footer.php'; ?>

</div>

<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="dist/js/adminlte.js"></script>
</body>
</html>