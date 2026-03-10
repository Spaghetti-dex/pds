<div style="float:right">

<button onclick="menu()">👤</button>

<div id="menu" style="display:none">

<a href="../admin/profile.php">Modify Account</a>
<a href="../auth/logout.php">Logout</a>
<a href="#">About</a>

</div>

</div>

<script>
function menu(){
let m=document.getElementById("menu");
m.style.display = m.style.display=="none" ? "block":"none";
}
</script>


