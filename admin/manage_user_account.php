<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";

// Load USER accounts only
$users = [];
$stmtUsers = $conn->prepare("SELECT id, username, email FROM users WHERE role = 'user' ORDER BY username ASC");
$stmtUsers->execute();
$resultUsers = $stmtUsers->get_result();

while ($row = $resultUsers->fetch_assoc()) {
    $users[] = $row;
}
$stmtUsers->close();

if (isset($_POST['update'])) {
    $target_id = (int)($_POST['target_id'] ?? 0);
    $new_username = trim($_POST['new_username'] ?? '');
    $new_email = trim($_POST['new_email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($target_id <= 0 || $new_username === '' || $new_email === '') {
        $message = "Please fill in the required fields.";
    } elseif (strlen($new_username) < 3) {
        $message = "Username must be at least 3 characters.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user' LIMIT 1");
        $checkUser->bind_param("i", $target_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows === 0) {
            $message = "Selected user account not found.";
        } else {
            $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $checkUsername->bind_param("si", $new_username, $target_id);
            $checkUsername->execute();
            $usernameResult = $checkUsername->get_result();

            if ($usernameResult->num_rows > 0) {
                $message = "Username is already in use.";
            } else {
                $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $checkEmail->bind_param("si", $new_email, $target_id);
                $checkEmail->execute();
                $emailResult = $checkEmail->get_result();

                if ($emailResult->num_rows > 0) {
                    $message = "Email is already in use.";
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'user'");
                    $stmt->bind_param("ssi", $new_username, $new_email, $target_id);

                    if ($stmt->execute()) {
                        if ($new_password !== '' || $confirm_password !== '') {
                            if (strlen($new_password) < 6) {
                                $message = "Password must be at least 6 characters.";
                            } elseif ($new_password !== $confirm_password) {
                                $message = "Passwords do not match.";
                            } else {
                                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                                $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'user'");
                                $stmt2->bind_param("si", $hashed_password, $target_id);

                                if ($stmt2->execute()) {
                                    $message = "User account updated successfully.";
                                } else {
                                    $message = "Username/email updated, but password update failed.";
                                }

                                $stmt2->close();
                            }
                        } else {
                            $message = "User account updated successfully.";
                        }
                    } else {
                        $message = "Error updating user account.";
                    }

                    $stmt->close();
                }

                $checkEmail->close();
            }

            $checkUsername->close();
        }

        $checkUser->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage User Account</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#efefef url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:20px;
    overflow-x:hidden;
}

.container{
    width:500px;
    max-width:95vw;
    max-height:none;
    background:#f8f8f8;
    border:3px solid #22361e;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.20);
    margin:20px 0;
}

.header{
    position:relative;
    background:#22361e;
    color:#fff;
    text-align:center;
    padding:24px 20px;
    font-size:26px;
    font-weight:bold;
}

.home-btn{
    position:absolute;
    left:15px;
    top:50%;
    transform:translateY(-50%);
    width:36px;
    height:36px;
    border-radius:50%;
    background:rgba(255,255,255,0.2);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    text-decoration:none;
    transition:0.3s;
}

.home-btn:hover{
    background:rgba(255,255,255,0.35);
}

.form-area{
    padding:20px;
}

label{
    display:block;
    margin-bottom:6px;
    font-weight:bold;
    color:#22361e;
}

input, select{
    width:100%;
    padding:10px 12px;
    border:2px solid #888;
    border-radius:10px;
    background:#efe8c2;
    margin-bottom:14px;
    font-size:14px;
}

input:focus, select:focus{
    outline:none;
    border-color:#22361e;
}

.info-box{
    background:#eef4ec;
    border:1px solid #c7d5c2;
    border-radius:12px;
    padding:12px;
    margin-bottom:14px;
}

.info-row{
    font-size:13px;
    margin-bottom:6px;
    word-break:break-word;
}

.info-row:last-child{
    margin-bottom:0;
}

.password-wrap{
    position:relative;
    margin-bottom:14px;
}

.password-wrap input{
    width:100%;
    height:52px;
    padding:10px 50px 10px 14px;
    border:2px solid #888;
    border-radius:18px;
    background:#cfd8e7;
    font-size:15px;
    margin-bottom:0;
}

.eye{
    position:absolute;
    right:16px;
    top:50%;
    transform:translateY(-50%);
    display:flex;
    align-items:center;
    justify-content:center;
    width:24px;
    height:24px;
    font-size:15px;
    color:#3b3128;
    cursor:pointer;
    line-height:1;
}

.eye:hover{
    color:#000;
}

button{
    width:45%;
    padding:11px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
    display:block;
    margin:20px auto 0;
}

button:hover{
    background:#789c78;
}

.message{
    margin-bottom:12px;
    font-weight:bold;
    color:#b00020;
    word-break:break-word;
}

.success{
    color:#1f5f1f;
}

.note{
    font-size:12px;
    margin-top:-4px;
    margin-bottom:12px;
    color:#444;
}

@media (max-width: 520px){
    body{
        padding:10px;
    }

    .container{
        width:100%;
        max-width:100%;
        border-width:2px;
        border-radius:20px;
        margin:10px 0;
    }

    .header{
        padding:18px 14px;
        font-size:22px;
    }

    .home-btn{
        left:10px;
        width:34px;
        height:34px;
    }

    .form-area{
        padding:16px 12px;
    }

    input, select{
        font-size:15px;
        padding:10px 12px;
    }

    .password-wrap input{
        height:auto;
        min-height:48px;
        padding:10px 44px 10px 12px;
        border-radius:14px;
        font-size:15px;
    }

    button{
        width:100%;
    }
}
</style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
            <i class="fa-solid fa-house"></i>
        </a>
        Manage User Account
    </div>

    <div class="form-area">

        <?php if ($message !== ""): ?>
            <div class="message <?php echo (stripos($message, 'successfully') !== false) ? 'success' : ''; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <label>Select User Account</label>
            <select name="target_id" id="target_id" required>
                <option value="">-- Select User --</option>
                <?php foreach ($users as $user): ?>
                    <option
                        value="<?php echo $user['id']; ?>"
                        data-username="<?php echo htmlspecialchars($user['username']); ?>"
                        data-email="<?php echo htmlspecialchars($user['email']); ?>"
                        <?php echo (isset($_POST['target_id']) && (int)$_POST['target_id'] === (int)$user['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo htmlspecialchars($user['username'] . " (" . $user['email'] . ")"); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div class="info-box">
                <div class="info-row">
                    <strong>Current Username:</strong>
                    <span id="current_username">None</span>
                </div>
                <div class="info-row">
                    <strong>Current Email:</strong>
                    <span id="current_email_text">None</span>
                </div>
            </div>

            <label>New Username</label>
            <input
                type="text"
                name="new_username"
                id="new_username"
                value="<?php echo htmlspecialchars($_POST['new_username'] ?? ''); ?>"
                required
            >

            <label>New Email</label>
            <input
                type="email"
                name="new_email"
                id="new_email"
                value="<?php echo htmlspecialchars($_POST['new_email'] ?? ''); ?>"
                required
            >

            <label>New Password</label>
            <div class="password-wrap">
                <input type="password" name="new_password" id="new_password">
                <i class="fa-solid fa-eye-slash eye" onclick="togglePassword('new_password', this)"></i>
            </div>

            <label>Confirm New Password</label>
            <div class="password-wrap">
                <input type="password" name="confirm_password" id="confirm_password">
                <i class="fa-solid fa-eye-slash eye" onclick="togglePassword('confirm_password', this)"></i>
            </div>

            <div class="note">Leave password fields blank if you do not want to change the password.</div>

            <button type="submit" name="update">Update User Account</button>
        </form>
    </div>
</div>

<script>
const userSelect = document.getElementById("target_id");
const currentUsername = document.getElementById("current_username");
const currentEmailText = document.getElementById("current_email_text");
const newUsername = document.getElementById("new_username");
const newEmail = document.getElementById("new_email");

function updateUserInfo() {
    const selectedOption = userSelect.options[userSelect.selectedIndex];

    if (selectedOption && selectedOption.value !== "") {
        const username = selectedOption.getAttribute("data-username") || "";
        const email = selectedOption.getAttribute("data-email") || "";

        currentUsername.textContent = username;
        currentEmailText.textContent = email;

        if (newUsername.value === "" || userSelect.dataset.filled !== "yes") {
            newUsername.value = username;
        }

        if (newEmail.value === "" || userSelect.dataset.filled !== "yes") {
            newEmail.value = email;
        }

        userSelect.dataset.filled = "yes";
    } else {
        currentUsername.textContent = "None";
        currentEmailText.textContent = "None";
        newUsername.value = "";
        newEmail.value = "";
        userSelect.dataset.filled = "no";
    }
}

function togglePassword(id, icon){
    const input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}

userSelect.addEventListener("change", function(){
    userSelect.dataset.filled = "no";
    updateUserInfo();
});

updateUserInfo();
</script>

</body>
</html>