<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';
$stud_id = $_SESSION['id'];
//$stud_id=$_GET['id'];
$details = getStudentByStudId($stud_id);
// print_r ($details);
// exit;
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
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .hall-ticket {
            border: 2px solid black;
            padding: 20px;
            width: 800px;
            margin: auto;
            background: #fff;
        }
        .header {
            text-align: center;
        }
        .header h2, .header h3 {
            margin: 5px 0;
        }
        .info-table th {
            width: 40%;
        }
        .student-photo {
            text-align: right;
        }
        .student-photo img {
            width: 100px;
            height: 100px;
            border: 1px solid black;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        .footer-content {
    display: flex;
    justify-content: space-between;
    width: 100%;
}

.left {
    text-align: left;
}

.right {
    text-align: right;
}
.header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 10px 20px;
}

.logo {
    width: 80px; /* Adjust size as needed */
    height: auto;
}

.header {
    text-align: center;
    flex-grow: 1; /* Ensures the header takes the center space */
}

.left-logo {
    margin-right: 20px; /* Adds spacing between logo and header */
}

.right-logo {
    margin-left: 20px; /* Adds spacing between header and logo */
}

.print-btn-container {
    text-align: center;
    margin-top: 20px;
}

.print-btn {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
}

.print-btn:hover {
    background-color: #0056b3;
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
            <div class="hall-ticket">
            <div class="header-container">
    <img src="../assets/img/logosainik.jpeg" alt="School Logo" class="logo left-logo">
    <div class="header">
        <h3>Shri Tuljabhavani Mandir Trust</h3>
        <h4>Shri Tuljabhavani Sainiki Vidyalaya, Tuljapur</h4>
    </div>
    <img src="../assets/img/tuljabhawani.jpeg" alt="Devi Logo" class="logo right-logo">
</div>

        
        <table class="table table-bordered info-table">
            <tr>
                <th>Student ID</th>
                <td><?php echo "SS96" . (30000 + $details['stud_id']); ?></td>
                <td rowspan="4" class="student-photo" style="text-align: center; vertical-align: middle;">
    <img src="<?php echo $details['studphoto']; ?>" alt="Student Photo" style="display: block; margin: auto;">
</td>

            </tr>
            <tr>
                <th>Name</th>
                <td><?php echo ucfirst($details['firstname']) . ' ' . ucfirst($details['fathername']) . ' ' . ucfirst($details['surname']); ?></td>
            </tr>
            <tr>
                <th>Registration Category</th>
                <td><?php echo ucfirst($details['category']); ?></td>
            </tr>
            <tr>
                <th>Exam Centre</th>
                <td><?php echo $details['centre']; ?></td>
            </tr>
            <tr>
                <th>Date of Exam</th>
                <td><?php echo $details['exam_date']; ?></td>
            </tr>
            <tr>
                <th>Reporting Time</th>
                <td>11.00 Pm</td>
            </tr>
            <tr>
                <th>Exam Time</th>
                <td>11.30Am To 2.00 PM</td>
            </tr>
            <tr>
                <th>Marks</th>
                <td>100 Marks</td>
            </tr>
        </table>
        
       
        <div class="footer">
        <div class="footer-content">
            <p class="left">Shri Ghodke V.B<br>Principal - S.T.B.S.V</p>
            <p class="right">Col. Deshmukh Makrand (Retd.)<br>Commandant - S.T.B.S.V</p>
        </div>
    </div>
   
    </div>


    </div>

<button onclick="printHallTicket()" class="btn btn-primary">Print Hall Ticket</button>


            <!-- /.content -->
        </div>
        <div class="print-btn-container">
    <button id="printBtn" class="print-btn">Print Hall Ticket</button>
</div>


<script>
    document.getElementById("printBtn").addEventListener("click", function () {
        var hallTicket = document.querySelector(".hall-ticket").innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = hallTicket;
        window.print();
        location.reload(); // Refresh the page after printing
    });
</script>


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