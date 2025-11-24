<?php
include_once '../config/config.php';

// Get current user id from session (if any)
$uid = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
$student_display_name = 'Guest';
$avatar_src = 'dist/img/user2-160x160.jpg';

if ($uid && ($GLOBALS['conn'] ?? null) instanceof mysqli) {
    $conn = $GLOBALS['conn'];
    $sql = "SELECT participant_name, ticket, email, whatsappno FROM `user` WHERE id = ? LIMIT 1";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, 'i', $uid);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = $res ? mysqli_fetch_assoc($res) : null;
        mysqli_stmt_close($stmt);
        if ($row) {
            $student_display_name = trim($row['participant_name']) !== '' ? $row['participant_name'] : ('User#' . $row['id']);
        }
    }
}
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
      style="opacity: .8">
    <span class="brand-text font-weight-light"><?php echo h(function_exists('getcompany') ? getcompany() : 'Company'); ?></span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="<?php echo h($avatar_src); ?>" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="profile.php" class="d-block"><?php echo h($student_display_name); ?></a>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item menu-open">
          <a href="portal.php" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="profile.php" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>Profile</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="paymentrecipt.php" class="nav-link">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>Payment Receipt</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>