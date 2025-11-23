<?php
session_start();
if (isset($_SESSION['type1']) != 'admin') {
  header('location: login.php');
}
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';
$details = getStudentByStudId($_GET['id']);
// print_r($details);


$SL = json_decode($details['SL']);
// $Leavingcertificate = json_decode($details['Leaving_Certificate']);
// $Previous_Marks_Memo = json_decode($details['Previous_Marks_Memo']);
// $Caste_Certificate = json_decode($details['Caste_Certificate']);
// $NonCreamy_layer = json_decode($details['NonCreamy_layer']);
// $Gap_Certificate = json_decode($details['Gap_Certificate']);
// $Aadhar_Card = json_decode($details['Aadhar_Card']);
// $Bank_PassBook = json_decode($details['Bank_PassBook']);
// $Bonafied_Certificate = json_decode($details['Bonafied_Certificate']);
// $Income_Certificate = json_decode($details['Income_Certificate']);
// $Domacile_Certificate = json_decode($details['Domacile_Certificate']);
$name_of_exam = json_decode($details['name_of_exam']);
$name_of_board = json_decode($details['name_of_board']);
$passing_year = json_decode($details['passing_year']);
$marks_obtained = json_decode($details['marks_obtained']);
$total_marks = json_decode($details['total_marks']);
$percentage = json_decode($details['percentage']);

// print_r($details);
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
              <h1>Student Details</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Student List</li>
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
                <!-- /.card-header -->
                <div class="card-body">
                  <table id="example1" class="table table-bordered table-striped">
                    <tr>
                      <th style="width:20%">Student Name</th>
                      <td>  <?php echo $details['surname'] . ' ' . $details['fathername'] . ' ' . $details['firstname']; ?></td>
                    </tr>
                    <tr>
                      <th>Mobile No.</th>
                      <td><?php echo $details['whatsappno']; ?></td>
                    </tr>
                    <tr>
                      <th>Admission Year</th>
                      <td><?php echo $details['addmission_year']; ?></td>
                    </tr>
                    <tr>
                      <th>Email Id</th>
                      <td><?php echo $details['email']; ?></td>
                    </tr>
                    <tr>
                      <th>Academic Year</th>
                      <td>
                        <?php echo $details['acadamic_year']; ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Medium</th>
                      <td><?php echo $details['Medium']; ?></td>
                    </tr>
                    <tr>
                      <th>Gender </th>
                      <td><?php echo $details['gender']; ?></td>
                    </tr>
                    <tr>
                      <th>Date of Birth</th>
                      <td><?php echo $details['dob']; ?></td>
                    </tr>
                    <tr>
                      <th>Mother's Name</th>
                      <td><?php echo $details['mothers_name']; ?></td>
                    </tr>
                    <tr>
                      <th>Gender</th>
                      <td><?php echo $details['gender']; ?></td>
                    </tr>
                    <tr>
                      <th>Alternate Mobile</th>
                      <td><?php echo $details['alternateno']; ?></td>
                    </tr>
                   
                    <tr>
                      <th>Adhar No </th>
                      <td><?php echo $details['aadhar']; ?></td>
                    </tr>
                    <tr>
                      <th>Category</th>
                      <td><?php echo $details['category']; ?></td>
                    </tr>
                    <tr>
                      <th>Address</th>
                      <td><?php echo $details['address']; ?></td>
                    </tr>
                    <tr>
                      <th>School Name</th>
                      <td><?php echo $details['schoolname']; ?></td>
                    </tr>
                    <tr>
                      <th>Previous Standard</th>
                      <td><?php echo $details['previousstd']; ?></td>
                    </tr>
                    <tr>
                      <th>Grade</th>
                      <td><?php echo $details['grade']; ?></td>
                    </tr>
                    <tr>
                      <th>Board </th>
                      <td><?php echo $details['board']; ?></td>
                    </tr>
                    <tr>
                      <th> Language</th>
                      <td><?php echo $details['language']; ?></td>
                    </tr>
                    <tr>
                      <th>Center</th>
                      <td><?php echo $details['centre']; ?></td>
                    </tr>

                    <tr>
                      <th>Student Photo</th>
                      <td><img class="mprofile" src="../uploads/<?php echo $details['studphoto']; ?>" alt=""
                          style="width: 100px; height: 100px;margin-right: 30px;"></td>
                    </tr>
                    <tr>
                      <th>Student Adhar</th>
                     
                      <td><img class="mprofile" src="../uploads/<?php echo $details['studaadhar']; ?>" alt=""
                          style="width: 100px; height: 100px;margin-right: 30px;"></td>
                    </tr>
                    <tr>
                      <th>Student Signature</th>
                      <td><img class="mprofile" src="../uploads/<?php echo $details['studsign']; ?>" alt=""
                          style="width: 100px; height: 100px;margin-right: 30px;"></td>
                    </tr>
                    <tr>
                      <th>Amount  </th>
                      <td><?php echo $details['amount']; ?></td>
                    </tr>
                    <tr>
                      <th>Status</th>
                      <td><?php echo $details['status']; ?></td>
                    </tr>
                    
                    <!-- <tr>
                      <th>Leaving Certificate</th>
                      <td>
                        <?php
                        if (is_array($Leavingcertificate)) {
                          foreach ($Leavingcertificate as $lc) {
                            // echo ' ';
                            echo $lc;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Previous Marks Memo</th>
                      <td>
                        <?php
                        if (is_array($Previous_Marks_Memo)) {
                          foreach ($Previous_Marks_Memo as $marksheet) {
                            echo $marksheet;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Caste Certificate</th>
                      <td>
                        <?php
                        if (is_array($Caste_Certificate)) {
                          foreach ($Caste_Certificate as $caste) {
                            echo $caste;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Non-Creamy layer</th>
                      <td>
                        <?php
                        if (is_array($NonCreamy_layer)) {
                          foreach ($NonCreamy_layer as $non) {
                            echo $non;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Gap Certificate</th>
                      <td>
                        <?php
                        if (is_array($Gap_Certificate)) {
                          foreach ($Gap_Certificate as $gap) {
                            echo $gap;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Aadhar Card</th>
                      <td>
                        <?php
                        if (is_array($Aadhar_Card)) {
                          foreach ($Aadhar_Card as $aadhar) {
                            echo $aadhar;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Bank Pass-Book</th>
                      <td>
                        <?php
                        if (is_array($Bank_PassBook)) {
                          foreach ($Bank_PassBook as $passbook) {
                            echo $passbook;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Bonafied Certificate</th>
                      <td>
                        <?php
                        if (is_array($Bonafied_Certificate)) {
                          foreach ($Bonafied_Certificate as $bonafied) {
                            echo $bonafied;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Income Certificate</th>
                      <td>
                        <?php
                        if (is_array($Income_Certificate)) {
                          foreach ($Income_Certificate as $income) {
                            echo $income;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <th>Domacile Certificate</th>
                      <td>
                        <?php
                        if (is_array($Domacile_Certificate)) {
                          foreach ($Domacile_Certificate as $doma) {
                            echo $doma;
                            echo ' ,';
                          }
                        }
                        ?>
                      </td>
                    </tr> -->
                  </table>
                  <!-- <table class="table table-bordered">
                    <thead>
                      <th>Name Of Exam</th>
                      <th>Name Of Board/Univercity</th>
                      <th>Month & Year Of Passing</th>
                      <th>Marks Obtained</th>
                      <th>Total Marks</th>
                      <th>Percentage</th>
                      <th>Marksheet Photo</th>
                    </thead>
                    <tbody>
                      <tr>
                        <th><?php if (isset($name_of_exam[0]))
                          echo $name_of_exam[0]; ?></th>

                        <td><?php if (isset($name_of_board[0]))
                          echo $name_of_board[0]; ?></td>

                        <td><?php if (isset($passing_year[0]))
                          echo $passing_year[0]; ?></td>

                        <td><?php if (isset($marks_obtained[0]))
                          echo $marks_obtained[0]; ?></td>

                        <td><?php if (isset($total_marks[0]))
                          echo $total_marks[0]; ?></td>

                        <td><?php if (isset($percentage[0]))
                          echo $percentage[0]; ?></td>

                        <td>
                          <img class="mprofile" src="../images/sscmarks/<?php echo $details['sscmarks']; ?>" width="50%"
                            alt="">

                        </td>
                      </tr>
                      <tr>
                        <th><?php if (isset($name_of_exam[1]))
                          echo $name_of_exam[1]; ?></th>

                        <td><?php if (isset($name_of_board[1]))
                          echo $name_of_board[1]; ?></td>

                        <td><?php if (isset($passing_year[1]))
                          echo $passing_year[1]; ?></td>

                        <td><?php if (isset($marks_obtained[1]))
                          echo $marks_obtained[1]; ?></td>

                        <td><?php if (isset($total_marks[1]))
                          echo $total_marks[1]; ?></td>

                        <td><?php if (isset($percentage[1]))
                          echo $percentage[1]; ?></td>

                        <td><img class="mprofile" src="../images/hscmarks/<?php echo $details['hscmarks']; ?>"
                            width="50%" alt="">
                        </td>

                      </tr>
                      <tr>
                        <th><?php if (isset($name_of_exam[2]))
                          echo $name_of_exam[2]; ?></th>

                        <td><?php if (isset($name_of_board[2]))
                          echo $name_of_board[2]; ?></td>

                        <td><?php if (isset($passing_year[2]))
                          echo $passing_year[2]; ?></td>

                        <td><?php if (isset($marks_obtained[2]))
                          echo $marks_obtained[2]; ?></td>

                        <td><?php if (isset($total_marks[2]))
                          echo $total_marks[2]; ?></td>

                        <td><?php if (isset($percentage[2]))
                          echo $percentage[2]; ?></td>

                        <td><img class="mprofile" src="../images/fymarks/<?php echo $details['fymarks']; ?>" width="50%"
                            alt="">
                        </td>
                      </tr>
                      <tr>
                        <th><?php if (isset($name_of_exam[3]))
                          echo $name_of_exam[3]; ?></th>

                        <td><?php if (isset($name_of_board[3]))
                          echo $name_of_board[3]; ?></td>

                        <td><?php if (isset($passing_year[3]))
                          echo $passing_year[3]; ?></td>

                        <td><?php if (isset($marks_obtained[3]))
                          echo $marks_obtained[3]; ?></td>

                        <td><?php if (isset($total_marks[3]))
                          echo $total_marks[3]; ?></td>

                        <td><?php if (isset($percentage[3]))
                          echo $percentage[3]; ?></td>

                        <td><img class="mprofile" src="../images/symarks/<?php echo $details['symarks']; ?>" width="50%"
                            alt="">
                        </td>

                      </tr>
                      <tr>
                        <th><?php if (isset($name_of_exam[4]))
                          echo $name_of_exam[4]; ?></th>

                        <td><?php if (isset($name_of_board[4]))
                          echo $name_of_board[4]; ?></td>

                        <td><?php if (isset($passing_year[4]))
                          echo $passing_year[4]; ?></td>

                        <td><?php if (isset($marks_obtained[4]))
                          echo $marks_obtained[4]; ?></td>

                        <td><?php if (isset($total_marks[4]))
                          echo $total_marks[4]; ?></td>

                        <td><?php if (isset($percentage[4]))
                          echo $percentage[4]; ?></td>

                        <td><img class="mprofile" src="../images/tymarks/<?php echo $details['tymarks']; ?>" width="50%"
                            alt="">
                        </td>

                      </tr>
                      <tr>
                        <th><?php if (isset($name_of_exam[5]))
                          echo $name_of_exam[5]; ?></th>

                        <td><?php if (isset($name_of_board[5]))
                          echo $name_of_board[5]; ?></td>

                        <td><?php if (isset($passing_year[5]))
                          echo $passing_year[5]; ?></td>

                        <td><?php if (isset($marks_obtained[5]))
                          echo $marks_obtained[5]; ?></td>

                        <td><?php if (isset($total_marks[5]))
                          echo $total_marks[5]; ?></td>

                        <td><?php if (isset($percentage[5]))
                          echo $percentage[5]; ?></td>

                        <td>
                          <img class="mprofile" src="../images/pgfymarks/<?php echo $details['pgfymarks']; ?>"
                            width="50%" alt="">
                        </td>

                      </tr>
                    </tbody>
                  </table> -->
                </div>
                <!-- /.card-body -->
              </div>
              <!-- /.card -->
              <a href="studentlist.php" class="btn btn-primary float-right">Back to List</a>
              <a href="addStudent.php?id=<?php echo $details['stud_id']; ?>"
                class="btn btn-primary float-right mr-2">Edit</a>

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

  <!-- AdminLTE App -->
  <script src="dist/js/adminlte.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- Page specific script -->

</body>

</html>