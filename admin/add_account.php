<?php
session_start();
require_once "../includes/admin_check.php";
include "../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$message = "";
$messageType = "";

if (isset($_POST['create'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (strlen($username) < 3) {
        $message = "Username must be at least 3 characters.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = "error";
    } elseif (!in_array($role, ['user', 'admin'], true)) {
        $message = "Invalid role selected.";
        $messageType = "error";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or email already exists.";
            $messageType = "error";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hash, $role);

            if ($stmt->execute()) {
                $messageType = "success";
                $message = "Account created successfully!";

                // Clear form after success
                $_POST = [];
            } else {
                $message = "Error creating account.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>

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
                radial-gradient(circle at top left, rgba(116, 163, 112, 0.22), transparent 30%),
                radial-gradient(circle at bottom right, rgba(34, 54, 30, 0.18), transparent 28%),
                linear-gradient(135deg, #eef2ea 0%, #dfe8da 100%);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 28px 14px;
            color: #1f2f1e;
        }

        .page {
            width: 100%;
            max-width: 760px;
        }

        .wrapper {
            width: 100%;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(34, 54, 30, 0.14);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(6px);
        }

        .header {
            position: relative;
            background: linear-gradient(135deg, #22361e 0%, #2f4b2b 100%);
            color: #fff;
            padding: 30px 24px 26px;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
        }

        .home-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: 0.25s;
            flex-shrink: 0;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .header h1 {
            font-size: 31px;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .header p {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.88);
            max-width: 580px;
        }

        .content {
            padding: 26px;
        }

        .message {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
            border: 1px solid transparent;
            font-weight: 600;
        }

        .message i {
            margin-top: 2px;
        }

        .message.success {
            background: #edf7ec;
            color: #1d5a1f;
            border-color: #bfd8bc;
        }

        .message.error {
            background: #fff1f1;
            color: #a12828;
            border-color: #efc5c5;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: #f5f8f3;
            border: 1px solid #d9e4d5;
            border-radius: 18px;
            padding: 16px 14px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #607260;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 700;
            color: #1a341d;
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
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #22361e;
            font-size: 14px;
        }

        .input-wrap {
            position: relative;
        }

        input,
        select {
            width: 100%;
            min-height: 54px;
            padding: 14px 16px;
            border: 1.5px solid #c8d4c2;
            border-radius: 16px;
            background: #fbfcfa;
            font-size: 15px;
            color: #203120;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #5f8a59;
            box-shadow: 0 0 0 4px rgba(95, 138, 89, 0.14);
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
            color: #607260;
            pointer-events: none;
            font-size: 14px;
        }

        .password-wrap {
            position: relative;
        }

        .password-wrap input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #4c5e4d;
            font-size: 15px;
            border: none;
            background: transparent;
        }

        .toggle-password:hover {
            color: #000;
        }

        .helper-box {
            background: linear-gradient(180deg, #f7faf5 0%, #eff5ec 100%);
            border: 1px solid #d5e1d1;
            border-radius: 18px;
            padding: 16px;
            margin-bottom: 2px;
        }

        .helper-box-title {
            font-size: 13px;
            font-weight: 700;
            color: #547054;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 10px;
        }

        .helper-row {
            font-size: 14px;
            color: #253625;
            line-height: 1.6;
        }

        .btn-row {
            display: flex;
            justify-content: center;
            margin-top: 26px;
        }

        .btn {
            border: none;
            border-radius: 16px;
            padding: 15px 24px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .2s ease;
            min-width: 240px;
            color: #fff;
            background: linear-gradient(135deg, #2f6a28 0%, #214b1a 100%);
            box-shadow: 0 10px 22px rgba(33, 75, 26, 0.18);
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 26px rgba(33, 75, 26, 0.24);
        }

        .footer-note {
            margin-top: 18px;
            font-size: 12.5px;
            color: #667767;
            line-height: 1.6;
            text-align: center;
        }

        @media (max-width: 768px) {
            .stats,
            .form-grid {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 20px;
            }

            .header {
                padding: 24px 20px 22px;
            }

            .header h1 {
                font-size: 26px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 14px 10px;
            }

            .wrapper {
                border-radius: 22px;
            }

            .header h1 {
                font-size: 22px;
            }

            .header p {
                font-size: 13px;
            }

            input,
            select,
            .btn {
                min-height: 50px;
                font-size: 14px;
            }

            .home-btn {
                width: 38px;
                height: 38px;
            }

            .btn {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="page">
    <div class="wrapper">
        <div class="header">
            <div class="header-top">
                <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
                    <i class="fa-solid fa-house"></i>
                </a>

                <div class="header-badge">
                    <i class="fa-solid fa-user-plus"></i>
                    Account Creation
                </div>
            </div>

            <h1>Add User Account</h1>
            <p>
                Create a new account for a user or admin. Fill in the account details below and choose the correct role before saving.
            </p>
        </div>

        <div class="content">
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <i class="fa-solid <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <div><?php echo htmlspecialchars($message); ?></div>
                </div>
            <?php endif; ?>

            <div class="stats">
                <div class="stat-box">
                    <div class="stat-label">Action</div>
                    <div class="stat-value">Create</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Access Level</div>
                    <div class="stat-value">Admin Only</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Roles</div>
                    <div class="stat-value">User / Admin</div>
                </div>
            </div>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                            placeholder="Enter username"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            placeholder="Enter email address"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter password"
                                required
                            >
                            <button type="button" class="toggle-password" onclick="togglePassword()" aria-label="Toggle password visibility">
                                <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <div class="input-wrap">
                            <select name="role" id="role">
                                <option value="user" <?php echo (($_POST['role'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                            <span class="select-icon">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group full">
                        <div class="helper-box">
                            <div class="helper-box-title">Account Creation Notes</div>
                            <div class="helper-row">
                                Password must be at least 6 characters long. Username and email must be unique. Choose <strong>User</strong> for regular accounts and <strong>Admin</strong> for administrator access.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="btn-row">
                    <button type="submit" name="create" class="btn">
                        <i class="fa-solid fa-plus"></i>
                        Create Account
                    </button>
                </div>

                <div class="footer-note">
                    Double-check the selected role before creating the account.
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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