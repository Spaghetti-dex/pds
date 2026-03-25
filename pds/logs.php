<?php
require_once "../includes/auth_check.php";
include "../config/database.php";

$search = trim($_GET['search'] ?? '');
$action_filter = trim($_GET['action'] ?? '');

$sql = "SELECT * FROM audit_logs WHERE 1=1";
$params = [];
$types = "";

if ($search !== '') {
    $sql .= " AND (username LIKE ? OR description LIKE ? OR person_id LIKE ?)";
    $searchLike = "%" . $search . "%";
    $params[] = $searchLike;
    $params[] = $searchLike;
    $params[] = $searchLike;
    $types .= "sss";
}

if ($action_filter !== '') {
    $sql .= " AND action = ?";
    $params[] = $action_filter;
    $types .= "s";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

/* GET DISTINCT ACTIONS FOR FILTER */
$actions_result = $conn->query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f4f4f4;
            margin: 0;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 1200px;
            margin: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .top-left {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .top-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        h1 {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            background: #2f402c;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background: #243320;
        }

        .btn-reset {
            background: #777;
        }

        .btn-reset:hover {
            background: #5f5f5f;
        }

        form.filter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            width: 100%;
        }

        input[type="text"], select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            min-width: 220px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #2f402c;
            color: white;
        }

        .empty-message {
            padding: 15px;
            background: #fff3cd;
            border: 1px solid #ffe69c;
            border-radius: 6px;
            color: #664d03;
            margin-top: 15px;
        }
    </style>
</head>

<script>
let debounceTimer;

function debounceSubmit() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        document.querySelector(".filter-form").submit();
    }, 500); // 0.5 second delay
}
</script>
<body>
    <div class="card">
        <div class="top-bar">
            <div class="top-left">
                <h1>Audit Logs</h1>
                <a href="../dashboard/dashboard.php" class="btn">Home</a>
            </div>

            <div class="top-right" style="width:100%;">
                <form method="GET" class="filter-form">
                    <input 
                        type="text" 
                        name="search"
                        placeholder="Search username, description, person ID"
                        value="<?php echo htmlspecialchars($search); ?>"
                        oninput="debounceSubmit()"
                    >

                    <select name="action" onchange="this.form.submit()">
                        <option value="">All Actions</option>
                        <?php while($action_row = $actions_result->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($action_row['action']); ?>"
                                <?php echo ($action_filter === $action_row['action']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($action_row['action']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit" class="btn">Search</button>
                    <a href="logs.php" class="btn btn-reset">Reset</a>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Person ID</th>
                    <th>Username</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>Date / Time</th>
                </tr>

                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['person_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['action']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No logs found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>