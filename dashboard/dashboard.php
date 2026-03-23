<?php
//require_once "../includes/auth_check.php";
include "../includes/header.php"; ?>

<style>


body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background-image:url("../assets/background.jpg");
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    background-attachment:fixed;
}


.dashboard-title{
    display:none;
}


.dashboard-wrap{
    width:100%;
    text-align:center;
    padding-top:230px;
    box-sizing:border-box;
}


a[href*="create.php"],
a[href*="view.php"]{
    text-decoration:none;
    display:inline-block;
    margin:0 25px;
    vertical-align:top;
}


a[href*="create.php"] button,
a[href*="view.php"] button{
    width:350px;
    height:320px;
    background:#ece7be;
    border:2px solid #8f8a6a;
    border-radius:12px;
    cursor:pointer;
    font-size:0;
    position:relative;
    box-shadow:0 4px 12px rgba(0,0,0,0.25);
    transition:all .25s ease;
}


a[href*="create.php"] button:hover,
a[href*="view.php"] button:hover{
    transform:translateY(-10px);
    box-shadow:0 14px 28px rgba(0,0,0,0.35);
    background:#f1ecc8;
}


a[href*="create.php"] button::before,
a[href*="view.php"] button::before{
    content:"";
    position:absolute;
    top:40px;
    left:50%;
    transform:translateX(-50%);
    width:180px;
    height:180px;
    background-repeat:no-repeat;
    background-position:center;
    background-size:contain;
}


a[href*="create.php"] button::before{
    background-image:url("../assets/create.png");
}

a[href*="view.php"] button::before{
    background-image:url("../assets/view.png");
}


a[href*="create.php"] button::after,
a[href*="view.php"] button::after{
    position:absolute;
    bottom:30px;
    left:0;
    right:0;
    text-align:center;
    font-size:24px;
    font-weight:bold;
    color:#000;
    text-transform:uppercase;
}

a[href*="create.php"] button::after{
    content:"CREATE";
}

a[href*="view.php"] button::after{
    content:"VIEW";
}

</style>

<div class="dashboard-wrap">

<h2 class="dashboard-title">Dashboard</h2>

<a href="../pds/create.php">
    <button>Create</button>
</a>

<a href="../pds/view.php">
    <button>View</button>
</a>

</div>