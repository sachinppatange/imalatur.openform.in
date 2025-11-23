<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlBulkAdmin.php';
include_once '../controller/ctrlBulkAssociate.php';
include_once '../controller/ctrlBulkUser.php';
include_once '../controller/ctrlBulkSuperAdmin.php';
// print_r($_SESSION);
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
} else {
    $id = '';
}
//  $type = $_SESSION['type'];
if ($_SESSION['type'] == 'superadmin') {

    $details = getSuperadminById($id);
} elseif ($_SESSION['type'] == 'associate') {

    $details = getAssociateById($id);
} elseif ($_SESSION['type'] == 'admin') {
    $details = getAdminById($id);
} else {

    $details = getUsersById($id);
}
// print_r($details);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>INCOZYSPACE</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

    <style>
        .student-profile .card {
            border-radius: 10px;
        }

        .student-profile .card .card-header .profile_img {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 10px auto;
            border: 10px solid #ccc;
            border-radius: 50%;
        }

        .student-profile .card h3 {
            font-size: 20px;
            font-weight: 700;
        }

        .student-profile .card p {
            font-size: 16px;
            color: #000;
        }

        .student-profile .table th,
        .student-profile .table td {
            font-size: 14px;
            padding: 5px 10px;
            color: #000;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

        <!-- Navbar -->
        <?php include_once 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include_once 'sidebar.php'; ?>
        <!-- Main Sidebar Container End -->


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper" style="background-color:#fff;">
            <!-- Student Profile -->
            <div class="student-profile py-4">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h3 class="mb-0"><i class="far fa-clone pr-1"></i>Profile Information</h3>
                                </div>
                                <div class="card-body pt-0">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="30%">Name</th>
                                            <td>
                                                <?php
                                                if ($_SESSION['type'] == 'superadmin') {
                                                    echo $details['username'];
                                                } elseif ($_SESSION['type'] == 'associate') {
                                                    echo $details['associatename'];
                                                } elseif ($_SESSION['type'] == 'admin') {
                                                    echo $details['adminname'];
                                                } else {
                                                    echo $details['membername'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Username</th>
                                            <td>
                                                <?php
                                                if ($_SESSION['type'] == 'superadmin') {
                                                    echo $details['username'];
                                                } elseif ($_SESSION['type'] == 'associate') {
                                                    echo $details['username'];
                                                } elseif ($_SESSION['type'] == 'admin') {
                                                    echo $details['username'];
                                                } else {
                                                    echo $details['username'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Password</th>
                                            <td>
                                                <?php
                                                if ($_SESSION['type'] == 'superadmin') {
                                                    echo $details['password'];
                                                } elseif ($_SESSION['type'] == 'associate') {
                                                    echo $details['password'];
                                                } elseif ($_SESSION['type'] == 'admin') {
                                                    echo $details['password'];
                                                } else {
                                                    echo $details['password'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">
                                                <?php if ($_SESSION['type'] == 'superadmin') { ?>
                                                    Company Name
                                                <?php
                                                } else {
                                                    ?>
                                                    City
                                                    <?php
                                                }
                                                ?>
                                            </th>
                                            <td>
                                                <?php
                                                if ($_SESSION['type'] == 'superadmin') {
                                                    echo $details['company_name'];
                                                } elseif ($_SESSION['type'] == 'associate') {
                                                    echo $details['city'];
                                                } elseif ($_SESSION['type'] == 'admin') {
                                                    echo $details['city'];
                                                } else {
                                                    echo $details['city'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">
                                            <?php if ($_SESSION['type'] == 'superadmin') { ?>
                                                
                                                <?php
                                                } else {
                                                    ?>
                                                    Join Date
                                                    <?php
                                                }
                                                ?></th>
                                            <td>
                                            <?php
                                                if ($_SESSION['type'] == 'superadmin') {
                                                    echo '';
                                                } elseif ($_SESSION['type'] == 'associate') {
                                                    echo $details['joindate'];
                                                } elseif ($_SESSION['type'] == 'admin') {
                                                    echo $details['joindate'];
                                                } else {
                                                    echo $details['joindate'];
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- footer start  -->

        <?php include_once 'footer.php'; ?>
        <!-- footer start End -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="dist/js/pages/dashboard.js"></script>
</body>

</html>