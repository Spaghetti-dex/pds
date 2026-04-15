<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$message = "";
$messageType = "";

// Adjust this if your session uses a different key
$currentUserId = $_SESSION['user_id'] ?? 0;

// Handle update
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

// Handle delete
if (isset($_POST['delete'])) {
    $id = (int)($_POST['user_id'] ?? 0);

    if ($id <= 0) {
        $message = "Please select an admin account to delete.";
        $messageType = "error";
    } else {
        $checkRole = $conn->prepare("SELECT id, username FROM users WHERE id = ? AND role = 'admin' LIMIT 1");
        $checkRole->bind_param("i", $id);
        $checkRole->execute();
        $roleResult = $checkRole->get_result();

        if ($roleResult->num_rows === 0) {
            $message = "Selected admin account not found.";
            $messageType = "error";
        } else {
            $adminRow = $roleResult->fetch_assoc();

            if ((int)$currentUserId === $id) {
                $message = "You cannot delete your own account.";
                $messageType = "error";
            } else {
                $countStmt = $conn->prepare("SELECT COUNT(*) AS total_admins FROM users WHERE role = 'admin'");
                $countStmt->execute();
                $countResult = $countStmt->get_result()->fetch_assoc();
                $countStmt->close();

                if ((int)$countResult['total_admins'] <= 1) {
                    $message = "Cannot delete the last admin account.";
                    $messageType = "error";
                } else {
                    $deleteStmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'admin'");
                    $deleteStmt->bind_param("i", $id);
                    $deleteStmt->execute();
                    $deleteStmt->close();

                    $message = "Admin account deleted successfully.";
                    $messageType = "success";
                }
            }
        }

        $checkRole->close();
    }
}

// Reload admin users after actions
$users = [];
$stmtUsers = $conn->prepare("SELECT id, username, email, role FROM users WHERE role = 'admin' ORDER BY username ASC");
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
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(120, 160, 110, 0.22), transparent 30%),
                radial-gradient(circle at bottom right, rgba(31, 74, 24, 0.20), transparent 28%),
                linear-gradient(135deg, #eef2ea 0%, #dde6d8 100%);
            padding: 28px 16px;
            color: #16311b;
        }

        .page {
            width: 100%;
            max-width: 760px;
            margin: 0 auto;
        }

        .card {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid rgba(32, 67, 29, 0.14);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(29, 49, 24, 0.12);
            backdrop-filter: blur(6px);
        }

        .header {
            position: relative;
            padding: 34px 28px 30px;
            background: linear-gradient(135deg, #214b1a 0%, #173714 100%);
            color: #fff;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 16px;
        }

        .home-btn {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.14);
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s ease;
            flex-shrink: 0;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.24);
            transform: translateY(-1px);
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            letter-spacing: 0.4px;
        }

        .header h1 {
            font-size: 32px;
            line-height: 1.2;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 15px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.88);
            max-width: 560px;
        }

        .content {
            padding: 28px;
        }

        .message {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 22px;
            padding: 15px 16px;
            border-radius: 16px;
            font-size: 14px;
            line-height: 1.6;
            border: 1px solid transparent;
        }

        .message i {
            margin-top: 2px;
            font-size: 16px;
        }

        .message.success {
            background: #edf7ec;
            color: #1d5a1f;
            border-color: #bfd8bc;
        }

        .message.error {
            background: #fff0f0;
            color: #a12828;
            border-color: #efc3c3;
        }

        .info-panel {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .info-box {
            background: #f4f7f2;
            border: 1px solid #d9e3d4;
            border-radius: 18px;
            padding: 16px 14px;
        }

        .info-box .label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #5d715f;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .info-box .value {
            font-size: 18px;
            font-weight: 700;
            color: #18351c;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 700;
            color: #1b351f;
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        input,
        select {
            width: 100%;
            min-height: 54px;
            border: 1.5px solid #c8d4c2;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 15px;
            outline: none;
            background: #fbfcfa;
            color: #1d2d20;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        input:focus,
        select:focus {
            border-color: #5e8a57;
            box-shadow: 0 0 0 4px rgba(94, 138, 87, 0.14);
            background: #fff;
        }

        select {
            appearance: none;
            cursor: pointer;
            padding-right: 46px;
        }

        .select-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #5f6f61;
            font-size: 14px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 50px;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #4a5c4c;
            font-size: 15px;
            cursor: pointer;
        }

        .helper-text {
            margin-top: 8px;
            font-size: 13px;
            line-height: 1.5;
            color: #627364;
        }

        .danger-box {
            margin-top: 26px;
            border: 1px solid #f0caca;
            background: linear-gradient(180deg, #fff8f8 0%, #fff2f2 100%);
            border-radius: 20px;
            padding: 18px;
        }

        .danger-box h3 {
            color: #962f2f;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .danger-box p {
            color: #7f4444;
            font-size: 14px;
            line-height: 1.6;
        }

        .btn-row {
            display: flex;
            gap: 14px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 16px;
            padding: 15px 22px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn-primary {
            flex: 1 1 240px;
            color: #fff;
            background: linear-gradient(135deg, #2f6a28 0%, #214b1a 100%);
            box-shadow: 0 10px 22px rgba(33, 75, 26, 0.18);
        }

        .btn-primary:hover {
            box-shadow: 0 14px 26px rgba(33, 75, 26, 0.24);
        }

        .btn-danger {
            flex: 1 1 240px;
            color: #fff;
            background: linear-gradient(135deg, #cf5c5c 0%, #b34141 100%);
            box-shadow: 0 10px 22px rgba(179, 65, 65, 0.18);
        }

        .btn-danger:hover {
            box-shadow: 0 14px 26px rgba(179, 65, 65, 0.24);
        }

        .footer-note {
            margin-top: 20px;
            font-size: 12.5px;
            color: #667767;
            line-height: 1.6;
            text-align: center;
        }

        @media (max-width: 768px) {
            .header {
                padding: 26px 20px 24px;
            }

            .content {
                padding: 20px;
            }

            .header h1 {
                font-size: 27px;
            }

            .info-panel,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .btn-row {
                flex-direction: column;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px 10px;
            }

            .card {
                border-radius: 22px;
            }

            .header h1 {
                font-size: 23px;
            }

            .header p {
                font-size: 14px;
            }

            input,
            select,
            .btn {
                min-height: 50px;
                font-size: 14px;
            }

            .home-btn {
                width: 40px;
                height: 40px;
            }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="card">
        <div class="header">
            <div class="header-top">
                <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
                    <i class="fa-solid fa-house"></i>
                </a>

                <div class="header-badge">
                    <i class="fa-solid fa-user-shield"></i>
                    Admin Settings
                </div>
            </div>

            <h1>Manage Admin Account</h1>
            <p>
                Update login details for an admin account or remove an admin account safely.
                Deleting your own account and deleting the last admin are both blocked.
            </p>
        </div>

        <div class="content">
            <?php if ($message !== ""): ?>
                <div class="message <?php echo $messageType; ?>">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
            <?php endif; ?>

            <div class="info-panel">
                <div class="info-box">
                    <div class="label">Admin Accounts</div>
                    <div class="value"><?php echo count($users); ?></div>
                </div>
                <div class="info-box">
                    <div class="label">Page Access</div>
                    <div class="value">Restricted</div>
                </div>
                <div class="info-box">
                    <div class="label">Actions</div>
                    <div class="value">Update / Delete</div>
                </div>
            </div>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group full">
                        <label for="user_id">Select Admin Account</label>
                        <div class="input-wrap">
                            <select name="user_id" id="user_id" required>
                                <option value="">Select Admin Account</option>
                                <?php foreach ($users as $u): ?>
                                    <option
                                        value="<?php echo $u['id']; ?>"
                                        data-username="<?php echo htmlspecialchars($u['username']); ?>"
                                        data-email="<?php echo htmlspecialchars($u['email']); ?>"
                                        <?php echo (isset($_POST['user_id']) && (int)$_POST['user_id'] === (int)$u['id']) ? 'selected' : ''; ?>
                                    >
                                        <?php echo htmlspecialchars($u['username'] . " (" . $u['email'] . ")"); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="select-icon">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            name="username"
                            id="username"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            placeholder="Enter username"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="Enter email address"
                            required
                        >
                    </div>

                    <div class="form-group full">
                        <label for="password">New Password</label>
                        <div class="password-wrap">
                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Leave blank if you do not want to change the password"
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password visibility">
                                <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                        <div class="helper-text">
                            Password will only be updated if you enter a new one.
                        </div>
                    </div>
                </div>

                <div class="danger-box">
                    <h3><i class="fa-solid fa-triangle-exclamation"></i> Danger Zone</h3>
                    <p>
                        Deleting an admin account permanently removes it from the system.
                        This action cannot be undone.
                    </p>
                </div>

                <div class="btn-row">
                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Update Admin Account
                    </button>

                    <button
                        type="submit"
                        name="delete"
                        class="btn btn-danger"
                        onclick="return confirm('Are you sure you want to delete this admin account? This action cannot be undone.');"
                    >
                        <i class="fa-solid fa-trash"></i>
                        Delete Admin Account
                    </button>
                </div>

                <div class="footer-note">
                    Select an admin account first. The username and email fields will auto-fill based on your selection.
                </div>
            </form>
        </div>
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

    if (!opt || !opt.value) {
        username.value = "";
        email.value = "";
        return;
    }

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