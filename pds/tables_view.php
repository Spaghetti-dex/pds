<?php
require_once "../includes/auth_check.php";
include "../config/database.php";
include "../includes/header.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$tableMap = [
    'addresses' => 'Address Records',
    'education' => 'Educational Background',
    'eligibility' => 'Eligibility Records',
    'personal_info' => 'Personal Information',
    'training' => 'Learning and Development'
];

function table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare("
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
        AND table_name = ?
    ");
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count > 0;
}

function get_columns(mysqli $conn, string $table): array {
    $stmt = $conn->prepare("
        SELECT COLUMN_NAME
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
        AND table_name = ?
        ORDER BY ORDINAL_POSITION
    ");
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $result = $stmt->get_result();

    $cols = [];
    while ($row = $result->fetch_assoc()) {
        $cols[] = $row['COLUMN_NAME'];
    }
    $stmt->close();
    return $cols;
}

function full_name_sql($alias) {
    return "TRIM(CONCAT(
        COALESCE($alias.surname,''), ', ',
        COALESCE($alias.firstname,''), ' ',
        COALESCE($alias.middlename,''), ' ',
        COALESCE($alias.extension,'')
    ))";
}

function clean_display_name($name): string {
    $name = trim((string)$name);
    $name = preg_replace('/\s+/', ' ', $name);
    return $name === '' ? 'Unknown' : $name;
}

function build_grouped_rows(array $rows, bool $showName, bool $isPersonalInfo): array {
    if (!$showName || empty($rows)) {
        foreach ($rows as $i => $row) {
            $rows[$i]['_group_start'] = true;
            $rows[$i]['_group_size'] = 1;
            $rows[$i]['_group_key'] = (string)$i;
            $rows[$i]['full_name'] = clean_display_name($row['full_name'] ?? 'Unknown');
        }
        return $rows;
    }

    foreach ($rows as $i => $row) {
        $rows[$i]['full_name'] = clean_display_name($row['full_name'] ?? 'Unknown');
        if ($isPersonalInfo) {
            $rows[$i]['_group_key'] = 'person_' . (string)($row['id'] ?? $i);
        } else {
            $personId = $row['person_id'] ?? '';
            $rows[$i]['_group_key'] = 'person_' . (string)$personId . '_' . $rows[$i]['full_name'];
        }
        $rows[$i]['_group_start'] = false;
        $rows[$i]['_group_size'] = 1;
    }

    $count = count($rows);
    $i = 0;

    while ($i < $count) {
        $start = $i;
        $key = $rows[$i]['_group_key'];
        $size = 1;
        $i++;

        while ($i < $count && $rows[$i]['_group_key'] === $key) {
            $size++;
            $i++;
        }

        $rows[$start]['_group_start'] = true;
        $rows[$start]['_group_size'] = $size;
    }

    return $rows;
}

$existingTables = [];
foreach ($tableMap as $real => $label) {
    if (table_exists($conn, $real)) {
        $existingTables[] = $real;
    }
}

sort($existingTables);

$selectedTable = $_GET['table'] ?? ($existingTables[0] ?? '');
if (!in_array($selectedTable, $existingTables, true)) {
    $selectedTable = $existingTables[0] ?? '';
}

$columns = $selectedTable ? get_columns($conn, $selectedTable) : [];
$rows = [];

if ($selectedTable !== '') {
    if ($selectedTable === 'personal_info') {
        $sql = "
            SELECT t.*, " . full_name_sql('t') . " AS full_name
            FROM personal_info t
            ORDER BY t.surname ASC, t.firstname ASC, t.middlename ASC, t.extension ASC
        ";
    } elseif (in_array('person_id', $columns, true)) {
        $orderTail = in_array('type', $columns, true)
            ? "t.type ASC, t.id ASC"
            : (in_array('id', $columns, true) ? "t.id ASC" : "1 ASC");

        $sql = "
            SELECT t.*, " . full_name_sql('pi') . " AS full_name
            FROM {$selectedTable} t
            LEFT JOIN personal_info pi ON pi.id = t.person_id
            ORDER BY pi.surname ASC, pi.firstname ASC, pi.middlename ASC, pi.extension ASC, {$orderTail}
        ";
    } else {
        $sql = "
            SELECT t.*
            FROM {$selectedTable} t
            ORDER BY t.id DESC
        ";
    }

    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

$showName = ($selectedTable === 'personal_info' || in_array('person_id', $columns, true));
$rows = build_grouped_rows($rows, $showName, $selectedTable === 'personal_info');

$displayColumns = [];
foreach ($columns as $col) {
    if ($col === 'id' || $col === 'person_id') continue;
    $displayColumns[] = $col;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Database Viewer</title>
<style>
*,
*::before,
*::after{
    box-sizing:border-box;
}

body{
    font-family:Arial, sans-serif;
    background:#e6e6e6;
    margin:0;
    color:#222;
}

.page{
    max-width:1380px;
    margin:110px auto 30px;
    padding:0 16px 24px;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    margin-bottom:18px;
    flex-wrap:wrap;
}

.page-title{
    margin:0;
    font-size:28px;
    font-weight:700;
    color:#22361e;
}

.home-btn,
.load-btn{
    display:inline-block;
    border:none;
    border-radius:8px;
    padding:10px 18px;
    font-size:14px;
    font-weight:700;
    cursor:pointer;
    text-decoration:none;
    transition:0.2s ease;
}

.home-btn{
    background:#22361e;
    color:#fff;
}

.home-btn:hover{
    background:#2d4728;
}

.load-btn{
    background:#22361e;
    color:#fff;
}

.load-btn:hover{
    background:#2d4728;
}

.card{
    background:#fff;
    padding:22px;
    border-radius:14px;
    margin-bottom:20px;
    box-shadow:0 2px 10px rgba(0,0,0,0.06);
    border:1px solid #d7d7d7;
    width:100%;
    overflow:hidden;
}

.toolbar{
    display:flex;
    align-items:end;
    gap:14px;
    flex-wrap:wrap;
}

.field{
    display:flex;
    flex-direction:column;
    gap:7px;
    min-width:260px;
}

.field label{
    font-size:14px;
    font-weight:700;
    color:#22361e;
}

.field select{
    appearance:none;
    -webkit-appearance:none;
    -moz-appearance:none;
    background:#f7f7f7;
    border:1px solid #bfc7bc;
    border-radius:10px;
    padding:11px 42px 11px 14px;
    font-size:14px;
    color:#222;
    outline:none;
    background-image:
        linear-gradient(45deg, transparent 50%, #22361e 50%),
        linear-gradient(135deg, #22361e 50%, transparent 50%);
    background-position:
        calc(100% - 18px) calc(50% - 3px),
        calc(100% - 12px) calc(50% - 3px);
    background-size:6px 6px, 6px 6px;
    background-repeat:no-repeat;
}

.field select:focus{
    border-color:#22361e;
    box-shadow:0 0 0 3px rgba(34,54,30,0.12);
}

.table-title{
    margin:0 0 14px 0;
    font-size:24px;
    font-weight:700;
    color:#22361e;
}

.table-wrap{
    width:100%;
    overflow-x:auto;
    overflow-y:hidden;
    border:1px solid #d7d7d7;
    border-radius:12px;
    background:#fff;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:1000px;
    background:#fff;
}

th,
td{
    border:1px solid #d7d7d7;
    padding:10px 12px;
    text-align:left;
    vertical-align:top;
    font-size:14px;
}

th{
    background:#f0f3ef;
    color:#22361e;
    font-weight:700;
    white-space:nowrap;
}

tbody tr:nth-child(even){
    background:#fafafa;
}

tbody tr:hover{
    background:#f5f8f4;
}

.person{
    font-weight:700;
    color:#22361e;
    min-width:220px;
    background:#eef4eb;
}

.group-note{
    display:inline-block;
    margin-bottom:6px;
    padding:3px 8px;
    border-radius:999px;
    background:#eef2ee;
    color:#22361e;
    border:1px solid #c9d5c9;
    font-size:12px;
    font-weight:700;
}

.empty-state{
    padding:18px;
    color:#666;
    font-style:italic;
}

@media (max-width: 768px){
    .page{
        margin:90px auto 20px;
        padding:0 12px 20px;
    }

    .top-bar{
        flex-direction:column;
        align-items:stretch;
    }

    .toolbar{
        flex-direction:column;
        align-items:stretch;
    }

    .field{
        min-width:0;
        width:100%;
    }

    .home-btn,
    .load-btn{
        width:100%;
        text-align:center;
    }

    .card{
        padding:16px;
    }

    .page-title{
        font-size:24px;
    }
}
</style>
</head>
<body>

<div class="page">

    <div class="top-bar">
        <h1 class="page-title">Database Viewer</h1>
        <a href="../dashboard/dashboard.php" class="home-btn">🏠︎ Home</a>
    </div>

    <div class="card">
        <form method="GET">
            <div class="toolbar">
                <div class="field">
                    <label for="table">Select Data</label>
                    <select name="table" id="table" onchange="this.form.submit()">
                        <?php foreach ($existingTables as $t): ?>
                            <option value="<?php echo e($t); ?>" <?php echo ($t === $selectedTable ? 'selected' : ''); ?>>
                                <?php echo e($tableMap[$t] ?? $t); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 class="table-title"><?php echo e($tableMap[$selectedTable] ?? $selectedTable); ?></h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <?php if ($showName): ?>
                            <th>Person Name</th>
                        <?php endif; ?>

                        <?php foreach ($displayColumns as $col): ?>
                            <th><?php echo e($col); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($rows)): ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php if ($showName && !empty($row['_group_start'])): ?>
                                    <td class="person" rowspan="<?php echo (int)$row['_group_size']; ?>">
                                        <?php echo e($row['full_name']); ?>
                                    </td>
                                <?php endif; ?>

                                <?php foreach ($displayColumns as $col): ?>
                                    <td><?php echo e($row[$col] ?? ''); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo count($displayColumns) + ($showName ? 1 : 0); ?>" class="empty-state">
                                No records found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>