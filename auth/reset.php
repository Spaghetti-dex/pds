<?php
session_start();
include "../config/database.php";

$message = "";
$success = false;

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot.php");
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
            $success = true;
        } else {
            $message = "Error updating password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reset Password</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('../assets/background.jpg');
    background-size: cover;
}

.box{
    width:420px;
    background:#d9d9dd;
    border:3px solid black;
    border-radius:25px;
    overflow:hidden;
    text-align:center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

.header{
    background:#2f3f28;
    color:white;
    padding:40px 0 60px 0;
    font-size:24px;
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

.form{
    padding:40px;
}

.form-group{
    margin-bottom:20px;
    text-align:left;
}

input{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:2px solid #888;
    background:#efe8c2;
}

button{
    margin-top:10px;
    padding:10px 25px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#789c78;
}

.password-box{
    position: relative;
}

.password-box input{
    padding-right:40px;
}

.eye{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
}

.message{
    margin-bottom:10px;
    font-weight:bold;
    color:#b00020;
}

.success-box{
    margin-top:20px;
    padding:15px;
    background:#e6f5e6;
    border:2px solid #2f3f28;
    border-radius:10px;
    color:#1d4d1d;
    font-weight:bold;
}

.hourglass{
    font-size:30px;
    display:block;
    margin-bottom:10px;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    0%{ transform:rotate(0deg); }
    100%{ transform:rotate(360deg); }
}
</style>
</head>

<body>

<div class="box">

    <div class="header">
        RESET PASSWORD
    </div>

    <div class="form">

        <?php if ($success): ?>

            <div class="success-box">
                <span class="hourglass">⏳</span>
                <div><?php echo htmlspecialchars($message); ?></div>
                <div>Redirecting in <span id="count">3</span> seconds...</div>
            </div>

        <?php else: ?>

            <?php if ($message != ""): ?>
                <div class="message"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label>New Password</label>
                    <div class="password-box">
                        <input type="password" name="new_password" id="new_password" required>
                        <i class="fa-solid fa-eye-slash eye" onclick="toggle('new_password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="password-box">
                        <input type="password" name="confirm_password" id="confirm_password" required>
                        <i class="fa-solid fa-eye-slash eye" onclick="toggle('confirm_password', this)"></i>
                    </div>
                </div>

                <button type="submit" name="update">Update Password</button>

            </form>

        <?php endif; ?>

    </div>

</div>

<script>
function toggle(id, icon){
    const input = document.getElementById(id);

    if(input.type === "password"){
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}

<?php if ($success): ?>
let count = 3;
let counter = document.getElementById("count");

let interval = setInterval(() => {
    count--;
    counter.innerText = count;

    if(count <= 0){
        clearInterval(interval);
        window.location.href = "login.php";
    }
}, 1000);
<?php endif; ?>
</script>

</body>
</html>