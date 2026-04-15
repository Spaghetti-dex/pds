<?php
require_once "../includes/auth_check.php";
include "../config/database.php";

$search = trim($_GET['search'] ?? '');
$action_filter = trim($_GET['action'] ?? '');

// Get counts for summary cards
$totalLogs = 0;
$totalActions = 0;

$countResult = $conn->query("SELECT COUNT(*) AS total FROM audit_logs");
if ($countResult && $row = $countResult->fetch_assoc()) {
    $totalLogs = (int)$row['total'];
}

$actionCountResult = $conn->query("SELECT COUNT(DISTINCT action) AS total_actions FROM audit_logs");
if ($actionCountResult && $row = $actionCountResult->fetch_assoc()) {
    $totalActions = (int)$row['total_actions'];
}

// Main query
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

// Count filtered results
$filteredCount = $result->num_rows;

// Get distinct actions for filter
$actions_result = $conn->query("SELECT DISTINCT action FROM audit_logs ORDER BY action ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(116, 163, 112, 0.22), transparent 30%),
                radial-gradient(circle at bottom right, rgba(34, 54, 30, 0.18), transparent 28%),
                linear-gradient(135deg, #eef2ea 0%, #dfe8da 100%);
            padding: 28px 14px;
            color: #1f2f1e;
        }

        .page {
            width: 100%;
            max-width: 1280px;
            margin: 0 auto;
        }

        .panel {
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(34, 54, 30, 0.14);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.12);
            backdrop-filter: blur(6px);
        }

        .header {
            background: linear-gradient(135deg, #22361e 0%, #2f4b2b 100%);
            color: #fff;
            padding: 30px 24px 26px;
        }

        .header-top {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .home-btn {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: 0.25s;
            flex-shrink: 0;
        }

        .home-btn:hover {
            background: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .header h1 {
            font-size: 31px;
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .header p {
            font-size: 14px;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.88);
            max-width: 700px;
        }

        .content {
            padding: 24px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 22px;
        }

        .stat-box {
            background: #f5f8f3;
            border: 1px solid #d9e4d5;
            border-radius: 18px;
            padding: 16px 14px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #607260;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 20px;
            font-weight: 700;
            color: #1a341d;
        }

        .filter-card {
            background: linear-gradient(180deg, #f7faf5 0%, #eff5ec 100%);
            border: 1px solid #d5e1d1;
            border-radius: 22px;
            padding: 18px;
            margin-bottom: 22px;
        }

        .filter-title {
            font-size: 13px;
            font-weight: 700;
            color: #547054;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 14px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 2fr 1fr auto auto;
            gap: 12px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #22361e;
            font-size: 14px;
        }

        .input-wrap {
            position: relative;
        }

        input[type="text"],
        select {
            width: 100%;
            min-height: 52px;
            padding: 14px 16px;
            border: 1.5px solid #c8d4c2;
            border-radius: 16px;
            background: #fbfcfa;
            font-size: 15px;
            color: #203120;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        input[type="text"]:focus,
        select:focus {
            outline: none;
            border-color: #5f8a59;
            box-shadow: 0 0 0 4px rgba(95, 138, 89, 0.14);
            background: #fff;
        }

        select {
            appearance: none;
            cursor: pointer;
            padding-right: 46px;
        }

        .select-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #607260;
            pointer-events: none;
            font-size: 14px;
        }

        .btn {
            border: none;
            border-radius: 16px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 52px;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, #2f6a28 0%, #214b1a 100%);
            box-shadow: 0 10px 22px rgba(33, 75, 26, 0.18);
        }

        .btn-primary:hover {
            box-shadow: 0 14px 26px rgba(33, 75, 26, 0.24);
        }

        .btn-reset {
            color: #fff;
            background: linear-gradient(135deg, #7a7a7a 0%, #5e5e5e 100%);
            box-shadow: 0 10px 22px rgba(94, 94, 94, 0.16);
        }

        .btn-reset:hover {
            box-shadow: 0 14px 26px rgba(94, 94, 94, 0.22);
        }

        .results-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .results-text {
            font-size: 14px;
            color: #526452;
            font-weight: 600;
        }

        .table-card {
            background: #fff;
            border: 1px solid #dde7d9;
            border-radius: 22px;
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 980px;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #e8eee4;
            font-size: 14px;
        }

        th {
            background: #23391f;
            color: white;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            position: sticky;
            top: 0;
        }

        tbody tr:nth-child(even) {
            background: #fbfcfa;
        }

        tbody tr:hover {
            background: #f2f7ef;
        }

        .action-badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            background: #e8f2e4;
            color: #294d24;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .muted {
            color: #617462;
        }

        .empty-state {
            padding: 28px 20px;
            text-align: center;
            background: #fffdf3;
            color: #6b5a12;
        }

        .empty-state i {
            font-size: 26px;
            margin-bottom: 10px;
            display: block;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .empty-state-text {
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 992px) {
            .filter-form {
                grid-template-columns: 1fr 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 14px 10px;
            }

            .panel {
                border-radius: 22px;
            }

            .header {
                padding: 24px 20px 22px;
            }

            .header h1 {
                font-size: 24px;
            }

            .header p {
                font-size: 13px;
            }

            .content {
                padding: 18px;
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            input[type="text"],
            select,
            .btn {
                min-height: 50px;
                font-size: 14px;
            }

            .home-btn {
                width: 38px;
                height: 38px;
            }
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="panel">
            <div class="header">
                <div class="header-top">
                    <a href="../dashboard/dashboard.php" class="home-btn" title="Home">
                        <i class="fa-solid fa-house"></i>
                    </a>

                    <div class="header-badge">
                        <i class="fa-solid fa-clipboard-list"></i>
                        System Monitoring
                    </div>
                </div>

                <h1>Audit Logs</h1>
                <p>
                    Review recorded system activities, filter by action type, and search by username, description, or person ID.
                </p>
            </div>

            <div class="content">
                <div class="stats">
                    <div class="stat-box">
                        <div class="stat-label">Total Logs</div>
                        <div class="stat-value"><?php echo number_format($totalLogs); ?></div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-label">Filtered Results</div>
                        <div class="stat-value"><?php echo number_format($filteredCount); ?></div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-label">Action Types</div>
                        <div class="stat-value"><?php echo number_format($totalActions); ?></div>
                    </div>
                </div>

                <div class="filter-card">
                    <div class="filter-title">Search and Filter Logs</div>

                    <form method="GET" class="filter-form">
                        <div class="form-group">
                            <label for="search">Search</label>
                            <input
                                type="text"
                                id="search"
                                name="search"
                                placeholder="Search username, description, person ID"
                                value="<?php echo htmlspecialchars($search); ?>"
                                oninput="debounceSubmit()"
                            >
                        </div>

                        <div class="form-group">
                            <label for="action">Action</label>
                            <div class="input-wrap">
                                <select name="action" id="action" onchange="this.form.submit()">
                                    <option value="">All Actions</option>
                                    <?php while ($action_row = $actions_result->fetch_assoc()): ?>
                                        <option
                                            value="<?php echo htmlspecialchars($action_row['action']); ?>"
                                            <?php echo ($action_filter === $action_row['action']) ? 'selected' : ''; ?>
                                        >
                                            <?php echo htmlspecialchars($action_row['action']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <span class="select-icon">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Search
                        </button>

                        <a href="logs.php" class="btn btn-reset">
                            <i class="fa-solid fa-rotate-left"></i>
                            Reset
                        </a>
                    </form>
                </div>

                <div class="results-bar">
                    <div class="results-text">
                        Showing <strong><?php echo number_format($filteredCount); ?></strong> result<?php echo $filteredCount === 1 ? '' : 's'; ?>
                        <?php if ($search !== '' || $action_filter !== ''): ?>
                            with current filters applied
                        <?php endif; ?>
                    </div>
                </div>

                <div class="table-card">
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Person ID</th>
                                    <th>Username</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>Date / Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result->num_rows > 0): ?>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['person_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                                            <td>
                                                <span class="action-badge">
                                                    <?php echo htmlspecialchars($row['action']); ?>
                                                </span>
                                            </td>
                                            <td class="muted"><?php echo htmlspecialchars($row['description']); ?></td>
                                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="padding: 0;">
                                            <div class="empty-state">
                                                <i class="fa-solid fa-folder-open"></i>
                                                <div class="empty-state-title">No logs found</div>
                                                <div class="empty-state-text">
                                                    Try changing your search text or selected action filter.
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    let debounceTimer;

    function debounceSubmit() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            document.querySelector(".filter-form").submit();
        }, 500);
    }
    </script>
</body>
</html>