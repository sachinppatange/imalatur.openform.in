<?php
// Admin Dashboard: Shows list of users and actions. UI/UX as per userpanel/dashboard.php, but for admin.
// Only accessible to logged-in admin.

session_start();
require_once __DIR__ . '/admin_auth.php';
require_once __DIR__ . '/../config/app_config.php';
require_once __DIR__ . '/../config/wa_config.php';

// --- Authentication: Redirect if not logged in as admin ---
if (empty($_SESSION['admin_auth_user'])) {
    header('Location: admin_login.php?next=admin_dashboard.php');
    exit;
}

// --- Connect DB and get user list ---
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER, DB_PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $stmt = $pdo->query("SELECT full_name, email, address_line1, address_line2, pincode, city, state FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $users = [];
    $db_error = $e->getMessage();
}

// --- Admin info ---
$admin_phone = $_SESSION['admin_auth_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root { --primary:#2563eb; --bg:#f8fafc; --card:#fff; --text:#0f172a; --muted:#64748b; --border:#e2e8f0; }
        body { margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif; background:var(--bg); color:var(--text);}
        .wrap { min-height:100dvh; display:grid; place-items:center; padding:16px;}
        .card { width:100%; max-width:900px; background:var(--card); border-radius:14px; box-shadow:0 10px 30px rgba(2,8,23,.08); padding:24px;}
        h1 { margin:0 0 10px; font-size:26px; }
        .topbar { display:flex;justify-content:space-between;align-items:center;margin-bottom:18px; }
        .admin-info { color:var(--muted); font-size:15px;}
        .btn { padding:8px 18px; background:var(--primary); color:#fff; border-radius:10px; border:0; font-weight:600; cursor:pointer; text-decoration:none; font-size:16px;}
        .logout-btn { background:#e11d48;}
        table { width:100%; border-collapse:collapse; margin-top:16px;}
        th, td { padding:10px 7px; text-align:left; border-bottom:1px solid var(--border);}
        th { background:#f1f5f9; color:#1e293b; }
        tr:last-child td { border-bottom:none;}
        .empty { color:var(--muted); text-align:center; padding:30px;}
        @media (max-width: 900px) { .card{padding:12px;} th, td{font-size:13px;} }
        .scroll-table { overflow-x:auto; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <div class="topbar">
            <div>
                <h1>Admin Dashboard</h1>
                <div class="admin-info">
                    Logged in as: <b><?php echo htmlspecialchars($admin_phone); ?></b>
                </div>
            </div>
            <a href="admin_logout.php" class="btn logout-btn" title="Logout">Logout</a>
        </div>

        <h2 style="font-size:20px;margin-top:14px;">Registered Users</h2>
        <?php if (!empty($db_error)): ?>
            <div class="empty">Database Error: <?php echo htmlspecialchars($db_error); ?></div>
        <?php elseif (empty($users)): ?>
            <div class="empty">No users found.</div>
        <?php else: ?>
            <div class="scroll-table">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Address Line 1</th>
                        <th>Address Line 2</th>
                        <th>Pincode</th>
                        <th>City</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['address_line1']); ?></td>
                        <td><?php echo htmlspecialchars($u['address_line2']); ?></td>
                        <td><?php echo htmlspecialchars($u['pincode']); ?></td>
                        <td><?php echo htmlspecialchars($u['city']); ?></td>
                        <td><?php echo htmlspecialchars($u['state']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>