<?php
session_start();
include_once "../config/config.php";
include_once '../controller/ctrlgetStudDetails.php';


if (empty($_SESSION['id'])) {
    header("location: ../index.php");
}
require_once __DIR__ . '/../vendor/autoload.php';

$studid = $_SESSION['id'];

$student = getStudentById($studid);
$examname = json_decode($student['name_of_exam']);
$boardname = json_decode($student['name_of_board']);
$passingyear = json_decode($student['passing_year']);
$obtmarks = json_decode($student['marks_obtained']);
$totmarks = json_decode($student['total_marks']);
$perc = json_decode($student['percentage']);

// print_r($student);

// ob_start(); 

$html = '<html>
<style>
th {
            font-size: 10px;
            text-align: left;
        }
        td p {
            margin-bottom: 15px;
        }
        #main {
            height: 100%;
        } 
        
        #bank th {
           vertical-align: top;
        }       
</style>
    <body>
         <h3 style="text-align: center;"><u>Student Form</u></h3>
        <section class="section" style="border:1px solid black; ">
         <table border="0" cellspacing="2" cellpadding="5" class="table table-bordered"
         style="border:1px solid #dfdfdf; width:100%; height: 100%;" bordercolor="gray">
         <tr>
             <td colspan="2">
                 <table>
                     <tr>
                         <td width="25%">
                             <img src="./../assets/img/logo.jpg" alt="" width="12%">
                         </td>
                         
                         
                         <td width="25%" style="text-align: right;">
                             <h4>GOVINDLAL KANHAIYALAL JOSHI (NIGHT) COMMERCE COLLEGE, LATUR</h4>
                         </td>
                     </tr>
                 </table>
             </td>
         </tr>
         <tr>
             <td colspan="2">
                 <table border="2" cellspacing="2" cellpadding="5" class="table table-bordered"
                     style="border:1px solid #000;  width:100%; margin:-8px;" bordercolor="">

                     <tr>   
                         <td width="50%" style="">
                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Course Name</b> : ' . ($student['course_name'] ?? '') . '</p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Acadamic Year</b> : ' . ($student['acadamic_year'] ?? '') . '</p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Medium</b> :' . ($student['Medium'] ?? '') . '</p>
                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Student Name</b>: ' . ($student['stud_name'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Gender </b>: ' . ($student['gender'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Place Of Birth </b>: ' . ($student['place_of_birth'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Marital Status </b>: ' . ($student['marital_status'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Date Of Birth </b>: ' . ($student['DOB'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Nationality </b>: ' . ($student['nationality'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Blood Group </b>: ' . ($student['blood_group'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Physically Challenged </b>: ' . ($student['physically_challenged'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Blind </b>: ' . ($student['blind'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Have Voter_id </b>: ' . ($student['have_voter_id'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Student Whatsapp </b>: ' . ($student['stud_whatsapp'] ?? '') . '
                             </p>

                              <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Payment Status</b>: ' . ($student['pstatus'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Payment Date</b>: ' . ($student['pdate'] ?? '') . '
                             </p>
                         </td>

                         <td width="50%" style="">
                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Addmission Year</b> : ' . ($student['addmission_year'] ?? '') . '</p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>SL </b>: ' . ($student['SL'] ?? '') . '</p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Enrolment No</b>: ' . ($student['enrolment_no'] ?? '') . '</p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Fathers Full Name</b>: ' . ($student['fathers_full_name'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Mothers Name</b>: ' . ($student['mothers_name'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;"> 
                             <p style="font-size:14px;padding-bottom:10px;"><b>Caste</b>: ' . ($student['caste'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;"> 
                             <p style="font-size:14px;padding-bottom:10px;"><b>Subcaste</b>: ' . ($student['subcaste'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Religion</b>: ' . ($student['religion'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Fathers Occupation</b>: ' . ($student['fathers_occupation'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:12px;padding-bottom:10px;"><b>Parents Mobile</b>: ' . ($student['parents_mobile'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Permanent Address</b>: ' . ($student['permanent_address'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Correspondence Address</b>: ' . ($student['correspondence_address'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Student Aadhar</b>: ' . ($student['stud_aadhar'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Student Email</b>: ' . ($student['stud_email'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Student ABC_id</b>: ' . ($student['ABC_id'] ?? '') . '
                             </p>

                             <hr style="margin:2px;padding:0px;color:#fff;">
                             <p style="font-size:14px;padding-bottom:10px;"><b>Paid Amount</b>: ' . ($student['pamount'] ?? '') . '
                             </p>
                         </td>
                     </tr>
                 </table>
                 <table border="2" cellspacing="2" cellpadding="5" class="table table-bordered"
                     style="border:1px solid #000;  width:100%; margin-top:12px;" bordercolor="">
                    <tr>
                        <thead>
                            <th style="font-size:14px;">Exam Name</th>
                            <th  style="font-size:14px;">Board Name</th>
                            <th  style="font-size:14px;">Passing Year</th>
                            <th  style="font-size:14px;">Marks Obtained</th>
                            <th  style="font-size:14px;">Total Marks</th>
                            <th  style="font-size:14px;">Percentage</th>
                        </thead>
                        <tbody>
                           <tr>
                                <td>' . ($examname[0] ?? '') . '</td>
                                <td>' . ($boardname[0] ?? '') . '</td>
                                <td>' . ($passingyear[0] ?? '') . '</td>
                                <td>' . ($obtmarks[0] ?? '') . '</td>
                                <td>' . ($totmarks[0] ?? '') . '</td>
                                <td>' . ($perc[0] ?? '') . '</td>
                            </tr>
                           <tr>
                                <td>' . ($examname[1] ?? '') . '</td>
                                <td>' . ($boardname[1] ?? '') . '</td>
                                <td>' . ($passingyear[1] ?? '') . '</td>
                                <td>' . ($obtmarks[1] ?? '') . '</td>
                                <td>' . ($totmarks[1] ?? '') . '</td>
                                <td>' . ($perc[1] ?? '') . '</td>
                            </tr>
                           <tr>
                                <td>' . ($examname[2] ?? '') . '</td>
                                <td>' . ($boardname[2] ?? '') . '</td>
                                <td>' . ($passingyear[2] ?? '') . '</td>
                                <td>' . ($obtmarks[2] ?? '') . '</td>
                                <td>' . ($totmarks[2] ?? '') . '</td>
                                <td>' . ($perc[2] ?? '') . '</td>
                            </tr>
                           <tr>
                                <td>' . ($examname[3] ?? '') . '</td>
                                <td>' . ($boardname[3] ?? '') . '</td>
                                <td>' . ($passingyear[3] ?? '') . '</td>
                                <td>' . ($obtmarks[3] ?? '') . '</td>
                                <td>' . ($totmarks[3] ?? '') . '</td>
                                <td>' . ($perc[3] ?? '') . '</td>
                            </tr>
                           <tr>
                                <td>' . ($examname[4] ?? '') . '</td>
                                <td>' . ($boardname[4] ?? '') . '</td>
                                <td>' . ($passingyear[4] ?? '') . '</td>
                                <td>' . ($obtmarks[4] ?? '') . '</td>
                                <td>' . ($totmarks[4] ?? '') . '</td>
                                <td>' . ($perc[4] ?? '') . '</td>
                            </tr>
                           <tr>
                                <td>' . ($examname[5] ?? '') . '</td>
                                <td>' . ($boardname[5] ?? '') . '</td>
                                <td>' . ($passingyear[5] ?? '') . '</td>
                                <td>' . ($obtmarks[5] ?? '') . '</td>
                                <td>' . ($totmarks[5] ?? '') . '</td>
                                <td>' . ($perc[5] ?? '') . '</td>
                            </tr>
                        </tbody>
                    </tr>
                 </table>
             </td>
         </tr>
     </table>
    </section>
 </body>   
</html>';
// echo $html;
// ob_start();
// $html = ob_get_clean();
// $html = '<h4> This Is test </h4>';
require_once __DIR__ . '/../vendor/autoload.php';

$mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
//$footer = 'Trinitron Indprojects';
//$mpdf->SetFooter($footer);
$mpdf->WriteHTML($html);
$mpdf->Output();