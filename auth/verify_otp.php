<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include "../config/database.php";

$msg = "";
$success = false;

/* prevent direct access */
if(!isset($_SESSION['reset_email'])){
    echo "Session expired. Please request OTP again.";
    exit;
}

$email = $_SESSION['reset_email'];

if(isset($_POST['verify'])){

    $otp = trim($_POST['otp']);
    $new = trim($_POST['password']);

    if($otp == "" || $new == ""){
        $msg = "All fields required.";
    }
    else{

        $stmt = $conn->prepare("SELECT otp_code, otp_expire FROM users WHERE email=?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $stmt->store_result();

        if($stmt->num_rows == 0){
            $msg = "User not found.";
        }
        else{
            $stmt->bind_result($db_otp,$expire);
            $stmt->fetch();
        }

        $stmt->close();

        if($msg == ""){

            if($otp != $db_otp){
                $msg = "Invalid OTP.";
            }
            elseif(strtotime($expire) < time()){
                $msg = "OTP expired.";
            }
            else{

                $hash = password_hash($new,PASSWORD_DEFAULT);

                $stmt2 = $conn->prepare("UPDATE users 
                    SET password=?, otp_code=NULL, otp_expire=NULL 
                    WHERE email=?");

                $stmt2->bind_param("ss",$hash,$email);

                if($stmt2->execute()){
                    session_destroy();
                    $msg = "Password reset successful.";
                    $success = true;
                }else{
                    $msg = "Update failed.";
                }

                $stmt2->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Verify OTP</title>
</head>
<body>

<h2>Verify OTP</h2>

<?php if($msg!=""){ ?>
    <p><?php echo $msg; ?></p>
<?php } ?>

<?php if($success){ ?>
    <p>Redirecting to login in <span id="count">3</span> seconds...</p>

    <script>
        let c = 3;
        let timer = setInterval(function(){

            c--;
            document.getElementById("count").innerHTML = c;

            if(c <= 0){
                clearInterval(timer);
                window.location.href = "/pds/auth/login.php";
            }

        },1000);
    </script>
<?php } ?>

<form method="POST">

OTP Code<br>
<input type="text" name="otp"><br><br>

New Password<br>
<input type="password" name="password"><br><br>

<button name="verify">Reset Password</button>

</form>

</body>
</html>