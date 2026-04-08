<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";
$messageType = "";

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
        $messageType = "error";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageType = "error";
    } else {
        $checkRole = $conn->prepare("SELECT id FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
        $checkRole->bind_param("i", $id);
        $checkRole->execute();
        $roleResult = $checkRole->get_result();

        if ($roleResult->num_rows === 0) {
            $message = "Selected admin account not found.";
            $messageType = "error";
        } else {
            $check1 = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1");
            $check1->bind_param("si", $new_username, $id);
            $check1->execute();

            if ($check1->get_result()->num_rows > 0) {
                $message = "Username already exists.";
                $messageType = "error";
            } else {
                $check2 = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
                $check2->bind_param("si", $new_email, $id);
                $check2->execute();

                if ($check2->get_result()->num_rows > 0) {
                    $message = "Email already exists.";
                    $messageType = "error";
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
                    $messageType = "success";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admin Account</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #e9e9e9;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            overflow-x: hidden;
        }

        .wrapper {
            width: 100%;
            max-width: 650px;
            background: #f5f5f5;
            border: 3px solid #2d4725;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.08);
            margin: 20px 0;
        }

        .header {
            position: relative;
            background: linear-gradient(90deg, #1f4a18, #173714);
            color: #fff;
            text-align: center;
            padding: 28px 20px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
        }

        .home-btn {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            font-size: 16px;
            transition: 0.2s ease;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .content {
            padding: 30px 34px;
        }

        .message {
            margin-bottom: 18px;
            padding: 13px 15px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.success {
            background: #dde4da;
            color: #1f5f1f;
            border: 1px solid #b9c7b2;
        }

        .message.error {
            background: #f3dede;
            color: #8a1f1f;
            border: 1px solid #dfb2b2;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: #102d1a;
            margin-bottom: 8px;
        }

        input,
        select {    
            width: 100%;
            height: 52px;
            border: 2px solid #8e8e8e;
            border-radius: 14px;
            padding: 0 14px;
            font-size: 16px !important;
            line-height: 1;
            outline: none;
        }

        select {
            background: #dfd7b3;
        }

        .input-blue {
            background: #cfd8e7;
        }

        .input-yellow {
            background: #dfd7b3;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 46px;
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #333;
            cursor: pointer;
            font-size: 15px;
        }

        .helper-text {
            margin-top: -4px;
            margin-bottom: 18px;
            font-size: 14px;
            color: #333;
        }

        .btn {
            width: 50%;
            background: #98b38e;
            border: none;
            border-radius: 24px;
            padding: 14px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
            display: block;
            margin: 20px auto 0;
        }

        .btn:hover {
            background: #87a57d;
        }

        @media (max-width: 700px) {
            .wrapper {
                max-width: 95%;
            }

            .content {
                padding: 24px 20px;
            }

            .header h1 {
                font-size: 27px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .wrapper {
                border-radius: 20px;
                border-width: 2px;
            }

            .content {
                padding: 18px 14px;
            }

            .header {
                padding: 18px 14px;
            }

            .header h1 {
                font-size: 22px;
            }

            .home-btn {
                width: 34px;
                height: 34px;
                left: 10px;
                font-size: 14px;
            }

            input,
            select {
                height: auto;
                min-height: 48px;
                padding: 10px 12px;
                font-size: 15px !important;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="header">
        <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
            <i class="fa-solid fa-house"></i>
        </a>
        <h1>Manage Admin Account</h1>
    </div>

    <div class="content">
        <?php if ($message != ""): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="user_id">Select Admin Account</label>
                <select name="user_id" id="user_id" required>
                    <option value="">Select Admin Account</option>
                    <?php foreach ($users as $u): ?>
                        <option
                            value="<?php echo $u['id']; ?>"
                            data-username="<?php echo htmlspecialchars($u['username']); ?>"
                            data-email="<?php echo htmlspecialchars($u['email']); ?>"
                            <?php echo (isset($_POST['user_id']) && $_POST['user_id'] == $u['id']) ? 'selected' : ''; ?>
                        >
                            <?php echo htmlspecialchars($u['username'] . " (" . $u['email'] . ")"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="username">New Username</label>
                <input
                    type="text"
                    name="username"
                    id="username"
                    class="input-blue"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="email">New Email</label>
                <input
                    type="email"
                    name="email"
                    id="email"
                    class="input-yellow"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="input-blue"
                    >
                    <span class="toggle-password" onclick="togglePassword()">
                        <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
            </div>

            <div class="helper-text">
                Leave password blank if you do not want to change it.
            </div>

            <button type="submit" name="update" class="btn">Update Admin Account</button>
        </form>
    </div>
</div>

<script>
const select = document.getElementById("user_id");
const username = document.getElementById("username");
const email = document.getElementById("email");

function fillFieldsFromSelected() {
    const opt = select.options[select.selectedIndex];
    if (!opt || !opt.value) return;

    if (!username.value) {
        username.value = opt.getAttribute("data-username") || "";
    }

    if (!email.value) {
        email.value = opt.getAttribute("data-email") || "";
    }
}

select.addEventListener("change", function () {
    const opt = this.options[this.selectedIndex];
    username.value = opt.getAttribute("data-username") || "";
    email.value = opt.getAttribute("data-email") || "";
});

window.addEventListener("load", fillFieldsFromSelected);

function togglePassword() {
    const input = document.getElementById("password");
    const icon = document.getElementById("eyeIcon");

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
</script>

</body>
</html>