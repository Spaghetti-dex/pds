<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - PDS System</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body{
    background:#efefef url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
    min-height:100vh;
    padding:110px 20px 30px 20px;
}

.about-container{
    max-width:800px;
    margin:0 auto;
    background:#f8f8f8;
    border:3px solid #22361e;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.20);
}

.about-header{
    background:#22361e;
    color:#fff;
    text-align:center;
    padding:30px 20px;
}

.about-header h1{
    font-size:30px;
    margin-bottom:8px;
    letter-spacing:1px;
}

.about-header p{
    font-size:14px;
    opacity:0.95;
}

.about-body{
    padding:35px 30px;
    text-align:center;
}

.logo-box{
    width:120px;
    height:120px;
    margin:0 auto 20px auto;
    border-radius:50%;
    border:3px solid #22361e;
    background:#fff url("../assets/rtu_logo.png") no-repeat center center;
    background-size:80%;
    box-shadow:0 4px 10px rgba(0,0,0,0.15);
}

.system-title{
    font-size:28px;
    font-weight:bold;
    color:#22361e;
    margin-bottom:15px;
}

.system-desc{
    font-size:16px;
    color:#333;
    line-height:1.8;
    margin-bottom:25px;
}

.line{
    width:100%;
    height:1px;
    background:#cfcfcf;
    margin:25px 0;
}

.section-title{
    font-size:20px;
    color:#22361e;
    margin-bottom:18px;
    font-weight:bold;
}

.dev-list{
    list-style:none;
    padding:0;
    margin:0;
}

.dev-list li{
    background:#e9efe7;
    margin:10px auto;
    padding:12px 15px;
    border-radius:12px;
    max-width:420px;
    font-size:16px;
    font-weight:bold;
    color:#1d1d1d;
    border:1px solid #c9d6c5;
}

.back-btn{
    display:inline-block;
    margin-top:25px;
    padding:12px 24px;
    background:#8fae8d;
    color:#000;
    text-decoration:none;
    border-radius:20px;
    font-weight:bold;
    transition:0.3s;
}

.back-btn:hover{
    background:#789c78;
    transform:scale(1.05);
}
</style>
</head>
<body>

<div class="about-container">
    <div class="about-header">
        <h1>ABOUT</h1>
        <p>Personal Data Sheet Management System</p>
    </div>

    <div class="about-body">
        <div class="logo-box"></div>

        <div class="system-title">PDS System</div>

        <div class="system-desc">
            This <strong>PDS System</strong> is developed by students of
            <strong>Rizal Technological University</strong>.<br>
            The system is designed to help manage and organize personal data sheet records efficiently and accurately.
        </div>

        <div class="line"></div>

        <div class="section-title">Developed By</div>

        <ul class="dev-list">
            <li>Charles Miguel Mayani</li>
            <li>Yasmin Jade Pilapil</li>
            <li>Leslie Mangobos</li>
        </ul>

        <a href="../dashboard/dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>
</div>

</body>
</html>