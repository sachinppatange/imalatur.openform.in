<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';
$stud_id = $_SESSION['id'];
//$stud_id=$_GET['id'];
$details = getStudentByStudId($stud_id);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo getcompany(); ?></title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">


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
                                            <th width="30%">Student ID</th>
                                            <td>
                                                <?php
                                                echo "SS96" . (30000 + $details['stud_id']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Name</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['firstname']) . '  ' . ucfirst($details['fathername']) . '  ' . ucfirst($details['surname']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Mother Name</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['mothername']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Email</th>
                                            <td>
                                                <?php
                                                echo $details['email'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Course</th>
                                            <td>
                                                <?php
                                                echo $details['course'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Fee Category</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['category']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Registration Category</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['adcategory']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Address</th>
                                            <td>
                                                <?php
                                                echo $details['address'];
                                                ?>
                                            </td>
                                        </tr>schoolname
                                        <tr>
                                            <th width="30%">School Name</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['schoolname']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Previous Std</th>
                                            <td>
                                                <?php
                                                echo $details['previousstd'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Grade</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['grade']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Board</th>
                                            <td>
                                                <?php
                                                echo strtoupper($details['board']);
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Language</th>
                                            <td>
                                                <?php
                                                echo ucfirst($details['language']);
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Amount</th>
                                            <td>
                                                <?php
                                                echo $details['amount'];
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Mobile</th>
                                            <td>
                                                <?php
                                                echo $details['whatsappno'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Aadhar Number</th>
                                            <td>
                                                <?php
                                                echo $details['aadhar'];
                                                ?>
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Present School name</th>
                                            <td>
                                                <?php
                                                echo $details['schoolname'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Previous Standard</th>
                                            <td>
                                                <?php
                                                echo $details['previousstd'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Centre</th>
                                            <td>
                                                <?php
                                                echo $details['centre'];
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Photo</th>
                                            <td>
                                                <img src="<?php echo $details['studphoto']; ?>" style="width:100px;" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Aadhar</th>
                                            <td>
                                                <img src="<?php echo $details['studaadhar']; ?>" style="width:100px;" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th width="30%">Sign</th>
                                            <td>
                                                <img src="<?php echo $details['studsign']; ?>" style="width:100px;" />
                                            </td>
                                        </tr>

                                        <tr>
                                            <th width="30%">Registration Date & Time</th>
                                            <td>
                                                <?php
                                                echo $details['createdon'];
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