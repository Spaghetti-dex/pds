<?php
session_start();
include "../config/database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

$msg = "";

if (isset($_POST['send'])) {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $msg = "Please enter your email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            $otp = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
            $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            $stmt2 = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $stmt2->bind_param("ssi", $otp, $expiry, $user['id']);

            if ($stmt2->execute()) {
                $_SESSION['reset_email'] = $email;
                unset($_SESSION['otp_verified']);

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = "smtp.gmail.com";
                    $mail->SMTPAuth = true;
                    $mail->Username = $_ENV['USERMAIL'];
                    $mail->Password = $_ENV['MAILER'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($_ENV['USERMAIL'], "PDS System");
                    $mail->addAddress($email, $user['username']);

                    $mail->isHTML(true);
                    $mail->Subject = "Password Reset One-Time Passcode (OTP)";
                    $mail->Body = "
                    <html>
                    <body style='font-family:Arial, Helvetica, sans-serif; background:#f4f4f4; padding:30px;'>
                        <div style='max-width:600px; margin:auto; background:#fff; border:1px solid #ddd;'>
                            <div style='background:#22361e; color:#fff; padding:20px; text-align:center; font-size:22px; font-weight:bold;'>
                                Password Reset Verification
                            </div>
                            <div style='padding:30px; color:#333; font-size:15px; line-height:1.6;'>
                                <p>Good day, <strong>" . htmlspecialchars($user['username']) . "</strong>.</p>
                                <hr>
                                <p>We received a request to reset your account password. Please use the One-Time Passcode (OTP) below to continue:</p>
                                <div style='text-align:center; margin:25px 0;'>
                                    <span style='display:inline-block; padding:14px 28px; font-size:30px; letter-spacing:6px; font-weight:bold; color:#22361e; border:2px dashed #22361e; background:#f8fdf8;'>
                                        {$otp}
                                    </span>
                                </div>
                                <p>This OTP will expire in <strong>10 minutes</strong>.</p>
                                <hr>
                                <p>If you did not request a password reset, you may safely ignore this email.</p>
                                <p>Sincerely,<br><strong>PDS System</strong></p>
                            </div>
                        </div>
                    </body>
                    </html>
                    ";

                    $mail->send();

                    header("Location: verify_otp.php");
                    exit;
                } catch (Exception $e) {
                    $msg = "Mailer error: " . $mail->ErrorInfo;
                }
            } else {
                $msg = "Failed to save OTP.";
            }

            $stmt2->close();
        } else {
            $msg = "Email not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Arial, Helvetica, sans-serif;}
body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background-image:url('../assets/background.jpg');
    background-size:cover;
}
.box{
    width:420px;
    background:#d9d9dd;
    border:3px solid black;
    border-radius:25px;
    overflow:hidden;
    text-align:center;
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}
.header{
    background:#2f3f28;
    color:white;
    padding:40px 0 60px 0;
    font-size:26px;
    font-weight:bold;
    position:relative;
}
.header::after{
    content:"";
    position:absolute;
    bottom:-25px;
    left:0;
    width:100%;
    height:60px;
    background:#d9d9dd;
    border-top-left-radius:50% 40px;
    border-top-right-radius:50% 40px;
}
.form-area{padding:40px;}
.text{margin-bottom:18px;font-size:14px;color:#333;line-height:1.5;}
input{
    width:100%;
    padding:12px;
    border-radius:8px;
    border:2px solid #888;
    background:#efe8c2;
    margin-bottom:18px;
}
button{
    padding:10px 25px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
}
.message{margin-bottom:15px;font-weight:bold;color:#b00020;}
</style>
</head>
<body>
<div class="box">
    <div class="header">FORGOT PASSWORD</div>
    <div class="form-area">
        <div class="text">Enter your email address and we will send a 6-digit OTP.</div>

        <?php if ($msg != ""): ?>
            <div class="message"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Enter email" required>
            <button type="submit" name="send">Send OTP</button>
        </form>
    </div>
</div>
</body>
</html>