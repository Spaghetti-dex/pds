<?php
session_start();
if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot.php");
    exit();
}
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
            $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

            $stmt2 = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?");
            $stmt2->bind_param("ssi", $otp, $expiry, $user['id']);

            if ($stmt2->execute()) {
                $_SESSION['reset_email'] = $email;

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
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset='UTF-8'>
                    </head>
                    <body style='margin:0; padding:0; background-color:#f4f4f4; font-family:Arial, Helvetica, sans-serif;'>
                        <table width='100%' cellpadding='0' cellspacing='0' border='0' style='background-color:#f4f4f4; padding:30px 0;'>
                            <tr>
                                <td align='center'>
                                    <table width='600' cellpadding='0' cellspacing='0' border='0' style='background-color:#ffffff; border:1px solid #dcdcdc; border-collapse:collapse;'>

                                        <tr>
                                            <td style='background-color:#22361e; color:#ffffff; padding:20px; text-align:center; font-size:22px; font-weight:bold;'>
                                                Password Reset Verification
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:25px 30px 10px 30px; color:#333333; font-size:15px; line-height:1.6;'>
                                                Good day, <strong>" . htmlspecialchars($user['username']) . "</strong>.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:0 30px 15px 30px;'>
                                                <hr style='border:0; border-top:1px solid #d9d9d9;'>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:0 30px 10px 30px; color:#333333; font-size:15px; line-height:1.6;'>
                                                We received a request to reset your account password. Please use the One-Time Passcode (OTP) below to continue:
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align='center' style='padding:15px 30px;'>
                                                <div style='display:inline-block; padding:14px 28px; font-size:30px; letter-spacing:6px; font-weight:bold; color:#22361e; border:2px dashed #22361e; background-color:#f8fdf8;'>
                                                    {$otp}
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:10px 30px 10px 30px; color:#333333; font-size:15px; line-height:1.6;'>
                                                This OTP will expire in <strong>10 minutes</strong>.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:0 30px 15px 30px;'>
                                                <hr style='border:0; border-top:1px solid #d9d9d9;'>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:0 30px 10px 30px; color:#555555; font-size:14px; line-height:1.6;'>
                                                If you did not request a password reset, you may safely ignore this email. No changes will be made to your account unless this OTP is used.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style='padding:20px 30px 30px 30px; color:#555555; font-size:14px; line-height:1.6;'>
                                                Sincerely,<br>
                                                <strong>PDS System</strong>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
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

<h2>Forgot Password</h2>
<?php if ($msg != "") echo "<p>$msg</p>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter email" required><br><br>
    <button type="submit" name="send">Send OTP</button>
</form>