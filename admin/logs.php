<?php
require_once "../includes/admin_check.php";
include "../config/database.php";

$logs = [];
$result = $conn->query("SELECT * FROM activity_logs ORDER BY created_at DESC");

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>System Logs</title>
<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, Helvetica, sans-serif;
}

body{
    background:#efefef url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
    min-height:100vh;
    padding:110px 20px 30px 20px;
}

.container{
    max-width:1100px;
    margin:auto;
    background:#fff;
    border:3px solid #22361e;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}

.header{
    background:#22361e;
    color:#fff;
    padding:22px;
    text-align:center;
    font-size:28px;
    font-weight:bold;
}

.content{
    padding:20px;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
}

th, td{
    border:1px solid #ccc;
    padding:12px;
    text-align:left;
    vertical-align:top;
    font-size:14px;
}

th{
    background:#dfe8db;
    color:#22361e;
}

tr:nth-child(even){
    background:#f8f8f8;
}

.no-data{
    padding:20px;
    text-align:center;
    font-weight:bold;
    color:#555;
}
</style>
</head>
<body>

<div class="container">
    <div class="header">System Logs</div>

    <div class="content">
        <?php if (count($logs) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Admin</th>
                        <th>Action</th>
                        <th>Target User</th>
                        <th>Details</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($log['id']); ?></td>
                            <td><?php echo htmlspecialchars($log['admin_username'] ?? 'System'); ?></td>
                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                            <td><?php echo htmlspecialchars($log['target_username'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['details'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($log['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-data">No logs found.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html> 