<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";

// Load users for dropdown
$users = [];
$userQuery = $conn->query("SELECT id, username, email FROM users ORDER BY username ASC");
if ($userQuery) {
    while ($row = $userQuery->fetch_assoc()) {
        $users[] = $row;
    }
}

if (isset($_POST['update'])) {
    $target_id = (int)($_POST['target_id'] ?? 0);
    $new_email = trim($_POST['new_email'] ?? '');

    if ($target_id <= 0 || $new_email === '') {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        $checkUser = $conn->prepare("SELECT id FROM users WHERE id = ? LIMIT 1");
        $checkUser->bind_param("i", $target_id);
        $checkUser->execute();
        $userResult = $checkUser->get_result();

        if ($userResult->num_rows === 0) {
            $message = "Selected user not found.";
        } else {
            $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
            $checkEmail->bind_param("si", $new_email, $target_id);
            $checkEmail->execute();
            $emailResult = $checkEmail->get_result();

            if ($emailResult->num_rows > 0) {
                $message = "Email is already in use by another account.";
            } else {
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->bind_param("si", $new_email, $target_id);

                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        $message = "Email updated successfully.";
                    } else {
                        $message = "No changes were made.";
                    }
                } else {
                    $message = "Error updating email.";
                }

                $stmt->close();
            }

            $checkEmail->close();
        }

        $checkUser->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Change User Email</title>
<style>
body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f5f5f5;
    padding:40px;
}
.container{
    width:460px;
    margin:auto;
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
input, select{
    width:100%;
    padding:10px;
    margin-top:8px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:8px;
    box-sizing:border-box;
}
button{
    padding:10px 20px;
    border:none;
    background:#22361e;
    color:#fff;
    border-radius:8px;
    cursor:pointer;
}
button:hover{
    background:#2f4a28;
}
.message{
    margin-bottom:15px;
    font-weight:bold;
}
.info-box{
    background:#f3f7f2;
    border:1px solid #cfd9cc;
    border-radius:10px;
    padding:12px;
    margin-bottom:15px;
}
.info-row{
    margin-bottom:8px;
    font-size:14px;
}
.info-label{
    font-weight:bold;
    color:#22361e;
}
.readonly-input{
    background:#f2f2f2;
}
</style>
</head>
<body>
<div class="container">
    <h2>Change User Email</h2>

    <?php if ($message !== ""): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Select User</label>
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
                <span class="info-label">Selected Username:</span>
                <span id="selected_username">None</span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Email:</span>
            </div>
            <input type="text" id="current_email" class="readonly-input" readonly value="">
        </div>

        <label>New Email</label>
        <input type="email" name="new_email" required value="<?php echo htmlspecialchars($_POST['new_email'] ?? ''); ?>">

        <button type="submit" name="update">Update Email</button>
    </form>
</div>

<script>
const userSelect = document.getElementById("target_id");
const selectedUsername = document.getElementById("selected_username");
const currentEmail = document.getElementById("current_email");

function updateUserInfo() {
    const selectedOption = userSelect.options[userSelect.selectedIndex];

    if (selectedOption && selectedOption.value !== "") {
        selectedUsername.textContent = selectedOption.getAttribute("data-username") || "";
        currentEmail.value = selectedOption.getAttribute("data-email") || "";
    } else {
        selectedUsername.textContent = "None";
        currentEmail.value = "";
    }
}

userSelect.addEventListener("change", updateUserInfo);
updateUserInfo();
</script>
</body>
</html>