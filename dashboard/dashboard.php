<?php
require_once "../includes/auth_check.php";
include "../includes/header.php"; 
?>

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
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    gap:30px;
    flex-wrap:wrap;
    text-align:center;
    box-sizing:border-box;
    padding:40px 20px;
}

/* SHARED BUTTON STYLE */
.dashboard-link{
    text-decoration:none;
    display:block;
    width:min(100%, 350px);
}

.dashboard-btn{
    width:100%;
    height:320px;
    background:#ece7be;
    border:2px solid #8f8a6a;
    border-radius:12px;
    cursor:pointer;
    font-size:0;
    position:relative;
    box-shadow:0 4px 12px rgba(0,0,0,0.25);
    transition:all .25s ease;
    -webkit-appearance:none;
    appearance:none;
}

.dashboard-btn:hover{
    transform:translateY(-10px);
    box-shadow:0 14px 28px rgba(0,0,0,0.35);
    background:#f1ecc8;
}

/* ICON */
.dashboard-btn::before{
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

/* LABEL */
.dashboard-btn::after{
    position:absolute;
    bottom:30px;
    left:0;
    right:0;
    text-align:center;
    font-size:22px;
    font-weight:bold;
    color:#000;
    text-transform:uppercase;
}

/* INDIVIDUAL BUTTONS */
.create-btn::before{
    background-image:url("../assets/create.png");
}
.create-btn::after{
    content:"CREATE";
}

.view-btn::before{
    background-image:url("../assets/view.png");
}
.view-btn::after{
    content:"VIEW";
}

.table-btn::before{
    background-image:url("../assets/table-icon.png"); 
}
.table-btn::after{
    content:"FULL TABLE VIEW";
}

/* Tablet */
@media (max-width: 1024px){
    .dashboard-link{
        width:min(100%, 300px);
    }

    .dashboard-btn{
        height:270px;
    }

    .dashboard-btn::before{
        width:140px;
        height:140px;
        top:32px;
    }

    .dashboard-btn::after{
        font-size:18px;
        bottom:22px;
    }
}

/* Mobile */
@media (max-width: 768px){
    body{
        background-attachment:scroll;
    }

    .dashboard-wrap{
        flex-direction:column;
        gap:18px;
        padding:25px 14px;
    }

    .dashboard-link{
        width:100%;
        max-width:320px;
    }

    .dashboard-btn{
        height:220px;
    }

    .dashboard-btn::before{
        width:110px;
        height:110px;
        top:28px;
    }

    .dashboard-btn::after{
        font-size:18px;
        bottom:18px;
    }
}

/* Small Mobile */
@media (max-width: 480px){
    .dashboard-wrap{
        padding:20px 12px;
        gap:15px;
    }

    .dashboard-btn{
        height:180px;
        border-radius:10px;
    }

    .dashboard-btn::before{
        width:85px;
        height:85px;
        top:20px;
    }

    .dashboard-btn::after{
        font-size:16px;
        bottom:14px;
    }
}

</style>

<div class="dashboard-wrap">

<h2 class="dashboard-title">Dashboard</h2>

<a href="../pds/create.php" class="dashboard-link">
    <button class="dashboard-btn create-btn"></button>
</a>

<a href="../pds/view.php" class="dashboard-link">
    <button class="dashboard-btn view-btn"></button>
</a>

<a href="../pds/tables_view.php" class="dashboard-link">
    <button class="dashboard-btn table-btn"></button>
</a>

</div>