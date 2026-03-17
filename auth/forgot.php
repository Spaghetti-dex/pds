<?php
session_start();
include "../config/database.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "../vendor/autoload.php";

$msg = "";

if(isset($_POST['send'])){

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0){

        $otp = rand(100000,999999);
        $expire = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $stmt2 = $conn->prepare("UPDATE users SET otp_code=?, otp_expire=? WHERE email=?");
        $stmt2->bind_param("sss",$otp,$expire,$email);
        $stmt2->execute();

        $_SESSION['reset_email'] = $email;

        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "mayanicharles10@gmail.com";
            $mail->Password = "rhhdhvkaajghymme";
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom("YOUR_GMAIL@gmail.com","PDS System");
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Your OTP Code";
            $mail->Body = "<h2>Your OTP is: $otp</h2><p>Valid for 5 minutes.</p>";

            $mail->send();

            header("Location: verify_otp.php");
            exit;

        }catch(Exception $e){
            $msg = "Mailer error.";
        }

    }else{
        $msg = "Email not found.";
    }
}
?>

<h2>Forgot Password</h2>
<?php if($msg!=""){ echo $msg; } ?>

<form method="POST">
<input type="email" name="email" placeholder="Enter email"><br><br>
<button name="send">Send OTP</button>
</form>