<?php
// register.php - Updated robust implementation
// Place this file in userpanel/register.php
// - Uses send_whatsapp_otp() from whatsapp.php if available
// - Stores OTP in user session (fallback) and optionally logs to DB if db() exists
// - Returns JSON for all responses
// - Defensive: checks file existence, catches exceptions, respects APP_DEBUG

// Ensure no output before JSON
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/json; charset=utf-8');
}

// Helper: safe JSON response and exit
function json_response(array $payload, int $httpStatus = 200): void {
    if (php_sapi_name() !== 'cli') {
        http_response_code($httpStatus);
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    } else {
        // CLI: pretty print
        echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    }
    exit;
}

// Load config files safely
$baseDir = realpath(__DIR__ . '/../') ?: __DIR__ . '/..';
$appConfig = $baseDir . '/config/app_config.php';
$waConfig  = $baseDir . '/config/wa_config.php';
$whatsapp  = __DIR__ . '/whatsapp.php';
$userRepo  = __DIR__ . '/user_repository.php';

// Optional includes — require_once only if file exists
if (file_exists($appConfig)) {
    require_once $appConfig;
}
if (file_exists($waConfig)) {
    require_once $waConfig;
}
if (file_exists($whatsapp)) {
    require_once $whatsapp;
}
if (file_exists($userRepo)) {
    require_once $userRepo;
}

// Configure error display based on APP_DEBUG (if defined)
if (defined('APP_DEBUG') && APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

// Start session for OTP fallback storage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helpers
function log_debug(string $msg): void {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        error_log('[DEBUG - register.php] ' . $msg);
    }
    if (defined('LOG_FILE') && LOG_FILE) {
        @file_put_contents(LOG_FILE, '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}

function sanitize_phone(string $phone): string {
    // remove non-digits
    $digits = preg_replace('/\D+/', '', $phone);
    // remove leading zeros if country code will be prepended
    return ltrim($digits, '0');
}

function get_full_phone(string $phone): string {
    $phone = sanitize_phone($phone);
    $cc = defined('WA_COUNTRY_CODE') ? (string)WA_COUNTRY_CODE : '91';
    // If the incoming phone already starts with country code, don't double-prefix
    if (strpos($phone, $cc) === 0) {
        return $phone;
    }
    return $cc . $phone;
}

function generate_otp(int $length = 4): string {
    $max = (int) bcpow('10', (string)$length) - 1;
    $min = 0;
    try {
        $num = random_int($min, $max);
    } catch (Throwable $e) {
        // fallback
        $num = mt_rand($min, $max);
    }
    return str_pad((string)$num, $length, '0', STR_PAD_LEFT);
}

// Only accept POST
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if (strtoupper($method) !== 'POST') {
    json_response(['ok' => false, 'message' => 'Invalid request method. Use POST.'], 405);
}

// Read raw POST or form parameters
$action = $_POST['action'] ?? null;
if (!$action) {
    // also accept JSON body
    $raw = file_get_contents('php://input');
    if ($raw) {
        $data = json_decode($raw, true);
        if (is_array($data) && isset($data['action'])) {
            $action = $data['action'];
            // merge into $_POST-like array for convenience
            $_POST = array_merge($_POST, $data);
        }
    }
}

if (!$action) {
    json_response(['ok' => false, 'message' => 'Missing action parameter.'], 400);
}

try {
    switch ($action) {
        case 'send_otp':
            $phoneRaw = trim((string)($_POST['phone'] ?? ''));
            if ($phoneRaw === '') {
                json_response(['ok' => false, 'message' => 'Phone is required.'], 400);
            }
            $fullPhone = get_full_phone($phoneRaw);
            if (strlen($fullPhone) < 6) {
                json_response(['ok' => false, 'message' => 'Invalid phone number.'], 400);
            }

            $otpLength = defined('OTP_LENGTH') ? (int)OTP_LENGTH : 4;
            if ($otpLength <= 0 || $otpLength > 10) $otpLength = 4;
            $otp = generate_otp($otpLength);
            $expirySeconds = defined('OTP_EXPIRY_SECONDS') ? (int)OTP_EXPIRY_SECONDS : 300;
            $expiry = time() + $expirySeconds;

            // Try sending via WhatsApp API if function exists
            $waResult = null;
            if (function_exists('send_whatsapp_otp')) {
                try {
                    // send_whatsapp_otp should accept ($phone, $otp)
                    $waResult = send_whatsapp_otp($fullPhone, $otp);
                    log_debug("send_whatsapp_otp() returned: " . json_encode($waResult));
                } catch (Throwable $e) {
                    log_debug("Exception in send_whatsapp_otp(): " . $e->getMessage());
                    $waResult = ['ok' => false, 'error' => $e->getMessage()];
                }
            } else {
                log_debug("send_whatsapp_otp() not available; skipping API call.");
                // simulate success only in dev mode
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    $waResult = ['ok' => true, 'http_code' => 200, 'response' => 'DEBUG_ONLY_NO_API_CALL'];
                } else {
                    $waResult = ['ok' => false, 'error' => 'WhatsApp function not available'];
                }
            }

            if (empty($waResult['ok'])) {
                // Do not store OTP on failure to send
                $msg = 'Failed to send OTP.';
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    $msg .= ' ' . ($waResult['error'] ?? json_encode($waResult));
                }
                json_response(['ok' => false, 'message' => $msg, 'debug' => $waResult], 500);
            }

            // Store OTP in session as fallback. If DB helper exists, attempt DB storage too (best-effort).
            $_SESSION['otp_store'][$fullPhone] = [
                'otp' => $otp,
                'expires' => $expiry,
                'created_at' => time(),
            ];

            // Optional: store in DB if db() exists and there's an 'otps' or similar table.
            if (function_exists('db')) {
                try {
                    $pdo = db();
                    // Try creating a simple otps table if not exists (best-effort; silently ignore errors)
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS otps (
                            phone VARCHAR(32) PRIMARY KEY,
                            otp VARCHAR(16) NOT NULL,
                            expires_at DATETIME NOT NULL,
                            created_at DATETIME NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                    $stmt = $pdo->prepare("REPLACE INTO otps (phone, otp, expires_at, created_at) VALUES (:phone, :otp, FROM_UNIXTIME(:expires), FROM_UNIXTIME(:created))");
                    $stmt->execute([
                        ':phone' => $fullPhone,
                        ':otp' => $otp,
                        ':expires' => $expiry,
                        ':created' => time(),
                    ]);
                } catch (Throwable $e) {
                    // log but don't break flow
                    log_debug("DB OTP store failed: " . $e->getMessage());
                }
            }

            // Successful response
            json_response([
                'ok' => true,
                'message' => 'OTP sent successfully.',
                'phone' => $fullPhone,
                'otp_length' => $otpLength,
                'expires_in' => $expirySeconds,
                // In debug mode we return otp for easier local testing
                'otp' => (defined('APP_DEBUG') && APP_DEBUG) ? $otp : null,
                'wa_debug' => (defined('APP_DEBUG') && APP_DEBUG) ? $waResult : null,
            ], 200);
            break;

        case 'verify_and_create':
            $name = trim((string)($_POST['name'] ?? ''));
            $phoneRaw = trim((string)($_POST['phone'] ?? ''));
            $otpInput = trim((string)($_POST['otp'] ?? ''));
            $tshirt = trim((string)($_POST['tshirt'] ?? ''));
            $ticket = trim((string)($_POST['ticket'] ?? ''));

            if ($phoneRaw === '' || $otpInput === '') {
                json_response(['ok' => false, 'message' => 'Phone and OTP are required.'], 400);
            }
            $fullPhone = get_full_phone($phoneRaw);

            // Check session store first
            $stored = $_SESSION['otp_store'][$fullPhone] ?? null;
            $verified = false;
            $reason = '';

            if ($stored && isset($stored['otp'])) {
                if ((string)$stored['otp'] === (string)$otpInput) {
                    if ($stored['expires'] >= time()) {
                        $verified = true;
                        // remove used OTP
                        unset($_SESSION['otp_store'][$fullPhone]);
                    } else {
                        $reason = 'OTP expired.';
                    }
                } else {
                    $reason = 'Invalid OTP.';
                }
            } else {
                $reason = 'No OTP found for this phone.';
            }

            // As fallback, check DB 'otps' table if db() exists
            if (!$verified && function_exists('db')) {
                try {
                    $pdo = db();
                    $stmt = $pdo->prepare("SELECT otp, UNIX_TIMESTAMP(expires_at) AS expires FROM otps WHERE phone = :phone LIMIT 1");
                    $stmt->execute([':phone' => $fullPhone]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) {
                        if ((string)$row['otp'] === (string)$otpInput) {
                            if ((int)$row['expires'] >= time()) {
                                $verified = true;
                                // optional: delete row
                                $pdo->prepare("DELETE FROM otps WHERE phone = :phone")->execute([':phone' => $fullPhone]);
                            } else {
                                $reason = 'OTP expired (DB).';
                            }
                        } else {
                            $reason = 'Invalid OTP (DB).';
                        }
                    }
                } catch (Throwable $e) {
                    log_debug("DB OTP verify error: " . $e->getMessage());
                }
            }

            if (!$verified) {
                json_response(['ok' => false, 'message' => 'OTP verification failed.', 'reason' => $reason], 400);
            }

            // OTP verified. Create user if possible.
            $created = false;
            $userId = null;
            if (function_exists('db')) {
                try {
                    $pdo = db();
                    // Attempt to create minimal users table and insert (best-effort)
                    $pdo->exec("
                        CREATE TABLE IF NOT EXISTS users (
                            id INT AUTO_INCREMENT PRIMARY KEY,
                            name VARCHAR(191),
                            phone VARCHAR(32) UNIQUE,
                            tshirt VARCHAR(32),
                            ticket VARCHAR(32),
                            created_at DATETIME NOT NULL
                        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
                    ");
                    $stmt = $pdo->prepare("INSERT INTO users (name, phone, tshirt, ticket, created_at) VALUES (:name, :phone, :tshirt, :ticket, NOW()) ON DUPLICATE KEY UPDATE name = VALUES(name), tshirt = VALUES(tshirt), ticket = VALUES(ticket)");
                    $stmt->execute([
                        ':name' => $name ?: null,
                        ':phone' => $fullPhone,
                        ':tshirt' => $tshirt ?: null,
                        ':ticket' => $ticket ?: null,
                    ]);
                    // get id
                    $userId = $pdo->lastInsertId();
                    if (!$userId) {
                        // if duplicate updated, fetch id
                        $stmt = $pdo->prepare("SELECT id FROM users WHERE phone = :phone LIMIT 1");
                        $stmt->execute([':phone' => $fullPhone]);
                        $r = $stmt->fetch(PDO::FETCH_ASSOC);
                        $userId = $r['id'] ?? null;
                    }
                    $created = true;
                } catch (Throwable $e) {
                    log_debug("DB user create error: " . $e->getMessage());
                    // continue to non-db fallback below
                }
            }

            if (!$created) {
                // Fallback: keep user data in session (non-persistent)
                $_SESSION['users'][$fullPhone] = [
                    'name' => $name,
                    'phone' => $fullPhone,
                    'tshirt' => $tshirt,
                    'ticket' => $ticket,
                    'created_at' => date('c'),
                ];
                $created = true;
            }

            // Success
            json_response([
                'ok' => true,
                'message' => 'OTP verified and user registered.',
                'phone' => $fullPhone,
                'user_id' => $userId,
                'created_in_db' => (bool)$userId,
                'user_record' => (defined('APP_DEBUG') && APP_DEBUG) ? ($_SESSION['users'][$fullPhone] ?? null) : null,
            ], 200);
            break;

        default:
            json_response(['ok' => false, 'message' => 'Unknown action.'], 400);
    }
} catch (Throwable $e) {
    // Unexpected error
    $errMsg = 'Server error.';
    if (defined('APP_DEBUG') && APP_DEBUG) {
        $errMsg = $e->getMessage();
    }
    log_debug("Unhandled exception: " . $e->getMessage() . ' | ' . $e->getTraceAsString());
    json_response(['ok' => false, 'message' => $errMsg], 500);
}