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
    $parts = [
        trim((string)($person['firstname'] ?? '')),
        trim((string)($person['middlename'] ?? '')),
        trim((string)($person['surname'] ?? '')),
        trim((string)($person['extension'] ?? '')),
    ];
    return trim(preg_replace('/\s+/', ' ', implode(' ', array_filter($parts, fn($v) => $v !== ''))));
}

function format_dropdown_name(array $person): string {
    $name = trim(
        (string)($person['surname'] ?? '') . ', ' .
        (string)($person['firstname'] ?? '') . ' ' .
        (string)($person['middlename'] ?? '') . ' ' .
        (string)($person['extension'] ?? '')
    );
    return preg_replace('/\s+/', ' ', $name);
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
         ORDER BY surname ASC, firstname ASC, middlename ASC"
    );
} else {
    $stmt = $conn->prepare(
        "SELECT id, firstname, middlename, surname, extension, email, mobile
         FROM personal_info
         ORDER BY surname ASC, firstname ASC, middlename ASC"
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

$fullName = $person ? format_full_name($person) : 'Resume Preview';
$photoSrc = $person ? make_photo_src($person['photo'] ?? null, $person['photo_type'] ?? null) : '../assets/profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDS Print</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f6f4;
            color: #1b1b1b;
        }

        .page-wrap {
            max-width: 1100px;
            margin: 24px auto;
            padding: 0 16px 40px;
        }

        .toolbar {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            background: #ffffff;
            border: 1px solid #d7dde8;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
            box-shadow: 0 10px 24px rgba(16, 24, 40, 0.06);
        }

        .toolbar .left,
        .toolbar .right {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar label {
            font-weight: 700;
            font-size: 14px;
        }

        .toolbar select {
            min-width: 360px;
            max-width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #c9d3e1;
            font-size: 14px;
            background: #fff;
        }

        .btn {
            border: 0;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #22361e;
            color: #fff;
        }

        .btn-secondary {
            background: #eef2eb;
            color: #22361e;
        }

        .home-btn {
            background: #22361e;
            color: #fff;
        }

        .resume-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 14px 38px rgba(15, 23, 42, 0.12);
            padding: 18mm 15mm 16mm;
        }

        .resume-header {
            display: grid;
            grid-template-columns: 1fr 110px;
            gap: 16px;
            align-items: start;
            border-bottom: 3px solid #22361e;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .name-block h1 {
            margin: 0 0 6px;
            font-size: 30px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .title-text {
            margin: 0;
            font-size: 14px;
            color: #51604a;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .logo-box {
            display: flex;
            justify-content: flex-end;
            align-items: flex-start;
        }

        .logo-box img {
            max-width: 96px;
            max-height: 96px;
            object-fit: contain;
        }

        .top-grid {
            display: grid;
            grid-template-columns: 130px 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .photo-box img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #d8dee9;
        }

        .summary-box {
            background: #f7f8f5;
            border: 1px solid #dce4f0;
            border-radius: 12px;
            padding: 14px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px 20px;
        }

        .field strong {
            display: block;
            font-size: 12px;
            color: #5b6755;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .field span {
            font-size: 14px;
        }

        .section {
            margin-top: 18px;
        }

        .section h2 {
            font-size: 15px;
            color: #22361e;
            border-bottom: 2px solid #d6e0f2;
            padding-bottom: 6px;
            margin: 0 0 10px;
            letter-spacing: 0.7px;
            text-transform: uppercase;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 20px;
        }

        .resume-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .resume-table th,
        .resume-table td {
            border: 1px solid #dce4f0;
            padding: 8px 9px;
            vertical-align: top;
            text-align: left;
        }

        .resume-table th {
            background: #e9eee4;
            color: #2f3f2b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .empty-note {
            padding: 10px 12px;
            background: #fbfcfa;
            border: 1px dashed #c9d3e1;
            border-radius: 10px;
            color: #667061;
            font-size: 13px;
        }

        @page {
            size: A4 portrait;
            margin: 0;
        }

        @media print {
            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
                background: #f5f6f4 !important;
                color: #1b1b1b !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .toolbar,
            .no-print {
                display: none !important;
            }

            .page-wrap {
                max-width: 1100px !important;
                margin: 24px auto !important;
                padding: 0 16px 40px !important;
            }

            .resume-sheet {
                width: 210mm !important;
                min-height: 297mm !important;
                margin: 0 auto !important;
                background: #fff !important;
                box-shadow: 0 14px 38px rgba(15, 23, 42, 0.12) !important;
                padding: 18mm 15mm 16mm !important;
            }

            .resume-header {
                display: grid !important;
                grid-template-columns: 1fr 110px !important;
                gap: 16px !important;
                align-items: start !important;
                border-bottom: 3px solid #22361e !important;
                padding-bottom: 12px !important;
                margin-bottom: 16px !important;
            }

            .top-grid {
                display: grid !important;
                grid-template-columns: 130px 1fr !important;
                gap: 18px !important;
                margin-bottom: 18px !important;
            }

            .contact-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 8px 20px !important;
            }

            .info-grid {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px 20px !important;
            }

            .summary-box {
                background: #f7f8f5 !important;
                border: 1px solid #dce4f0 !important;
                border-radius: 12px !important;
                padding: 14px !important;
            }

            .resume-table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 13px !important;
            }

            .resume-table th,
            .resume-table td {
                border: 1px solid #dce4f0 !important;
                padding: 8px 9px !important;
                vertical-align: top !important;
                text-align: left !important;
            }

            .resume-table th {
                background: #e9eee4 !important;
                color: #2f3f2b !important;
            }

            .photo-box img {
                width: 130px !important;
                height: 130px !important;
                object-fit: cover !important;
                border-radius: 12px !important;
                border: 1px solid #d8dee9 !important;
            }

            .logo-box {
                display: flex !important;
                justify-content: flex-end !important;
                align-items: flex-start !important;
            }

            .logo-box img {
                max-width: 96px !important;
                max-height: 96px !important;
                object-fit: contain !important;
            }

            .section,
            .summary-box,
            .resume-header,
            .top-grid,
            .resume-table,
            .resume-table tr,
            .resume-table td,
            .resume-table th {
                break-inside: avoid;
                page-break-inside: avoid;
            }
        }

        @media (max-width: 900px) {
            .resume-sheet {
                width: 100%;
                min-height: auto;
            }

            .top-grid,
            .resume-header,
            .info-grid,
            .contact-grid {
                grid-template-columns: 1fr;
            }

            .logo-box {
                justify-content: flex-start;
            }

            .toolbar select {
                min-width: 240px;
            }
        }
    </style>
</head>
<body>
<div class="page-wrap">
    <div class="toolbar no-print">
        <div class="left">
            <label for="personSelect">Select person</label>
            <select id="personSelect" onchange="changePerson(this.value)">
                <?php foreach ($people as $row): ?>
                    <option value="<?php echo (int)$row['id']; ?>" <?php echo ((int)$row['id'] === (int)$selected_id) ? 'selected' : ''; ?>>
                        <?php echo e(format_dropdown_name($row)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="right">
            <a href="../dashboard/dashboard.php" class="btn btn-secondary home-btn">🏠︎ Home</a>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='view.php?id=<?php echo (int)$selected_id; ?>'">✎ Open Edit</button>
            <button type="button" class="btn btn-primary" onclick="window.print()">🗐 Print</button>
        </div>
    </div>

    <div class="resume-sheet">
        <div class="resume-header">
            <div class="name-block">
                <h1><?php echo e($fullName); ?></h1>
                <p class="title-text">Personal Data Sheet</p>
            </div>
            <div class="logo-box">
                <img src="<?php echo e($logoPath); ?>" alt="Company Logo" onerror="this.style.display='none'">
            </div>
        </div>

        <?php if ($person): ?>
            <div class="top-grid">
                <div class="photo-box">
                    <img src="<?php echo e($photoSrc); ?>" alt="Profile Photo">
                </div>
                <div class="summary-box">
                    <div class="contact-grid">
                        <div class="field"><strong>Email</strong><span><?php echo e($person['email'] ?? ''); ?></span></div>
                        <div class="field"><strong>Mobile</strong><span><?php echo e($person['mobile'] ?? ''); ?></span></div>
                        <div class="field"><strong>Telephone</strong><span><?php echo e($person['telephone'] ?? ''); ?></span></div>
                        <div class="field"><strong>Date of Birth</strong><span><?php echo e(format_date_display($person['dob'] ?? '')); ?></span></div>
                        <div class="field"><strong>Birth Place</strong><span><?php echo e($person['birth_place'] ?? ''); ?></span></div>
                        <div class="field"><strong>Civil Status</strong><span><?php echo e($person['civil_status'] ?? ''); ?></span></div>
                        <div class="field"><strong>Sex</strong><span><?php echo e($person['sex'] ?? ''); ?></span></div>
                        <div class="field"><strong>Citizenship</strong><span><?php echo e(trim(($person['citizenship'] ?? '') . ' ' . ($person['dual_country'] ?? ''))); ?></span></div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Address Information</h2>
                <div class="info-grid">
                    <div class="field">
                        <strong>Residential Address</strong>
                        <span><?php echo e(format_address($residential)); ?></span>
                    </div>
                    <div class="field">
                        <strong>Permanent Address</strong>
                        <span><?php echo e(format_address($permanent)); ?></span>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Personal Details</h2>
                <div class="info-grid">
                    <div class="field"><strong>Height</strong><span><?php echo e($person['height'] ?? ''); ?></span></div>
                    <div class="field"><strong>Weight</strong><span><?php echo e($person['weight'] ?? ''); ?></span></div>
                    <div class="field"><strong>Blood Type</strong><span><?php echo e($person['blood_type'] ?? ''); ?></span></div>
                    <div class="field"><strong>UMID</strong><span><?php echo e($person['umid'] ?? ''); ?></span></div>
                    <div class="field"><strong>Pag-IBIG</strong><span><?php echo e($person['pagibig'] ?? ''); ?></span></div>
                    <div class="field"><strong>PhilHealth</strong><span><?php echo e($person['philhealth'] ?? ''); ?></span></div>
                    <div class="field"><strong>PhilSys</strong><span><?php echo e($person['philsys'] ?? ''); ?></span></div>
                    <div class="field"><strong>TIN</strong><span><?php echo e($person['tin'] ?? ''); ?></span></div>
                </div>
            </div>

            <div class="section">
                <h2>Educational Background</h2>
                <?php if (!empty($education_records)): ?>
                    <table class="resume-table">
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
                                <td><?php echo e($row['education_level'] ?? $row['level'] ?? ''); ?></td>
                                <td><?php echo e($row['school_name'] ?? ''); ?></td>
                                <td><?php echo e($row['course'] ?? ''); ?></td>
                                <td><?php echo e(format_date_display($row['edu_from'] ?? '')); ?> - <?php echo e(format_date_display($row['edu_to'] ?? '')); ?></td>
                                <td><?php echo e($row['year_graduated'] ?? ''); ?></td>
                                <td><?php echo e($row['honors'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-note">No education records found.</div>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>Service Eligibility</h2>
                <?php if (!empty($eligibility_records)): ?>
                    <table class="resume-table">
                        <thead>
                            <tr>
                                <th>Career Service</th>
                                <th>Rating</th>
                                <th>Exam Date</th>
                                <th>Exam Place</th>
                                <th>License</th>
                                <th>License Number</th>
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
                    <div class="empty-note">No eligibility records found.</div>
                <?php endif; ?>
            </div>

            <div class="section">
                <h2>Training / Learning and Development</h2>
                <?php if (!empty($training_records)): ?>
                    <table class="resume-table">
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
                                <td><?php echo e($row['title'] ?? $row['training_title'] ?? ''); ?></td>
                                <td><?php echo e($row['hours'] ?? ''); ?></td>
                                <td><?php echo e(format_date_display($row['training_from'] ?? '')); ?> - <?php echo e(format_date_display($row['training_to'] ?? '')); ?></td>
                                <td><?php echo e($row['type'] ?? $row['training_type'] ?? ''); ?></td>
                                <td><?php echo e($row['sponsor'] ?? ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-note">No training records found.</div>
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