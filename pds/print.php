<?php
ob_start();
require_once "../includes/auth_check.php";
include "../config/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare(
        "SELECT COUNT(*) AS cnt
         FROM information_schema.tables
         WHERE table_schema = DATABASE() AND table_name = ?"
    );
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    return ((int)($row['cnt'] ?? 0)) > 0;
}

function get_columns(mysqli $conn, string $table): array {
    if (!table_exists($conn, $table)) {
        return [];
    }

    $stmt = $conn->prepare(
        "SELECT COLUMN_NAME AS colname
         FROM information_schema.columns
         WHERE table_schema = DATABASE() AND table_name = ?
         ORDER BY ORDINAL_POSITION"
    );
    $stmt->bind_param("s", $table);
    $stmt->execute();
    $result = $stmt->get_result();

    $cols = [];
    while ($row = $result->fetch_assoc()) {
        $cols[] = $row['colname'];
    }
    $stmt->close();
    return $cols;
}

function has_column(array $columns, string $name): bool {
    return in_array($name, $columns, true);
}

function normalize_address_row(array $row): array {
    return [
        'house1'      => $row['house1'] ?? ($row['house'] ?? ''),
        'street'      => $row['street'] ?? '',
        'subdivision' => $row['subdivision'] ?? '',
        'barangay'    => $row['barangay'] ?? '',
        'city'        => $row['city'] ?? '',
        'province'    => $row['province'] ?? '',
        'zip'         => $row['zip'] ?? '',
    ];
}

function format_full_name(array $person): string {
    $last = trim((string)($person['surname'] ?? ''));
    $first = trim((string)($person['firstname'] ?? ''));
    $middle = trim((string)($person['middlename'] ?? ''));
    $ext = trim((string)($person['extension'] ?? ''));
    $parts = array_filter([$first, $middle, $last, $ext], fn($v) => $v !== '');
    return trim(preg_replace('/\s+/', ' ', implode(' ', $parts)));
}

function format_dropdown_name(array $person): string {
    $last = trim((string)($person['surname'] ?? ''));
    $first = trim((string)($person['firstname'] ?? ''));
    $middle = trim((string)($person['middlename'] ?? ''));
    $ext = trim((string)($person['extension'] ?? ''));
    return trim(preg_replace('/\s+/', ' ', $last . ', ' . $first . ' ' . $middle . ' ' . $ext));
}

function format_address(array $address): string {
    $parts = [
        $address['house1'] ?? '',
        $address['street'] ?? '',
        $address['subdivision'] ?? '',
        $address['barangay'] ?? '',
        $address['city'] ?? '',
        $address['province'] ?? '',
        $address['zip'] ?? '',
    ];
    $parts = array_values(array_filter(array_map('trim', $parts), fn($v) => $v !== ''));
    return implode(', ', $parts);
}

function format_date_display($value): string {
    $value = trim((string)$value);
    if ($value === '') {
        return '';
    }
    $ts = strtotime($value);
    return $ts ? date('F j, Y', $ts) : $value;
}

function make_photo_src(?string $photo, ?string $photoType): string {
    if (!empty($photo) && !empty($photoType)) {
        return 'data:' . $photoType . ';base64,' . base64_encode($photo);
    }
    return '../assets/profile.png';
}

$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$logoPath = '../assets/logo.png';

$education_table = table_exists($conn, 'education') ? 'education' : null;
$eligibility_table = table_exists($conn, 'service_eligibility')
    ? 'service_eligibility'
    : (table_exists($conn, 'eligibility') ? 'eligibility' : null);
$training_table = table_exists($conn, 'learning_development')
    ? 'learning_development'
    : (table_exists($conn, 'training') ? 'training' : null);

$education_columns = $education_table ? get_columns($conn, $education_table) : [];
$eligibility_columns = $eligibility_table ? get_columns($conn, $eligibility_table) : [];
$training_columns = $training_table ? get_columns($conn, $training_table) : [];
$personal_info_columns = get_columns($conn, 'personal_info');
$has_photo_column = in_array('photo', $personal_info_columns, true);
$has_photo_type_column = in_array('photo_type', $personal_info_columns, true);

$people = [];
if ($has_photo_column && $has_photo_type_column) {
    $stmt = $conn->prepare(
        "SELECT id, firstname, middlename, surname, extension, email, mobile, photo, photo_type
         FROM personal_info
         ORDER BY surname ASC, firstname ASC, middlename ASC, extension ASC"
    );
} else {
    $stmt = $conn->prepare(
        "SELECT id, firstname, middlename, surname, extension, email, mobile
         FROM personal_info
         ORDER BY surname ASC, firstname ASC, middlename ASC, extension ASC"
    );
}
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $people[] = $row;
}
$stmt->close();

if ($selected_id <= 0 && !empty($people)) {
    $selected_id = (int)$people[0]['id'];
}

$person = null;
$residential = [];
$permanent = [];
$education_records = [];
$eligibility_records = [];
$training_records = [];

if ($selected_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM personal_info WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $person = $result->fetch_assoc();
    $stmt->close();

    if ($person && table_exists($conn, 'addresses')) {
        $stmt = $conn->prepare("SELECT * FROM addresses WHERE person_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $addrResult = $stmt->get_result();
        while ($row = $addrResult->fetch_assoc()) {
            $normalized = normalize_address_row($row);
            $type = strtolower(trim((string)($row['type'] ?? '')));
            if ($type === 'residential') {
                $residential = $normalized;
            } elseif ($type === 'permanent') {
                $permanent = $normalized;
            }
        }
        $stmt->close();
    }

    if ($person && $education_table && has_column($education_columns, 'person_id')) {
        $stmt = $conn->prepare("SELECT * FROM `{$education_table}` WHERE person_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $eduResult = $stmt->get_result();
        while ($row = $eduResult->fetch_assoc()) {
            $education_records[] = $row;
        }
        $stmt->close();
    }

    if ($person && $eligibility_table && has_column($eligibility_columns, 'person_id')) {
        $stmt = $conn->prepare("SELECT * FROM `{$eligibility_table}` WHERE person_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $eligResult = $stmt->get_result();
        while ($row = $eligResult->fetch_assoc()) {
            $eligibility_records[] = $row;
        }
        $stmt->close();
    }

    if ($person && $training_table && has_column($training_columns, 'person_id')) {
        $stmt = $conn->prepare("SELECT * FROM `{$training_table}` WHERE person_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $trainResult = $stmt->get_result();
        while ($row = $trainResult->fetch_assoc()) {
            $training_records[] = $row;
        }
        $stmt->close();
    }
}

$fullName = $person ? format_full_name($person) : 'No record selected';
$photoSrc = $person ? make_photo_src($person['photo'] ?? null, $person['photo_type'] ?? null) : '../assets/profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Printable Resume</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f4;
            color: #111;
        }
        .page {
            max-width: 1280px;
            margin: 0 auto;
            padding: 24px;
        }
        .top-link {
            color: #22361e;
            font-weight: 700;
            text-decoration: none;
        }
        h1 {
            margin: 10px 0 18px;
            font-size: 30px;
        }
        .card-simple,
        .resume-card {
            background: #fff;
            border: 1px solid #d7d7d7;
            border-radius: 12px;
            padding: 18px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
        }
        .card-simple { margin-bottom: 18px; }
        .toolbar-grid {
            display: grid;
            grid-template-columns: minmax(260px, 1fr) auto;
            gap: 16px;
            align-items: end;
        }
        .simple-field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .simple-field label,
        .section-title,
        .detail-label {
            font-size: 14px;
            font-weight: 700;
        }
        select {
            width: 100%;
            height: 42px;
            padding: 8px 12px;
            border: 1px solid #555;
            border-radius: 6px;
            background: #e9e9ee;
            font-size: 14px;
        }
        .search-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        .btn-primary,
        .btn-secondary,
        .btn-link {
            border: none;
            border-radius: 6px;
            padding: 10px 16px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary { background: #22361e; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-link { background: #22361e; color: #fff; }
        .resume-card {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            border: 3px solid #000;
            padding: 22px 26px 28px;
            background: #c7d1c3;
        }
        .resume-header {
            display: grid;
            grid-template-columns: 1fr 120px;
            gap: 20px;
            align-items: start;
            border-bottom: 3px solid #22361e;
            padding-bottom: 16px;
            margin-bottom: 18px;
        }
        .resume-title {
            margin: 0 0 8px;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .resume-subtitle {
            margin: 0;
            font-size: 14px;
            letter-spacing: 1px;
            color: #22361e;
            font-weight: 700;
            text-transform: uppercase;
        }
        .logo-box {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
        }
        .logo-box img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }
        .photo-box {
            background: #fff;
            border: 1px solid #666;
            border-radius: 10px;
            padding: 8px;
        }
        .photo-box img {
            display: block;
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
        }
        .summary-box {
            background: #eef2eb;
            border: 1px solid #7f8a79;
            border-radius: 10px;
            padding: 14px;
        }
        .summary-grid,
        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 18px;
        }
        .detail-item {
            background: rgba(255,255,255,0.45);
            border-radius: 8px;
            padding: 8px 10px;
        }
        .detail-label {
            display: block;
            color: #34412f;
            margin-bottom: 4px;
            text-transform: uppercase;
            font-size: 12px;
        }
        .detail-value {
            display: block;
            font-size: 14px;
            word-break: break-word;
        }
        .resume-section { margin-top: 18px; }
        .section-heading {
            margin: 0 0 10px;
            padding: 8px 12px;
            background: #22361e;
            color: #fff;
            border-radius: 8px;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }
        th, td {
            border: 1px solid #d7d7d7;
            padding: 10px;
            text-align: left;
            vertical-align: top;
            font-size: 13px;
        }
        th {
            background: #f0f3ef;
            font-size: 12px;
            text-transform: uppercase;
        }
        .empty-note {
            background: #fff;
            border: 1px dashed #8d9688;
            border-radius: 8px;
            padding: 12px;
            color: #444;
            font-size: 13px;
        }
        .muted {
            color: #666;
            font-size: 13px;
        }
        @media (max-width: 1000px) {
            .page { padding: 16px; }
            .toolbar-grid,
            .hero-grid,
            .summary-grid,
            .detail-grid,
            .resume-header {
                grid-template-columns: 1fr;
            }
            .search-actions { justify-content: flex-start; }
            .resume-card {
                width: 100%;
                min-height: auto;
                padding: 16px;
            }
            .logo-box { justify-content: flex-start; }
        }
        @media print {
            @page { size: A4; margin: 10mm; }
            body { background: #fff; }
            .page { max-width: none; margin: 0; padding: 0; }
            .card-simple, .top-link, h1 { display: none !important; }
            .resume-card {
                width: auto;
                min-height: auto;
                margin: 0;
                box-shadow: none;
                border: none;
                padding: 0;
                background: #fff;
            }
            .summary-box,
            .photo-box,
            table,
            .empty-note {
                background: #fff !important;
            }
            .section-heading {
                color: #000;
                background: #e9ece7;
                border: 1px solid #999;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <a href="../dashboard/dashboard.php" class="top-link">Home</a>
    <h1>Printable Resume</h1>

    <div class="card-simple">
        <div class="toolbar-grid">
            <div class="simple-field">
                <label for="personSelect">Select person</label>
                <select id="personSelect" onchange="changePerson(this.value)">
                    <?php foreach ($people as $row): ?>
                        <option value="<?php echo (int)$row['id']; ?>" <?php echo ((int)$row['id'] === (int)$selected_id) ? 'selected' : ''; ?>>
                            <?php echo e(format_dropdown_name($row)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="muted">Names are sorted alphabetically by last name.</span>
            </div>
            <div class="search-actions">
                <a class="btn-secondary" href="search_view_edit_delete.php?id=<?php echo (int)$selected_id; ?>">Open Edit</a>
                <button type="button" class="btn-primary" onclick="window.print()">Print Resume</button>
            </div>
        </div>
    </div>

    <div class="resume-card">
        <div class="resume-header">
            <div>
                <h2 class="resume-title"><?php echo e($fullName); ?></h2>
                <p class="resume-subtitle">Personal Data Sheet</p>
            </div>
            <div class="logo-box">
                <img src="<?php echo e($logoPath); ?>" alt="Logo" onerror="this.style.display='none'">
            </div>
        </div>

        <?php if ($person): ?>
            <div class="hero-grid">
                <div class="photo-box">
                    <img src="<?php echo e($photoSrc); ?>" alt="Profile Photo">
                </div>
                <div class="summary-box">
                    <div class="summary-grid">
                        <div class="detail-item"><span class="detail-label">Email</span><span class="detail-value"><?php echo e($person['email'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Mobile</span><span class="detail-value"><?php echo e($person['mobile'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Telephone</span><span class="detail-value"><?php echo e($person['telephone'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Date of Birth</span><span class="detail-value"><?php echo e(format_date_display($person['dob'] ?? '')); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Birth Place</span><span class="detail-value"><?php echo e($person['birth_place'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Civil Status</span><span class="detail-value"><?php echo e($person['civil_status'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Sex</span><span class="detail-value"><?php echo e($person['sex'] ?? ''); ?></span></div>
                        <div class="detail-item"><span class="detail-label">Citizenship</span><span class="detail-value"><?php echo e(trim(($person['citizenship'] ?? '') . ' ' . ($person['dual_country'] ?? ''))); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="resume-section">
                <h3 class="section-heading">Address Information</h3>
                <div class="detail-grid">
                    <div class="detail-item">
                        <span class="detail-label">Residential Address</span>
                        <span class="detail-value"><?php echo e(format_address($residential)); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Permanent Address</span>
                        <span class="detail-value"><?php echo e(format_address($permanent)); ?></span>
                    </div>
                </div>
            </div>

            <div class="resume-section">
                <h3 class="section-heading">Personal Details</h3>
                <div class="detail-grid">
                    <div class="detail-item"><span class="detail-label">Height</span><span class="detail-value"><?php echo e($person['height'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">Weight</span><span class="detail-value"><?php echo e($person['weight'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">Blood Type</span><span class="detail-value"><?php echo e($person['blood_type'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">UMID</span><span class="detail-value"><?php echo e($person['umid'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">Pag-IBIG</span><span class="detail-value"><?php echo e($person['pagibig'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">PhilHealth</span><span class="detail-value"><?php echo e($person['philhealth'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">PhilSys</span><span class="detail-value"><?php echo e($person['philsys'] ?? ''); ?></span></div>
                    <div class="detail-item"><span class="detail-label">TIN</span><span class="detail-value"><?php echo e($person['tin'] ?? ''); ?></span></div>
                </div>
            </div>

            <div class="resume-section">
                <h3 class="section-heading">Educational Background</h3>
                <?php if (!empty($education_records)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Level</th>
                                <th>School</th>
                                <th>Course</th>
                                <th>Inclusive Dates</th>
                                <th>Year Graduated</th>
                                <th>Honors</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($education_records as $row): ?>
                                <tr>
                                    <td><?php echo e($row['education_level'] ?? ''); ?></td>
                                    <td><?php echo e($row['school_name'] ?? ''); ?></td>
                                    <td><?php echo e($row['course'] ?? ''); ?></td>
                                    <td><?php echo e(trim(format_date_display($row['edu_from'] ?? '') . ' - ' . format_date_display($row['edu_to'] ?? ''))); ?></td>
                                    <td><?php echo e($row['year_graduated'] ?? ''); ?></td>
                                    <td><?php echo e($row['honors'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-note">No educational background found.</div>
                <?php endif; ?>
            </div>

            <div class="resume-section">
                <h3 class="section-heading">Service Eligibility</h3>
                <?php if (!empty($eligibility_records)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Career Service</th>
                                <th>Rating</th>
                                <th>Exam Date</th>
                                <th>Exam Place</th>
                                <th>License</th>
                                <th>License No.</th>
                                <th>Valid Until</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eligibility_records as $row): ?>
                                <tr>
                                    <td><?php echo e($row['career_service'] ?? ''); ?></td>
                                    <td><?php echo e($row['rating'] ?? ''); ?></td>
                                    <td><?php echo e(format_date_display($row['exam_date'] ?? '')); ?></td>
                                    <td><?php echo e($row['exam_place'] ?? ''); ?></td>
                                    <td><?php echo e($row['license'] ?? ''); ?></td>
                                    <td><?php echo e($row['license_number'] ?? ''); ?></td>
                                    <td><?php echo e(format_date_display($row['valid_until'] ?? '')); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-note">No service eligibility record found.</div>
                <?php endif; ?>
            </div>

            <div class="resume-section">
                <h3 class="section-heading">Learning and Development</h3>
                <?php if (!empty($training_records)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Hours</th>
                                <th>Inclusive Dates</th>
                                <th>Type</th>
                                <th>Sponsor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($training_records as $row): ?>
                                <tr>
                                    <td><?php echo e($row['title'] ?? ''); ?></td>
                                    <td><?php echo e($row['hours'] ?? ''); ?></td>
                                    <td><?php echo e(trim(format_date_display($row['training_from'] ?? '') . ' - ' . format_date_display($row['training_to'] ?? ''))); ?></td>
                                    <td><?php echo e($row['type'] ?? ''); ?></td>
                                    <td><?php echo e($row['sponsor'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-note">No learning and development record found.</div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="empty-note">No person record found.</div>
        <?php endif; ?>
    </div>
</div>

<script>
function changePerson(id) {
    const url = new URL(window.location.href);
    url.searchParams.set('id', id);
    window.location.href = url.toString();
}
</script>
</body>
</html>
<?php ob_end_flush(); ?>
