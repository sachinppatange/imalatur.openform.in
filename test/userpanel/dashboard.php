<?php
session_start();
require_once __DIR__ . '/../userpanel/auth.php';
require_once __DIR__ . '/../userpanel/user_repository.php';

require_auth();

$e164 = current_user() ?? '';

function h(?string $v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Format phone as +CC AAA BBB CCCC (best-effort)
$displayPhone = '+' . $e164;
if ($e164) {
    $ccLen = 2; // adjust if needed
    $cc = substr($e164, 0, $ccLen);
    $local = substr($e164, $ccLen);
    if (strlen($local) === 10) {
        $displayPhone = "+$cc " . substr($local,0,3) . " " . substr($local,3,3) . " " . substr($local,6);
    }
}

// Load profile (optional fields)
$user = user_get($e164) ?? [
    'phone' => $e164,
    'full_name' => '',
    'email' => '',
    'address_line_1' => '',
    'address_line_2' => '',
    'pincode' => '',
    'city' => '',
    'state' => '',
];

// Profile completeness
$fields = ['full_name','email','address_line_1','address_line_2','pincode','city','state'];
$filled = 0; foreach ($fields as $f) { if (!empty($user[$f])) $filled++; }
$percent = (int) round($filled / count($fields) * 100);

// Any data?
$hasAny = $filled > 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { --primary:#2563eb; --secondary:#0ea5e9; --bg:#f8fafc; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; --ring:#93c5fd; }
        * { box-sizing: border-box; }
        body { margin:0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:var(--bg); color:var(--text); }
        .wrap { min-height:100dvh; display:grid; place-items:center; padding:16px; }
        .card { width:100%; max-width:560px; background:var(--card); border-radius:14px; box-shadow:0 10px 30px rgba(2,8,23,.08); padding:20px; }
        @media (min-width:420px){ .card{ padding:24px; } }

        .topbar { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; gap:10px; }
        .nav-left { display:flex; gap:8px; flex-wrap:wrap; }
        .link { text-decoration:none; color:#fff; background:#0f172a; padding:8px 12px; border-radius:10px; font-size:14px; font-weight:600; }
        .link.outline { background:#fff; color:#0f172a; border:1px solid #0f172a; }

        h1 { margin:0 0 6px; font-size:22px; text-align:center; }
        .sub { text-align:center; color:var(--muted); margin-bottom:12px; }

        .panel { background:#f8fafc; border:1px dashed var(--border); border-radius:12px; padding:12px; }
        .row { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
        .k { color:#0f172a; font-weight:600; }
        .v { color:#0f172a; white-space:pre-wrap; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:12px 14px; border:0; border-radius:12px; background:var(--primary); color:#fff; font-size:14px; font-weight:600; cursor:pointer; box-shadow:0 6px 16px rgba(37,99,235,.25); text-decoration:none; }
        .btn.outline { background:#fff; color:var(--primary); border:1px solid var(--primary); box-shadow:none; }
        .btn.block { display:block; width:100%; margin-top:12px; }
        .copy { background:#e2e8f0; border:0; padding:8px 10px; border-radius:10px; cursor:pointer; }
        .grid { display:grid; gap:12px; margin-top:12px; }
        .cardItem { border:1px solid var(--border); border-radius:12px; padding:12px; background:#fff; }
        .muted { color:var(--muted); }
        .pill { display:inline-block; padding:4px 8px; background:#eef2ff; color:#1e293b; border-radius:999px; font-size:12px; margin-right:6px; }
        .bar { height:10px; background:#e2e8f0; border-radius:999px; overflow:hidden; }
        .bar > div { height:100%; background:#2563eb; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="topbar">
            <div class="nav-left">
                <a class="link" href="profile.php">Profile</a>
                <a class="link outline" href="login.php">Switch account</a>
            </div>
            <a class="link" href="logout.php" style="background:#ef4444;">Logout</a>
        </div>

        <h1>Dashboard</h1>
        <div class="sub">Welcome! You are logged in.</div>

        <div class="panel">
            <div class="row">
                <div>
                    <div class="k">User ID (Phone)</div>
                    <div class="v" id="phoneDisp"><?php echo h($displayPhone); ?></div>
                </div>
                <button class="copy" id="copyBtn" type="button" title="Copy phone">Copy</button>
            </div>
            <div style="margin-top:10px;">
                <div class="k" style="margin-bottom:6px;">Profile completeness</div>
                <div class="bar"><div style="width:<?php echo $percent; ?>%;"></div></div>
                <div class="muted" style="text-align:right;margin-top:4px;"><?php echo $percent; ?>%</div>
            </div>
        </div>

        <div class="grid">
            <div class="cardItem">
                <div class="row" style="margin-bottom:6px;">
                    <div class="k">Profile summary</div>
                    <a class="btn outline" href="profile.php">Edit Profile</a>
                </div>

                <?php if (!$hasAny): ?>
                    <div class="muted">Your profile is empty. Click “Edit Profile” to add Full Name, Email, Address, Pincode, City, and State.</div>
                <?php else: ?>
                    <div style="display:grid; gap:10px;">
                        <?php if (!empty($user['full_name'])): ?>
                            <div><span class="pill">Full Name</span> <strong><?php echo h($user['full_name']); ?></strong></div>
                        <?php endif; ?>
                        <?php if (!empty($user['email'])): ?>
                            <div><span class="pill">Email</span> <?php echo h($user['email']); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($user['address_line_1']) || !empty($user['address_line_2'])): ?>
                            <div>
                                <span class="pill">Address</span>
                                <div class="v">
                                    <?php echo h(trim(($user['address_line_1'] ?? '') . "\n" . ($user['address_line_2'] ?? ''))); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($user['city']) || !empty($user['state']) || !empty($user['pincode'])): ?>
                            <div>
                                <span class="pill">Location</span>
                                <div class="v">
                                    <?php
                                        $parts = array_filter([ $user['city'] ?? '', $user['state'] ?? '', $user['pincode'] ?? '' ]);
                                        echo h(implode(', ', $parts));
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cardItem">
                <div class="k" style="margin-bottom:8px;">Quick actions</div>
                <div style="display:grid; gap:10px;">
                    <a class="btn outline" href="profile.php">Go to Profile</a>
                    <a class="btn outline" href="logout.php">Logout</a>
                </div>
            </div>
        </div>

        <a class="btn block" href="profile.php">Edit Profile</a>
    </div>
</div>

<script>
    // Copy phone
    const copyBtn = document.getElementById('copyBtn');
    const phoneDisp = document.getElementById('phoneDisp');
    if (copyBtn && phoneDisp) {
        copyBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(phoneDisp.textContent.trim());
                copyBtn.textContent = 'Copied!';
                setTimeout(()=> copyBtn.textContent='Copy', 1400);
            } catch (_) {
                copyBtn.textContent = 'Copy failed';
                setTimeout(()=> copyBtn.textContent='Copy', 1400);
            }
        });
    }
</script>
</body>
</html>