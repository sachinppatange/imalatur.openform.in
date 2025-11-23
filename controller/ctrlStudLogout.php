<?php
    session_start();

    session_destroy();

    header('location: ../web/studentlogin.php');
?>