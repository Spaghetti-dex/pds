<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: /pds/auth/login.php");
    exit();
}else {
    header("Location: /pds/dashboard/dashboard.php");
}
?>