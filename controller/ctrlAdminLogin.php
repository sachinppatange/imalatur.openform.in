<?php
include_once "../config/config.php"; 
$userid=$_POST['email'];
$pass=$_POST['password'];

  $sql="SELECT * FROM admin WHERE username='$userid' and password='$pass'";
$result=mysqli_query($GLOBALS['conn'],$sql) or die(mysql_error());
$row=mysqli_fetch_array($result);
 $count=mysqli_num_rows($result);

if(($pass==$row['password']) && ($count==1))
{
session_start(); 

$_SESSION['id']=$row['aid'];
$_SESSION['email']=$row['email'];
$_SESSION['type1']= 'admin';

header("Location: ../admin/index.php");
}
else {
header("Location: ../admin/login.php");
}
?>
