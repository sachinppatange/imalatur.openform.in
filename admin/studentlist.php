<?php
session_start();
if (isset($_SESSION['type1']) != 'admin') {
    header('location: login.php');
}
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';
$student = getAllStudent();
// print_r($student);
// exit;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars(getcompany(), ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">


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
                            <h1>Student List</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active">Student list</li>
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
                                    <!-- <h3 class="card-title">DataTable with default features</h3> -->
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr.No.</th>
                                                <th>Student ID</th>
                                                <th>Mobile No.</th>
                                                <th>Student Name</th>
                                                <th>Admission Category</th>
                                                <th>Fathers Name</th>
                                                <th>Center</th>
                                                <th>Email Id</th>
                                                <th>Course Name</th>
                                                <!-- <th>Admission Year</th>
                                                <th>Academic Year</th> -->
                                                <th>Medium</th>
                                                <th>Gender</th>
                                                <th>DOB</th>

                                                <th>Mobile No</th>
                                                <th>Alternate Mobile</th>
                                                <th>Adhar No</th>
                                                <th>Category</th>
                                                <th>Address</th>
                                                <th>School Name</th>
                                                <th>Previous Standard</th>
                                                <th>Grade</th>
                                                <th>Board</th>
                                                <th>Language</th>

                                                <th>Student Photo</th>
                                                <th>Student Adhar</th>
                                                <th>Student Signature</th>
                                                <th>Amount</th>
                                                <th>Status</th>
												<th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $cnt = 1;
                                            foreach ($student as $val) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo $cnt; ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        echo "SS96" . (30000 + $val['stud_id']);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['whatsappno']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['surname'] . ' ' . $val['firstname'] . ' ' . $val['fathername']; ?>

                                                    </td>
                                                    <td>
                                                        <?php echo $val['adcategory']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['fathername'] . ' ' . $val['surname']; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $val['centre']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['email']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['course']; ?>
                                                    </td>
                                                    <!-- <td>
                                                        <?php echo $val['addmission_year']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['acadamic_year']; ?>
                                                    </td> -->
                                                    <td>
                                                        <?php echo $val['Medium']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['gender']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['dob']; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $val['whatsappno']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['alternateno']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['aadhar']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['category']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['address']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['schoolname']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['previousstd']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['grade']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['board']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['language']; ?>
                                                    </td>

                                                    <td>
                                                        <?php echo $val['studphoto']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['studaadhar']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['studsign']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['amount']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $val['status']; ?>
                                                    </td>
													<td>
                                                        <?php echo $val['createdon']; ?>
                                                    </td>

                                                    <td><a href="viewStudent.php?id=<?php echo $val['stud_id']; ?>"><i
                                                                class="fas fa-file-alt" style="color:blue"></i></a>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;

                                                        <a href="addStudent.php?id=<?php echo $val['stud_id']; ?>"><i
                                                                class="fas fa-edit" style="color:green"></i></a>
                                                        &nbsp;&nbsp;|&nbsp;&nbsp;

                                                        <a href="#"><i class="fas fa-trash" style="color:red"
                                                                onclick="delstudent(<?php echo $val['stud_id']; ?>)"></i></a>
                                                    </td>

                                                </tr>
                                                <?php
                                                $cnt++;
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
        function delstudent(id) {
            var ans = confirm("Are you sure to Delete This Student Record?");
            if (ans) {
                location.href = "../controller/ctrlDelStudent.php?id=" + id;
            }
        }
    </script>
</body>

</html>