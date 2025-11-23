<?php
include_once '../config/config.php';
    $id = $_GET['id'];

    $query = "DELETE FROM student WHERE stud_id = '$id'";
    $result=mysqli_query($GLOBALS['conn'],$query);

    if($result==true){
         header('location: ../admin/studentlist.php');
    }
?>