<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

// Safe GET access: use 'userid' instead of 'studmaxid'
$userid = isset($_GET['userid']) ? $_GET['userid'] : null;

// Get student details only if id provided
$stud = null;
if ($userid) {
    $stud = getStudentByStudId($userid);
}

// Razorpay PHP library
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;

// Razorpay keys (ensure these are correct / not exposed in public repo)
$api_key = 'rzp_live_D53J9UWwYtGimn';
$api_secret = 'w0SnqzH2SOOIc0gnUR7cYO3r';

// Amount in paise: use stored amount if present, otherwise default to 1 INR -> 100 paise
$amount = (!empty($stud) && isset($stud['amount']) && $stud['amount'] !== '') ? ($stud['amount'] * 100) : 100;

$api = new Api($api_key, $api_secret);

// Create order only when student exists (safe)
$order_id = '';
$order = null;
if (!empty($stud)) {
    $order = $api->order->create([
        'amount' => $amount,
        'currency' => 'INR',
        'receipt' => 'order_receipt_1001'
    ]);
    $order_id = $order->id;
}

// Safely prepare prefill values
$surname = isset($stud['surname']) ? $stud['surname'] : '';
$firstname = isset($stud['firstname']) ? $stud['firstname'] : '';
$stud_id = isset($stud['stud_id']) ? $stud['stud_id'] : ''; // will be passed as userid in redirects
$email = isset($stud['email']) ? $stud['email'] : '';
$whatsappno = isset($stud['whatsappno']) ? $stud['whatsappno'] : '';
$order_amount = ($order && isset($order->amount)) ? $order->amount : $amount;
$order_currency = ($order && isset($order->currency)) ? $order->currency : 'INR';

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
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
    .file-input-wrapper input[type="file"]{ opacity:0; position:absolute; top:0; left:0; width:100%; height:100%; cursor:pointer; }
    .file-input-wrapper label{ background-color:#f8f9fc; padding:8px; border-radius:8px; border:2px dashed #4e73df; display:inline-block; width:100%; text-align:center; font-size:14px; color:#4e73df; cursor:pointer; }
    .image-container{ display:flex; justify-content:center; align-items:center; gap:20px; }
    .image-container img{ width:25%; height:auto; }
  </style>

  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <script>
    function startPayment() {
      var options = {
        key: "<?php echo $api_key; ?>",
        amount: <?php echo $order_amount; ?>,
        currency: "<?php echo $order_currency; ?>",
        name: "<?php echo htmlspecialchars($surname . " " . $firstname, ENT_QUOTES); ?>",
        description: "Student Registration Fee",
        image: "https://cdn.razorpay.com/logos/GhRQcyean79PqE_medium.png",
        order_id: "<?php echo $order_id; ?>",
        theme: { color: "#738276" },
        handler: function (response) {
          // pass userid parameter on success
          window.location.href = "./portal.php?chk=success&userid=<?php echo $stud_id; ?>";
        },
        prefill: {
          name: "<?php echo htmlspecialchars($surname . " " . $firstname, ENT_QUOTES); ?>",
          email: "<?php echo htmlspecialchars($email, ENT_QUOTES); ?>",
          contact: "<?php echo htmlspecialchars($whatsappno, ENT_QUOTES); ?>"
        },
        notes: { address: "Customer Address" },
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
      var val = sel.value;
      var hidden = document.getElementById('ticket_amount');
      var display = document.getElementById('ticket_amount_display');
      if (val === '' || isNaN(val)) {
        hidden.value = '';
        display.value = 'Select ticket amount';
      } else {
        hidden.value = val;
        var formatted = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(parseFloat(val));
        display.value = formatted + ' (Selected)';
      }
    }

    // set default to 1200 on load and prepare DOB conversion
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
            // picker.value is YYYY-MM-DD
            var parts = picker.value.split('-');
            if (parts.length === 3) {
              hiddenDob.value = parts[2] + '/' + parts[1] + '/' + parts[0];
            } else {
              hiddenDob.value = '';
            }
          } else {
            hiddenDob.value = '';
          }
          // proceed with submit
        });
      }
    });
  </script>
</head>
<body style="background-color:#87CEFA" onload="showpayment('<?php echo isset($_GET['userid']) ? htmlspecialchars($_GET['userid'], ENT_QUOTES) : ''; ?>')">
  <div class="container">
    <div class="card p-4 m-3">
      <div class="row">
        <div class="col-md-6 mt-4">
          <div class="card shadow-lg">
            <div class="card-header text-center">
              <h3 class="text-primary">Registration Form</h3>
            </div>
            <div class="card-body">
              <form action="../controller/ctrlStudRegistrationtest.php" method="post" enctype="multipart/form-data" id="registrationForm">
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
                  <input type="email" class="form-control" name="email" id="email" placeholder="Enter email">
                </div>

                <!-- Mobile - WhatsApp (optional) -->
                <div class="form-group">
                  <label for="whatsapp">Mobile - WhatsApp (please re-check)</label>
                  <input type="tel" class="form-control" name="whatsapp" id="whatsapp" placeholder="Enter WhatsApp number" maxlength="10" pattern="[0-9]{10}">
                </div>

                <!-- Participant Name (optional) -->
                <div class="form-group">
                  <label for="participant_name">Participant Name</label>
                  <input type="text" class="form-control" name="participant_name" id="participant_name" placeholder="Enter participant name">
                </div>

                <!-- Gender (optional) -->
                <div class="form-group">
                  <label>Gender</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="Male">
                    <label class="form-check-label" for="gender_male">Male</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="Female">
                    <label class="form-check-label" for="gender_female">Female</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender_other" value="Other">
                    <label class="form-check-label" for="gender_other">Other</label>
                  </div>
                </div>

                <!-- Date of Birth: calendar only (visible date picker). Hidden 'dob' posts dd/mm/yyyy -->
                <div class="form-group">
                  <label for="dob_picker">Date of Birth</label>
                  <input type="date" class="form-control" id="dob_picker" placeholder="Select date">
                  <small class="form-text text-muted">Format submitted as: dd/mm/yyyy</small>
                  <!-- Hidden field (controller expects dd/mm/yyyy) -->
                  <input type="hidden" name="dob" id="dob" value="">
                </div>

                <!-- Address (optional) -->
                <div class="form-group">
                  <label for="address">Address</label>
                  <textarea class="form-control" name="address" id="address" placeholder="Enter full address" rows="2"></textarea>
                </div>

                <!-- Area/ Address (optional) -->
                <div class="form-group">
                  <label for="area">Area/ Address</label>
                  <input type="text" class="form-control" name="area" id="area" placeholder="Enter area / locality">
                </div>

                <!-- City (optional) -->
                <div class="form-group">
                  <label for="city">City</label>
                  <input type="text" class="form-control" name="city" id="city" placeholder="Enter city">
                </div>

                <!-- State (optional) -->
                <div class="form-group">
                  <label for="state">State</label>
                  <input type="text" class="form-control" name="state" id="state" placeholder="Enter state">
                </div>

                <!-- T-shirt Size (optional) -->
                <div class="form-group">
                  <label for="tshirt_size">T-shirt Size (unisex)</label>
                  <select class="form-control" name="tshirt_size" id="tshirt_size">
                    <option value="">Select size</option>
                    <option value="XS">XS</option>
                    <option value="S">S</option>
                    <option value="M">M</option>
                    <option value="L">L</option>
                    <option value="XL">XL</option>
                    <option value="XXL">XXL</option>
                  </select>
                </div>

                <!-- Emergency Contact Name (optional) -->
                <div class="form-group">
                  <label for="emergency_name">Emergency Contact Name <small class="text-muted">For minors, enter Parent's / Guardian's details</small></label>
                  <input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="Enter emergency contact name">
                </div>

                <!-- Emergency Contact Number (optional) -->
                <div class="form-group">
                  <label for="emergency_number">Emergency Contact Number</label>
                  <input type="tel" class="form-control" name="emergency_number" id="emergency_number" placeholder="Enter emergency contact number" maxlength="10" pattern="[0-9]{10}">
                </div>

                <!-- Blood Group (optional) -->
                <div class="form-group">
                  <label for="blood_group">Blood Group</label>
                  <select class="form-control" name="blood_group" id="blood_group">
                    <option value="">Select blood group</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
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