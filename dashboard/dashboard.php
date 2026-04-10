<?php
require_once "../includes/auth_check.php";
include "../includes/header.php"; 
?>

<style>
html, body{
    margin:0;
    padding:0;
    height:100%;
    overflow:hidden;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background-image:url("../assets/background.jpg");
    background-size:cover;
    background-position:center;
    background-repeat:no-repeat;
    background-attachment:fixed;

    display:flex;
    justify-content:center;
    align-items:center;
}

/* =========================
   🔹 LAYOUT SWITCH
   change this class:
   - dashboard-grid  (2x2)
   - dashboard-row   (side by side)
========================= */

.dashboard-wrap{
    gap:25px;
}

/* GRID (2x2) */
.dashboard-grid{
    display:grid;
    grid-template-columns:repeat(2, 280px);
}

/* ROW (side by side) */
.dashboard-row{
    display:flex;
    gap:25px;
    flex-wrap:wrap;
    justify-content:center;
}

/* BUTTON */
.dashboard-btn{
    width:280px;
    height:250px;
    background:#ece7be;
    border:2px solid #8f8a6a;
    border-radius:12px;
    cursor:pointer;
    font-size:0;
    position:relative;
    box-shadow:0 4px 10px rgba(0,0,0,0.25);
    transition:all .2s ease;
}

.dashboard-btn:hover{
    transform:translateY(-6px);
    box-shadow:0 12px 22px rgba(0,0,0,0.3);
    background:#f1ecc8;
}

/* ICON */
.dashboard-btn::before{
    content:"";
    position:absolute;
    top:28px;
    left:50%;
    transform:translateX(-50%);
    width:150px;
    height:150px;
    background-repeat:no-repeat;
    background-position:center;
    background-size:contain;
}

/* LABEL */
.dashboard-btn::after{
    position:absolute;
    bottom:22px;
    left:0;
    right:0;
    text-align:center;
    font-size:18px;
    font-weight:bold;
    color:#000;
    text-transform:uppercase;
}

/* BUTTON TYPES */
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

.print-btn::before{
    background-image:url("../assets/print.png");
}
.print-btn::after{
    content:"PRINT";
}

/* MOBILE */
@media (max-width: 900px){
    html, body{
        overflow:auto;
    }

    body{
        display:block;
    }

    .dashboard-row{
        flex-direction:column;
        align-items:center;
    }

    .dashboard-grid{
        grid-template-columns:1fr;
        justify-items:center;
    }

    .dashboard-btn{
        width:240px;
        height:200px;
    }
}
</style>

<!-- 🔁 CHANGE CLASS HERE -->
<div class="dashboard-wrap dashboard-row">
<!-- use dashboard-grid OR dashboard-row -->

    <a href="../pds/create.php">
        <button class="dashboard-btn create-btn"></button>
    </a>

    <a href="../pds/view.php">
        <button class="dashboard-btn view-btn"></button>
    </a>

    <a href="../pds/tables_view.php">
        <button class="dashboard-btn table-btn"></button>
    </a>

    <a href="../pds/print.php">
        <button class="dashboard-btn print-btn"></button>
    </a>

</div>