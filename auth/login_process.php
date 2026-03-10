<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "../config/database.php"; // include DB

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (!$username || !$password) {
    die("Please enter both username and password.");
}

// Prepare and execute statement
$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $user['username'];

        // Redirect to dashboard folder
        header("Location: ../dashboard/dashboard.php");
        exit();
    } else {
        echo "Invalid password";
    }
} else {
    echo "User not found";
}

$conn->close();
?>