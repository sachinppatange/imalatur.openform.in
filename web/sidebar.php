<?php 
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

$stud_id = 1; // Replace with the actual student ID
$student = getStudentRegByMaxId($stud_id);
?>


<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
      style="opacity: .8">
    <span class="brand-text font-weight-light">
        <?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?>
    </span>
</a>


  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
      </div>
      
      <div class="info"> 
      <a href="#" class="d-block">
    <?php if ($student): // Check if student data was retrieved ?>
        <div class="student-name">
            <strong><?php echo htmlspecialchars($student['stud_name'], ENT_QUOTES, 'UTF-8'); ?></strong> <!-- Display student name -->
        </div>
    <?php else: ?>
        <div class="student-name">
            <strong>No student found.</strong>
        </div>
    <?php endif; ?>
</a>

</div> 
   
    </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
          <a href="portal.php" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
              <!-- <i class="right fas fa-angle-left"></i> -->
            </p>
          </a>
        </li>
          <!-- <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-user-shield"></i>
              <p>
                Students
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="studentlist.php" class="nav-link">
                  <i class="far fa-circle"></i>&nbsp;
                  <p>
                    Student List
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="member.php" class="nav-link">
                  <i class="far fa-circle"></i>&nbsp;
                  <p>
                    Member List
                  </p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link">
              <i class="fas fa-user-shield"></i>
              <p>
                Members
                <i class="fas fa-angle-left right"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="painmemberlist.php" class="nav-link">
                  <i class="far fa-circle"></i>&nbsp;
                  <p>
                    Paid Member List
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="unpainmemberlist.php" class="nav-link">
                  <i class="far fa-circle"></i>&nbsp;
                  <p>
                   Unpaid Member List
                  </p>
                </a>
              </li>
            </ul> -->
          </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>