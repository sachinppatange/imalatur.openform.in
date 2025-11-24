<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

// Get user id from session (fallback to GET id if present)
$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : (isset($_GET['id']) ? intval($_GET['id']) : 0);

if ($user_id <= 0) {
    // not logged in / no id — redirect to login or show message
    header('Location: studentlogin.php');
    exit;
}

// Fetch user row from `user` table
$conn = $GLOBALS['conn'] ?? null;
$user = null;
if ($conn instanceof mysqli) {
    $sql = "SELECT id, ticket, email, whatsappno, participant_name, gender, dob, address, area, city, state, tshirt_size, emergency_name, emergency_number, blood_group, ticket_amount, amount, status, createdby, createdon
            FROM `user` WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) $user = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    } else {
        // prepare failed — fallback to previous helper if available
        if (function_exists('getStudentByStudId')) {
            $user = getStudentByStudId($user_id);
        }
    }
} else {
    // no DB connection
    if (function_exists('getStudentByStudId')) {
        $user = getStudentByStudId($user_id);
    }
}

// If no user found, show message and exit
if (empty($user)) {
    echo "<p>User not found. Please login or register.</p>";
    exit;
}

// Map user fields to $details array so the rest of the template remains largely unchanged
$details = [];

// stud_id (legacy) -> use user id
$details['stud_id'] = $user['id'];

// Name mapping: participant_name may contain full name — split for firstname/surname best-effort
$fullName = trim((string)$user['participant_name']);
if ($fullName === '') {
    $details['firstname'] = '';
    $details['surname'] = '';
} else {
    $parts = preg_split('/\s+/', $fullName);
    $details['firstname'] = isset($parts[0]) ? $parts[0] : '';
    // remainder as surname
    array_shift($parts);
    $details['surname'] = implode(' ', $parts);
}

// No father/mother fields in user table — leave blank (or you can map if you store them elsewhere)
$details['fathername'] = '';
$details['mothername'] = '';

// Email
$details['email'] = $user['email'] ?? '';

// Course / ticket - map ticket to course for display
$details['course'] = $user['ticket'] ?? '';
// Fee category / adcategory / category not present — blank
$details['category'] = '';
$details['adcategory'] = '';

// Address & area/city/state
$details['address'] = $user['address'] ?? '';
$details['area'] = $user['area'] ?? '';
$details['city'] = $user['city'] ?? '';
$details['state'] = $user['state'] ?? '';

// Fields not present in user table: schoolname, previousstd, grade, board, language, centre
$details['schoolname'] = '';
$details['previousstd'] = '';
$details['grade'] = '';
$details['board'] = '';
$details['language'] = '';
$details['centre'] = '';

// Amount preference: ticket_amount -> amount
if (!empty($user['ticket_amount']) && is_numeric($user['ticket_amount'])) {
    $details['amount'] = $user['ticket_amount'];
} elseif (!empty($user['amount']) && is_numeric($user['amount'])) {
    $details['amount'] = $user['amount'];
} else {
    $details['amount'] = '';
}

// Mobile / whatsapp
$details['whatsappno'] = $user['whatsappno'] ?? '';

// Aadhar not present
$details['aadhar'] = '';

// Photo/aadhar/sign were removed from user table — display placeholder text or skip images
$details['studphoto'] = '';
$details['studaadhar'] = '';
$details['studsign'] = '';

// createdon
$details['createdon'] = $user['createdon'] ?? '';

// For compatibility, if you want to show tshirt_size/emergency/blood_group separately, you can access $user[...] directly

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars(function_exists('getcompany') ? getcompany() : 'Profile', ENT_QUOTES); ?></title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <?php include_once 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include_once 'sidebar.php'; ?>
        <!-- Main Sidebar Container End -->


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="background-color:#fff;">
            <!-- Student Profile -->
            <div class="student-profile py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Profile Information</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">User ID</th>
                                            <td>
                                                <?php
                                                echo "SS96" . (30000 + intval($details['stud_id']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Name</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['firstname']) . ' ' . ucfirst($details['surname']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Mother Name</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['mothername']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Email</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['email']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Ticket / Course</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['course']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Fee Category</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['category']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Registration Category</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['adcategory']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Address</th>
                                            <td>
                                                <?php
                                                echo nl2br(htmlspecialchars($details['address']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">School Name</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['schoolname']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Previous Std</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['previousstd']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Grade</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['grade']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Board</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(strtoupper($details['board']));
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Language</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars(ucfirst($details['language']));
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Amount</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['amount']);
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Mobile</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['whatsappno']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Aadhar Number</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['aadhar']);
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Present School name</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['schoolname']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Previous Standard</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['previousstd']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Centre</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['centre']);
                                                ?>
                                            </td>
                                        </tr>

                                        <!-- Photos: removed from user table — show placeholder or skip -->
                                        <tr>
                                            <th width="30%">Photo</th>
                                            <td>
                                                <?php
                                                if (!empty($details['studphoto'])) {
                                                    echo '<img src="' . htmlspecialchars($details['studphoto']) . '" style="width:100px;" />';
                                                } else {
                                                    echo 'Not available';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Aadhar</th>
                                            <td>
                                                <?php
                                                if (!empty($details['studaadhar'])) {
                                                    echo '<img src="' . htmlspecialchars($details['studaadhar']) . '" style="width:100px;" />';
                                                } else {
                                                    echo 'Not available';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sign</th>
                                            <td>
                                                <?php
                                                if (!empty($details['studsign'])) {
                                                    echo '<img src="' . htmlspecialchars($details['studsign']) . '" style="width:100px;" />';
                                                } else {
                                                    echo 'Not available';
                                                }
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Registration Date & Time</th>
                                            <td>
                                                <?php
                                                echo htmlspecialchars($details['createdon']);
                                                ?>
                                            </td>
                                        </tr>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- /.content -->
        </div>

        <?php include_once 'footer.php'; ?>
        <!-- footer start End -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>
</body>

</html>