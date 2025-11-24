<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index.php" class="nav-link">Home</a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <div class="dropdown">
      <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
        aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-user"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
        <?php
        // Safe access to DB and session to show user info from `user` table
        include_once __DIR__ . '/../config/config.php';
        $uid = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
        function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

        if ($uid && ($GLOBALS['conn'] ?? null) instanceof mysqli) {
            $conn = $GLOBALS['conn'];
            $sql = "SELECT id, participant_name, email, whatsappno FROM `user` WHERE id = ? LIMIT 1";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, 'i', $uid);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $user = $res ? mysqli_fetch_assoc($res) : null;
                mysqli_stmt_close($stmt);
            } else {
                $user = null;
            }
        } else {
            $user = null;
        }

        if ($user) {
            // Display basic user info and links
            echo '<div class="dropdown-item"><strong>' . h($user['participant_name']) . '</strong></div>';
            if (!empty($user['email'])) {
                echo '<div class="dropdown-item small text-muted">' . h($user['email']) . '</div>';
            }
            if (!empty($user['whatsappno'])) {
                echo '<div class="dropdown-item small text-muted">+' . h($user['whatsappno']) . '</div>';
            }
            echo '<div class="dropdown-divider"></div>';
            echo '<a class="dropdown-item" href="profile.php">Profile</a>';
            echo '<a class="dropdown-item" href="paymentrecipt.php">Receipt</a>';
            echo '<a class="dropdown-item" href="logout.php">Logout</a>';
        } else {
            // Not logged in / no user
            echo '<a class="dropdown-item" href="studentlogin.php">Login</a>';
            echo '<a class="dropdown-item" href="index.php">Register</a>';
        }
        ?>
      </div>
    </div>
  </ul>
</nav>
<?php
?>