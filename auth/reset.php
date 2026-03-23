<?php
session_start();


include "../config/database.php";

$message = "";

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];

if (isset($_POST['update'])) {
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($new) || empty($confirm)) {
        $message = "Please fill in all fields.";
    } elseif (strlen($new) < 6) {
        $message = "Password must be at least 6 characters.";
    } elseif ($new !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?, reset_token = NULL, reset_expiry = NULL
            WHERE email = ?
        ");
        $stmt->bind_param("ss", $hash, $email);

        if ($stmt->execute()) {
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);
            $message = "Password successfully reset.";
        } else {
            $message = "Error updating password.";
        }

        $stmt->close();
    }
}
?>

<h2>Reset Password</h2>
<?php if ($message != "") echo "<p>$message</p>"; ?>

<form method="POST">
    New Password<br>
    <input type="password" name="new_password" required><br><br>

    Confirm Password<br>
    <input type="password" name="confirm_password" required><br><br>

    <button type="submit" name="update">Update Password</button>
</form>