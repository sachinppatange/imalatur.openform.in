<?php
// Landing page for SaaS app: Choose User Login or Admin Login
// UI/UX style matches previous (login.php, dashboard.php) design

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>123 Welcome </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { --primary:#2563eb; --secondary:#0ea5e9; --bg:#f8fafc; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; --ring:#93c5fd; }
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }
        .wrap { min-height: 100dvh; display: grid; place-items: center; padding: 16px; }
        .card { width: 100%; max-width: 460px; background: var(--card); border-radius: 14px; box-shadow: 0 10px 30px rgba(2,8,23,.08); padding: 24px; text-align: center; }
        h1 { margin: 0 0 8px; font-size: 26px; }
        .sub { color: var(--muted); font-size: 16px; margin-bottom: 18px; }
        .btn { display: block; width: 100%; padding: 16px 0; margin: 10px 0; border: 0; border-radius: 12px; background: var(--primary); color: #fff; font-size: 17px; font-weight: 600; cursor: pointer; box-shadow: 0 6px 16px rgba(37,99,235,.12); transition: background .15s; }
        .btn.secondary { background: var(--secondary);}
        .btn.outline { background: #fff; color: var(--primary); border: 1px solid var(--primary);}
        .links { display: grid; gap: 12px; margin-top: 18px;}
        .logo { margin-bottom: 14px; }
        .note { font-size: 12px; color: var(--muted); margin-top: 14px; }
        @media (max-width: 480px) { .card { padding: 14px; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card" role="region" aria-label="Landing">
        <div class="logo">
            <img src="https://rsat.openform.in/assets/logo.png" alt="RSAT Logo" style="height:54px;">
        </div>
        <h1>Welcome to RSAT Portal</h1>
        <div class="sub">Choose how you want to log in</div>
        <div class="links">
            <a class="btn" href="userpanel/login.php">User Login</a>
            <a class="btn secondary" href="userpanel/signup.php">Create User Account</a>
            <a class="btn outline" href="adminpanel/admin_login.php">Admin Login</a>
        </div>
        <div class="note">
            Powered by WhatsApp OTP authentication.<br>
            For support contact admin.
        </div>
    </div>
</div>
</body>
</html>