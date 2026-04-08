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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
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
    <title>Add Account</title>

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
            max-width: 600px;
            background: #f5f5f5;
            border: 3px solid #2d4725;
            border-radius: 28px;
            overflow: hidden;
            margin: 20px 0;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        .header {
            position: relative;
            background: linear-gradient(90deg, #1f4a18, #173714);
            color: #fff;
            text-align: center;
            padding: 24px 18px;
        }

        .header h1 {
            font-size: 28px;
            line-height: 1.2;
            padding: 0 45px;
        }

        .home-btn {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.14);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.28);
        }

        .content {
            padding: 25px 30px;
        }

        .message {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message.success {
            background: #dde4da;
            color: #1f5f1f;
        }

        .message.error {
            background: #f3dede;
            color: #8a1f1f;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 6px;
            font-size: 15px;
        }

        input,
        select {
            width: 100%;
            min-height: 48px;
            padding: 12px;
            border-radius: 10px;
            border: 2px solid #8e8e8e;
            outline: none;
            font-size: 16px;
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
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #333;
            font-size: 15px;
        }

        .btn {
            width: 100%;
            max-width: 220px;
            background: #98b38e;
            border: none;
            padding: 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.2s ease;
            display: block;
            margin: 20px auto 0;
        }

        .btn:hover {
            background: #87a57d;
        }

        @media (max-width: 768px) {
            body {
                padding: 14px;
            }

            .wrapper {
                border-radius: 22px;
                margin: 10px 0;
            }

            .content {
                padding: 22px 20px;
            }

            .header {
                padding: 20px 16px;
            }

            .header h1 {
                font-size: 24px;
                padding: 0 42px;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .wrapper {
                border-width: 2px;
                border-radius: 18px;
            }

            .header {
                padding: 18px 14px;
            }

            .header h1 {
                font-size: 20px;
                padding: 0 38px;
            }

            .home-btn {
                width: 34px;
                height: 34px;
                left: 10px;
                font-size: 14px;
            }

            .content {
                padding: 18px 14px;
            }

            label {
                font-size: 14px;
            }

            input,
            select {
                min-height: 46px;
                font-size: 15px;
                padding: 10px 12px;
            }

            .btn {
                width: 100%;
                max-width: 100%;
                padding: 13px;
                font-size: 15px;
                border-radius: 14px;
            }

            .message {
                font-size: 13px;
                padding: 10px;
            }
        }

        @media (max-width: 360px) {
            .header h1 {
                font-size: 18px;
            }

            .content {
                padding: 14px 12px;
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
        <h1>Add User Account</h1>
    </div>

    <div class="content">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    class="input-blue"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    class="input-yellow"
                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="input-blue"
                        required
                    >
                    <span class="toggle-password" onclick="togglePassword()">
                        <i id="eyeIcon" class="fa-solid fa-eye-slash"></i>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="input-yellow">
                    <option value="user" <?php echo (($_POST['role'] ?? '') === 'user') ? 'selected' : ''; ?>>User</option>
                    <option value="admin" <?php echo (($_POST['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
            </div>

            <button type="submit" name="create" class="btn">Create Account</button>
        </form>
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