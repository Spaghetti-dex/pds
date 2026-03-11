<?php
session_start();
if(!isset($_SESSION['username'])){
    // Absolute path from localhost
    header("Location: /pds_system/auth/login.php");
    exit();
}
?>