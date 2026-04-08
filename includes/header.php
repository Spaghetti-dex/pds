<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$account_name = "Account User";
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if (isset($_SESSION['fullname']) && !empty($_SESSION['fullname'])) {
    $account_name = $_SESSION['fullname'];
} elseif (isset($_SESSION['name']) && !empty($_SESSION['name'])) {
    $account_name = $_SESSION['name'];
} elseif (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $account_name = $_SESSION['username'];
}
?>

<style>
body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:#efefef url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
}


.topbar{
    height:70px;
    background:#22361e;
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1000;
    display:flex;
    align-items:center;
    padding:0 14px;
    box-sizing:border-box;
}


.topbar-left{
    display:flex;
    align-items:center;
    gap:10px;
}

.topbar-logo{
    width:60px;
    height:60px;
    background:url("../assets/pcgg_logo.png") no-repeat center center;
    background-size:contain;
    flex-shrink:0;
}


.topbar-text{
    line-height:1.05;
    color:#fff;
}

.topbar-text .small{
    font-size:10px;
    font-weight:bold;
    letter-spacing:.3px;
}

.topbar-text .main{
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
}

.topbar-text .sub{
    font-size:10px;
    font-style:italic;
}


div[style*="float:right"]{
    position:fixed;
    top:0;
    right:14px;
    height:70px;
    display:flex;
    align-items:center;
    z-index:1100;
    float:none !important;
    margin:0 !important;
}


div[style*="float:right"] > button{
    width:40px;
    height:40px;
    border-radius:50%;
    border:2px solid #ffffff;
    background:#ffffff;
    cursor:pointer;
    padding:0;
    outline:none;
    background-image:url("../assets/profile_logo.png");
    background-repeat:no-repeat;
    background-position:center;
    background-size:48px 48px;
    box-shadow:0 2px 4px rgba(0,0,0,.25);
}

#menu{
    position:absolute;
    top:58px;
    right:0;
    width:255px;
    background:#f3f3f3;
    border:2px solid #2f2f2f;
    border-radius:28px 0 24px 24px;
    overflow:hidden;
    box-shadow:0 3px 8px rgba(0,0,0,.20);
    padding:0;
}


.menu-header{
    padding:14px 18px 10px 18px;
    background:#f3f3f3;
    border-bottom:1px solid #555;
}

.menu-signed{
    font-size:12px;
    color:#111;
    margin-bottom:3px;
}

.menu-name{
    font-size:16px;
    font-weight:700;
    color:#000;
    line-height:1.2;
    word-break:break-word;
}

.menu-main{
    padding:4px 0;
    background:#f3f3f3;
}

.menu-footer{
    border-top:1px solid #555;
    background:#f3f3f3;
}

.menu-item,
.menu-subitem,
.menu-logout{
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    color:#0a0a0a;
    background:#f3f3f3;
    box-sizing:border-box;
}

.menu-item{
    width:100%;
    padding:10px 18px;
    font-size:14px;
    border:none;
    cursor:pointer;
    text-align:left;
    font-family:Arial, Helvetica, sans-serif;
}

.menu-subitem{
    padding:8px 18px 8px 42px;
    font-size:14px;
}

.menu-logout{
    padding:10px 18px 12px 18px;
    font-size:14px;
}

.menu-item:hover,
.menu-subitem:hover,
.menu-logout:hover{
    background:#e8e8e8;
}


.menu-icon{
    width:18px;
    height:18px;
    flex-shrink:0;
    background-repeat:no-repeat;
    background-position:center;
    background-size:contain;
}

.icon-settings{
    background-image:url("../assets/settings.png");
}

.icon-admin{
    background-image:url("../assets/admin.jpg.png");
}

.icon-user{
    background-image:url("../assets/service.png");
}

.icon-add{
    background-image:url("../assets/create.png");
}

.icon-about{
    background-image:url("../assets/about.png");
}

.icon-slogs{
    background-image:url("../assets/slogs.png");
}

.icon-logout{
    background-image:url("../assets/logout.png");
}




.menu-arrow{
    margin-left:auto;
    width:10px;
    height:10px;
    border-right:2px solid #1f5a85;
    border-bottom:2px solid #1f5a85;
    transform:rotate(45deg);
    transition:transform .2s ease;
    flex-shrink:0;
}


.menu-arrow.open{
    transform:rotate(225deg);
    margin-top:4px;
}


#settingsSubmenu{
    display:none;
}
</style>

<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-logo"></div>
        <div class="topbar-text">
            <div class="small">REPUBLIC OF THE PHILIPPINES</div>
            <div class="main">PRESIDENTIAL COMMISSION ON GOOD GOVERNANCE</div>
            <div class="sub">The People’s Commission</div>
        </div>
    </div>
</div>

<div style="float:right">

<button onclick="menu()"></button>

<div id="menu" style="display:none">

    <div class="menu-header">
        <div class="menu-signed">Signed in as</div>
        <div class="menu-name"><?php echo htmlspecialchars($account_name); ?></div>
    </div>

    <div class="menu-main">

        <?php if ($is_admin): ?>
        <button type="button" class="menu-item" onclick="toggleSettings()">
            <span class="menu-icon icon-settings"></span>
            <span>Settings</span>
            <span class="menu-arrow" id="settingsArrow"></span>
        </button>

        <div id="settingsSubmenu">
                <a href="../admin/add_account.php" class="menu-subitem">
                    <span class="menu-icon icon-add"></span>
                    <span>Add Account</span>
                </a>

                <a href="../admin/account_modifier.php" class="menu-subitem">
                    <span class="menu-icon icon-admin"></span>
                    <span>Admin Account Modifier</span>
                </a>
                <a href="../admin/manage_user_account.php" class="menu-subitem">
                    <span class="menu-icon icon-user"></span>
                    <span>Manage User Account</span>
                </a>

                <a href="../pds/logs.php" class="menu-subitem">
                    <span class="menu-icon icon-slogs"></span>
                    <span>System Logs</span>
                </a>

        </div>
        <?php endif; ?>

        <a href="../auth/about.php" class="menu-subitem" style="padding-left:18px;">
            <span class="menu-icon icon-about"></span>
            <span>About</span>
        </a>
    </div>

    <div class="menu-footer">
        <a href="../auth/logout.php" class="menu-logout">
            <span class="menu-icon icon-logout"></span>
            <span>Logout</span>
        </a>
    </div>

</div>

</div>

<script>

     document.querySelector('.topbar-logo').addEventListener('click', () => {
          window.location.href = '../dashboard/dashboard.php'
        })

function menu(){
    let m = document.getElementById("menu");
    m.style.display = m.style.display == "none" ? "block" : "none";
}

function toggleSettings(){
    let s = document.getElementById("settingsSubmenu");
    let a = document.getElementById("settingsArrow");

    if(!s || !a){
        return;
    }

    if(s.style.display == "none" || s.style.display == ""){
        s.style.display = "block";
        a.classList.add("open");
    }else{
        s.style.display = "none";
        a.classList.remove("open");
    }
}
</script>