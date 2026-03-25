<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_logged_username() {
    if (isset($_SESSION['username']) && $_SESSION['username'] !== '') {
        return $_SESSION['username'];
    }

    if (isset($_SESSION['user_name']) && $_SESSION['user_name'] !== '') {
        return $_SESSION['user_name'];
    }

    if (isset($_SESSION['fullname']) && $_SESSION['fullname'] !== '') {
        return $_SESSION['fullname'];
    }

    return 'Unknown User';
}

function write_audit_log($conn, $person_id, $action, $description) {
    $username = get_logged_username();

    $stmt = $conn->prepare("INSERT INTO audit_logs (person_id, username, action, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $person_id, $username, $action, $description);
    $stmt->execute();
    $stmt->close();
}
?>