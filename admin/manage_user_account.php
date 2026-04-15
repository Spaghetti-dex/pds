<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";
$messageType = "";

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
        $messageType = "error";
    } elseif (strlen($new_username) < 3) {
        $message = "Username must be at least 3 characters.";
        $messageType = "error";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageType = "error";
    } else {
        $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user' LIMIT 1");
        $checkUser->bind_param("i", $target_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows === 0) {
            $message = "Selected user account not found.";
            $messageType = "error";
        } else {
            $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $checkUsername->bind_param("si", $new_username, $target_id);
            $checkUsername->execute();
            $usernameResult = $checkUsername->get_result();

            if ($usernameResult->num_rows > 0) {
                $message = "Username is already in use.";
                $messageType = "error";
            } else {
                $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $checkEmail->bind_param("si", $new_email, $target_id);
                $checkEmail->execute();
                $emailResult = $checkEmail->get_result();

                if ($emailResult->num_rows > 0) {
                    $message = "Email is already in use.";
                    $messageType = "error";
                } else {
                    if ($new_password !== '' || $confirm_password !== '') {
                        if (strlen($new_password) < 6) {
                            $message = "Password must be at least 6 characters.";
                            $messageType = "error";
                        } elseif ($new_password !== $confirm_password) {
                            $message = "Passwords do not match.";
                            $messageType = "error";
                        } else {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ? AND role = 'user'");
                            $stmt->bind_param("sssi", $new_username, $new_email, $hashed_password, $target_id);

                            if ($stmt->execute()) {
                                $message = "User account updated successfully.";
                                $messageType = "success";
                            } else {
                                $message = "Error updating user account.";
                                $messageType = "error";
                            }

                            $stmt->close();
                        }
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role = 'user'");
                        $stmt->bind_param("ssi", $new_username, $new_email, $target_id);

                        if ($stmt->execute()) {
                            $message = "User account updated successfully.";
                            $messageType = "success";
                        } else {
                            $message = "Error updating user account.";
                            $messageType = "error";
                        }

                        $stmt->close();
                    }
                }

                $checkEmail->close();
            }

            $checkUsername->close();
        }

        $checkUser->close();
    }
}

// DELETE USER
if (isset($_POST['delete'])) {
    $target_id = (int)($_POST['target_id'] ?? 0);

    if ($target_id <= 0) {
        $message = "Please select a user account to delete.";
        $messageType = "error";
    } else {
        $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'user' LIMIT 1");
        $checkUser->bind_param("i", $target_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows === 0) {
            $message = "Selected user account not found.";
            $messageType = "error";
        } else {
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
            $deleteStmt->bind_param("i", $target_id);

            if ($deleteStmt->execute()) {
                $message = "User account deleted successfully.";
                $messageType = "success";
                $_POST['target_id'] = '';
                $_POST['new_username'] = '';
                $_POST['new_email'] = '';
            } else {
                $message = "Error deleting user account.";
                $messageType = "error";
            }

            $deleteStmt->close();
        }

        $checkUser->close();
    }
}

// Reload USER accounts after update/delete
$users = [];
$stmtUsers = $conn->prepare("SELECT id, username, email FROM users WHERE role = 'user' ORDER BY username ASC");
$stmtUsers->execute();
$resultUsers = $stmtUsers->get_result();

while ($row = $resultUsers->fetch_assoc()) {
    $users[] = $row;
}
$stmtUsers->close();
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
    min-height:100vh;
    background:
        radial-gradient(circle at top left, rgba(116, 163, 112, 0.22), transparent 30%),
        radial-gradient(circle at bottom right, rgba(34, 54, 30, 0.18), transparent 28%),
        linear-gradient(135deg, #eef2ea 0%, #dfe8da 100%);
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:28px 14px;
    color:#1f2f1e;
}

.page{
    width:100%;
    max-width:760px;
}

.container{
    width:100%;
    background:rgba(255,255,255,0.94);
    border:1px solid rgba(34,54,30,0.14);
    border-radius:28px;
    overflow:hidden;
    box-shadow:0 18px 46px rgba(0,0,0,0.12);
    backdrop-filter:blur(6px);
}

.header{
    position:relative;
    background:linear-gradient(135deg, #22361e 0%, #2f4b2b 100%);
    color:#fff;
    padding:30px 24px 26px;
}

.header-top{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:14px;
}

.home-btn{
    width:42px;
    height:42px;
    border-radius:50%;
    background:rgba(255,255,255,0.16);
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    text-decoration:none;
    transition:0.25s;
    flex-shrink:0;
}

.home-btn:hover{
    background:rgba(255,255,255,0.28);
    transform:translateY(-1px);
}

.header-badge{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:8px 14px;
    border-radius:999px;
    background:rgba(255,255,255,0.14);
    font-size:13px;
    font-weight:700;
    letter-spacing:.4px;
}

.header h1{
    font-size:31px;
    margin-bottom:8px;
    line-height:1.2;
}

.header p{
    font-size:14px;
    line-height:1.6;
    color:rgba(255,255,255,0.88);
    max-width:580px;
}

.form-area{
    padding:26px;
}

.message{
    display:flex;
    align-items:flex-start;
    gap:12px;
    padding:14px 16px;
    border-radius:16px;
    margin-bottom:20px;
    font-size:14px;
    line-height:1.6;
    border:1px solid transparent;
    font-weight:600;
}

.message i{
    margin-top:2px;
}

.message.success{
    background:#edf7ec;
    color:#1d5a1f;
    border-color:#bfd8bc;
}

.message.error{
    background:#fff1f1;
    color:#a12828;
    border-color:#efc5c5;
}

.stats{
    display:grid;
    grid-template-columns:repeat(3, 1fr);
    gap:14px;
    margin-bottom:24px;
}

.stat-box{
    background:#f5f8f3;
    border:1px solid #d9e4d5;
    border-radius:18px;
    padding:16px 14px;
}

.stat-label{
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.7px;
    color:#607260;
    font-weight:700;
    margin-bottom:8px;
}

.stat-value{
    font-size:18px;
    font-weight:700;
    color:#1a341d;
}

.form-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}

.form-group{
    display:flex;
    flex-direction:column;
}

.form-group.full{
    grid-column:1 / -1;
}

label{
    display:block;
    margin-bottom:8px;
    font-weight:700;
    color:#22361e;
    font-size:14px;
}

.input-wrap{
    position:relative;
}

input, select{
    width:100%;
    min-height:54px;
    padding:14px 16px;
    border:1.5px solid #c8d4c2;
    border-radius:16px;
    background:#fbfcfa;
    font-size:15px;
    color:#203120;
    transition:border-color .2s ease, box-shadow .2s ease, background .2s ease;
}

input:focus, select:focus{
    outline:none;
    border-color:#5f8a59;
    box-shadow:0 0 0 4px rgba(95,138,89,0.14);
    background:#fff;
}

select{
    appearance:none;
    cursor:pointer;
    padding-right:46px;
}

.select-icon{
    position:absolute;
    right:16px;
    top:50%;
    transform:translateY(-50%);
    color:#607260;
    pointer-events:none;
    font-size:14px;
}

.info-box{
    background:linear-gradient(180deg, #f7faf5 0%, #eff5ec 100%);
    border:1px solid #d5e1d1;
    border-radius:18px;
    padding:16px;
    margin-bottom:2px;
}

.info-box-title{
    font-size:13px;
    font-weight:700;
    color:#547054;
    text-transform:uppercase;
    letter-spacing:.6px;
    margin-bottom:12px;
}

.info-row{
    font-size:14px;
    margin-bottom:10px;
    word-break:break-word;
    color:#253625;
}

.info-row:last-child{
    margin-bottom:0;
}

.password-wrap{
    position:relative;
}

.password-wrap input{
    padding-right:48px;
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
    color:#4c5e4d;
    cursor:pointer;
}

.eye:hover{
    color:#000;
}

.note{
    font-size:13px;
    margin-top:8px;
    color:#617462;
    line-height:1.5;
}

.danger-box{
    margin-top:24px;
    border:1px solid #efcaca;
    background:linear-gradient(180deg, #fff8f8 0%, #fff2f2 100%);
    border-radius:20px;
    padding:18px;
}

.danger-box h3{
    color:#983434;
    font-size:18px;
    margin-bottom:8px;
}

.danger-box p{
    color:#7d4646;
    font-size:14px;
    line-height:1.6;
}

.btn-row{
    display:flex;
    gap:14px;
    flex-wrap:wrap;
    margin-top:26px;
}

.btn{
    border:none;
    border-radius:16px;
    padding:15px 22px;
    font-size:15px;
    font-weight:700;
    cursor:pointer;
    transition:transform .15s ease, box-shadow .2s ease;
}

.btn:hover{
    transform:translateY(-1px);
}

.btn-primary{
    flex:1 1 240px;
    color:#fff;
    background:linear-gradient(135deg, #2f6a28 0%, #214b1a 100%);
    box-shadow:0 10px 22px rgba(33,75,26,0.18);
}

.btn-primary:hover{
    box-shadow:0 14px 26px rgba(33,75,26,0.24);
}

.btn-danger{
    flex:1 1 240px;
    color:#fff;
    background:linear-gradient(135deg, #cf5c5c 0%, #b34141 100%);
    box-shadow:0 10px 22px rgba(179,65,65,0.18);
}

.btn-danger:hover{
    box-shadow:0 14px 26px rgba(179,65,65,0.24);
}

.footer-note{
    margin-top:18px;
    font-size:12.5px;
    color:#667767;
    line-height:1.6;
    text-align:center;
}

@media (max-width: 768px){
    .stats,
    .form-grid{
        grid-template-columns:1fr;
    }

    .form-area{
        padding:20px;
    }

    .header{
        padding:24px 20px 22px;
    }

    .header h1{
        font-size:26px;
    }

    .btn-row{
        flex-direction:column;
    }
}

@media (max-width: 480px){
    body{
        padding:14px 10px;
    }

    .container{
        border-radius:22px;
    }

    .header h1{
        font-size:22px;
    }

    .header p{
        font-size:13px;
    }

    input, select, .btn{
        min-height:50px;
        font-size:14px;
    }

    .home-btn{
        width:38px;
        height:38px;
    }
}
</style>
</head>
<body>

<div class="page">
    <div class="container">
        <div class="header">
            <div class="header-top">
                <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
                    <i class="fa-solid fa-house"></i>
                </a>

                <div class="header-badge">
                    <i class="fa-solid fa-users"></i>
                    User Management
                </div>
            </div>

            <h1>Manage User Account</h1>
            <p>
                Update account details for user accounts or remove a selected user account from the system.
            </p>
        </div>

        <div class="form-area">

            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
            <?php endif; ?>

            <div class="stats">
                <div class="stat-box">
                    <div class="stat-label">User Accounts</div>
                    <div class="stat-value"><?php echo count($users); ?></div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Access Level</div>
                    <div class="stat-value">Admin Only</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Available Actions</div>
                    <div class="stat-value">Update / Delete</div>
                </div>
            </div>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="target_id">Select User Account</label>
                        <div class="input-wrap">
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
                            <span class="select-icon">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group full">
                        <div class="info-box">
                            <div class="info-box-title">Current Selected User Details</div>
                            <div class="info-row">
                                <strong>Current Username:</strong>
                                <span id="current_username">None</span>
                            </div>
                            <div class="info-row">
                                <strong>Current Email:</strong>
                                <span id="current_email_text">None</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new_username">New Username</label>
                        <input
                            type="text"
                            name="new_username"
                            id="new_username"
                            value="<?php echo htmlspecialchars($_POST['new_username'] ?? ''); ?>"
                            placeholder="Enter new username"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="new_email">New Email</label>
                        <input
                            type="email"
                            name="new_email"
                            id="new_email"
                            value="<?php echo htmlspecialchars($_POST['new_email'] ?? ''); ?>"
                            placeholder="Enter new email address"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-wrap">
                            <input type="password" name="new_password" id="new_password" placeholder="Enter new password">
                            <i class="fa-solid fa-eye-slash eye" onclick="togglePassword('new_password', this)"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-wrap">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                            <i class="fa-solid fa-eye-slash eye" onclick="togglePassword('confirm_password', this)"></i>
                        </div>
                    </div>

                    <div class="form-group full">
                        <div class="note">Leave password fields blank if you do not want to change the password.</div>
                    </div>
                </div>

                <div class="danger-box">
                    <h3><i class="fa-solid fa-triangle-exclamation"></i> Danger Zone</h3>
                    <p>
                        Deleting a user account permanently removes it from the system.
                        This action cannot be undone.
                    </p>
                </div>

                <div class="btn-row">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Update User Account
                    </button>

                    <button
                        type="submit"
                        name="delete"
                        class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this user account? This action cannot be undone.');"
                    >
                        <i class="fa-solid fa-trash"></i>
                        Delete User Account
                    </button>
                </div>

                <div class="footer-note">
                    Select a user account first. The form fields will auto-fill based on your selected user.
                </div>
            </form>
        </div>
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

window.addEventListener("load", updateUserInfo);
</script>

</body>
</html>