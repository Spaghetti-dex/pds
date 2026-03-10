<?php include "../includes/auth_check.php"; ?>
<?php include "../config/database.php"; ?>

<form method="POST">

Change Email
<input name="email">

Change Password
<input type="password" name="password">

<button>Update</button>

</form>

<?php

if($_POST){

$pass=password_hash($_POST['password'],PASSWORD_DEFAULT);

$stmt=$conn->prepare("
UPDATE users SET email=?,password=? WHERE id=?
");

$stmt->bind_param(
"ssi",
$_POST['email'],
$pass,
$_SESSION['user_id']
);

$stmt->execute();

echo "Updated";

}