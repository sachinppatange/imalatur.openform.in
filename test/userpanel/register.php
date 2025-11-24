<?php
/**
 * userpanel/register.php
 *
 * Full registration handler:
 *  - GET: renders a mobile-first registration form
 *  - POST action=send_otp: sends OTP via WhatsApp (via userpanel/whatsapp.php helper or Graph API if configured)
 *  - POST action=verify_and_create: verifies OTP, creates/updates user, creates registration, optionally creates Razorpay order
 *
 * Goals / fixes in this version:
 *  - Always return valid JSON for POST requests (prevents client "Network error during verification")
 *  - Use output buffering to avoid stray output breaking JSON
 *  - Centralized error handling and logging
 *  - Clear debug output when APP_DEBUG is true
 *  - Robust validation and helpful error messages
 *
 * Requirements:
 *  - config/app_config.php (optional) to set DB_HOST, DB_NAME, DB_USER, DB_PASS, WA_* and RAZORPAY_* constants
 *  - Optional userpanel/whatsapp.php providing send_whatsapp_otp($to_e164, $otp) => array('ok'=>bool, ...)
 *  - DB tables: users (id, name, phone, is_phone_verified, created_at, updated_at),
 *               otps (id, phone, code, expires_at, created_at),
 *               registrations (id, user_id, ticket_code, ticket_price, tshirt_size, external_order_id, status, created_at, updated_at)
 *
 * Usage notes:
 *  - Keep APP_DEBUG = false in production.
 *  - Ensure writable logs/ directory exists (project-root/logs) or change error_log path below.
 */

session_start();
date_default_timezone_set('Asia/Kolkata');

/* ---------------------------
   Configuration & environment
   --------------------------- */
if (!defined('APP_DEBUG')) define('APP_DEBUG', false);

/* Error handling: do not display errors in production */
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
$log_path = __DIR__ . '/../logs/php-error.log';
if (!file_exists(dirname($log_path))) {
    // try to create logs directory (best-effort)
    @mkdir(dirname($log_path), 0750, true);
}
ini_set('error_log', $log_path);

/* Use output buffering to avoid stray output breaking JSON responses */
ob_start();

/* Load app config if present */
$config_file = __DIR__ . '/../config/app_config.php';
if (file_exists($config_file)) {
    require_once $config_file;
}

/* Optional whatsapp helper */
$wh_helper = __DIR__ . '/whatsapp.php';
if (file_exists($wh_helper)) require_once $wh_helper;

/* DB defaults (can be overridden in config) */
$DB_HOST = defined('DB_HOST') ? DB_HOST : ($_ENV['DB_HOST'] ?? '127.0.0.1');
$DB_NAME = defined('DB_NAME') ? DB_NAME : ($_ENV['DB_NAME'] ?? 'imalatur');
$DB_USER = defined('DB_USER') ? DB_USER : ($_ENV['DB_USER'] ?? 'root');
$DB_PASS = defined('DB_PASS') ? DB_PASS : ($_ENV['DB_PASSWORD'] ?? ($_ENV['DB_PASS'] ?? ''));
$DB_CHARSET = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';
$DB_PORT = defined('DB_PORT') ? DB_PORT : 3306;

/* Razorpay keys (optional) */
$RAZORPAY_KEY_ID = defined('RAZORPAY_KEY_ID') ? RAZORPAY_KEY_ID : (getenv('RAZORPAY_KEY_ID') ?: '');
$RAZORPAY_KEY_SECRET = defined('RAZORPAY_KEY_SECRET') ? RAZORPAY_KEY_SECRET : (getenv('RAZORPAY_KEY_SECRET') ?: '');

/* WhatsApp defaults (optional) */
$WA_COUNTRY_CODE = defined('WA_COUNTRY_CODE') ? WA_COUNTRY_CODE : (defined('APP_COUNTRY_CODE') ? APP_COUNTRY_CODE : '91');

/* Tickets */
$ticket_options = [
    '800'  => ['label' => '5 km',  'price' => 800,  'desc' => 'Fun Run'],
    '1000' => ['label' => '10 km', 'price' => 1000, 'desc' => '10K — timed'],
    '1200' => ['label' => '21 km', 'price' => 1200, 'desc' => 'Half Marathon'],
];

/* OTP config */
if (!defined('OTP_LENGTH')) define('OTP_LENGTH', 6);
if (!defined('OTP_EXPIRY_SECONDS')) define('OTP_EXPIRY_SECONDS', 10 * 60);
if (!defined('OTP_RESEND_COOLDOWN')) define('OTP_RESEND_COOLDOWN', 60);

/* CSRF for GET form (simple) */
if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
$csrf_token = $_SESSION['csrf_token'];

/* ---------------------------
   Helpers
   --------------------------- */

function respond_json(array $data, int $status = 200) {
    // ensure any stray buffered output is captured (and logged), but not sent before JSON
    $buf = ob_get_clean();
    if ($buf && !defined('APP_DEBUG') || (defined('APP_DEBUG') && !APP_DEBUG)) {
        // log suppressed output
        error_log("Suppressed output before JSON response: " . substr($buf, 0, 2000));
    } elseif ($buf && APP_DEBUG) {
        // include debug output in response in debug mode
        $data['_debug_output'] = $buf;
    }
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function respond_ok(array $data = []) {
    respond_json(array_merge(['ok' => true], $data), 200);
}

function respond_error(string $message, int $status = 400, array $extra = []) {
    $payload = array_merge(['ok' => false, 'message' => $message], $extra);
    respond_json($payload, $status);
}

function generate_otp(int $len = OTP_LENGTH): string {
    return str_pad((string) random_int(0, (int) pow(10, $len) - 1), $len, '0', STR_PAD_LEFT);
}

/* Preferred: user-provided function send_whatsapp_otp($to_e164, $otp) */
function send_whatsapp_otp_fallback($to_e164, $otp) {
    // If WA_GRAPH_VERSION, WA_PHONE_ID and WA_TOKEN are set in config, send using Graph API template (simple text template fallback)
    if (defined('WA_PHONE_ID') && defined('WA_TOKEN') && defined('WA_GRAPH_VERSION')) {
        $url = sprintf('https://graph.facebook.com/%s/%s/messages', WA_GRAPH_VERSION, WA_PHONE_ID);
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to_e164,
            'type' => 'text',
            'text' => ['body' => "Your OTP for registration is: {$otp}"]
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . WA_TOKEN,
                'Content-Type: application/json'
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 25,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $resp = curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($err) {
            return ['ok' => false, 'error' => 'cURL error: ' . $err];
        }
        $data = json_decode($resp, true);
        if ($code >= 200 && $code < 300) return ['ok' => true, 'http_code' => $code, 'response' => $data];
        return ['ok' => false, 'http_code' => $code, 'response' => $data];
    }

    // Dev fallback: log OTP (only succeed if APP_DEBUG true)
    error_log("DEV OTP for {$to_e164}: {$otp}");
    if (defined('APP_DEBUG') && APP_DEBUG) return ['ok' => true, 'note' => 'DEV_LOG'];
    return ['ok' => false, 'error' => 'WhatsApp not configured'];
}

function send_whatsapp_otp_wrapper($to_e164, $otp) {
    if (function_exists('send_whatsapp_otp')) {
        try {
            $r = send_whatsapp_otp($to_e164, $otp);
            if (is_array($r) && !empty($r['ok'])) return $r;
            return array_merge(['ok' => false], (array)$r);
        } catch (Throwable $e) {
            error_log("send_whatsapp_otp() threw: " . $e->getMessage());
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
    return send_whatsapp_otp_fallback($to_e164, $otp);
}

/* DB-backed OTP store/verify */
function store_otp_db(PDO $pdo, string $phone_e164, string $otp): bool {
    $hash = password_hash($otp, PASSWORD_DEFAULT);
    $expires = (new DateTime('+'.OTP_EXPIRY_SECONDS.' seconds'))->format('Y-m-d H:i:s');
    $stmt = $pdo->prepare("INSERT INTO otps (phone, code, expires_at, created_at) VALUES (?, ?, ?, NOW())");
    return (bool)$stmt->execute([$phone_e164, $hash, $expires]);
}

function verify_otp_db(PDO $pdo, string $phone_e164, string $otp): bool {
    $stmt = $pdo->prepare("SELECT id, code, expires_at FROM otps WHERE phone = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$phone_e164]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) return false;
    if (new DateTime() > new DateTime($row['expires_at'])) return false;
    if (password_verify($otp, $row['code']) || hash_equals($row['code'], $otp)) {
        $del = $pdo->prepare("DELETE FROM otps WHERE id = ?");
        $del->execute([$row['id']]);
        return true;
    }
    return false;
}

/* Razorpay order */
function create_razorpay_order_api(string $key_id, string $key_secret, int $amount_rupees, string $receipt) {
    $amount_paise = intval(round($amount_rupees * 100));
    $payload = json_encode(['amount' => $amount_paise, 'currency' => 'INR', 'receipt' => $receipt, 'payment_capture' => 1]);
    $ch = curl_init('https://api.razorpay.com/v1/orders');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $key_id . ':' . $key_secret,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 30,
    ]);
    $resp = curl_exec($ch);
    $errno = curl_errno($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($errno) throw new Exception('Razorpay cURL error: ' . $err);
    $data = json_decode($resp, true);
    if ($status < 200 || $status >= 300) {
        $msg = $data['error']['description'] ?? ($data['error']['message'] ?? $resp);
        throw new Exception("Razorpay API returned {$status}: {$msg}");
    }
    return $data;
}

/* Simple user helpers */
function find_user_by_phone(PDO $pdo, string $phone) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? LIMIT 1");
    $stmt->execute([$phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function create_user_by_phone(PDO $pdo, string $name, string $phone) {
    $stmt = $pdo->prepare("INSERT INTO users (name, phone, is_phone_verified, auth_type, created_at) VALUES (?, ?, 1, 'otp', NOW())");
    $stmt->execute([$name, $phone]);
    return (int)$pdo->lastInsertId();
}
function update_user_verified(PDO $pdo, int $user_id, string $name = '') {
    if ($name) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, is_phone_verified = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$name, $user_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET is_phone_verified = 1, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$user_id]);
    }
}
function do_login(int $user_id) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user_id;
    $_SESSION['logged_in_at'] = time();
}

/* ---------------------------
   Database connection
   --------------------------- */
$pdo = null;
$dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset={$DB_CHARSET}";
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // For POST always respond JSON error
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        respond_error('Database connection error. See server logs.', 500, ['error' => APP_DEBUG ? $e->getMessage() : '']);
    }
    // For GET, log and continue to render a simple error marker on page
    error_log("DB connect error: " . $e->getMessage());
}

/* ---------------------------
   POST handlers (AJAX)
   --------------------------- */
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    // Clear any stray output buffer to avoid breaking JSON
    ob_clean();

    try {
        // Force JSON response header in POST
        header('Content-Type: application/json; charset=utf-8');

        $action = $_POST['action'] ?? $_POST['act'] ?? '';

        // send_otp
        if ($action === 'send_otp') {
            $phone_raw = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
            if (strlen($phone_raw) !== 10) respond_error('Enter a valid 10-digit mobile number', 400);

            $to_e164 = $WA_COUNTRY_CODE . $phone_raw;

            // rate limit: session-per-phone
            if (!isset($_SESSION['otp_rate'])) $_SESSION['otp_rate'] = [];
            $last = $_SESSION['otp_rate'][$to_e164]['ts'] ?? 0;
            if ($last && (time() - $last) < OTP_RESEND_COOLDOWN) {
                respond_error('Please wait before requesting another OTP', 429);
            }

            $otp = generate_otp();

            // store in DB if available
            if ($pdo instanceof PDO) {
                try {
                    store_otp_db($pdo, $to_e164, $otp);
                } catch (Throwable $e) {
                    // log and continue
                    error_log("Failed to store OTP in DB: " . $e->getMessage());
                }
            }

            $res = send_whatsapp_otp_wrapper($to_e164, $otp);
            if (empty($res['ok'])) {
                $err = $res['error'] ?? ($res['meta'] ?? 'Failed to send OTP');
                respond_error('Failed to send OTP. ' . (is_string($err) ? $err : json_encode($err)), 500);
            }

            $_SESSION['otp_rate'][$to_e164] = ['ts' => time(), 'attempts' => 0];
            respond_ok(['message' => 'OTP sent via WhatsApp. Please check your WhatsApp messages.']);
        }

        // verify_and_create
        if ($action === 'verify_and_create') {
            $name = trim($_POST['name'] ?? '');
            $phone_raw = preg_replace('/\D+/', '', $_POST['phone'] ?? '');
            $tshirt = trim($_POST['tshirt'] ?? '');
            $ticket = $_POST['ticket'] ?? '';
            $otp = trim($_POST['otp'] ?? '');

            if (!$name) respond_error('Please enter your full name', 400);
            if (strlen($phone_raw) !== 10) respond_error('Enter valid 10-digit mobile number', 400);
            if (!isset($ticket_options[$ticket])) respond_error('Invalid ticket selected', 400);
            if (!preg_match('/^\d{' . OTP_LENGTH . '}$/', $otp)) respond_error('Enter valid OTP', 400);

            $to_e164 = $WA_COUNTRY_CODE . $phone_raw;

            if (!($pdo instanceof PDO)) respond_error('Server database unavailable', 500);

            // verify
            $verified = verify_otp_db($pdo, $to_e164, $otp);
            if (!$verified) respond_error('Invalid or expired OTP', 401);

            // find/create user
            $user = find_user_by_phone($pdo, $to_e164);
            if (!$user) {
                $user_id = create_user_by_phone($pdo, $name, $to_e164);
            } else {
                $user_id = (int)$user['id'];
                update_user_verified($pdo, $user_id, $name);
            }

            // login
            do_login($user_id);

            // create registration
            $ticket_price = intval($ticket_options[$ticket]['price']);
            $stmt = $pdo->prepare("INSERT INTO registrations (user_id, ticket_code, ticket_price, tshirt_size, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$user_id, $ticket, $ticket_price, $tshirt]);
            $registration_id = (int)$pdo->lastInsertId();

            // create Razorpay order if configured
            if (!empty($RAZORPAY_KEY_ID) && !empty($RAZORPAY_KEY_SECRET)) {
                try {
                    $receipt = 'reg_' . $registration_id . '_' . time();
                    $order = create_razorpay_order_api($RAZORPAY_KEY_ID, $RAZORPAY_KEY_SECRET, $ticket_price, $receipt);
                    // store external order id
                    $upd = $pdo->prepare("UPDATE registrations SET external_order_id = ?, updated_at = NOW() WHERE id = ?");
                    $upd->execute([$order['id'], $registration_id]);

                    respond_ok([
                        'message' => 'Registration created — proceed to payment',
                        'registration_id' => $registration_id,
                        'razorpay' => [
                            'order_id' => $order['id'],
                            'amount' => $order['amount'],
                            'currency' => $order['currency'],
                            'key_id' => $RAZORPAY_KEY_ID,
                            'name' => 'IMA Latur Runathon',
                            'description' => $ticket_options[$ticket]['label'] . ' registration'
                        ]
                    ]);
                } catch (Throwable $e) {
                    error_log("Razorpay order creation failed: " . $e->getMessage());
                    respond_ok([
                        'message' => 'Registration created but payment order could not be created. Contact support.',
                        'registration_id' => $registration_id,
                        'warning' => APP_DEBUG ? $e->getMessage() : ''
                    ]);
                }
            }

            // no payment configured
            respond_ok([
                'message' => 'Registration created. Please follow organizer instructions for payment.',
                'registration_id' => $registration_id
            ]);
        }

        respond_error('Unknown action', 400);
    } catch (Throwable $e) {
        // Catch-all: respond JSON error
        error_log("Exception in register.php POST: " . $e->getMessage() . "\n" . $e->getTraceAsString());
        if (APP_DEBUG) {
            respond_error('Server error: ' . $e->getMessage(), 500, ['trace' => $e->getTraceAsString()]);
        } else {
            respond_error('Server error', 500);
        }
    }
}

/* ---------------------------
   GET: render the registration page
   --------------------------- */
/* Clean output buffer before sending HTML page */
ob_end_clean();

$sel_ticket = $_GET['ticket'] ?? array_key_first($ticket_options);
if (!isset($ticket_options[$sel_ticket])) $sel_ticket = array_key_first($ticket_options);

function h($s){ return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?><!doctype html>
<html lang="mr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register — IMA Latur Runathon</title>
  <style>
    :root{--primary:#2563eb;--muted:#64748b}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;margin:0;background:#f7fafc;color:#0f172a}
    .wrap{max-width:720px;margin:18px auto;padding:16px}
    .card{background:#fff;padding:16px;border-radius:12px;box-shadow:0 8px 24px rgba(2,8,23,0.06)}
    h1{margin:0 0 12px;font-size:20px}
    label{display:block;margin-top:10px;font-size:14px;color:#111827}
    input[type="text"], input[type="tel"], select{width:100%;padding:10px;border-radius:8px;border:1px solid #e6eefc;margin-top:6px;font-size:14px}
    .ticket-list{display:flex;flex-direction:column;gap:8px;margin-top:10px}
    .ticket-item{display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:8px;border:1px solid #eef6ff;background:#fff;cursor:pointer}
    .ticket-item.active{border-color:var(--primary);box-shadow:0 12px 34px rgba(37,99,235,0.08)}
    .row{display:flex;gap:8px}
    .btn{display:inline-block;padding:12px 14px;border-radius:10px;background:var(--primary);color:#fff;font-weight:800;border:none;cursor:pointer;margin-top:10px}
    .btn.ghost{background:#fff;color:var(--primary);border:1px solid var(--primary)}
    .note{font-size:13px;color:var(--muted);margin-top:8px}
    .otp-box{margin-top:10px;display:none}
    .message{margin-top:10px;padding:8px;border-radius:8px}
    .message.success{background:#ecfdf5;color:#065f46;border:1px solid #bbf7d0}
    .message.error{background:#fff7f5;color:#7f1d1d;border:1px solid #fecaca}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="card">
      <h1>Quick Registration</h1>
      <div class="note">Guest checkout with mobile OTP. Account will be auto-created after verification.</div>

      <form id="regForm" onsubmit="return false;">
        <label for="name">Full name</label>
        <input id="name" name="name" type="text" required placeholder="Your full name">

        <label for="phone">Mobile (10 digits)</label>
        <input id="phone" name="phone" type="tel" required placeholder="e.g. 9876543210" inputmode="numeric" maxlength="10">

        <label for="tshirt">T-shirt size</label>
        <select id="tshirt" name="tshirt">
          <option value="">Select size</option>
          <option>XS</option><option>S</option><option>M</option><option>L</option><option>XL</option><option>XXL</option>
        </select>

        <label style="margin-top:12px">Choose distance</label>
        <div class="ticket-list" id="ticketList">
          <?php foreach ($ticket_options as $code => $info): $active = ($code === $sel_ticket) ? ' active' : ''; ?>
            <div class="ticket-item<?php echo $active ?>" data-ticket="<?php echo h($code); ?>">
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

        <div class="note">Tap "Send OTP" to receive a one-time code on your WhatsApp. Then enter OTP and complete registration.</div>

        <div style="margin-top:12px">
          <button class="btn" id="sendOtpBtn">Send OTP</button>
          <button class="btn ghost" id="backBtn" type="button" onclick="window.history.back()">Cancel</button>
        </div>

        <div class="otp-box" id="otpBox">
          <label for="otp">Enter OTP</label>
          <div class="row">
            <input id="otp" name="otp" type="text" placeholder="<?php echo OTP_LENGTH ?>-digit code" style="flex:1">
            <button class="btn" id="verifyBtn" style="flex:0 0 160px">Verify & Pay</button>
          </div>
        </div>

        <div id="msg" role="status" aria-live="polite"></div>
      </form>
    </div>
  </div>

<script>
/*
  Client JS: robust fetch handling.
  - Always read response as text, try JSON.parse, if fails show server text (helps debug PHP warnings)
  - Use credentials: 'same-origin' to keep session
*/
(function(){
  const ticketList = document.getElementById('ticketList');
  const ticketItems = Array.from(ticketList.querySelectorAll('.ticket-item'));
  let selectedTicket = '<?php echo addslashes($sel_ticket); ?>';
  function setActive(code){
    ticketItems.forEach(it => it.classList.toggle('active', it.getAttribute('data-ticket') === code));
    selectedTicket = code;
  }
  ticketItems.forEach(it => it.addEventListener('click', () => setActive(it.getAttribute('data-ticket'))));
  setActive(selectedTicket);

  const sendOtpBtn = document.getElementById('sendOtpBtn');
  const verifyBtn = document.getElementById('verifyBtn');
  const nameInput = document.getElementById('name');
  const phoneInput = document.getElementById('phone');
  const tshirtInput = document.getElementById('tshirt');
  const otpBox = document.getElementById('otpBox');
  const otpInput = document.getElementById('otp');
  const msg = document.getElementById('msg');

  function showMsg(type, text){
    msg.innerHTML = '<div class="message '+(type==='ok'?'success':'error')+'">'+text+'</div>';
  }

  function parseJsonSafe(text) {
    try {
      return { ok: true, json: JSON.parse(text) };
    } catch (e) {
      return { ok: false, text: text };
    }
  }

  sendOtpBtn.addEventListener('click', function(e){
    e.preventDefault();
    const phone = phoneInput.value.replace(/\D/g,'').trim();
    if (phone.length !== 10) { showMsg('err','Enter a valid 10-digit mobile number'); return; }
    sendOtpBtn.disabled = true; sendOtpBtn.textContent = 'Sending...';

    const fd = new FormData();
    fd.append('action','send_otp');
    fd.append('phone', phone);

    fetch('', { method:'POST', body: fd, credentials: 'same-origin' })
      .then(r => r.text())
      .then(text => {
        const parsed = parseJsonSafe(text);
        if (!parsed.ok) {
          showMsg('err','Server response: ' + parsed.text);
          console.error('Non-JSON send_otp response:', parsed.text);
          return;
        }
        const j = parsed.json;
        if (j.ok) {
          showMsg('ok', j.message || 'OTP sent via WhatsApp');
          otpBox.style.display = 'block';
        } else {
          showMsg('err', j.message || ('Error: ' + JSON.stringify(j)));
        }
      })
      .catch(err => {
        console.error('Fetch/send_otp error:', err);
        showMsg('err','Network error while sending OTP. Check console and server logs.');
      })
      .finally(()=>{ sendOtpBtn.disabled = false; sendOtpBtn.textContent = 'Send OTP'; });
  });

  verifyBtn.addEventListener('click', function(e){
    e.preventDefault();
    const name = nameInput.value.trim();
    const phone = phoneInput.value.replace(/\D/g,'').trim();
    const tshirt = tshirtInput.value;
    const otp = otpInput.value.trim();

    if (!name) { showMsg('err','Please enter your full name'); return; }
    if (phone.length !== 10) { showMsg('err','Enter valid 10-digit mobile'); return; }
    if (!selectedTicket) { showMsg('err','Select a distance'); return; }
    if (!/^\d{<?php echo (int)OTP_LENGTH; ?>}$/.test(otp)) { showMsg('err','Enter valid OTP'); return; }

    verifyBtn.disabled = true; verifyBtn.textContent = 'Verifying...';

    const fd = new FormData();
    fd.append('action','verify_and_create');
    fd.append('name', name);
    fd.append('phone', phone);
    fd.append('tshirt', tshirt);
    fd.append('ticket', selectedTicket);
    fd.append('otp', otp);

    fetch('', { method:'POST', body: fd, credentials: 'same-origin' })
      .then(r => r.text())
      .then(text => {
        const parsed = parseJsonSafe(text);
        if (!parsed.ok) {
          showMsg('err','Server response: ' + parsed.text);
          console.error('Non-JSON verify_and_create response:', parsed.text);
          return;
        }
        const j = parsed.json;
        if (!j.ok) {
          showMsg('err', j.message || ('Error: ' + JSON.stringify(j)));
          return;
        }
        if (j.razorpay) {
          showMsg('ok','Proceeding to payment...');
          const options = {
            key: j.razorpay.key_id,
            amount: j.razorpay.amount,
            currency: j.razorpay.currency,
            name: j.razorpay.name,
            description: j.razorpay.description,
            order_id: j.razorpay.order_id,
            handler: function(response){
              // After payment, redirect to dashboard (server should verify)
              window.location.href = '/userpanel/dashboard.php';
            },
            prefill: { name: name, contact: phone },
            theme: { color: '#2563eb' }
          };
          const script = document.createElement('script');
          script.src = 'https://checkout.razorpay.com/v1/checkout.js';
          script.onload = function(){ try { new Razorpay(options).open(); } catch (e) { console.error(e); showMsg('err','Payment popup failed'); } };
          document.body.appendChild(script);
        } else {
          showMsg('ok', j.message || 'Registered. Redirecting...');
          setTimeout(()=> window.location.href = '/userpanel/dashboard.php', 1200);
        }
      })
      .catch(err => {
        console.error('Fetch/verify error:', err);
        showMsg('err','Network error during verification. Check console and server logs.');
      })
      .finally(()=>{ verifyBtn.disabled = false; verifyBtn.textContent = 'Verify & Pay'; });
  });
})();
</script>
</body>
</html>