<?php
session_start();
include "../config/database.php";

$msg = "";

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot.php");
    exit;
}

$email = $_SESSION['reset_email'];

if (isset($_POST['verify'])) {
    $otp = trim(
        ($_POST['otp1'] ?? '') .
        ($_POST['otp2'] ?? '') .
        ($_POST['otp3'] ?? '') .
        ($_POST['otp4'] ?? '') .
        ($_POST['otp5'] ?? '') .
        ($_POST['otp6'] ?? '')
    );

    if (strlen($otp) !== 6 || !ctype_digit($otp)) {
        $msg = "Please enter the complete 6-digit OTP.";
    } else {
        $stmt = $conn->prepare("SELECT reset_token, reset_expiry FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if ($user['reset_token'] === $otp) {
                if (strtotime($user['reset_expiry']) >= time()) {
                    $_SESSION['otp_verified'] = true;
                    header("Location: reset.php");
                    exit;
                } else {
                    $msg = "OTP has expired.";
                }
            } else {
                $msg = "Invalid OTP.";
            }
        } else {
            $msg = "User not found.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Verify OTP</title>
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
    width:460px;
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
.form-area{padding:40px 30px;}
.text{margin-bottom:18px;font-size:14px;color:#333;line-height:1.5;}
.message{margin-bottom:15px;font-weight:bold;color:#b00020;}
.otp-group{
    display:flex;
    justify-content:center;
    gap:10px;
    margin-bottom:22px;
}
.otp-input{
    width:48px;
    height:58px;
    text-align:center;
    font-size:24px;
    font-weight:bold;
    border:2px solid #888;
    border-radius:999px;
    background:#efe8c2;
}
button{
    padding:10px 25px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
}
</style>
</head>
<body>
<div class="box">
    <div class="header">VERIFY OTP</div>
    <div class="form-area">
        <div class="text">Enter the 6-digit OTP sent to your email.</div>

        <?php if ($msg != ""): ?>
            <div class="message"><?php echo htmlspecialchars($msg); ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="otp-group">
                <input type="text" name="otp1" class="otp-input" maxlength="1" required>
                <input type="text" name="otp2" class="otp-input" maxlength="1" required>
                <input type="text" name="otp3" class="otp-input" maxlength="1" required>
                <input type="text" name="otp4" class="otp-input" maxlength="1" required>
                <input type="text" name="otp5" class="otp-input" maxlength="1" required>
                <input type="text" name="otp6" class="otp-input" maxlength="1" required>
            </div>
            <button type="submit" name="verify">Verify OTP</button>
        </form>
    </div>
</div>

<script>
const inputs = document.querySelectorAll(".otp-input");
inputs.forEach((input, index) => {
    input.addEventListener("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
        if (this.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener("keydown", function (e) {
        if (e.key === "Backspace" && this.value === "" && index > 0) {
            inputs[index - 1].focus();
        }
    });
});
</script>
</body>
</html>