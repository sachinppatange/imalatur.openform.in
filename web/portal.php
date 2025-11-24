<?php
session_start();
include_once '../config/config.php';

// Preferred GET param: userid (backwards compatible with studid)
$userid = null;
if (isset($_GET['userid']) && $_GET['userid'] !== '') {
    $userid = intval($_GET['userid']);
} elseif (isset($_GET['studid']) && $_GET['studid'] !== '') {
    $userid = intval($_GET['studid']);
} elseif (isset($_SESSION['id'])) {
    $userid = intval($_SESSION['id']);
}

$conn = $GLOBALS['conn'] ?? null;
if (!$conn || !($conn instanceof mysqli)) {
    http_response_code(500);
    exit('Database connection not found. Check config.php');
}

// Razorpay credentials — move to config.php for production safety
$api_key = 'rzp_live_dfhtnkmedcTWBN';
$api_secret = 'jzFO7kSdSOXJ7RLF7JeuyRoj';

// Read Razorpay redirect parameters (if any)
$rp_payment_id = isset($_GET['razorpay_payment_id']) ? trim($_GET['razorpay_payment_id']) : '';
$rp_order_id   = isset($_GET['razorpay_order_id']) ? trim($_GET['razorpay_order_id']) : '';
$rp_signature  = isset($_GET['razorpay_signature']) ? trim($_GET['razorpay_signature']) : '';

// Verify signature and update status (only if all params present)
if ($rp_payment_id !== '' && $rp_order_id !== '' && $rp_signature !== '' && $userid) {
    // Require Razorpay SDK
    require_once __DIR__ . '/razorpay-php/Razorpay.php';
    try {
        $api = new \Razorpay\Api\Api($api_key, $api_secret);
        $attributes = [
            'razorpay_order_id'   => $rp_order_id,
            'razorpay_payment_id' => $rp_payment_id,
            'razorpay_signature'  => $rp_signature
        ];
        // This will throw Exception on failure
        $api->utility->verifyPaymentSignature($attributes);

        // signature valid -> update user.status = 'Success'
        $stmt = mysqli_prepare($conn, "UPDATE `user` SET `status` = 'Success' WHERE id = ? LIMIT 1");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $userid);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            error_log('portal.php: failed to prepare update user status: ' . mysqli_error($conn));
        }

        $_SESSION['message'] = 'Payment verified and status updated.';
        // redirect to clean URL (avoid re-processing on refresh)
        header('Location: ./portal.php?userid=' . urlencode($userid));
        exit;
    } catch (Exception $e) {
        error_log('portal.php: Razorpay signature verification failed: ' . $e->getMessage());
        $_SESSION['error1'] = 'Payment verification failed. Please contact support.';
        header('Location: ./portal.php?userid=' . urlencode($userid));
        exit;
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

// Determine order amount (in paise) with precedence: ticket_amount -> amount -> default 1 INR
$order_amount = 100;
$order_currency = 'INR';
if (!empty($user)) {
    if (isset($user['ticket_amount']) && $user['ticket_amount'] !== '' && is_numeric($user['ticket_amount'])) {
        $order_amount = intval(floatval($user['ticket_amount']) * 100);
    } elseif (isset($user['amount']) && $user['amount'] !== '' && is_numeric($user['amount'])) {
        $order_amount = intval(floatval($user['amount']) * 100);
    } else {
        $order_amount = 100;
    }
}

// Create Razorpay order only if user exists and not already paid
$order_id = '';
if (!empty($user) && strtolower(trim((string)($user['status'] ?? ''))) !== 'success' && strtolower(trim((string)($user['status'] ?? ''))) !== 'paid') {
    try {
        require_once __DIR__ . '/razorpay-php/Razorpay.php';
        $razor = new \Razorpay\Api\Api($api_key, $api_secret);
        $order = $razor->order->create([
            'amount' => $order_amount,
            'currency' => $order_currency,
            'receipt' => 'receipt_user_' . $user['id'],
        ]);
        $order_id = isset($order->id) ? $order->id : '';
    } catch (Exception $e) {
        error_log('portal.php: Razorpay order create failed: ' . $e->getMessage());
        $order_id = '';
    }
}

// Prefill values for Razorpay popup (safe)
$prefill_name = trim((string)($user['participant_name'] ?? ''));
if ($prefill_name === '') {
    $prefill_name = trim(((string)($user['surname'] ?? '') . ' ' . (string)($user['firstname'] ?? '')));
}
$prefill_email = $user['email'] ?? '';
$prefill_contact = $user['whatsappno'] ?? '';

// JSON-encode PHP values for JS usage
$js = [
    'api_key' => $api_key,
    'order_amount' => $order_amount,
    'order_currency' => $order_currency,
    'order_id' => $order_id,
    'prefill_name' => $prefill_name,
    'prefill_email' => $prefill_email,
    'prefill_contact' => $prefill_contact,
    'userid' => $userid,
    'user_status' => $user['status'] ?? ''
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo htmlspecialchars(function_exists('getcompany') ? getcompany() : 'Portal', ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- keep your original CSS/JS includes -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>

  <script>
    const RZP = <?php echo json_encode($js, JSON_UNESCAPED_UNICODE); ?>;

    function startPayment() {
      if (!RZP.order_id) {
        alert('Payment not available for this user (maybe already paid or order creation failed).');
        return;
      }

      var options = {
        key: RZP.api_key,
        amount: RZP.order_amount,
        currency: RZP.order_currency,
        name: RZP.prefill_name || 'Participant',
        description: "Registration Fee",
        order_id: RZP.order_id,
        prefill: {
          name: RZP.prefill_name || '',
          email: RZP.prefill_email || '',
          contact: RZP.prefill_contact || ''
        },
        handler: function (response) {
          // Redirect back with Razorpay parameters for server-side verification
          var url = "./portal.php?userid=" + encodeURIComponent(RZP.userid || '');
          url += "&razorpay_payment_id=" + encodeURIComponent(response.razorpay_payment_id || '');
          url += "&razorpay_order_id=" + encodeURIComponent(response.razorpay_order_id || '');
          url += "&razorpay_signature=" + encodeURIComponent(response.razorpay_signature || '');
          window.location.href = url;
        },
        theme: { color: "#738276" },
        modal: { escape: false }
      };
      var rzp = new Razorpay(options);
      rzp.open();
    }
  </script>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include_once 'navbar.php'; ?>
    <?php include_once 'sidebar.php'; ?>

    <div class="content-wrapper" style="background-color:#fff;">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6"><h1 class="m-0">Dashboard</h1></div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <section class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- Profile card -->
            <div class="col-lg-3 col-6">
              <div class="small-box bg-info">
                <div class="inner">
                  <h3><?php echo !empty($user) ? htmlspecialchars($user['id'], ENT_QUOTES) : '0'; ?></h3>
                  <p>View Profile</p>
                </div>
                <div class="icon"><i class="ion ion-bag"></i></div>
                <a href="profile.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
              </div>
            </div>

            <?php if (!empty($user) && strtolower(trim((string)$user['status'])) === 'success') { ?>
              <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                  <div class="inner"><h3>1</h3><p>View Receipt</p></div>
                  <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  <a href="paymentrecipt.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
            <?php } else { ?>
              <div class="col-lg-3 col-6">
                <div class="small-box bg-danger">
                  <div class="inner"><h3>0</h3><p>Payment Pending</p></div>
                  <div class="icon"><i class="ion ion-stats-bars"></i></div>
                  <button class="btn btn-light" onclick="startPayment()">Make Payment</button>
                </div>
              </div>
            <?php } ?>

          </div>
        </div>
      </section>
    </div>

    <?php include_once 'footer.php'; ?>
    <aside class="control-sidebar control-sidebar-dark"></aside>
  </div>

  <script src="/plugins/jquery/jquery.min.js"></script>
  <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="/dist/js/adminlte.js"></script>
</body>
</html>