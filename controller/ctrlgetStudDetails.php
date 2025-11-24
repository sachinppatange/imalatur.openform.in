<?php
include_once '../config/config.php';

function getAllStudent()
{
    $leftAr = array();
    $query = "select * from user order by id DESC";
    $result = mysqli_query($GLOBALS['conn'], $query);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        while ($arr = mysqli_fetch_array($result)) {
            array_push($leftAr, $arr);
        }
    }
    return $leftAr;
}
function getStudentById($id)
{
    $arr = array();
    $sql = "select * from user where `createdby`='$id'";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}
function getStudentByStudId($id)
{
    $arr = array();
    $sql = "select * from user where `id`='$id'";

    $res = mysqli_query($GLOBALS['conn'], $sql);
    // print_r($res);
    // exit;
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}

function getStudentMobileById($id)
{
    $arr = array();
    $sql = "select * from user where `createdby`='$id'";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}
function getStudentPaymentStatusById($id)
{
    $arr = array();
    $sql = "select * from user where `createdby`='$id'";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}
function getAllRegStudent()
{
    $leftAr = array();
    $query = "select * from user order by id  DESC";
    $result = mysqli_query($GLOBALS['conn'], $query);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        while ($arr = mysqli_fetch_array($result)) {
            array_push($leftAr, $arr);
        }
    }
    return $leftAr;
}
function getStudentRegById($id)
{
    $arr = array();
    $sql = "select * from user where `id`='$id'";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}


function getStudentRegByMaxId($id)
{
    $arr = array();
    $sql = "SELECT * FROM user WHERE id  = (SELECT MAX(id ) FROM student);
";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $count = mysqli_num_rows($res);
    if ($count == 1) {
        $arr = mysqli_fetch_array($res);
        return $arr;
    }
}





function gettotalStudents()
{
    $sql = "SELECT COUNT(*) AS total_records FROM user";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $row = mysqli_fetch_assoc($res);
    return $row['total_records'];
}
function gettotalMembers()
{
    $sql = "SELECT COUNT(*) AS total_records FROM user";
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $row = mysqli_fetch_assoc($res);
    return $row['total_records'];
}
function getTotalPaidStudent()
{
    $sql = "SELECT COUNT(*) AS total_records FROM user WHERE status = 'success'";
    $res = mysqli_query($GLOBALS['conn'], $sql);

    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return $row['total_records'];
    } else {
        return 0;
    }
}
function getTotalunPaidStudent()
{
    $sql = "SELECT COUNT(*) AS total_records 
    FROM user 
    WHERE status IS NULL OR status != 'success' OR status = ''";
    // $sql = "SELECT COUNT(*) AS total_records FROM student WHERE status != 'Success'";
    $res = mysqli_query($GLOBALS['conn'], $sql);

    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return $row['total_records'];
    } else {
        return 0;
    }
}







function getAllPaidStudent()
{
    $leftAr = array();
    $query = "select * from user where status ='success' order by id DESC";
    $result = mysqli_query($GLOBALS['conn'], $query);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        while ($arr = mysqli_fetch_array($result)) {
            array_push($leftAr, $arr);
        }
    }
    return $leftAr;
}

function getAllUnpaidStudent()
{
    $leftAr = array();
    $query = "SELECT * 
    FROM user 
    WHERE status IS NULL OR status != 'success' OR status = '' 
    ORDER BY id DESC";

    $result = mysqli_query($GLOBALS['conn'], $query);
    $count = mysqli_num_rows($result);
    if ($count > 0) {
        while ($arr = mysqli_fetch_array($result)) {
            array_push($leftAr, $arr);
        }
    }
    return $leftAr;
}







?>