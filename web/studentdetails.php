<?php
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';
$stud_id = $_GET['stud_id'];
$student = getStudentById($stud_id);
$SL = json_decode($student['SL']);
$Leavingcertificate = json_decode($student['Leaving_Certificate']);
$Previous_Marks_Memo = json_decode($student['Previous_Marks_Memo']);
$Caste_Certificate = json_decode($student['Caste_Certificate']);
$NonCreamy_layer = json_decode($student['NonCreamy_layer']);
$Gap_Certificate = json_decode($student['Gap_Certificate']);
$Aadhar_Card = json_decode($student['Aadhar_Card']);
$Bank_PassBook = json_decode($student['Bank_PassBook']);
$Bonafied_Certificate = json_decode($student['Bonafied_Certificate']);
$Income_Certificate = json_decode($student['Income_Certificate']);
$Domacile_Certificate = json_decode($student['Domacile_Certificate']);
$name_of_exam = json_decode($student['name_of_exam']);
$name_of_board = json_decode($student['name_of_board']);
$passing_year = json_decode($student['passing_year']);
$marks_obtained = json_decode($student['marks_obtained']);
$total_marks = json_decode($student['total_marks']);
$percentage = json_decode($student['percentage']);
// print_r($student);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Govindlal College</title>
    <link rel="icon" href="../assets/img/logo.jpg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</head>

<body>
    <header>
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex justify-content-end mt-3">
                    <a href="../controller/ctrlStudLogout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </header>
    <div class="container mt-3">
        <?php if ($_GET['chk'] == 1) { ?>
            <h4 style="color:green; padding:10px; border:2px solid gray;" align="center">Thanks You! Your Form
                has been <br>
                Submited Successfully </h4>
        <?php } ?><br>

        <div class="row">
            <div class="col-md-3"></div>
            <div class="col-md-6">

                <table class="table table-bordered">
                    <tr>
                        <th>Form Id</th>
                        <td><?php echo $student['stud_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Cource Name</th>
                        <td><?php echo $student['course_name'] ?></td>
                    </tr>
                    <!-- <tr>
                        <th>Cource Name</th>
                        <td><?php echo $student['course_name'] ?></td>
                    </tr> -->
                    <tr>
                        <th>Selected Year</th>
                        <td><?php echo $student['addmission_year'] ?></td>
                    </tr>
                    <tr>
                        <th>Academic Year</th>
                        <td><?php echo $student['acadamic_year'] ?></td>
                    </tr>
                    <tr>
                        <th>SL</th>
                        <td> <?php echo $student['SL']; ?></td>
                    </tr>
                    <tr>
                        <th>Medium</th>
                        <td><?php echo $student['Medium'] ?></td>
                    </tr>
                    <tr>
                        <th>Enrolment No</th>
                        <td><?php echo $student['enrolment_no'] ?></td>
                    </tr>
                    <tr>
                        <th>Student Name</th>
                        <td><?php echo $student['stud_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Father's Full Name</th>
                        <td><?php echo $student['fathers_full_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Mother's Name</th>
                        <td><?php echo $student['mothers_name'] ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo $student['gender'] ?></td>
                    </tr>
                    <tr>
                        <th>Place of Birth</th>
                        <td><?php echo $student['place_of_birth'] ?></td>
                    </tr>
                    <tr>
                        <th>Photo</th>
                        <td><img src="../images/<?php echo $student['photo']; ?>" width="50%" alt=""></td>
                    </tr>
                    <tr>
                        <th>Marital Status</th>
                        <td><?php echo $student['marital_status'] ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?php echo $student['DOB'] ?></td>
                    </tr>
                    <tr>
                        <th>Caste</th>
                        <td><?php echo $student['caste'] ?></td>
                    </tr>
                    <tr>
                        <th>Subcaste</th>
                        <td><?php echo $student['subcaste'] ?></td>
                    </tr>
                    <tr>
                        <th>Nationality</th>
                        <td><?php echo $student['nationality'] ?></td>
                    </tr>
                    <tr>
                        <th>Religion</th>
                        <td><?php echo $student['religion'] ?></td>
                    </tr>
                    <tr>
                        <th>Blood Group</th>
                        <td><?php echo $student['blood_group'] ?></td>
                    </tr>
                    <tr>
                        <th>Physically Challenged</th>
                        <td><?php echo $student['physically_challenged'] ?></td>
                    </tr>
                    <tr>
                        <th>Blind</th>
                        <td><?php echo $student['blind'] ?></td>
                    </tr>
                    <tr>
                        <th>Father's Occupation</th>
                        <td><?php echo $student['fathers_occupation'] ?></td>
                    </tr>
                    <tr>
                        <th>Parent's Contact No</th>
                        <td><?php echo $student['parents_mobile'] ?></td>
                    </tr>
                    <tr>
                        <th>Permanent Address</th>
                        <td><?php echo $student['permanent_address'] ?></td>
                    </tr>
                    <tr>
                        <th>Address for Correspondence</th>
                        <td><?php echo $student['correspondence_address'] ?></td>
                    </tr>
                    <tr>
                        <th>Do you have a Voter ID?</th>
                        <td><?php echo $student['have_voter_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Student's WhatsApp Number</th>
                        <td><?php echo $student['stud_whatsapp'] ?></td>
                    </tr>
                    <tr>
                        <th>Student's Aadhar Number</th>
                        <td><?php echo $student['stud_aadhar'] ?></td>
                    </tr>
                    <tr>
                        <th>Student's E-mail ID</th>
                        <td><?php echo $student['stud_email'] ?></td>
                    </tr>
                    <tr>
                        <th>ABC ID</th>
                        <td><?php echo $student['ABC_id'] ?></td>
                    </tr>
                    <tr>
                        <th>Leaving Certificate</th>
                        <td>
                            <?php
                            if (is_array($Leavingcertificate)) {
                                foreach ($Leavingcertificate as $lc) {
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
                    </tr>
                </table>

                <table class="table table-bordered">
                    <thead>
                        <th>Name Of Exam</th>
                        <th>Name Of Board/Univercity</th>
                        <th>Month & Year Of Passing</th>
                        <th>Marks Obtained</th>
                        <th>Total Marks</th>
                        <th>Percentage</th>
                        <th>Marksheets</th>
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

                            <td><img class="mprofile" src="../images/sscmarks/<?php echo $student['sscmarks']; ?>"
                                   width="100%" alt="" style="margin-right: 30px;"></td>
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

                            <td><img class="mprofile" src="../images/hscmarks/<?php echo $student['hscmarks']; ?>"
                                   width="100%" alt="" style="margin-right: 30px;"></td>
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

                            <td><img class="mprofile" src="../images/fymarks/<?php echo $student['fymarks']; ?>"
                                   width="100%" alt="" style="margin-right: 30px;"></td>
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

                            <td><img class="mprofile" src="../images/symarks/<?php echo $student['symarks']; ?>"
                                   width="100%" alt="" style="margin-right: 30px;"></td>
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

                            <td><img class="mprofile" src="../images/tymarks/<?php echo $student['tymarks']; ?>"
                          width="100%"  alt=""  style="margin-right: 30px;"></td>
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

                            <td><img class="mprofile" src="../images/pgfymarks/<?php echo $student['pgfymarks']; ?>" width="100%"  alt="" style="margin-right: 30px;"></td>
                        </tr>
                    </tbody>
                </table>


                <button class="btn btn-primary" onclick="printDocument()">Print</button>
            </div>
            <div class="col-md-3"></div>
        </div>
    </div>
    <script>
        function printDocument() {
            window.print();
        }
    </script>
</body>

</html>