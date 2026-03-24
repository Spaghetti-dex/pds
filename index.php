<?php

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}else {
    header("Location: dashboard/dashboard.php");
    exit();
}
?>