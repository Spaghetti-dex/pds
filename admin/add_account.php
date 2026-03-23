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

if (isset($_POST['create'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif (!in_array($role, ['admin', 'user'])) {
        $message = "Invalid role selected.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
        $check->bind_param("ss", $username, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "Username or email already exists.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hash, $role);
        if ($stmt->execute()) {
                $message = '
                    <div class="success-content">
                        <span class="hourglass">⌛</span>
                        <span>
                            Account created successfully.<br>
                            Redirecting in <strong id="countdown">3</strong> seconds...
                        </span>
                    </div>

                    <script>
                        let timeLeft = 3;
                        const countdownEl = document.getElementById("countdown");

                        const timer = setInterval(function () {
                            timeLeft--;

                            if (countdownEl) {
                                countdownEl.textContent = timeLeft;
                            }

                            if (timeLeft <= 0) {
                                clearInterval(timer);
                                window.location.href = "../dashboard/dashboard.php";
                            }
                        }, 1000);
                    </script>
                ';
            } else {
                $message = "Error creating account.";
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
<title>Add Account</title>
    <style>
        body{
                font-family:Arial, Helvetica, sans-serif;
                background:#f5f5f5;
                padding:40px;
            }
            .container{
                width:400px;
                margin:auto;
                background:white;
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
            }
            button{
                padding:10px 20px;
                border:none;
                background:#2f3f28;
                color:white;
                border-radius:8px;
                cursor:pointer;
            }
            button:hover{
                background:#44573c;
            }
            .message{
                margin-bottom:15px;
                color:#c00;
                font-weight:bold;
            }
            .success{
                color:green;
            }

            .success{
                color: #1f5f1f;
                background: #eef8ea;
                border: 1px solid #b7d7b0;
                padding: 12px;
                border-radius: 10px;
                margin-bottom: 15px;
            }

            .success-content{
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .hourglass{
                font-size: 24px;
                animation: flipGlass 1s infinite;
            }

            #countdown{
                font-size: 16px;
                font-weight: bold;
            }

            @keyframes flipGlass{
                0%{ transform: rotate(0deg); }
                50%{ transform: rotate(180deg); }
                100%{ transform: rotate(360deg); }
            }
    </style>
</head>
     <body>

        <div class="container">
            <h2>Add Account</h2>

            <?php if ($message != ""): ?>
            <div class="message success">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <label>Username</label>
                <input type="text" name="username" required>

                <label>Email</label>
                <input type="email" name="email" required>

                <label>Password</label>
                <input type="password" name="password" required>

                <label>Role</label>
                <select name="role">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
              </select>

         <button type="submit" name="create">Create Account</button>
    </form>
</div>

</body>
</html>