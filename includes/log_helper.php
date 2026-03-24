<?php
function add_log($conn, $action, $target_user_id = null, $target_username = null, $details = null) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $admin_id = $_SESSION['user_id'] ?? null;
    $admin_username = $_SESSION['username'] ?? 'System';

    $stmt = $conn->prepare("
        INSERT INTO activity_logs (admin_id, admin_username, action, target_user_id, target_username, details)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ississ",
        $admin_id,
        $admin_username,
        $action,
        $target_user_id,
        $target_username,
        $details
    );

    $stmt->execute();
    $stmt->close();
}
?>