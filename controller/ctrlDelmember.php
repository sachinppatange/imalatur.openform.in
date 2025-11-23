<?php
include_once '../config/config.php';
    $id = $_GET['id'];

    $query = "DELETE FROM student_registration WHERE stud_reg_id = '$id'";
    $result=mysqli_query($GLOBALS['conn'],$query);

    if($result==true){
         header('location: ../admin/member.php');
    }
?>