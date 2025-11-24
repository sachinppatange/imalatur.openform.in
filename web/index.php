<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

// Safe GET access: use 'userid' instead of 'studmaxid'
$userid = isset($_GET['userid']) ? $_GET['userid'] : null;

// Default values
$user = null;
$api_key = 'rzp_live_D53J9UWwYtGimn';
$api_secret = 'w0SnqzH2SOOIc0gnUR7cYO3r';
$order_id = '';
$order = null;
$order_amount = 100; // paise default = ₹1
$order_currency = 'INR';
$surname = $firstname = $participant_name = $email = $whatsappno = '';

// Try to read user record from `user` table using prepared statement
$conn = $GLOBALS['conn'] ?? null;
if ($conn instanceof mysqli && $userid) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM `user` WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if ($res && $row = mysqli_fetch_assoc($res)) {
            $user = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

// Fallback: if no user found, attempt old function (if it exists) to get student details
if (empty($user) && function_exists('getStudentByStudId') && $userid) {
    $user = getStudentByStudId($userid); // backward compatibility
}

// Prepare variables for Razorpay order and prefill
if (!empty($user)) {
    // Determine amount: prefer ticket_amount, else amount, else default 1 INR
    if (!empty($user['ticket_amount']) && is_numeric($user['ticket_amount'])) {
        $order_amount = floatval($user['ticket_amount']) * 100; // convert INR to paise
    } elseif (!empty($user['amount']) && is_numeric($user['amount'])) {
        $order_amount = floatval($user['amount']) * 100;
    } else {
        $order_amount = 100; // ₹1.00 default
    }

    // Prefill fields from user table (use participant_name if available)
    $participant_name = trim((string)($user['participant_name'] ?? ''));
    // For backward compatibility try surname/firstname if participant_name empty
    if ($participant_name === '') {
        $surname = $user['surname'] ?? '';
        $firstname = $user['firstname'] ?? '';
        $participant_name = trim($surname . ' ' . $firstname);
    }
    $email = $user['email'] ?? '';
    $whatsappno = $user['whatsappno'] ?? '';
}

// Initialize Razorpay only when we have API and a meaningful amount (order creation later)
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

$api = new Api($api_key, $api_secret);

// Create order only when we have a user and a positive amount
if (!empty($user) && intval($order_amount) > 0) {
    try {
        $order = $api->order->create([
            'amount' => intval($order_amount),
            'currency' => $order_currency,
            'receipt' => 'order_receipt_' . ($userid ?? '0'),
        ]);
        $order_id = $order->id ?? '';
    } catch (Exception $e) {
        // Log or handle error - keep $order_id empty so frontend won't try to pay
        error_log('Razorpay order creation failed: ' . $e->getMessage());
        $order = null;
        $order_id = '';
    }
}

// JS-safe values (use json_encode to escape)
$js_api_key = json_encode($api_key);
$js_order_amount = json_encode($order_amount);
$js_order_currency = json_encode($order_currency);
$js_order_id = json_encode($order_id);
$js_prefill_name = json_encode($participant_name);
$js_prefill_email = json_encode($email);
$js_prefill_contact = json_encode($whatsappno);
$js_userid = json_encode($userid);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <style>
    .size{ font-size:12px; }
    .rule li{ font-size:13px; }
    .error{ font-size:12px; color:red; }
    .card{ border-radius:12px; }
    .form-group{ padding:5px; }
    .form-control{ border-radius:10px; box-shadow:none; }
    .form-check-label{ font-size:14px; }
    .btn-primary{ background-color:#4e73df; border-color:#4e73df; border-radius:10px; }
  </style>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    // Use server-provided JSON-safe variables
    const RZP_KEY = <?php echo $js_api_key; ?>;
    const RZP_ORDER_AMOUNT = <?php echo $js_order_amount; ?>;
    const RZP_ORDER_CURRENCY = <?php echo $js_order_currency; ?>;
    const RZP_ORDER_ID = <?php echo $js_order_id; ?>;
    const PREFILL_NAME = <?php echo $js_prefill_name; ?>;
    const PREFILL_EMAIL = <?php echo $js_prefill_email; ?>;
    const PREFILL_CONTACT = <?php echo $js_prefill_contact; ?>;
    const USER_ID = <?php echo $js_userid; ?>;

    function startPayment() {
      if (!RZP_ORDER_ID || RZP_ORDER_ID === "") {
        alert('Payment is not available at the moment. Please try again later.');
        return;
      }

      var options = {
        key: RZP_KEY,
        amount: RZP_ORDER_AMOUNT,
        currency: RZP_ORDER_CURRENCY,
        name: PREFILL_NAME || 'Participant',
        description: "Registration Fee",
        order_id: RZP_ORDER_ID,
        theme: { color: "#738276" },
        handler: function (response) {
          // redirect to portal with userid
          var uid = USER_ID || '';
          window.location.href = "./portal.php?chk=success&userid=" + encodeURIComponent(uid);
        },
        prefill: {
          name: PREFILL_NAME || '',
          email: PREFILL_EMAIL || '',
          contact: PREFILL_CONTACT || ''
        },
        notes: { address: "" },
        modal: { escape: false }
      };
      var rzp = new Razorpay(options);
      rzp.open();
    }

    function showpayment(userid) {
      if (userid != "") {
        startPayment();
      }
    }

    function updateTicketAmount() {
      var sel = document.getElementById('ticket_select');
      var val = sel ? sel.value : '';
      var hidden = document.getElementById('ticket_amount');
      var display = document.getElementById('ticket_amount_display');
      if (!sel || val === '' || isNaN(val)) {
        if (hidden) hidden.value = '';
        if (display) display.value = 'Select ticket amount';
      } else {
        if (hidden) hidden.value = val;
        if (display) {
          var formatted = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(parseFloat(val));
          display.value = formatted + ' (Selected)';
        }
      }
    }

    document.addEventListener('DOMContentLoaded', function(){
      var sel = document.getElementById('ticket_select');
      if (sel) {
        for (var i=0;i<sel.options.length;i++){
          if (sel.options[i].value === '1200') { sel.selectedIndex = i; break; }
        }
        updateTicketAmount();
      }

      // convert date-picker value to dd/mm/yyyy in hidden dob before submit
      var form = document.getElementById('registrationForm');
      if (form) {
        form.addEventListener('submit', function(e){
          var picker = document.getElementById('dob_picker');
          var hiddenDob = document.getElementById('dob');
          if (picker && picker.value) {
            var parts = picker.value.split('-');
            if (parts.length === 3) {
              hiddenDob.value = parts[2] + '/' + parts[1] + '/' + parts[0];
            } else {
              hiddenDob.value = '';
            }
          } else {
            hiddenDob.value = '';
          }
        });
      }
    });
  </script>
</head>
<body style="background-color:#87CEFA" onload="showpayment('<?php echo htmlspecialchars($userid ?? '', ENT_QUOTES); ?>')">
  <div class="container">
    <div class="card p-4 m-3">
      <div class="row">
        <div class="col-md-6 mt-4">
          <div class="card shadow-lg">
            <div class="card-header text-center">
              <h3 class="text-primary">Registration Form</h3>
            </div>
            <div class="card-body">
              <form action="../controller/ctrlStudRegistration.php" method="post" enctype="multipart/form-data" id="registrationForm">
                <!-- Ticket (dropdown in English with INR values) -->
                <div class="form-group">
                  <label for="ticket_select">Ticket</label>
                  <select class="form-control" name="ticket" id="ticket_select" onchange="updateTicketAmount()">
                    <option value="">Select ticket</option>
                    <option value="1200">INR 1,200</option>
                    <option value="1000">INR 1,000</option>
                    <option value="800">INR 800</option>
                  </select>
                </div>

                <!-- Hidden ticket_amount (sent to controller). Will be updated by JS. -->
                <input type="hidden" name="ticket_amount" id="ticket_amount" value="1200">
                <div class="form-group">
                  <input type="text" class="form-control" id="ticket_amount_display" value="₹1,200.00 (Selected)" readonly>
                </div>

                <!-- Email (optional) -->
                <div class="form-group">
                  <label for="email">Email (please re-check)</label>
                  <input type="email" class="form-control" name="email" id="email" placeholder="Enter email" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>">
                </div>

                <!-- Mobile - WhatsApp (optional) -->
                <div class="form-group">
                  <label for="whatsapp">Mobile - WhatsApp (please re-check)</label>
                  <input type="tel" class="form-control" name="whatsapp" id="whatsapp" placeholder="Enter WhatsApp number" maxlength="10" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($whatsappno, ENT_QUOTES); ?>">
                </div>

                <!-- Participant Name (optional) -->
                <div class="form-group">
                  <label for="participant_name">Participant Name</label>
                  <input type="text" class="form-control" name="participant_name" id="participant_name" placeholder="Enter participant name" value="<?php echo htmlspecialchars($participant_name, ENT_QUOTES); ?>">
                </div>

                <!-- Gender (optional) -->
                <div class="form-group">
                  <label>Gender</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male" <?php echo (isset($user['gender']) && $user['gender']=='Male') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender_male">Male</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female" <?php echo (isset($user['gender']) && $user['gender']=='Female') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender_female">Female</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_other" value="Other" <?php echo (isset($user['gender']) && $user['gender']=='Other') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="gender_other">Other</label>
                  </div>
                </div>

                <!-- Date of Birth: calendar only (visible date picker). Hidden 'dob' posts dd/mm/yyyy -->
                <div class="form-group">
                  <label for="dob_picker">Date of Birth</label>
                  <input type="date" class="form-control" id="dob_picker" placeholder="Select date" value="<?php
                    // if user has dob in YYYY-MM-DD or dd/mm/yyyy convert to YYYY-MM-DD for date picker
                    $dob_val = $user['dob'] ?? '';
                    if ($dob_val) {
                      // if already YYYY-MM-DD
                      if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob_val)) {
                        echo htmlspecialchars($dob_val, ENT_QUOTES);
                      } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $dob_val)) {
                        $parts = explode('/', $dob_val);
                        echo htmlspecialchars($parts[2].'-'.$parts[1].'-'.$parts[0], ENT_QUOTES);
                      }
                    }
                  ?>">
                  <small class="form-text text-muted">Format submitted as: dd/mm/yyyy</small>
                  <!-- Hidden field (controller expects dd/mm/yyyy) -->
                  <input type="hidden" name="dob" id="dob" value="">
                </div>

                <!-- Address (optional) -->
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" name="address" id="address" placeholder="Enter full address" rows="2"><?php echo htmlspecialchars($user['address'] ?? '', ENT_QUOTES); ?></textarea>
                </div>

                <!-- Area/ Address (optional) -->
                <div class="form-group">
                  <label for="area">Area/ Address</label>
                  <input type="text" class="form-control" name="area" id="area" placeholder="Enter area / locality" value="<?php echo htmlspecialchars($user['area'] ?? '', ENT_QUOTES); ?>">
                </div>

                <!-- City (optional) -->
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" name="city" id="city" placeholder="Enter city" value="<?php echo htmlspecialchars($user['city'] ?? '', ENT_QUOTES); ?>">
                </div>

                <!-- State (optional) -->
                <div class="form-group">
                  <label for="state">State</label>
                  <input type="text" class="form-control" name="state" id="state" placeholder="Enter state" value="<?php echo htmlspecialchars($user['state'] ?? '', ENT_QUOTES); ?>">
                </div>

                <!-- T-shirt Size (optional) -->
                <div class="form-group">
                  <label for="tshirt_size">T-shirt Size (unisex)</label>
                  <select class="form-control" name="tshirt_size" id="tshirt_size">
                    <option value="">Select size</option>
                    <?php
                      $sizes = ['XS','S','M','L','XL','XXL'];
                      foreach ($sizes as $s) {
                        $sel = (isset($user['tshirt_size']) && $user['tshirt_size']==$s) ? 'selected' : '';
                        echo "<option value=\"{$s}\" {$sel}>{$s}</option>";
                      }
                    ?>
                  </select>
                </div>

                <!-- Emergency Contact Name (optional) -->
                <div class="form-group">
                  <label for="emergency_name">Emergency Contact Name <small class="text-muted">For minors, enter Parent's / Guardian's details</small></label>
                  <input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="Enter emergency contact name" value="<?php echo htmlspecialchars($user['emergency_name'] ?? '', ENT_QUOTES); ?>">
                </div>

                <!-- Emergency Contact Number (optional) -->
                <div class="form-group">
                  <label for="emergency_number">Emergency Contact Number</label>
                  <input type="tel" class="form-control" name="emergency_number" id="emergency_number" placeholder="Enter emergency contact number" maxlength="10" pattern="[0-9]{10}" value="<?php echo htmlspecialchars($user['emergency_number'] ?? '', ENT_QUOTES); ?>">
                </div>

                <!-- Blood Group (optional) -->
                <div class="form-group">
                  <label for="blood_group">Blood Group</label>
                  <select class="form-control" name="blood_group" id="blood_group">
                    <option value="">Select blood group</option>
                    <?php
                      $bgs = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                      foreach ($bgs as $bg) {
                        $sel = (isset($user['blood_group']) && $user['blood_group']==$bg) ? 'selected' : '';
                        echo "<option value=\"{$bg}\" {$sel}>{$bg}</option>";
                      }
                    ?>
                  </select>
                </div>

                <!-- Policies / Disclaimers (optional, not required) -->
                <div class="form-group">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="accept_policies" id="accept_policies">
                    <label class="form-check-label" for="accept_policies">
                      By continuing you confirm that you have read and understood Terms of Use, Privacy Policy and Cancellation Policy and agree to abide by them.
                    </label>
                  </div>
                </div>

                <div class="form-group mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="agree_disclaimer" id="agree_disclaimer">
                    <label class="form-check-label" for="agree_disclaimer">
                      I have read the Disclaimer and agree to the terms.
                    </label>
                  </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn-primary btn-block" name="submit">Submit</button>
              </form>

              <!-- Note: Icons removed from all labels and DOB uses a calendar picker (visible). Hidden dob is submitted in dd/mm/yyyy -->
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <p class="text-center" style="font-size:14px;">If you have already created an account, please click login button</p>
          <a href="studentlogin.php" class="btn" style="background-color:#1cc688;">LOGIN</a>
        </div>

      </div>
    </div>
  </div>

  <?php unset($_SESSION['message']); ?>
  <?php unset($_SESSION['error1']); ?>
</body>
</html>