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

    if ($target_id <= 0 || $new_username === '') {
        $message = "Please fill in all fields.";
    } elseif (strlen($new_username) < 3) {
        $message = "Username must be at least 3 characters.";
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
                $stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = ? AND role = 'user'");
                $stmt->bind_param("si", $new_username, $target_id);

                if ($stmt->execute()) {
                    $message = ($stmt->affected_rows > 0) ? "Username updated successfully." : "No changes were made.";
                } else {
                    $message = "Error updating username.";
                }

                $stmt->close();
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
<title>Change User Username</title>
<style>
body{font-family:Arial, Helvetica, sans-serif;background:#f5f5f5;padding:40px;}
.container{width:460px;margin:auto;background:#fff;padding:25px;border-radius:12px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
input, select{width:100%;padding:10px;margin-top:8px;margin-bottom:15px;border:1px solid #ccc;border-radius:8px;box-sizing:border-box;}
button{padding:10px 20px;border:none;background:#22361e;color:#fff;border-radius:8px;cursor:pointer;}
.message{margin-bottom:15px;font-weight:bold;}
.info-box{background:#f3f7f2;border:1px solid #cfd9cc;border-radius:10px;padding:12px;margin-bottom:15px;}
.info-row{margin-bottom:8px;font-size:14px;}
.info-label{font-weight:bold;color:#22361e;}
</style>
</head>
<body>
<div class="container">
    <h2>Change User Username</h2>

    <?php if ($message !== ""): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
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
                >
                    <?php echo htmlspecialchars($user['username'] . " (" . $user['email'] . ")"); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Current Username:</span>
                <span id="selected_username">None</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span id="selected_email">None</span>
            </div>
        </div>

        <label>New Username</label>
        <input type="text" name="new_username" required>

        <button type="submit" name="update">Update Username</button>
    </form>
</div>

<script>
const userSelect = document.getElementById("target_id");
const selectedUsername = document.getElementById("selected_username");
const selectedEmail = document.getElementById("selected_email");

function updateUserInfo() {
    const selectedOption = userSelect.options[userSelect.selectedIndex];
    if (selectedOption && selectedOption.value !== "") {
        selectedUsername.textContent = selectedOption.getAttribute("data-username") || "";
        selectedEmail.textContent = selectedOption.getAttribute("data-email") || "";
    } else {
        selectedUsername.textContent = "None";
        selectedEmail.textContent = "None";
    }
}
userSelect.addEventListener("change", updateUserInfo);
updateUserInfo();
</script>
</body>
</html>