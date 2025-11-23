<?php
session_start();
include_once '../config/config.php';
include_once '../controller/ctrlBulkProperty.php';
// $property = getAllProperty();
$property = getAllPropertyandAsso();
// print_r($property);
// print_r($all);
// print_r($_SESSION);
// $sessionid = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AdminLTE 3 | DataTables</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <style>
        .custom-button {
            padding: 8px 14px;
            /* Adjust padding as needed */
            font-size: 12px;
            width: 100px;
            border: none
        }
    </style>
    <script>
        function setMemActive(propid) {
            var ans = confirm("Are you sure you want to Accept this Property?");
            if (ans) {
                // alert('asd');
                location.href = "../controller/ctrlAddProperty.php?id=" + propid;
            }
        }

    </script>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <?php include_once 'navbar.php'; ?>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <?php include_once 'sidebar.php'; ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Properties</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Properties</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Properties List</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Project Name</th>
                                                <th>Developer Name</th>
                                                <th>Contact Associate</th>
                                                <th>Mobile</th>
                                                <th> Status</th>
                                                <?php if ($_SESSION['type'] === 'superadmin') {
                                                    ?>
                                                    <th>Approval Status</th>
                                                    <?php
                                                }
                                                ?>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cnt = 1;
                                            // print_r($property);
                                            foreach ($property as $val) {
                                                if ($_SESSION['type'] === 'associate' && $_SESSION['id'] == $val['id']) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $cnt; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $val['projectename']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $val['developername']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $val['associatename']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $val['mobile']; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            // echo $val['current_status'];
                                                            if ($_SESSION['type'] == 'associate') {
                                                                echo $val['current_status'];
                                                            } elseif ($_SESSION['type'] === 'superadmin') {
                                                                ?>
                                                                <form id="statusForm" action="../controller/ctrlUpdateCurrecntStatus.php"
                                                                    method="post">
                                                                    <div>
                                                                        <input type="hidden" name="id"
                                                                            value="<?php echo $val['propertyid']; ?>">

                                                                        <button type="submit" class=""
                                                                            name="currentstatus" value="Accepted" onclick="return confirm('Are you sure you want to  Accept this Property?')"
                                                                            style="background-color:green; cursor:pointer;border:none;"><i
                                                                                class="fas fa-thumbs-up"></i></button>

                                                                        <button type="submit"
                                                                            class="mt-2"
                                                                            name="currentstatus" value="Rejected" onclick="return confirm('Are you sure you want to  Reject this Property?')"
                                                                            style="background-color:red; cursor:pointer;border:none;"><i
                                                                                class="fas fa-thumbs-down"></i></button>
                                                                    </div>
                                                                </form>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php if ($_SESSION['type'] === 'superadmin') {
                                                            ?>
                                                            <td>
                                                               <?php echo $val['current_status']; ?>
                                                            </td>
                                                                <?php
                                                        }
                                                        ?>

                                                        <td><a href="viewProperty.php?id=<?php
                                                        echo $val['propertyid']; ?>"><i class="fas fa-file-alt"
                                                                    style="color:blue"></i></a>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;

                                                            <a href="addProperty.php?id=<?php
                                                            echo $val['propertyid']; ?>"><i class="fas fa-edit"
                                                                    style="color:green"></i></a>
                                                            &nbsp;&nbsp;|&nbsp;&nbsp;

                                                            <a href="#">
                                                                <i class="fas fa-trash" style="color:red" onclick="delProperty(<?php
                                                                echo $val['propertyid']; ?>)">
                                                                </i>
                                                            </a>

                                                        </td>

                                                    </tr>
                                                    <?php
                                                    $cnt++;
                                                } else {

                                                    if ($_SESSION['type'] === 'superadmin') {
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <?php echo $cnt; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $val['projectename']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $val['developername']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $val['associatename']; ?>
                                                            </td>
                                                            <td>
                                                                <?php echo $val['mobile']; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                // echo $val['current_status'];
                                                                if ($_SESSION['type'] == 'associate') {
                                                                    echo $val['current_status'];
                                                                } elseif ($_SESSION['type'] === 'superadmin') {
                                                                    ?>
                                                                    <form id="statusForm" action="../controller/ctrlUpdateCurrecntStatus.php"
                                                                        method="post">
                                                                        <div>
                                                                            <input type="hidden" name="id"
                                                                                value="<?php echo $val['propertyid']; ?>">

                                                                            <button type="submit" class=""
                                                                                name="currentstatus" value="Accepted" onclick="return confirm('Are you sure you want to  Accept this Property?')"
                                                                                style="background-color:green; cursor:pointer;border:none;"><i
                                                                                class="fas fa-thumbs-up"></i></button>

                                                                            <button type="submit"
                                                                                class="mt-2"
                                                                                name="currentstatus" value="Rejected" onclick="return confirm('Are you sure you want to  Reject this Property?')"
                                                                                style="background-color:red; cursor:pointer;border:none;"><i
                                                                                    class="fas fa-thumbs-down"></i></button>
                                                                        </div>
                                                                    </form>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($_SESSION['type'] === 'superadmin') {
                                                                    echo $val['current_status'];
                                                                }
                                                                ?>
                                                            </td>

                                                            <td><a href="viewProperty.php?id=<?php
                                                            echo $val['propertyid']; ?>"><i class="fas fa-file-alt"
                                                                        style="color:blue"></i></a>
                                                                &nbsp;&nbsp;|&nbsp;&nbsp;

                                                                <a href="addProperty.php?id=<?php
                                                                echo $val['propertyid']; ?>"><i class="fas fa-edit"
                                                                        style="color:green"></i></a>
                                                                &nbsp;&nbsp;|&nbsp;&nbsp;

                                                                <a href="#">
                                                                    <i class="fas fa-trash" style="color:red" onclick="delProperty(<?php
                                                                    echo $val['propertyid']; ?>)">
                                                                    </i>
                                                                </a>

                                                            </td>

                                                        </tr>
                                                        <?php
                                                        $cnt++;
                                                    }
                                                }
                                            }
                                            ?>
                                        </tbody>
                                        <!-- <tfoot>
                                            <th>Sr.No.</th>
                                            <th>Admin Name</th>
                                            <th>Username</th>
                                            <th>Password</th>
                                            <th>Mobile</th>
                                            <th>Action</th>
                                        </tfoot> -->
                                    </table>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <!-- footer Start -->
        <?php include_once 'footer.php'; ?>
        <!-- footer End -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function () {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": true,
                "lengthMenu": [10, 25, 50, 100],
                "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>

    <script>
        function delProperty(id) {
            var ans = confirm("Are you sure to Delete This Property?");
            if (ans) {
                location.href = "../controller/ctrlDelProperty.php?id=" + id;
            }
        }
    </script>
    <script>
    document.getElementById('statusForm').addEventListener('submit', function(event) {
        var currentStatus = document.querySelector('button[name="currentstatus"]:checked').value;
        if (currentStatus === 'Accepted') {
            event.preventDefault(); // Prevent the form from submitting
            alert('Record accepted'); // Show a popup message
            // You can also use a modal instead of an alert for a more sophisticated message
        }
    });
</script>
</body>

</html>