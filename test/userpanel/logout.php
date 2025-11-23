<?php
session_start();
// सेशन क्लिअर करून user ला लॉगआउट करा
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged out</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --card:#ffffff; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; }
        *{box-sizing:border-box;}
        body{margin:0; font-family:system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:var(--bg); color:var(--text);}
        .wrap{min-height:100dvh; display:grid; place-items:center; padding:16px;}
        .card{width:100%; max-width:460px; background:var(--card); border-radius:14px; box-shadow:0 10px 30px rgba(2,8,23,.08); padding:24px; text-align:center;}
        h1{margin:0 0 8px; font-size:22px;}
        .sub{color:var(--muted); margin-bottom:14px;}
        .btn{display:block; width:100%; padding:14px 16px; border:0; border-radius:12px; background:var(--primary); color:#fff; font-size:16px; font-weight:600; cursor:pointer; margin-top:10px;}
        .btn.outline{background:#fff; color:var(--primary); border:1px solid var(--primary);}
        .links{display:grid; gap:10px; margin-top:10px;}
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>You have been logged out</h1>
        <div class="sub">Thank you for visiting. You can log in again or create a new account.</div>
        <div class="links">
            <a class="btn" href="login.php">Login</a>
            <a class="btn outline" href="signup.php">Create New Account</a>
        </div>
    </div>
</div>
</body>
</html>