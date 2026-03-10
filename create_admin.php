<?php
include "config/database.php";

$username = "admin";
$email = "admin@email.com";
$pass = password_hash("admin123", PASSWORD_DEFAULT); // assign to variable

$stmt = $conn->prepare("INSERT INTO users(username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $email, $pass); // now use the variable

$stmt->execute();

if($stmt->affected_rows === 1){
    echo "Admin user created successfully.";
} else {
    echo "Failed to create admin user.";
}

$stmt->close();
$conn->close();
?>