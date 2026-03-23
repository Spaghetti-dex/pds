<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";

// Load ADMIN users only
$users = [];
$stmtUsers = $conn->prepare("SELECT id, username, email, role FROM users WHERE role = 'admin' ORDER BY username ASC");
$stmtUsers->execute();
$resultUsers = $stmtUsers->get_result();

while ($row = $resultUsers->fetch_assoc()) {
    $users[] = $row;
}
$stmtUsers->close();

if (isset($_POST['update'])) {
    $id = (int)($_POST['user_id'] ?? 0);
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['password'] ?? '';

    if ($id <= 0 || $new_username === '' || $new_email === '') {
        $message = "Please fill in all required fields.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // make sure target is admin
        $checkRole = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
        $checkRole->bind_param("i", $id);
        $checkRole->execute();
        $roleResult = $checkRole->get_result();

        if ($roleResult->num_rows === 0) {
            $message = "Selected admin account not found.";
        } else {
            $check1 = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $check1->bind_param("si", $new_username, $id);
            $check1->execute();

            if ($check1->get_result()->num_rows > 0) {
                $message = "Username already exists.";
            } else {
                $check2 = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $check2->bind_param("si", $new_email, $id);
                $check2->execute();

                if ($check2->get_result()->num_rows > 0) {
                    $message = "Email already exists.";
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'admin'");
                    $stmt->bind_param("ssi", $new_username, $new_email, $id);
                    $stmt->execute();

                    if (!empty($new_password)) {
                        $hash = password_hash($new_password, PASSWORD_DEFAULT);
                        $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'admin'");
                        $stmt2->bind_param("si", $hash, $id);
                        $stmt2->execute();
                        $stmt2->close();
                    }

                    $stmt->close();
                    $message = "Admin account updated successfully.";
                }

                $check2->close();
            }

            $check1->close();
        }

        $checkRole->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Account Modifier</title>
<style>
body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f4f4f4;
    padding:40px;
}
.box{
    width:450px;
    margin:auto;
    background:#fff;
    padding:25px;
    border-radius:10px;
}
input, select{
    width:100%;
    padding:10px;
    margin:10px 0;
    box-sizing:border-box;
}
button{
    padding:10px;
    background:#22361e;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
}
.info{
    background:#eef;
    padding:10px;
    margin-bottom:10px;
    border-radius:8px;
}
</style>
</head>
<body>

<div class="box">
    <h2>Admin Account Modifier</h2>

    <?php if($message!="") echo "<div class='info'>".htmlspecialchars($message)."</div>"; ?>

    <form method="POST">
        <select name="user_id" id="user_id" required>
            <option value="">Select Admin Account</option>
            <?php foreach($users as $u): ?>
                <option
                    value="<?php echo $u['id']; ?>"
                    data-username="<?php echo htmlspecialchars($u['username']); ?>"
                    data-email="<?php echo htmlspecialchars($u['email']); ?>"
                >
                    <?php echo htmlspecialchars($u['username']." (".$u['email'].")"); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Username</label>
        <input type="text" name="username" id="username" required>

        <label>Email</label>
        <input type="email" name="email" id="email" required>

        <label>Password (leave blank if no change)</label>
        <input type="password" name="password">

        <button type="submit" name="update">Update Admin Account</button>
    </form>
</div>

<script>
const select = document.getElementById("user_id");
const username = document.getElementById("username");
const email = document.getElementById("email");

select.addEventListener("change", function(){
    let opt = this.options[this.selectedIndex];
    username.value = opt.getAttribute("data-username") || "";
    email.value = opt.getAttribute("data-email") || "";
});
</script>

</body>
</html>