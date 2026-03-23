<?php
session_start();

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot.php");
    exit();
}
include "../config/database.php";

$msg = "";

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];

if (isset($_POST['verify'])) {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp)) {
        $msg = "Please enter the OTP.";
    } else {
        $stmt = $conn->prepare("SELECT id, reset_token, reset_expiry FROM users WHERE email = ? LIMIT 1");
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

<h2>Verify OTP</h2>
<?php if ($msg != "") echo "<p>$msg</p>"; ?>

<form method="POST">
    <input type="text" name="otp" maxlength="6" placeholder="Enter 6-digit OTP" required><br><br>
    <button type="submit" name="verify">Verify OTP</button>
</form>