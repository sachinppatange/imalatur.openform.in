<?php

session_start();
include_once '../config/config.php';
include_once '../controller/ctrlgetStudDetails.php';

$details = getStudentById($_POST['studid']);
// print_r($_POST);
$mb = getStudentMobileById($_SESSION['id']);
// print_r($mb);
// print_r($_SESSION);
// echo $mb['stud_whatsapp'];



if (isset($_POST['submit'])) {
    $studid = $_POST['studid'];
 
    $course =$_POST['courseAdmission']; 
    $admisonyear =$_POST['admissionyear']; 
    $academicyear =$_POST['yearSelect']; 
    $medium =$_POST['medium'];
    $firstname =$_POST['student_name'];  
    $surname =$_POST['surname']; 
    $fathername =$_POST['father_name']; 
    $mothername =$_POST['mother_name']; 
    $gender =$_POST['gender']; 
    $email =$_POST['email']; 
    $mobile =$_POST['whatsapp_number']; 
    $alternateno =$_POST['alternate_number']; 
    $adhar =$_POST['aadhar_number']; 
    $dob =$_POST['date_of_birth']; 
    $category =$_POST['category']; 
    $adress =$_POST['address']; 
    $schoolname =$_POST['schoolname']; 
    $previousstd =$_POST['previousstd']; 
    $grade =$_POST['grade']; 
    $board =$_POST['board']; 
    $language =$_POST['language']; 
    $center =$_POST['center']; 
     



    $targetDir = "../uploads/";

    // Handle student photo upload
    $studphoto = $_FILES['studphoto']['name'];
    $extstudphoto = pathinfo($studphoto, PATHINFO_EXTENSION);
    $photoname="photo_".$whatsapp.".".$extstudphoto;
    $targetFilePathPhoto = $targetDir .$photoname;
    move_uploaded_file($_FILES['studphoto']['tmp_name'], $targetFilePathPhoto);
    
    // Handle student Aadhar upload
    $adharphoto = $_FILES['studaadhar']['name'];
    $extstudaadhar = pathinfo($adharphoto, PATHINFO_EXTENSION);
    $aadharname="aadhar_".$whatsapp.".".$extstudaadhar;
    $targetFilePathAadhar = $targetDir .$aadharname;
    move_uploaded_file($_FILES['studaadhar']['tmp_name'], $targetFilePathAadhar);
    
    // Handle student signature upload
    $studsign = $_FILES['studsign']['name'];
    $extstudsign = pathinfo($studsign, PATHINFO_EXTENSION);
    $signname="sign_".$whatsapp.".".$extstudsign;
    $targetFilePathSign = $targetDir . $signname;
    move_uploaded_file($_FILES['studsign']['tmp_name'], $targetFilePathSign);

  
    // $targetDir = "../uploads/";
    
    // // Ensure the upload directory exists
    // if (!file_exists($targetDir)) {
    //     mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
    // }
    
    // // PHOTO Upload
    // $studphoto = $_FILES['photo']['name'];
    // $extstudphoto = pathinfo($studphoto, PATHINFO_EXTENSION);
    // $photoName = "photo_" . $whatsapp . "." . $extstudphoto;
    // $targetFilePathPhoto = $targetDir . $photoName;
    
    // if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePathPhoto)) {
    //     echo "Photo uploaded successfully.<br>";
    // } else {
    //     echo "Error uploading photo.<br>";
    // }
    
    // // ADHAR Upload
    // $studadhar = $_FILES['adharphoto']['name'];
    // $extstudadhar = pathinfo($studadhar, PATHINFO_EXTENSION);
    // $adharName = "adhar_" . $whatsapp . "." . $extstudadhar;
    // $targetFilePathAdhar = $targetDir . $adharName;
    
    // if (move_uploaded_file($_FILES['adharphoto']['tmp_name'], $targetFilePathAdhar)) {
    //     echo "Adhar photo uploaded successfully.<br>";
    // } else {
    //     echo "Error uploading Adhar photo.<br>";
    // }
    
    // // SIGNATURE Upload
    // $studsign = $_FILES['signature']['name'];
    // $extstudsign = pathinfo($studsign, PATHINFO_EXTENSION);
    // $signName = "sign_" . $whatsapp . "." . $extstudsign;
    // $targetFilePathSign = $targetDir . $signName;
    
    // if (move_uploaded_file($_FILES['signature']['tmp_name'], $targetFilePathSign)) {
    //     echo "Signature uploaded successfully.<br>";
    // } else {
    //     echo "Error uploading signature.<br>";
    // }











    // $studphoto = $_FILES['photo']['name'];
    // $studahar =$_FILES['adharphoto']['name'];
    // $studsign =$_FILES['signature']['name'];  
    // // $amount =$_POST['amount']; 
    $status =$_POST['status']; 
    $createdby = $_SESSION['id'];
    $createdon = date('Y-m-d H:i:s');


    // if (!empty($photo)) {
    //     $ext = pathinfo($photo, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $photo = "WP" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['photo']['tmp_name'], "../images/" . $photo);
    // } else {
    //     $photo = $details['photo'];
    // }

    // if (!empty($sscmarks)) {
    //     $ext = pathinfo($sscmarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $sscmarks = "SSC" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['sscmarks']['tmp_name'], "../images/sscmarks/" . $sscmarks);
    // } else {
    //     $sscmarks = $details['sscmarks'];
    // }

    // if (!empty($hscmarks)) {
    //     $ext = pathinfo($hscmarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $hscmarks = "HSC" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['hscmarks']['tmp_name'], "../images/hscmarks/" . $hscmarks);
    // } else {
    //     $hscmarks = $details['hscmarks'];
    // }

    // if (!empty($fymarks)) {
    //     $ext = pathinfo($fymarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $fymarks = "FY" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['fymarks']['tmp_name'], "../images/fymarks/" . $fymarks);
    // } else {
    //     $fymarks = $details['fymarks'];
    // }

    // if (!empty($symarks)) {
    //     $ext = pathinfo($symarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $symarks = "SY" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['symarks']['tmp_name'], "../images/symarks/" . $symarks);
    // } else {
    //     $symarks = $details['symarks'];
    // }

    // if (!empty($tymarks)) {
    //     $ext = pathinfo($tymarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $tymarks = "TY" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['tymarks']['tmp_name'], "../images/tymarks/" . $tymarks);
    // } else {
    //     $tymarks = $details['tymarks'];
    // }

    // if (!empty($pgfymarks)) {
    //     $ext = pathinfo($pgfymarks, PATHINFO_EXTENSION);
    //     $curr = date('YmdHis');
    //     $pgfymarks = "PGFY" . $curr . "." . $ext;
    //     move_uploaded_file($_FILES['pgfymarks']['tmp_name'], "../images/pgfymarks/" . $pgfymarks);
    // } else {
    //     $pgfymarks = $details['pgfymarks'];
    // }

    
        $query = "UPDATE `student` SET `Medium`='$medium',`surname`='$surname',`firstname`='$firstname',`fathername`='$fathername',`mothername`='$mothername',`gender`='$gender',`email`='$email',`whatsappno`='$mobile',`alternateno`='$alternateno',`aadhar`='$adhar',`course`='$course',`dob`='$dob',`category`='$category',`address`='$adress',`schoolname`='$schoolname',`previousstd`='$previousstd',`grade`='$grade',`board`='$board',`language`='$language',`centre`='$center',`studphoto`='$targetFilePathPhoto',`studaadhar`='$targetFilePathAadhar',`studsign`='$targetFilePathSign',`status`='$status',`createdby`='$createdby',`createdon`='$createdon' WHERE  stud_id = '$studid'";

        $result = mysqli_query($GLOBALS['conn'], $query);

        header('Location: ../admin/studentlist.php');

    }



?>