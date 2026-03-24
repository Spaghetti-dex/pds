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
        // confirm selected account is a USER account
        $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user' LIMIT 1");
        $checkUser->bind_param("i", $target_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows === 0) {
            $message = "Selected user account not found.";
        } else {
            // check duplicate username
            $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $checkUsername->bind_param("si", $new_username, $target_id);
            $checkUsername->execute();
            $usernameResult = $checkUsername->get_result();

            if ($usernameResult->num_rows > 0) {
                $message = "Username is already in use.";
            } else {
                // check duplicate email
                $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $checkEmail->bind_param("si", $new_email, $target_id);
                $checkEmail->execute();
                $emailResult = $checkEmail->get_result();

                if ($emailResult->num_rows > 0) {
                    $message = "Email is already in use.";
                } else {
                    // update username + email
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'user'");
                    $stmt->bind_param("ssi", $new_username, $new_email, $target_id);

                    if ($stmt->execute()) {
                        // update password only if entered
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
    padding:110px 20px 30px 20px;
}

.container{
    width:500px;
    margin:auto;
    background:#f8f8f8;
    border:3px solid #22361e;
    border-radius:25px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.20);
}

.header{
    background:#22361e;
    color:#fff;
    text-align:center;
    padding:28px 20px;
    font-size:28px;
    font-weight:bold;
}

.form-area{
    padding:30px;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:bold;
    color:#22361e;
}

input, select{
    width:100%;
    padding:11px 12px;
    border:2px solid #888;
    border-radius:10px;
    background:#efe8c2;
    margin-bottom:16px;
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
    padding:14px;
    margin-bottom:18px;
}

.info-row{
    margin-bottom:8px;
    font-size:14px;
}

.info-label{
    font-weight:bold;
    color:#22361e;
}

.password-wrap{
    position:relative;
}

.password-wrap input{
    padding-right:42px;
}

.eye{
    position:absolute;
    right:12px;
    top:40%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#333;
}

button{
    padding:11px 24px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    background:#789c78;
    transform:scale(1.03);
}

.message{
    margin-bottom:15px;
    font-weight:bold;
    color:#b00020;
}

.success{
    color:#1f5f1f;
}

.note{
    font-size:12px;
    color:#444;
    margin-top:-10px;
    margin-bottom:14px;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">Manage User Account</div>

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
                    <span class="info-label">Current Username:</span>
                    <span id="current_username">None</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Current Email:</span>
                    <span id="current_email_text">None</span>
                </div>
            </div>

            <label>New Username</label>
            <input type="text" name="new_username" id="new_username" value="<?php echo htmlspecialchars($_POST['new_username'] ?? ''); ?>" required>

            <label>New Email</label>
            <input type="email" name="new_email" id="new_email" value="<?php echo htmlspecialchars($_POST['new_email'] ?? ''); ?>" required>

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