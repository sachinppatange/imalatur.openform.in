<?php
$mobile = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mobile'])) {
    $mobile = htmlspecialchars($_POST['mobile']);

    header("location:../hallticket/$mobile.pdf");

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sainiki Vidyalaya, Tuljapur</title>
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

    .header-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        padding: 10px 20px;
    }

    .logo {
        width: 80px;
        height: auto;
    }

    .header {
        text-align: center;
        flex-grow: 1;
    }

    .left-logo {
        margin-right: 20px;
    }

    .right-logo {
        margin-left: 20px;
    }

    .mobile-form-container {
        max-width: 400px;
        margin: 30px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
    }

    .mobile-form-container input[type="text"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .mobile-form-container button {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
    }

    .mobile-form-container button:hover {
        background-color: #0056b3;
    }
    .hall-ticket {
    border: 2px solid black;
    padding: 20px;
    width: 100%;
    max-width: 800px;
    margin: auto;
    background: #fff;
    box-sizing: border-box;
}
.logo {
    max-width: 70px;
    height: auto;
}
@media (max-width: 600px) {
    .header-container {
        flex-direction: column;
        text-align: center;
    }

    .left-logo, .right-logo {
        margin: 10px 0;
    }

    .hall-ticket {
        padding: 10px;
    }

    .mobile-form-container {
        padding: 15px;
        width: 100%;
        margin: 20px 0;
    }

    .mobile-form-container input,
    .mobile-form-container button {
        font-size: 16px;
    }
}
body {
    padding: 10px;
}

</style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__shake" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div>

       


    
 


<div class="content-wrapper" style="background-color:#fff; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div class="hall-ticket">
        <div class="header-container" style="display: flex; align-items: center; justify-content: space-between; padding: 20px;">
            <img src="../assets/img/logosainik.jpeg" alt="School Logo" class="logo left-logo" style="height: 80px;">
            <div class="header" style="text-align: center;">
                <h3 style="margin: 0;">Shri Tuljabhavani Mandir Trust</h3>
                <h4 style="margin: 5px 0;">Shri Tuljabhavani Sainiki Vidyalaya, Tuljapur</h4>
            </div>
            <img src="../assets/img/tuljabhawani.jpeg" alt="Devi Logo" class="logo right-logo" style="height: 80px;">
        </div>

        <div class="mobile-form-container" style="max-width: 400px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
            <form action="" method="post">
                <label for="mobile" style="font-weight: bold; display: block; margin-bottom: 10px;">Enter Mobile Number:</label>
                <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter 10-digit mobile no." pattern="[0-9]{10}" required 
                    style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">

                <button type="submit" class="btn btn-primary"
                    style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px;">
                    Submit
                </button>
            </form>
<?php if (!empty($mobile)) : ?>
                <div style="margin-top: 20px; font-weight: bold; color: green;">
                    You entered Mobile Number: <?php echo $mobile; ?>
                </div>
            <?php endif; ?>
           
        </div>
    </div>
    
</div>



               

 

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