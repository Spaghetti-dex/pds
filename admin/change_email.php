<?php
include "../config/database.php";
include "../includes/auth_check.php";

$message = "";

if(isset($_POST['update_email'])){

    $new_email = trim($_POST['email']);

    if(empty($new_email)){
        $message = "Email is required.";
    }
    else{

        $user_id = $_SESSION['user_id']; // adjust if different session name

        $stmt = $conn->prepare("UPDATE users SET email=? WHERE id=?");
        $stmt->bind_param("si",$new_email,$user_id);

        if($stmt->execute()){
            $message = "Email successfully updated.";
        }else{
            $message = "Error updating email.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Change Email</title>
</head>
<body>

<h2>Change Email</h2>

<?php if($message!=""){ echo "<p>$message</p>"; } ?>

<form method="POST">
    <label>New Email</label><br>
    <input type="email" name="email"><br><br>

    <button type="submit" name="update_email">Update Email</button>
</form>

</body>
</html>