<div style="float:right">

<button onclick="menu()">👤</button>

<div id="menu" style="display:none">

    <div class="menu-header">
        <div class="menu-signed">Signed in as</div>
        <div class="menu-name"><?php echo htmlspecialchars($account_name); ?></div>
    </div>

    <div class="menu-main">
        <button type="button" class="menu-item" onclick="toggleSettings()">
            <span class="menu-icon icon-settings"></span>
            <span>Settings</span>
            <span class="menu-arrow" id="settingsArrow"></span>
        </button>

        <div id="settingsSubmenu">
            <a href="../admin/profile.php" class="menu-subitem">
                <span class="menu-icon icon-email"></span>
                <span>Change Email</span>
            </a>

            <a href="../admin/profile.php" class="menu-subitem">
                <span class="menu-icon icon-password"></span>
                <span>Change Password</span>
            </a>
        </div>

        <a href="#" class="menu-subitem" style="padding-left:18px;">
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
function menu(){
let m=document.getElementById("menu");
m.style.display = m.style.display=="none" ? "block":"none";
}
</script>


