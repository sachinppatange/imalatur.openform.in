<?php
/**
 * userpanel/register.php
 * Full Runathon registration form.
 *
 * - Guest registration with mobile OTP verification.
 * - On successful OTP verification: auto-create user (if not exists), session login, create registration record,
 *   create Razorpay order (server-side) and return order details for client-side checkout.
 *
 * Requirements:
 * - A MySQL database with tables: users, otps, registrations (see earlier schema).
 * - config/app_config.php (optional) to define DB and Razorpay credentials:
 *     DB_HOST, DB_NAME, DB_USER, DB_PASS
 *     RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET
 *
 * Notes:
 * - This is a working stub but you must integrate a real SMS/WhatsApp provider in send_otp() for production.
 * - Ensure HTTPS on production, strong session cookie settings, rate limiting, and cleanup of OTPs.
 */

session_start();
header('X-Frame-Options: DENY');

// Load config (optional)
$config_file = __DIR__ . '/../config/wa_config.php';
if (file_exists($config_file)) {
    include_once $config_file;
}

// Database connection (uses config constants if present)
$DB_HOST = defined('DB_HOST') ? DB_HOST : ($_ENV['DB_HOST'] ?? '127.0.0.1');
$DB_NAME = defined('DB_NAME') ? DB_NAME : ($_ENV['DB_NAME'] ?? 'imalatur');
$DB_USER = defined('DB_USER') ? DB_USER : ($_ENV['DB_USER'] ?? 'dbuser');
$DB_PASS = defined('DB_PASS') ? DB_PASS : ($_ENV['DB_PASS'] ?? 'dbpass');

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo "Database connection error";
    error_log("DB connect error: " . $e->getMessage());
    exit;
}

// Razorpay config (set in config/app_config.php)
$RAZORPAY_KEY_ID = defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : ($_ENV['RAZORPAY_KEY_ID'] ?? '');
$RAZORPAY_KEY_SECRET = defined('RAZORPAY_KEY_SECRET') ? RAZORPAY_KEY_SECRET : ($_ENV['RAZORPAY_KEY_SECRET'] ?? '');

// Ticket allowlist (server-side canonical list)
$ticket_options = [
    '800'  => ['label' => '5 km',  'price' => 800,  'desc' => 'Fun Run'],
    '1000' => ['label' => '10 km', 'price' => 1000, 'desc' => '10K — timed'],
    '1200' => ['label' => '21 km', 'price' => 1200, 'desc' => 'Half Marathon — chip timing'],
];

// Helper: send OTP and store in DB (replace send mechanism with real SMS/WhatsApp)
function generate_otp($length = 6) {
    return str_pad(random_int(0, pow(10, $length)-1), $length, '0', STR_PAD_LEFT);
}

function send_otp_and_store($phone, $pdo) {
    $code = generate_otp(6);
    $expires = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("INSERT INTO otps (phone, code, expires_at, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$phone, $code, $expires]);

    // TODO: integrate SMS/WhatsApp provider here. For now we log the code (for local testing).
    error_log("OTP for {$phone}: {$code}");

    return true;
}

function verify_otp_code($phone, $code, $pdo) {
    $stmt = $pdo->prepare("SELECT id, code, expires_at FROM otps WHERE phone = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$phone]);
    $row = $stmt->fetch();
    if (!$row) return false;
    if ($row['code'] !== $code) return false;
    if (new DateTime() > new DateTime($row['expires_at'])) return false;
    // delete used OTP (optional)
    $stmt = $pdo->prepare("DELETE FROM otps WHERE id = ?");
    $stmt->execute([$row['id']]);
    return true;
}

// Helper: find or create user by phone
function find_user_by_phone($phone, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? LIMIT 1");
    $stmt->execute([$phone]);
    return $stmt->fetch();
}

function create_user_by_phone($name, $phone, $pdo) {
    $stmt = $pdo->prepare("INSERT INTO users (name, phone, is_phone_verified, auth_type, created_at) VALUES (?, ?, 1, 'otp', NOW())");
    $stmt->execute([$name, $phone]);
    return $pdo->lastInsertId();
}

function login_user_by_id($user_id) {
    // Minimal session login
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['logged_in_at'] = time();
}

// Helper: create Razorpay order (server-side)
function create_razorpay_order($amount_in_rupees, $receipt_id, $key_id, $key_secret) {
    $amount_paise = intval(round($amount_in_rupees * 100)); // convert to paise
    $payload = json_encode([
        'amount' => $amount_paise,
        'currency' => 'INR',
        'receipt' => $receipt_id,
        'payment_capture' => 1
    ]);

    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, $key_id . ':' . $key_secret);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) {
        throw new Exception('Razorpay API error: ' . $err);
    }
    $data = json_decode($resp, true);
    if ($status < 200 || $status >= 300) {
        $msg = $data['error']['description'] ?? ($data['error']['message'] ?? json_encode($data));
        throw new Exception("Razorpay returned status {$status}: {$msg}");
    }
    return $data; // contains 'id', 'amount', 'currency', etc.
}

// Utility: JSON response helper
function json_ok($payload=[]) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['ok'=>true], $payload));
    exit;
}
function json_err($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['ok'=>false, 'message'=>$message]);
    exit;
}

// Handle AJAX / POST actions
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'send_otp') {
        $phone = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
        if (strlen($phone) !== 10) return json_err('Enter valid 10-digit mobile number');
        // rate limiting: simple example (1 OTP per 60s)
        $stmt = $pdo->prepare("SELECT created_at FROM otps WHERE phone = ? ORDER BY created_at DESC LIMIT 1");
        $stmt->execute([$phone]);
        $last = $stmt->fetchColumn();
        if ($last) {
            $last_dt = new DateTime($last);
            $now = new DateTime();
            if (($now->getTimestamp() - $last_dt->getTimestamp()) < 60) {
                return json_err('Please wait before requesting another OTP', 429);
            }
        }
        send_otp_and_store($phone, $pdo);
        return json_ok(['message'=>'OTP sent']);
    }

    if ($action === 'verify_and_create') {
        // collect and validate
        $name = trim($_POST['name'] ?? '');
        $phone = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
        $tshirt = trim($_POST['tshirt'] ?? '');
        $ticket = $_POST['ticket'] ?? '';
        $otp = trim($_POST['otp'] ?? '');

        if (!$name) json_err('Please enter full name');
        if (strlen($phone) !== 10) json_err('Enter valid 10-digit mobile number');
        if (!isset($ticket_options[$ticket])) json_err('Invalid ticket selected');

        // verify otp
        if (!verify_otp_code($phone, $otp, $pdo)) {
            json_err('Invalid or expired OTP', 401);
        }

        // find or create user
        $user = find_user_by_phone($phone, $pdo);
        if (!$user) {
            $user_id = create_user_by_phone($name, $phone, $pdo);
        } else {
            $user_id = $user['id'];
            // update name if blank
            if (empty($user['name']) && $name) {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, is_phone_verified = 1 WHERE id = ?");
                $stmt->execute([$name, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET is_phone_verified = 1 WHERE id = ?");
                $stmt->execute([$user_id]);
            }
        }

        // login user server-side
        login_user_by_id($user_id);

        // create registration record (status pending)
        $ticket_price = intval($ticket_options[$ticket]['price']);
        $stmt = $pdo->prepare("INSERT INTO registrations (user_id, ticket_code, ticket_price, tshirt_size, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->execute([$user_id, $ticket, $ticket_price, $tshirt]);
        $registration_id = $pdo->lastInsertId();

        // create Razorpay order if keys available
        if (!empty($RAZORPAY_KEY_ID) && !empty($RAZORPAY_KEY_SECRET)) {
            try {
                $receipt = "reg_" . $registration_id . "_" . time();
                $order = create_razorpay_order($ticket_price, $receipt, $RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET);
                // store order id on registration (optional)
                $stmt = $pdo->prepare("UPDATE registrations SET external_order_id = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$order['id'], $registration_id]);

                // return order info for client-side checkout
                json_ok([
                    'message' => 'Registration created, proceed to payment',
                    'registration_id' => (int)$registration_id,
                    'razorpay' => [
                        'order_id' => $order['id'],
                        'amount' => $order['amount'],
                        'currency' => $order['currency'],
                        'key_id' => $RAZORPAY_KEY_ID,
                        'name' => 'IMA Latur Runathon 2026',
                        'description' => $ticket_options[$ticket]['label'] . ' registration',
                    ]
                ]);
            } catch (Exception $e) {
                // leave registration as pending; report error but provide registration id
                error_log("Razorpay error: " . $e->getMessage());
                json_ok([
                    'message' => 'Registration created but failed to create payment order. Contact support.',
                    'registration_id' => (int)$registration_id,
                    'warning' => $e->getMessage()
                ]);
            }
        } else {
            // no Razorpay keys configured: return registration and instruct manual payment
            json_ok([
                'message' => 'Registration created (no payment gateway configured). Please contact organizer for manual payment.',
                'registration_id' => (int)$registration_id
            ]);
        }
    }

    // unknown action
    json_err('Unknown action', 400);
}

// If GET: show registration form (mobile-first)
$sel_ticket = $_GET['ticket'] ?? $default_ticket;
if (!isset($ticket_options[$sel_ticket])) $sel_ticket = $default_ticket;

?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — IMA Latur Runathon 2026</title>
  <style>
    :root{--primary:#2563eb;--muted:#64748b}
    *{box-sizing:border-box}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,"Noto Sans",Arial;margin:0;background:#f7fafc;color:#0f172a}
    .wrap{max-width:720px;margin:18px auto;padding:16px}
    .card{background:#fff;padding:16px;border-radius:12px;box-shadow:0 8px 24px rgba(2,8,23,0.06)}
    h1{margin:0 0 12px;font-size:20px}
    label{display:block;margin-top:10px;font-size:14px;color:#111827}
    input[type="text"], input[type="tel"], select{width:100%;padding:10px;border-radius:8px;border:1px solid #e6eefc;margin-top:6px}
    .row{display:flex;gap:8px}
    .btn{display:inline-block;padding:12px 14px;border-radius:10px;background:var(--primary);color:#fff;font-weight:800;border:none;cursor:pointer;margin-top:10px}
    .btn.ghost{background:#fff;color:var(--primary);border:1px solid var(--primary)}
    .note{font-size:13px;color:var(--muted);margin-top:8px}
    .ticket-list{display:flex;flex-direction:column;gap:8px;margin-top:10px}
    .ticket-item{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:8px;border:1px solid #eef6ff;background:#fff;cursor:pointer}
    .ticket-item.active{border-color:var(--primary);box-shadow:0 12px 34px rgba(37,99,235,0.08)}
    .otp-box{margin-top:10px;display:none}
    .message{margin-top:10px;padding:8px;border-radius:8px}
    .message.success{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
    .message.error{background:#fff7f5;color:#7f1d1d;border:1px solid #fecaca}
    .center{text-align:center}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card" role="form" aria-labelledby="heading">
      <h1 id="heading">Quick Registration</h1>
      <div class="note">Guest checkout with mobile OTP. Account will be auto-created and you will be logged in.</div>

      <form id="regForm" onsubmit="return false;">
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" required placeholder="Your full name">

        <label for="phone">Mobile (10 digits)</label>
        <input id="phone" name="phone" type="tel" required placeholder="e.g. 9876543210">

        <label for="tshirt">T-shirt size</label>
        <select id="tshirt" name="tshirt">
          <option value="">Select size</option>
          <option>XS</option><option>S</option><option>M</option><option>L</option><option>XL</option><option>XXL</option>
        </select>

        <label style="margin-top:12px">Choose distance</label>
        <div class="ticket-list" id="ticketList">
          <?php foreach ($ticket_options as $code => $info): ?>
            <div class="ticket-item<?php echo ($code === $sel_ticket) ? ' active' : ''; ?>" data-ticket="<?php echo h($code); ?>">
              <div>
                <div style="font-weight:800"><?php echo h($info['label']); ?></div>
                <div style="font-size:13px;color:var(--muted)"><?php echo h($info['desc']); ?></div>
              </div>
              <div style="text-align:right">
                <div style="font-weight:900">₹<?php echo number_format($info['price']); ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="note">Tap "Send OTP" to receive a one-time code on your mobile. Then enter OTP and complete registration.</div>

        <div style="margin-top:12px">
          <button class="btn" id="sendOtpBtn">Send OTP</button>
          <button class="btn ghost" id="backBtn" type="button" onclick="window.history.back()">Cancel</button>
        </div>

        <div class="otp-box" id="otpBox">
          <label for="otp">Enter OTP</label>
          <div class="row">
            <input id="otp" name="otp" type="text" placeholder="6-digit code" style="flex:1">
            <button class="btn" id="verifyBtn" style="flex:0 0 140px">Verify & Pay</button>
          </div>
        </div>

        <div id="msg" role="status" aria-live="polite"></div>
      </form>

      <div style="margin-top:12px" class="center small muted">
        By proceeding you agree to the event terms. For help contact organizers.
      </div>
    </div>
  </div>

<script>
(function(){
  var ticketList = document.getElementById('ticketList');
  var ticketItems = Array.from(ticketList.querySelectorAll('.ticket-item'));
  var selectedTicket = '<?php echo addslashes($sel_ticket); ?>';

  function setActiveTicket(code){
    ticketItems.forEach(function(it){
      var t = it.getAttribute('data-ticket');
      if(t === code) it.classList.add('active'); else it.classList.remove('active');
    });
    selectedTicket = code;
  }
  ticketItems.forEach(function(it){
    it.addEventListener('click', function(){ setActiveTicket(it.getAttribute('data-ticket')); });
  });
  setActiveTicket(selectedTicket);

  var sendOtpBtn = document.getElementById('sendOtpBtn');
  var verifyBtn = document.getElementById('verifyBtn');
  var nameInput = document.getElementById('name');
  var phoneInput = document.getElementById('phone');
  var tshirtInput = document.getElementById('tshirt');
  var otpBox = document.getElementById('otpBox');
  var otpInput = document.getElementById('otp');
  var msg = document.getElementById('msg');

  function showMsg(type, text){
    msg.innerHTML = '<div class="message '+(type==='ok'?'success':'error')+'">'+text+'</div>';
  }

  sendOtpBtn.addEventListener('click', function(e){
    e.preventDefault();
    var name = nameInput.value.trim();
    var phone = phoneInput.value.replace(/\D/g,'').trim();
    if(!name){ showMsg('err','Please enter your full name'); return; }
    if(phone.length !== 10){ showMsg('err','Enter a valid 10-digit mobile number'); return; }
    // send OTP via AJAX
    var data = new FormData();
    data.append('action','send_otp');
    data.append('phone', phone);
    fetch('', {method:'POST', body: data}).then(function(res){ return res.json(); }).then(function(json){
      if(json.ok){
        showMsg('ok', json.message || 'OTP sent');
        otpBox.style.display = 'block';
      } else {
        showMsg('err', json.message || 'Error sending OTP');
      }
    }).catch(function(){ showMsg('err','Network error'); });
  });

  verifyBtn.addEventListener('click', function(e){
    e.preventDefault();
    var name = nameInput.value.trim();
    var phone = phoneInput.value.replace(/\D/g,'').trim();
    var tshirt = tshirtInput.value || '';
    var otp = otpInput.value.trim();
    if(!name || phone.length !== 10 || !otp || !selectedTicket){ showMsg('err','Please fill all fields and enter OTP'); return; }

    var data = new FormData();
    data.append('action','verify_and_create');
    data.append('name', name);
    data.append('phone', phone);
    data.append('tshirt', tshirt);
    data.append('ticket', selectedTicket);
    data.append('otp', otp);

    showMsg('ok','Verifying...');

    fetch('', {method:'POST', body: data}).then(function(res){
      return res.json();
    }).then(function(json){
      if(!json.ok){
        showMsg('err', json.message || 'Verification failed');
        return;
      }
      // If Razorpay order info returned, open checkout
      if(json.razorpay && json.razorpay.order_id){
        showMsg('ok','Redirecting to payment...');
        // create checkout options
        var options = {
            "key": json.razorpay.key_id,
            "amount": json.razorpay.amount,
            "currency": json.razorpay.currency,
            "name": json.razorpay.name,
            "description": json.razorpay.description,
            "order_id": json.razorpay.order_id,
            "handler": function (response){
                // payment success handler — redirect to dashboard or success page
                window.location.href = '/userpanel/dashboard.php';
            },
            "prefill": {
                "name": name,
                "contact": phone
            },
            "theme": {
                "color": "#2563eb"
            }
        };
        // load Razorpay script dynamically then open
        var script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        script.onload = function(){
          var rzp = new Razorpay(options);
          rzp.open();
        };
        document.body.appendChild(script);
      } else {
        // No Razorpay configured — show message and redirect
        showMsg('ok', json.message || 'Registered. You are logged in.');
        setTimeout(function(){ window.location.href = '/userpanel/dashboard.php'; }, 1500);
      }
    }).catch(function(err){
      console.error(err);
      showMsg('err','Network error during verification');
    });
  });

})();
</script>
</body>
</html>