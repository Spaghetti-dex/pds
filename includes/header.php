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
:root{
    --topbar-h: 74px;
    --green-main: #22361e;
    --green-dark: #1a2a17;
    --green-soft: #2c4527;
    --gold-soft: #d6c37a;
    --menu-bg: #f3f3f3;
    --menu-border: #2f2f2f;
    --text-light: #ffffff;
    --text-dark: #111111;
}

body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:#efefef url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
}

/* ===== TOPBAR ===== */
.topbar{
    height:var(--topbar-h);
    background:linear-gradient(90deg, #22361e 0%, #22361e 58%, #2b4326 100%);
    position:fixed;
    top:0;
    left:0;
    right:0;
    z-index:1000;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 14px;
    box-sizing:border-box;
    border-bottom:1px solid rgba(255,255,255,0.08);
    box-shadow:0 4px 16px rgba(0,0,0,0.16);
    overflow:visible;
}

/* subtle moving assets, same color family */
.topbar::before{
    content:"";
    position:absolute;
    inset:0;
    background:
        linear-gradient(115deg, transparent 0%, rgba(255,255,255,0.06) 20%, transparent 40%),
        linear-gradient(295deg, transparent 0%, rgba(214,195,122,0.07) 18%, transparent 36%);
    background-size:240px 100%, 320px 100%;
    background-position:-240px 0, 100% 0;
    animation: headerShimmer 16s linear infinite;
    pointer-events:none;
    z-index:0;
}

.topbar::after{
    content:"";
    position:absolute;
    left:0;
    right:0;
    bottom:0;
    height:16px;
    background:
        radial-gradient(circle at 5% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 12% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px),
        radial-gradient(circle at 20% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 28% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px),
        radial-gradient(circle at 36% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 44% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px),
        radial-gradient(circle at 52% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 60% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px),
        radial-gradient(circle at 68% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 76% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px),
        radial-gradient(circle at 84% 120%, rgba(255,255,255,0.16) 0 9px, transparent 10px),
        radial-gradient(circle at 92% 120%, rgba(255,255,255,0.13) 0 8px, transparent 9px);
    opacity:.35;
    animation: crowdMove 10s ease-in-out infinite alternate;
    pointer-events:none;
    z-index:0;
}

.topbar-left,
.topbar-profile{
    position:relative;
    z-index:2;
}

/* left area */
.topbar-left{
    display:flex;
    align-items:center;
    gap:10px;
    min-width:0;
}

.topbar-logo-wrap{
    width:58px;
    height:58px;
    border-radius:14px;
    background:rgba(255,255,255,0.08);
    border:1px solid rgba(255,255,255,0.10);
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition:background .2s ease, transform .2s ease;
    flex-shrink:0;
}

.topbar-logo-wrap:hover{
    background:rgba(255,255,255,0.14);
    transform:translateY(-1px);
}

.topbar-logo{
    width:50px;
    height:50px;
    background:url("../assets/pcgg_logo.png") no-repeat center center;
    background-size:contain;
    flex-shrink:0;
}

.topbar-text{
    line-height:1.05;
    color:#fff;
    min-width:0;
}

.topbar-text .small{
    font-size:10px;
    font-weight:bold;
    letter-spacing:.4px;
    color:rgba(255,255,255,0.82);
    text-transform:uppercase;
}

.topbar-text .main{
    font-size:12px;
    font-weight:700;
    text-transform:uppercase;
    color:#fff;
    margin-top:2px;
}

.topbar-text .sub{
    font-size:10px;
    font-style:italic;
    color:#f0e2a8;
    margin-top:3px;
}

/* right area */
.topbar-profile{
    display:flex;
    align-items:center;
    position:relative;
}

.profile-button{
    width:42px;
    height:42px;
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
    box-shadow:0 2px 6px rgba(0,0,0,.25);
    transition:transform .15s ease, box-shadow .2s ease;
}

.profile-button:hover{
    transform:translateY(-1px);
    box-shadow:0 5px 12px rgba(0,0,0,.22);
}

/* ===== DROPDOWN ===== */
#menu{
    position:absolute;
    top:52px;
    right:0;
    width:255px;
    background:#f3f3f3;
    border:2px solid #2f2f2f;
    border-radius:28px 0 24px 24px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,.20);
    padding:0;
    z-index:2000;
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

/* optional spacer if needed in other pages */
.topbar-spacer{
    height:var(--topbar-h);
}

@keyframes headerShimmer{
    0%{
        background-position:-260px 0, 100% 0;
    }
    100%{
        background-position:120% 0, -180px 0;
    }
}

@keyframes crowdMove{
    0%{
        transform:translateX(0);
        opacity:.26;
    }
    100%{
        transform:translateX(8px);
        opacity:.38;
    }
}

@media (max-width: 700px){
    :root{
        --topbar-h: 68px;
    }

    .topbar{
        padding:0 10px;
    }

    .topbar-left{
        gap:8px;
        max-width:calc(100% - 58px);
    }

    .topbar-logo-wrap{
        width:48px;
        height:48px;
        border-radius:12px;
    }

    .topbar-logo{
        width:40px;
        height:40px;
    }

    .topbar-text .small{
        font-size:8px;
    }

    .topbar-text .main{
        font-size:10px;
    }

    .topbar-text .sub{
        font-size:9px;
    }

    .profile-button{
        width:38px;
        height:38px;
    }

    #menu{
        top:48px;
        width:245px;
    }
}

@media (max-width: 480px){
    .topbar-left{
        max-width:calc(100% - 50px);
    }

    .topbar-text .small{
        font-size:7px;
    }

    .topbar-text .main{
        font-size:9px;
        line-height:1.15;
    }

    .topbar-text .sub{
        font-size:8px;
    }

    #menu{
        width:min(250px, calc(100vw - 16px));
        right:0;
    }
}

@media (prefers-reduced-motion: reduce){
    .topbar::before,
    .topbar::after{
        animation:none !important;
    }
}
</style>

<div class="topbar">
    <div class="topbar-left">
        <div class="topbar-logo-wrap" title="Home">
            <div class="topbar-logo"></div>
        </div>

        <div class="topbar-text">
            <div class="small">REPUBLIC OF THE PHILIPPINES</div>
            <div class="main">PRESIDENTIAL COMMISSION ON GOOD GOVERNANCE</div>
            <div class="sub">The People’s Commission</div>
        </div>
    </div>

    <div class="topbar-profile">
        <button type="button" class="profile-button" onclick="menu()" aria-label="Open menu"></button>

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
</div>

<script>
document.querySelector('.topbar-logo-wrap').addEventListener('click', () => {
    window.location.href = '../dashboard/dashboard.php';
});

function menu(){
    const m = document.getElementById("menu");
    m.style.display = (m.style.display === "none" || m.style.display === "") ? "block" : "none";
}

function toggleSettings(){
    const s = document.getElementById("settingsSubmenu");
    const a = document.getElementById("settingsArrow");

    if(!s || !a){
        return;
    }

    if(s.style.display === "none" || s.style.display === ""){
        s.style.display = "block";
        a.classList.add("open");
    } else {
        s.style.display = "none";
        a.classList.remove("open");
    }
}

document.addEventListener("click", function(e){
    const menuBox = document.getElementById("menu");
    const profileWrap = document.querySelector(".topbar-profile");

    if (!menuBox || !profileWrap) return;

    if (!profileWrap.contains(e.target)) {
        menuBox.style.display = "none";
    }
});
</script>