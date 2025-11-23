<?php
include_once "../config/config.php"; 
?>

<footer class="main-footer">
<div class="text-center my-4"> <!-- Add some margin for spacing -->
    <strong>
        Copyright &copy; 2024-2025 
        <span class="brand-text" style="color: #007BFF; font-weight: bold;"> <!-- Inline style for color and bold text -->
            <?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?>
        </span>
    </strong>
    All rights reserved.
</div>
    <div class="float-right d-none d-sm-inline-block">
      <!-- <b>Version</b> 3.1.0 -->
    </div>
  </footer>