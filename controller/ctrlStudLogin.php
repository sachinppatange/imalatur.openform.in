<?php
include_once "../config/config.php";
session_start();

$mobile = $_GET['mobile'];


    try {
        $sql = "SELECT * FROM  student WHERE whatsappno='$mobile'";
        $res = mysqli_query($GLOBALS['conn'], $sql) or die("error");

        $count = mysqli_num_rows($res);
        if ($count == 1) {
            session_start();
            $arr = mysqli_fetch_array($res);
            //            print_r($arr);
            $_SESSION['id'] = $arr['stud_id'];
            $_SESSION['username'] = $arr['email'];
            $_SESSION['type'] = 'student';
            
            header("location: ../web/portal.php");
        } else {

            header("location: ../web/studentlogin.php");

        }
    } catch (EXCEPTION $err) {
        echo "error" . $err;
    }



?>