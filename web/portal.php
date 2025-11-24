<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php'; // keep for totalstud/totalmem etc.

// Accept userid (preferred) or studid (back-compat) or session id
$userid = null;
if (isset($_GET['userid']) && $_GET['userid'] !== '') {
    $userid = intval($_GET['userid']);
} elseif (isset($_GET['studid']) && $_GET['studid'] !== '') {
    $userid = intval($_GET['studid']);
} elseif (isset($_SESSION['id'])) {
    $userid = intval($_SESSION['id']);
}

// keep original incoming parameters for backward compatibility
$chk = isset($_GET['chk']) ? trim($_GET['chk']) : '';
$studid_param = isset($_GET['studid']) ? trim($_GET['studid']) : '';

// Database connection
$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection not found. Check config.php');
}

// Razorpay credentials (as in original file). For production move these to config.php and keep them secret.
$api_key = 'rzp_live_dfhtnkmedcTWBN';
$api_secret = 'jzFO7kSdSOXJ7RLF7JeuyRoj';

// If Razorpay returned a redirect with signature params, verify and update user.status
$rp_payment_id = isset($_GET['razorpay_payment_id']) ? trim($_GET['razorpay_payment_id']) : '';
$rp_order_id   = isset($_GET['razorpay_order_id']) ? trim($_GET['razorpay_order_id']) : '';
$rp_signature  = isset($_GET['razorpay_signature']) ? trim($_GET['razorpay_signature']) : '';

// Signature verification and status update when razorpay params are present
if ($rp_payment_id !== '' && $rp_order_id !== '' && $rp_signature !== '' && $userid) {
    // require Razorpay SDK (ensure folder exists at this path)
    require_once __DIR__ . '/razorpay-php/Razorpay.php';
    try {
        $api = new \Razorpay\Api\Api($api_key, $api_secret);
        $attributes = [
            'razorpay_order_id'   => $rp_order_id,
            'razorpay_payment_id' => $rp_payment_id,
            'razorpay_signature'  => $rp_signature
        ];
        // will throw Exception on failure
        $api->utility->verifyPaymentSignature($attributes);

        // Verification OK -> update user.status = 'Success'
        $stmt = mysqli_prepare($conn, "UPDATE `user` SET `status` = 'Success' WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $userid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            error_log('portal.php: failed to prepare update user status: ' . mysqli_error($conn));
        }

        // Optionally, you can store payment details in a payments table here.

        $_SESSION['message'] = 'Payment verified and status updated.';
        // Redirect to clean URL to avoid re-processing on refresh
        header('Location: ./portal.php?userid=' . urlencode($userid));
        exit;
    } catch (Exception $e) {
        error_log('portal.php: Razorpay signature verification failed: ' . $e->getMessage());
        $_SESSION['error1'] = 'Payment verification failed. Please contact support.';
        header('Location: ./portal.php?userid=' . urlencode($userid));
        exit;
    }
}

// Backward-compat: if chk=success used without signature (old flow), update user.status too
if ($chk === 'success' && $userid && ($rp_payment_id === '' && $rp_order_id === '' && $rp_signature === '')) {
    $stmt_upd = mysqli_prepare($conn, "UPDATE `user` SET `status` = 'Success' WHERE id = ? LIMIT 1");
    if ($stmt_upd) {
        mysqli_stmt_bind_param($stmt_upd, 'i', $userid);
        mysqli_stmt_execute($stmt_upd);
        mysqli_stmt_close($stmt_upd);
    } else {
        error_log('portal.php: prepare failed for updating user status (legacy chk): ' . mysqli_error($conn));
    }
}

// Fetch user row from `user` table
$user = null;
if ($userid) {
    $sql = "SELECT id, ticket, email, whatsappno, participant_name, gender, dob, address, area, city, state, tshirt_size, emergency_name, emergency_number, blood_group, ticket_amount, amount, status, createdby, createdon
            FROM `user` WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res) $user = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    } else {
        error_log('portal.php: prepare failed for select user: ' . mysqli_error($conn));
    }
}

// Map fetched user data into $stud array to preserve the rest of the page (so original UI/links remain unchanged)
$stud = [];
if (!empty($user)) {
    $stud['surname']   = isset($user['participant_name']) ? explode(' ', trim($user['participant_name']))[0] ?? '' : '';
    $stud['firstname'] = isset($user['participant_name']) ? (strpos(trim($user['participant_name']), ' ') !== false ? trim(substr(trim($user['participant_name']), strpos(trim($user['participant_name']), ' ') + 1)) : '') : '';
    $stud['email']     = $user['email'] ?? '';
    $stud['whatsappno']= $user['whatsappno'] ?? '';
    // prefer ticket_amount then amount for compatibility with existing code
    if (!empty($user['ticket_amount']) && is_numeric($user['ticket_amount'])) {
        $stud['amount'] = $user['ticket_amount'];
    } else {
        $stud['amount'] = $user['amount'] ?? '';
    }
    $stud['stud_id']   = $user['id'];
    $stud['status']    = $user['status'] ?? '';
} else {
    // no user found: keep $stud minimal to avoid errors in existing UI
    $stud['surname'] = $stud['firstname'] = $stud['email'] = $stud['whatsappno'] = $stud['amount'] = $stud['stud_id'] = $stud['status'] = '';
}

// Include Razorpay PHP library usage (create order) only when we have an amount and a user and not already paid
$order_id = '';
$order = null;
$order_amount_paise = 100; // default ₹1

if (!empty($user) && strtolower(trim((string)($user['status'] ?? ''))) !== 'success' && strtolower(trim((string)($user['status'] ?? ''))) !== 'paid') {
    if (!empty($user['ticket_amount']) && is_numeric($user['ticket_amount'])) {
        $order_amount_paise = intval(floatval($user['ticket_amount']) * 100);
    } elseif (!empty($user['amount']) && is_numeric($user['amount'])) {
        $order_amount_paise = intval(floatval($user['amount']) * 100);
    } else {
        $order_amount_paise = 100;
    }

    try {
        require_once __DIR__ . '/razorpay-php/Razorpay.php';
        $razor = new \Razorpay\Api\Api($api_key, $api_secret);
        $order = $razor->order->create([
            'amount' => $order_amount_paise,
            'currency' => 'INR',
            'receipt' => 'order_receipt_user_' . ($user['id'] ?? '0')
        ]);
        $order_id = $order->id ?? '';
    } catch (Exception $e) {
        error_log('portal.php: Razorpay order creation failed: ' . $e->getMessage());
        $order = null;
        $order_id = '';
    }
} else {
    // if already paid, leave order_id empty and set amount to stored amount if needed
    if (!empty($user['amount']) && is_numeric($user['amount'])) {
        $order_amount_paise = intval(floatval($user['amount']) * 100);
    } else {
        $order_amount_paise = 0;
    }
}

// Keep original callback URL and other links unchanged
$callback_url = "https://registration.sainikividyalayatuljapur.in/web/paymentrecipt.php?chk=success";

// Prepare JS-safe values
$js_api_key = json_encode($api_key);
$js_order_amount = json_encode($order_amount_paise);
$js_order_currency = json_encode('INR');
$js_order_id = json_encode($order_id);
$js_prefill_name = json_encode(trim((string)($user['participant_name'] ?? ($stud['surname'].' '.$stud['firstname']))));
$js_prefill_email = json_encode($stud['email']);
$js_prefill_contact = json_encode($stud['whatsappno']);
$js_userid = json_encode($stud['stud_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">

  <!-- keep original CSS/JS includes -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<?php
// Output the original startPayment JS (using the created order info) — preserve original behavior and links
echo '<script>
    function startPayment() {
      var options = {
        key: ' . $js_api_key . ',
        amount: ' . $js_order_amount . ',
        currency: ' . $js_order_currency . ',
        name: ' . $js_prefill_name . ',
        description: "Student Registration Fee",
        image: "https://cdn.razorpay.com/logos/GhRQcyean79PqE_medium.png",
        order_id: ' . $js_order_id . ',
        theme: { color: "#738276" },
        handler: function (response) {
            // Payment success, redirect back similar to original flow (preserve link format)
            window.location.href = "./portal.php?chk=success&userid=" + ' . $js_userid . ';
        },
        prefill: {
            name: ' . $js_prefill_name . ',
            email: ' . $js_prefill_email . ',
            contact: ' . $js_prefill_contact . '
        },
        notes: { address: "Customer Address" },
        modal: { escape: false }
      };
      var rzp = new Razorpay(options);
      rzp.open();
    }
</script>';
?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">

    <!-- Navbar -->
    <?php include_once 'navbar.php'; ?>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <?php include_once 'sidebar.php'; ?>
    <!-- Main Sidebar Container End -->


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="background-color:#fff;">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard v1</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <!-- Small boxes (Stat box) -->
          <div class="row">
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php print_r($totalstud); ?></h3>

                  <p>View Profile</p>
                </div>
                <div class="icon">
                  <i class="ion ion-bag"></i>
                </div>
                <a href="profile.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <!-- ./col -->
        <?php if($stud['status']=="Success"){ ?>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-success">
                <div class="inner">
                  <h3><?php print_r($totalmem); ?></h3>

                  <p>View Reciept</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="paymentrecipt.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>
        <?php }else{ ?>
            <div class="col-lg-3 col-6">
              <!-- small box -->
              <div class="small-box bg-danger">
                <div class="inner">
                  <h3><?php print_r($totalmem); ?></h3>

                  <p>Payment Pending</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <button onclick="startPayment()">Make Payment</button>
              </div>
            </div>
        <?php } ?>
            <!--<div class="col-lg-3 col-6">
              <!-- small box -- >
              <div class="small-box bg-warning">
                <div class="inner">
                  <h3><?php print_r($totalmem); ?></h3>

                  <p>View Hall Ticket</p>
                </div>
                <div class="icon">
                  <i class="ion ion-stats-bars"></i>
                </div>
                <a href="paymentrecipt.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>-->
            <!-- ./col -->
          </div>
          <!-- /.row -->
        </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- footer start  -->

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