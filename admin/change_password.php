<?php
include "../config/database.php";
include "../includes/auth_check.php";

$message = "";

if(isset($_POST['update_password'])){

    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);

    if(empty($current) || empty($new) || empty($confirm)){
        $message = "All fields are required.";
    }
    elseif($new != $confirm){
        $message = "New password does not match.";
    }
    else{

        $user_id = $_SESSION['user_id']; // adjust if different

        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i",$user_id);
        $stmt->execute();
        $stmt->bind_result($db_password);
        $stmt->fetch();
        $stmt->close();

        if(!password_verify($current,$db_password)){
            $message = "Current password is incorrect.";
        }
        else{

            $hash = password_hash($new,PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si",$hash,$user_id);

            if($stmt->execute()){
                $message = "Password successfully updated.";
            }else{
                $message = "Error updating password.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Change Password</title>
</head>
<body>

<h2>Change Password</h2>

<?php if($message!=""){ echo "<p>$message</p>"; } ?>

<form method="POST">
    <label>Current Password</label><br>
    <input type="password" name="current_password"><br><br>

    <label>New Password</label><br>
    <input type="password" name="new_password"><br><br>

    <label>Confirm Password</label><br>
    <input type="password" name="confirm_password"><br><br>

    <button type="submit" name="update_password">Update Password</button>
</form>

</body>
</html>