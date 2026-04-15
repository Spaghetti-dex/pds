<?php
ob_start();
require_once "../includes/auth_check.php";
require_once "./../includes/audit_log.php";
include "../config/database.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}
function clean_value($value): string {
    if (is_array($value)) return '';
    return trim((string)$value);
}

function clean_array_values($values): array {
    if (!is_array($values)) return [];
    return array_map(function ($v) {
        return trim((string)$v);
    }, $values);
}

function normalize_date_value($value): ?string {
    $value = trim((string)$value);
    return $value === '' ? null : $value;
}

function row_has_any_value(array $row): bool {
    foreach ($row as $value) {
        if ($value !== null && trim((string)$value) !== '') {
            return true;
        }
    }
    return false;
}

function require_field(array &$errors, string $label, $value): void {
    if ($value === null || trim((string)$value) === '') {
        $errors[] = "$label is required.";
    }
}

function validate_regex_field(array &$errors, string $label, string $value, string $pattern, string $message = 'is invalid.'): void {
    if ($value !== '' && !preg_match($pattern, $value)) {
        $errors[] = "$label $message";
    }
}

function validate_date_field(array &$errors, string $label, ?string $value, bool $allowFuture = true): void {
    if ($value === null || $value === '') return;

    $dt = DateTime::createFromFormat('Y-m-d', $value);
    if (!$dt || $dt->format('Y-m-d') !== $value) {
        $errors[] = "$label is invalid.";
        return;
    }

    if (!$allowFuture) {
        $today = new DateTime('today');
        if ($dt > $today) {
            $errors[] = "$label cannot be in the future.";
        }
    }
}

function validate_date_range(array &$errors, string $fromLabel, ?string $from, string $toLabel, ?string $to): void {
    if (!$from || !$to) return;

    $fromDate = DateTime::createFromFormat('Y-m-d', $from);
    $toDate   = DateTime::createFromFormat('Y-m-d', $to);

    if ($fromDate && $toDate && $fromDate > $toDate) {
        $errors[] = "$toLabel cannot be earlier than $fromLabel.";
    }
}

function validate_positive_number_field(array &$errors, string $label, string $value): void {
    if ($value !== '' && (!preg_match('/^\d+(\.\d+)?$/', $value) || (float)$value <= 0)) {
        $errors[] = "$label must be a positive number.";
    }
}

function validate_year_field(array &$errors, string $label, string $value): void {
    if ($value !== '' && !preg_match('/^(19|20)\d{2}$/', $value)) {
        $errors[] = "$label must be a valid 4-digit year.";
    }
}

function make_row_label(string $type, int $index, string $field): string {
    return $type . ' row ' . ($index + 1) . ' - ' . $field;
}

function table_exists(mysqli $conn, string $table): bool {
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM information_schema.tables
        WHERE table_schema = DATABASE()
          AND table_name = ?
    ");
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

    $stmt = $conn->prepare("
        SELECT COLUMN_NAME AS colname
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
        if (isset($row['colname'])) {
            $cols[] = $row['colname'];
        }
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
        'zip'         => $row['zip'] ?? ''
    ];
}

function get_address_house_column(array $columns): string {
    if (in_array('house1', $columns, true)) {
        return 'house1';
    }
    if (in_array('house', $columns, true)) {
        return 'house';
    }
    return 'house1';
}

function make_photo_src(?string $photo, ?string $photoType): string {
    if (!empty($photo) && !empty($photoType)) {
        return 'data:' . $photoType . ';base64,' . base64_encode($photo);
    }
    return '../assets/profile.png';
}

$search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : "";
$selected_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
$sort = isset($_REQUEST['sort']) ? strtolower(trim($_REQUEST['sort'])) : 'asc';
$sort = ($sort === 'desc') ? 'desc' : 'asc';
$order = ($sort === 'desc') ? 'DESC' : 'ASC';

$message = "";
$error = "";

if (isset($_SESSION['success_message'])) {
    $message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$results = [];
$person = null;

$residential = [
    'house1' => '',
    'street' => '',
    'subdivision' => '',
    'barangay' => '',
    'city' => '',
    'province' => '',
    'zip' => ''
];

$permanent = [
    'house1' => '',
    'street' => '',
    'subdivision' => '',
    'barangay' => '',
    'city' => '',
    'province' => '',
    'zip' => ''
];

$education_records = [];
$eligibility_records = [];
$training_records = [];

/*
|--------------------------------------------------------------------------
| EXACT TABLE NAMES
|--------------------------------------------------------------------------
*/
$education_table = table_exists($conn, 'education') ? 'education' : null;

$eligibility_table = table_exists($conn, 'eligibility') ? 'eligibility' : null;

$training_table = table_exists($conn, 'learning_development')
    ? 'learning_development'
    : (table_exists($conn, 'training') ? 'training' : null);

$education_columns   = $education_table ? get_columns($conn, $education_table) : [];
$eligibility_columns = $eligibility_table ? get_columns($conn, $eligibility_table) : [];
$training_columns    = $training_table ? get_columns($conn, $training_table) : [];
$address_columns     = get_columns($conn, 'addresses');
$address_house_col   = get_address_house_column($address_columns);

$personal_info_columns = get_columns($conn, 'personal_info');
$has_photo_column = in_array('photo', $personal_info_columns, true);
$has_photo_type_column = in_array('photo_type', $personal_info_columns, true);

/*
|--------------------------------------------------------------------------
| DELETE
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_id = (int)($_POST['id'] ?? 0);

    try {
        $conn->begin_transaction();

        if (table_exists($conn, 'addresses')) {
            $stmt = $conn->prepare("DELETE FROM addresses WHERE person_id = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($education_table && has_column($education_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$education_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($eligibility_table && has_column($eligibility_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$eligibility_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
        }

        if ($training_table && has_column($training_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$training_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $delete_id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt = $conn->prepare("DELETE FROM personal_info WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        write_audit_log($conn, $delete_id, 'DELETE', "Deleted PDS record with ID " . $delete_id);
        $message = "Record deleted successfully.";
    } catch (Exception $ex) {
        $conn->rollback();
        $error = "Delete failed: " . $ex->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| UPDATE
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $selected_id = $id;

    $surname         = clean_value($_POST['surname'] ?? '');
    $firstname       = clean_value($_POST['firstname'] ?? '');
    $middlename      = clean_value($_POST['middlename'] ?? '');
    $extension       = clean_value($_POST['extension'] ?? '');
    $dob             = normalize_date_value($_POST['dob'] ?? '');
    $birth_place     = clean_value($_POST['birth_place'] ?? '');
    $sex             = clean_value($_POST['sex'] ?? '');
    $civil_status    = clean_value($_POST['civil_status'] ?? '');
    $height          = clean_value($_POST['height'] ?? '');
    $weight          = clean_value($_POST['weight'] ?? '');
    $blood_type      = clean_value($_POST['blood_type'] ?? '');
    $umid            = clean_value($_POST['umid'] ?? '');
    $pagibig         = clean_value($_POST['pagibig'] ?? '');
    $philhealth      = clean_value($_POST['philhealth'] ?? '');
    $philsys         = clean_value($_POST['philsys'] ?? '');
    $tin             = clean_value($_POST['tin'] ?? '');
    $agency_employee = clean_value($_POST['agency_employee'] ?? '');
    $citizenship     = clean_value($_POST['citizenship'] ?? '');
    $dual_country    = clean_value($_POST['dual_country'] ?? '');
    $telephone       = clean_value($_POST['telephone'] ?? '');
    $mobile          = clean_value($_POST['mobile'] ?? '');
    $email           = clean_value($_POST['email'] ?? '');

    $newPhotoData = null;
    $newPhotoType = null;
    $hasNewPhotoUpload = isset($_FILES['photo']) && ($_FILES['photo']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;

    $r_house1      = clean_value($_POST['r_house1'] ?? '');
    $r_street      = clean_value($_POST['r_street'] ?? '');
    $r_subdivision = clean_value($_POST['r_subdivision'] ?? '');
    $r_barangay    = clean_value($_POST['r_barangay'] ?? '');
    $r_city        = clean_value($_POST['r_city'] ?? '');
    $r_province    = clean_value($_POST['r_province'] ?? '');
    $r_zip         = clean_value($_POST['r_zip'] ?? '');

    $p_house1      = clean_value($_POST['p_house1'] ?? '');
    $p_street      = clean_value($_POST['p_street'] ?? '');
    $p_subdivision = clean_value($_POST['p_subdivision'] ?? '');
    $p_barangay    = clean_value($_POST['p_barangay'] ?? '');
    $p_city        = clean_value($_POST['p_city'] ?? '');
    $p_province    = clean_value($_POST['p_province'] ?? '');
    $p_zip         = clean_value($_POST['p_zip'] ?? '');

    $education_level = clean_array_values($_POST['education_level'] ?? []);
    $school_name     = clean_array_values($_POST['school_name'] ?? []);
    $course          = clean_array_values($_POST['course'] ?? []);
    $units           = clean_array_values($_POST['units'] ?? []);
    $edu_from        = $_POST['edu_from'] ?? [];
    $edu_to          = $_POST['edu_to'] ?? [];
    $year_graduated  = clean_array_values($_POST['year_graduated'] ?? []);
    $honors          = clean_array_values($_POST['honors'] ?? []);

    $career_service  = clean_array_values($_POST['career_service'] ?? []);
    $rating          = clean_array_values($_POST['rating'] ?? []);
    $exam_date       = $_POST['exam_date'] ?? [];
    $exam_place      = clean_array_values($_POST['exam_place'] ?? []);
    $license         = clean_array_values($_POST['license'] ?? []);
    $license_number  = clean_array_values($_POST['license_number'] ?? []);
    $valid_until     = $_POST['valid_until'] ?? [];

    $training_title  = clean_array_values($_POST['title'] ?? []);
    $hours           = clean_array_values($_POST['hours'] ?? []);
    $training_from   = $_POST['training_from'] ?? [];
    $training_to     = $_POST['training_to'] ?? [];
    $training_type   = clean_array_values($_POST['type'] ?? []);
    $sponsor         = clean_array_values($_POST['sponsor'] ?? []);

    $errors = [];

    /*
    |--------------------------------------------------------------------------
    | VALIDATION - MAIN RECORD
    |--------------------------------------------------------------------------
    */
    require_field($errors, 'Surname', $surname);
    require_field($errors, 'First name', $firstname);
    require_field($errors, 'Middle name', $middlename);
    require_field($errors, 'Date of birth', $dob);
    require_field($errors, 'Birth place', $birth_place);
    require_field($errors, 'Sex', $sex);
    require_field($errors, 'Civil status', $civil_status);
    require_field($errors, 'Blood type', $blood_type);
    require_field($errors, 'Height', $height);
    require_field($errors, 'Weight', $weight);
    require_field($errors, 'UMID', $umid);
    require_field($errors, 'Pag-IBIG', $pagibig);
    require_field($errors, 'PhilHealth', $philhealth);
    require_field($errors, 'PhilSys', $philsys);
    require_field($errors, 'TIN', $tin);
    require_field($errors, 'Agency Employee No.', $agency_employee);
    require_field($errors, 'Citizenship', $citizenship);

    require_field($errors, 'Residential house', $r_house1);
    require_field($errors, 'Residential street', $r_street);
    require_field($errors, 'Residential subdivision', $r_subdivision);
    require_field($errors, 'Residential barangay', $r_barangay);
    require_field($errors, 'Residential city', $r_city);
    require_field($errors, 'Residential province', $r_province);
    require_field($errors, 'Residential zip', $r_zip);

    require_field($errors, 'Permanent house', $p_house1);
    require_field($errors, 'Permanent street', $p_street);
    require_field($errors, 'Permanent subdivision', $p_subdivision);
    require_field($errors, 'Permanent barangay', $p_barangay);
    require_field($errors, 'Permanent city', $p_city);
    require_field($errors, 'Permanent province', $p_province);
    require_field($errors, 'Permanent zip', $p_zip);

    require_field($errors, 'Telephone', $telephone);
    require_field($errors, 'Mobile', $mobile);
    require_field($errors, 'Email', $email);

    if ($citizenship === 'Dual Citizen' && $dual_country === '') {
        $errors[] = 'Dual citizenship country is required.';
    }

    validate_regex_field($errors, 'Surname', $surname, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');
    validate_regex_field($errors, 'First name', $firstname, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');
    validate_regex_field($errors, 'Middle name', $middlename, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');

    $allowed_extensions = ['', 'Jr.', 'Sr.', 'II', 'III', 'IV', 'V'];

    if (!in_array($extension, $allowed_extensions, true)) {
        $errors[] = 'Invalid name extension selected.';
    }

    validate_date_field($errors, 'Date of birth', $dob, false);

    validate_regex_field($errors, 'Sex', $sex, "/^(Male|Female)$/", 'must be Male or Female.');
    validate_regex_field($errors, 'Civil status', $civil_status, "/^(Single|Married|Widowed|Separated)$/", 'is invalid.');
    validate_regex_field($errors, 'Blood type', $blood_type, "/^(A|B|AB|O)[+-]$/i", 'must be A+, A-, B+, B-, AB+, AB-, O+, or O-.');

    validate_positive_number_field($errors, 'Height', $height);
    validate_positive_number_field($errors, 'Weight', $weight);

    validate_regex_field($errors, 'UMID', $umid, "/^[0-9][0-9\- ]{3,29}$/", 'must contain numbers only. Hyphen and spaces are allowed.');
    validate_regex_field($errors, 'PhilSys', $philsys, "/^[0-9][0-9\- ]{3,29}$/", 'must contain numbers only. Hyphen and spaces are allowed.');
    validate_regex_field($errors, 'Pag-IBIG', $pagibig, "/^[0-9][0-9\- ]{3,29}$/", 'must contain numbers only. Hyphen and spaces are allowed.');
    validate_regex_field($errors, 'PhilHealth', $philhealth, "/^[0-9][0-9\- ]{3,29}$/", 'must contain numbers only. Hyphen and spaces are allowed.');
    validate_regex_field($errors, 'Agency Employee No.', $agency_employee, "/^[0-9][0-9\- ]{3,29}$/", 'must contain numbers only. Hyphen and spaces are allowed.');
    validate_regex_field($errors, 'TIN', $tin, "/^(\d{3}-\d{3}-\d{3}|\d{9}|\d{12}|\d{3}-\d{3}-\d{3}-\d{3})$/", 'is invalid.');

    validate_regex_field($errors, 'Citizenship', $citizenship, "/^(Filipino|Dual Citizen)$/", 'is invalid.');

    if ($dual_country !== '') {
        validate_regex_field($errors, 'Dual citizenship country', $dual_country, "/^[A-Za-zÑñ\s.'-]+$/", 'is invalid.');
    }

    validate_regex_field($errors, 'Residential zip', $r_zip, "/^\d{4}$/", 'must be 4 digits.');
    validate_regex_field($errors, 'Permanent zip', $p_zip, "/^\d{4}$/", 'must be 4 digits.');

    validate_regex_field($errors, 'Telephone', $telephone, "/^[0-9()\-\s]{7,15}$/", 'is invalid.');
    validate_regex_field($errors, 'Mobile', $mobile, "/^(09\d{9}|\+639\d{9})$/", 'must be 09XXXXXXXXX or +639XXXXXXXXX.');

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid.';
    }

    /*
    |--------------------------------------------------------------------------
    | PHOTO VALIDATION
    |--------------------------------------------------------------------------
    */
    if ($hasNewPhotoUpload) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedType = finfo_file($finfo, $_FILES['photo']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($detectedType, $allowedTypes, true)) {
            throw new Exception("Only JPG, PNG, GIF, and WEBP images are allowed.");
        }

        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            throw new Exception("Photo must not be larger than 5MB.");
        }

        $newPhotoData = file_get_contents($_FILES['photo']['tmp_name']);
        $newPhotoType = $detectedType;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION - EDUCATION
    |--------------------------------------------------------------------------
    */
    $educationRows = [];
    $educationCount = max(
        count($education_level),
        count($school_name),
        count($course),
        count($units),
        count($edu_from),
        count($edu_to),
        count($year_graduated),
        count($honors)
    );

    for ($i = 0; $i < $educationCount; $i++) {
        $row = [
            'education_level' => $education_level[$i] ?? '',
            'school_name' => $school_name[$i] ?? '',
            'course' => $course[$i] ?? '',
            'units' => $units[$i] ?? '',
            'edu_from' => normalize_date_value($edu_from[$i] ?? ''),
            'edu_to' => normalize_date_value($edu_to[$i] ?? ''),
            'year_graduated' => $year_graduated[$i] ?? '',
            'honors' => $honors[$i] ?? ''
        ];

        if (!row_has_any_value($row)) {
            continue;
        }

        require_field($errors, make_row_label('Education', $i, 'Level'), $row['education_level']);
        require_field($errors, make_row_label('Education', $i, 'School name'), $row['school_name']);
        require_field($errors, make_row_label('Education', $i, 'Course'), $row['course']);
        require_field($errors, make_row_label('Education', $i, 'Units'), $row['units']);
        require_field($errors, make_row_label('Education', $i, 'From date'), $row['edu_from']);
        require_field($errors, make_row_label('Education', $i, 'To date'), $row['edu_to']);
        require_field($errors, make_row_label('Education', $i, 'Year graduated'), $row['year_graduated']);

        validate_date_field($errors, make_row_label('Education', $i, 'From date'), $row['edu_from'], true);
        validate_date_field($errors, make_row_label('Education', $i, 'To date'), $row['edu_to'], true);
        validate_date_range($errors, make_row_label('Education', $i, 'From date'), $row['edu_from'], make_row_label('Education', $i, 'To date'), $row['edu_to']);
        validate_year_field($errors, make_row_label('Education', $i, 'Year graduated'), $row['year_graduated']);

        $educationRows[] = $row;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION - ELIGIBILITY
    |--------------------------------------------------------------------------
    */
    $eligibilityRows = [];
    $eligibilityCount = max(
        count($career_service),
        count($rating),
        count($exam_date),
        count($exam_place),
        count($license),
        count($license_number),
        count($valid_until)
    );

    for ($i = 0; $i < $eligibilityCount; $i++) {
        $row = [
            'career_service' => $career_service[$i] ?? '',
            'rating' => $rating[$i] ?? '',
            'exam_date' => normalize_date_value($exam_date[$i] ?? ''),
            'exam_place' => $exam_place[$i] ?? '',
            'license' => $license[$i] ?? '',
            'license_number' => $license_number[$i] ?? '',
            'valid_until' => normalize_date_value($valid_until[$i] ?? '')
        ];

        if (!row_has_any_value($row)) {
            continue;
        }

        require_field($errors, make_row_label('Eligibility', $i, 'Career service'), $row['career_service']);
        require_field($errors, make_row_label('Eligibility', $i, 'Rating'), $row['rating']);
        require_field($errors, make_row_label('Eligibility', $i, 'Exam date'), $row['exam_date']);
        require_field($errors, make_row_label('Eligibility', $i, 'Exam place'), $row['exam_place']);

        if ($row['rating'] !== '') {
            validate_regex_field($errors, make_row_label('Eligibility', $i, 'Rating'), $row['rating'], "/^\d+(\.\d+)?$/", 'must be numeric.');
        }

        validate_date_field($errors, make_row_label('Eligibility', $i, 'Exam date'), $row['exam_date'], false);

        if ($row['valid_until'] !== null) {
            validate_date_field($errors, make_row_label('Eligibility', $i, 'Valid until'), $row['valid_until'], true);
            validate_date_range($errors, make_row_label('Eligibility', $i, 'Exam date'), $row['exam_date'], make_row_label('Eligibility', $i, 'Valid until'), $row['valid_until']);
        }

        if ($row['license_number'] !== '') {
            validate_regex_field($errors, make_row_label('Eligibility', $i, 'License number'), $row['license_number'], "/^[A-Za-z0-9\- ]{3,30}$/", 'is invalid.');
        }

        $eligibilityRows[] = $row;
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION - TRAINING
    |--------------------------------------------------------------------------
    */
    $trainingRows = [];
    $trainingCount = max(
        count($training_title),
        count($hours),
        count($training_from),
        count($training_to),
        count($training_type),
        count($sponsor)
    );

    for ($i = 0; $i < $trainingCount; $i++) {
        $row = [
            'title' => $training_title[$i] ?? '',
            'hours' => $hours[$i] ?? '',
            'training_from' => normalize_date_value($training_from[$i] ?? ''),
            'training_to' => normalize_date_value($training_to[$i] ?? ''),
            'type' => $training_type[$i] ?? '',
            'sponsor' => $sponsor[$i] ?? ''
        ];

        if (!row_has_any_value($row)) {
            continue;
        }

        require_field($errors, make_row_label('Training', $i, 'Title'), $row['title']);
        require_field($errors, make_row_label('Training', $i, 'Hours'), $row['hours']);
        require_field($errors, make_row_label('Training', $i, 'From date'), $row['training_from']);
        require_field($errors, make_row_label('Training', $i, 'To date'), $row['training_to']);

        validate_positive_number_field($errors, make_row_label('Training', $i, 'Hours'), $row['hours']);
        validate_date_field($errors, make_row_label('Training', $i, 'From date'), $row['training_from'], true);
        validate_date_field($errors, make_row_label('Training', $i, 'To date'), $row['training_to'], true);
        validate_date_range($errors, make_row_label('Training', $i, 'From date'), $row['training_from'], make_row_label('Training', $i, 'To date'), $row['training_to']);

        $trainingRows[] = $row;
    }

    try {
        if (!empty($errors)) {
            throw new Exception(implode(" ", $errors));
        }

        $conn->begin_transaction();

        if ($has_photo_column && $has_photo_type_column && $hasNewPhotoUpload) {
            $stmt = $conn->prepare("
                UPDATE personal_info SET
                    surname = ?,
                    firstname = ?,
                    middlename = ?,
                    extension = ?,
                    dob = ?,
                    birth_place = ?,
                    sex = ?,
                    civil_status = ?,
                    height = ?,
                    weight = ?,
                    blood_type = ?,
                    umid = ?,
                    pagibig = ?,
                    philhealth = ?,
                    philsys = ?,
                    tin = ?,
                    agency_employee = ?,
                    citizenship = ?,
                    dual_country = ?,
                    telephone = ?,
                    mobile = ?,
                    email = ?,
                    photo_type = ?,
                    photo = ?
                WHERE id = ?
            ");

            $emptyBlob = null;

            $stmt->bind_param(
                "sssssssssssssssssssssssbi",
                $surname,
                $firstname,
                $middlename,
                $extension,
                $dob,
                $birth_place,
                $sex,
                $civil_status,
                $height,
                $weight,
                $blood_type,
                $umid,
                $pagibig,
                $philhealth,
                $philsys,
                $tin,
                $agency_employee,
                $citizenship,
                $dual_country,
                $telephone,
                $mobile,
                $email,
                $newPhotoType,
                $emptyBlob,
                $id
            );

            $stmt->send_long_data(23, $newPhotoData);
            $stmt->execute();
            $stmt->close();
        } else {
            $stmt = $conn->prepare("
                UPDATE personal_info SET
                    surname = ?,
                    firstname = ?,
                    middlename = ?,
                    extension = ?,
                    dob = ?,
                    birth_place = ?,
                    sex = ?,
                    civil_status = ?,
                    height = ?,
                    weight = ?,
                    blood_type = ?,
                    umid = ?,
                    pagibig = ?,
                    philhealth = ?,
                    philsys = ?,
                    tin = ?,
                    agency_employee = ?,
                    citizenship = ?,
                    dual_country = ?,
                    telephone = ?,
                    mobile = ?,
                    email = ?
                WHERE id = ?
            ");
            $stmt->bind_param(
                "ssssssssssssssssssssssi",
                $surname,
                $firstname,
                $middlename,
                $extension,
                $dob,
                $birth_place,
                $sex,
                $civil_status,
                $height,
                $weight,
                $blood_type,
                $umid,
                $pagibig,
                $philhealth,
                $philsys,
                $tin,
                $agency_employee,
                $citizenship,
                $dual_country,
                $telephone,
                $mobile,
                $email,
                $id
            );
            $stmt->execute();
            $stmt->close();
        }

        if (table_exists($conn, 'addresses')) {
            $resType = "residential";
            $stmt = $conn->prepare("SELECT id FROM addresses WHERE person_id = ? AND type = ? LIMIT 1");
            $stmt->bind_param("is", $id, $resType);
            $stmt->execute();
            $res = $stmt->get_result();
            $existingResidential = $res->fetch_assoc();
            $stmt->close();

            if ($existingResidential) {
                $stmt = $conn->prepare("
                    UPDATE addresses SET
                        `{$address_house_col}` = ?,
                        street = ?,
                        subdivision = ?,
                        barangay = ?,
                        city = ?,
                        province = ?,
                        zip = ?
                    WHERE person_id = ? AND type = ?
                ");
                $stmt->bind_param(
                    "sssssssis",
                    $r_house1,
                    $r_street,
                    $r_subdivision,
                    $r_barangay,
                    $r_city,
                    $r_province,
                    $r_zip,
                    $id,
                    $resType
                );
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO addresses
                    (person_id, type, `{$address_house_col}`, street, subdivision, barangay, city, province, zip)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "issssssss",
                    $id,
                    $resType,
                    $r_house1,
                    $r_street,
                    $r_subdivision,
                    $r_barangay,
                    $r_city,
                    $r_province,
                    $r_zip
                );
                $stmt->execute();
                $stmt->close();
            }

            $permType = "permanent";
            $stmt = $conn->prepare("SELECT id FROM addresses WHERE person_id = ? AND type = ? LIMIT 1");
            $stmt->bind_param("is", $id, $permType);
            $stmt->execute();
            $res = $stmt->get_result();
            $existingPermanent = $res->fetch_assoc();
            $stmt->close();

            if ($existingPermanent) {
                $stmt = $conn->prepare("
                    UPDATE addresses SET
                        `{$address_house_col}` = ?,
                        street = ?,
                        subdivision = ?,
                        barangay = ?,
                        city = ?,
                        province = ?,
                        zip = ?
                    WHERE person_id = ? AND type = ?
                ");
                $stmt->bind_param(
                    "sssssssis",
                    $p_house1,
                    $p_street,
                    $p_subdivision,
                    $p_barangay,
                    $p_city,
                    $p_province,
                    $p_zip,
                    $id,
                    $permType
                );
                $stmt->execute();
                $stmt->close();
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO addresses
                    (person_id, type, `{$address_house_col}`, street, subdivision, barangay, city, province, zip)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param(
                    "issssssss",
                    $id,
                    $permType,
                    $p_house1,
                    $p_street,
                    $p_subdivision,
                    $p_barangay,
                    $p_city,
                    $p_province,
                    $p_zip
                );
                $stmt->execute();
                $stmt->close();
            }
        }

        if ($education_table && has_column($education_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$education_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $eduInsertCols = [];
            $eduBindTypes = "i";
            $eduValueKeys = [];

            if (has_column($education_columns, 'person_id')) {
                $eduInsertCols[] = 'person_id';
                $eduValueKeys[] = 'person_id';
            }
            if (has_column($education_columns, 'education_level')) {
                $eduInsertCols[] = 'education_level';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'education_level';
            }
            if (has_column($education_columns, 'school_name')) {
                $eduInsertCols[] = 'school_name';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'school_name';
            }
            if (has_column($education_columns, 'course')) {
                $eduInsertCols[] = 'course';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'course';
            }
            if (has_column($education_columns, 'units')) {
                $eduInsertCols[] = 'units';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'units';
            }
            if (has_column($education_columns, 'edu_from')) {
                $eduInsertCols[] = 'edu_from';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'edu_from';
            }
            if (has_column($education_columns, 'edu_to')) {
                $eduInsertCols[] = 'edu_to';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'edu_to';
            }
            if (has_column($education_columns, 'year_graduated')) {
                $eduInsertCols[] = 'year_graduated';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'year_graduated';
            }
            if (has_column($education_columns, 'honors')) {
                $eduInsertCols[] = 'honors';
                $eduBindTypes .= "s";
                $eduValueKeys[] = 'honors';
            }

            if (count($eduInsertCols) > 1) {
                $colList = "`" . implode("`,`", $eduInsertCols) . "`";
                $placeholders = implode(",", array_fill(0, count($eduInsertCols), "?"));
                $stmt = $conn->prepare("INSERT INTO `{$education_table}` ({$colList}) VALUES ({$placeholders})");

                foreach ($educationRows as $rowData) {
                    $rowData['person_id'] = $id;

                    $bindValues = [];
                    foreach ($eduValueKeys as $key) {
                        $bindValues[] = $rowData[$key];
                    }

                    $params = [];
                    $params[] = $eduBindTypes;
                    foreach ($bindValues as $k => $v) {
                        $params[] = &$bindValues[$k];
                    }

                    call_user_func_array([$stmt, 'bind_param'], $params);
                    $stmt->execute();
                }

                $stmt->close();
            }
        }

        if ($eligibility_table && has_column($eligibility_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$eligibility_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $eligInsertCols = [];
            $eligBindTypes = "i";
            $eligValueKeys = [];

            if (has_column($eligibility_columns, 'person_id')) {
                $eligInsertCols[] = 'person_id';
                $eligValueKeys[] = 'person_id';
            }
            if (has_column($eligibility_columns, 'career_service')) {
                $eligInsertCols[] = 'career_service';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'career_service';
            }
            if (has_column($eligibility_columns, 'rating')) {
                $eligInsertCols[] = 'rating';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'rating';
            }
            if (has_column($eligibility_columns, 'exam_date')) {
                $eligInsertCols[] = 'exam_date';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'exam_date';
            }
            if (has_column($eligibility_columns, 'exam_place')) {
                $eligInsertCols[] = 'exam_place';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'exam_place';
            }
            if (has_column($eligibility_columns, 'license')) {
                $eligInsertCols[] = 'license';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'license';
            }
            if (has_column($eligibility_columns, 'license_number')) {
                $eligInsertCols[] = 'license_number';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'license_number';
            }
            if (has_column($eligibility_columns, 'valid_until')) {
                $eligInsertCols[] = 'valid_until';
                $eligBindTypes .= "s";
                $eligValueKeys[] = 'valid_until';
            }

            if (count($eligInsertCols) > 1) {
                $colList = "`" . implode("`,`", $eligInsertCols) . "`";
                $placeholders = implode(",", array_fill(0, count($eligInsertCols), "?"));
                $stmt = $conn->prepare("INSERT INTO `{$eligibility_table}` ({$colList}) VALUES ({$placeholders})");

                foreach ($eligibilityRows as $rowData) {
                    $rowData['person_id'] = $id;

                    $bindValues = [];
                    foreach ($eligValueKeys as $key) {
                        $bindValues[] = $rowData[$key];
                    }

                    $params = [];
                    $params[] = $eligBindTypes;
                    foreach ($bindValues as $k => $v) {
                        $params[] = &$bindValues[$k];
                    }

                    call_user_func_array([$stmt, 'bind_param'], $params);
                    $stmt->execute();
                }

                $stmt->close();
            }
        }

        if ($training_table && has_column($training_columns, 'person_id')) {
            $stmt = $conn->prepare("DELETE FROM `{$training_table}` WHERE person_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            $trainInsertCols = [];
            $trainBindTypes = "i";
            $trainValueKeys = [];

            if (has_column($training_columns, 'person_id')) {
                $trainInsertCols[] = 'person_id';
                $trainValueKeys[] = 'person_id';
            }
            if (has_column($training_columns, 'title')) {
                $trainInsertCols[] = 'title';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'title';
            }
            if (has_column($training_columns, 'hours')) {
                $trainInsertCols[] = 'hours';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'hours';
            }
            if (has_column($training_columns, 'training_from')) {
                $trainInsertCols[] = 'training_from';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'training_from';
            }
            if (has_column($training_columns, 'training_to')) {
                $trainInsertCols[] = 'training_to';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'training_to';
            }
            if (has_column($training_columns, 'type')) {
                $trainInsertCols[] = 'type';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'type';
            }
            if (has_column($training_columns, 'sponsor')) {
                $trainInsertCols[] = 'sponsor';
                $trainBindTypes .= "s";
                $trainValueKeys[] = 'sponsor';
            }

            if (count($trainInsertCols) > 1) {
                $colList = "`" . implode("`,`", $trainInsertCols) . "`";
                $placeholders = implode(",", array_fill(0, count($trainInsertCols), "?"));
                $stmt = $conn->prepare("INSERT INTO `{$training_table}` ({$colList}) VALUES ({$placeholders})");

                foreach ($trainingRows as $rowData) {
                    $rowData['person_id'] = $id;

                    $bindValues = [];
                    foreach ($trainValueKeys as $key) {
                        $bindValues[] = $rowData[$key];
                    }

                    $params = [];
                    $params[] = $trainBindTypes;
                    foreach ($bindValues as $k => $v) {
                        $params[] = &$bindValues[$k];
                    }

                    call_user_func_array([$stmt, 'bind_param'], $params);
                    $stmt->execute();
                }

                $stmt->close();
            }
        }

        $conn->commit();

        $full_name = trim($firstname . ' ' . $middlename . ' ' . $surname . ' ' . $extension);
        write_audit_log($conn, $id, 'UPDATE', "Updated PDS record for " . $full_name);

        $_SESSION['success_message'] = "Record updated successfully.";

        $redirectUrl = $_SERVER['PHP_SELF']
            . '?id=' . urlencode((string)$id)
            . '&search=' . urlencode($search)
            . '&sort=' . urlencode($sort)
            . '&saved=1';

        header("Location: " . $redirectUrl);
        exit;
    } catch (Exception $ex) {
        try {
            if (isset($conn) && $conn instanceof mysqli) {
                $conn->rollback();
            }
        } catch (Throwable $rollbackError) {
        }

        $error = "Update failed: " . $ex->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| SEARCH / LIST
|--------------------------------------------------------------------------
*/
$stmt = null;

if ($search !== "") {
    $searchTerm = trim($search);

    if (mb_strlen($searchTerm) === 1) {
        $prefix = $searchTerm . "%";

        if ($has_photo_column && $has_photo_type_column) {
            $stmt = $conn->prepare("
                SELECT id, firstname, middlename, surname, extension, email, mobile, photo, photo_type
                FROM personal_info
                WHERE surname LIKE ?
                ORDER BY surname {$order}, firstname {$order}
            ");
        } else {
            $stmt = $conn->prepare("
                SELECT id, firstname, middlename, surname, extension, email, mobile
                FROM personal_info
                WHERE surname LIKE ?
                ORDER BY surname {$order}, firstname {$order}
            ");
        }

        $stmt->bind_param("s", $prefix);
    } else {
        $like = "%" . $searchTerm . "%";

        if ($has_photo_column && $has_photo_type_column) {
            $stmt = $conn->prepare("
                SELECT id, firstname, middlename, surname, extension, email, mobile, photo, photo_type
                FROM personal_info
                WHERE firstname LIKE ?
                   OR surname LIKE ?
                   OR CONCAT(firstname, ' ', surname) LIKE ?
                   OR CONCAT(surname, ' ', firstname) LIKE ?
                   OR CONCAT(surname, ', ', firstname) LIKE ?
                ORDER BY surname {$order}, firstname {$order}
            ");
        } else {
            $stmt = $conn->prepare("
                SELECT id, firstname, middlename, surname, extension, email, mobile
                FROM personal_info
                WHERE firstname LIKE ?
                   OR surname LIKE ?
                   OR CONCAT(firstname, ' ', surname) LIKE ?
                   OR CONCAT(surname, ' ', firstname) LIKE ?
                   OR CONCAT(surname, ', ', firstname) LIKE ?
                ORDER BY surname {$order}, firstname {$order}
            ");
        }

        $stmt->bind_param("sssss", $like, $like, $like, $like, $like);
    }
} else {
    if ($has_photo_column && $has_photo_type_column) {
        $stmt = $conn->prepare("
            SELECT id, firstname, middlename, surname, extension, email, mobile, photo, photo_type
            FROM personal_info
            ORDER BY surname {$order}, firstname {$order}
        ");
    } else {
        $stmt = $conn->prepare("
            SELECT id, firstname, middlename, surname, extension, email, mobile
            FROM personal_info
            ORDER BY surname {$order}, firstname {$order}
        ");
    }
}

$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $results[] = $row;
}
$stmt->close();

/*
|--------------------------------------------------------------------------
| LOAD PERSON DETAILS
|--------------------------------------------------------------------------
*/
if ($selected_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM personal_info WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $person = $result->fetch_assoc();
    $stmt->close();

    if ($person) {
        $full_name = trim(
            ($person['firstname'] ?? '') . ' ' .
            ($person['middlename'] ?? '') . ' ' .
            ($person['surname'] ?? '') . ' ' .
            ($person['extension'] ?? '')
        );

        write_audit_log($conn, $selected_id, 'OPEN_EDIT', "Opened edit page for " . $full_name);
    }

    if ($person) {
        $address_rows = [];
        $stmt = $conn->prepare("SELECT * FROM addresses WHERE person_id = ? ORDER BY id ASC");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $addrResult = $stmt->get_result();
        while ($row = $addrResult->fetch_assoc()) {
            $address_rows[] = $row;
        }
        $stmt->close();

        foreach ($address_rows as $addr) {
            $normalized = normalize_address_row($addr);
            $type = strtolower(trim((string)($addr['type'] ?? '')));

            if ($type === 'residential') {
                $residential = $normalized;
            } elseif ($type === 'permanent') {
                $permanent = $normalized;
            }
        }

        $education_records = [];
        $eligibility_records = [];
        $training_records = [];

        if ($education_table && has_column($education_columns, 'person_id')) {
            $stmt = $conn->prepare("SELECT * FROM `{$education_table}` WHERE person_id = ? ORDER BY id ASC");
            $stmt->bind_param("i", $selected_id);
            $stmt->execute();
            $eduResult = $stmt->get_result();
            while ($row = $eduResult->fetch_assoc()) {
                $education_records[] = $row;
            }
            $stmt->close();
        }

        if ($eligibility_table && has_column($eligibility_columns, 'person_id')) {
            $stmt = $conn->prepare("SELECT * FROM `{$eligibility_table}` WHERE person_id = ? ORDER BY id ASC");
            $stmt->bind_param("i", $selected_id);
            $stmt->execute();
            $eligResult = $stmt->get_result();
            while ($row = $eligResult->fetch_assoc()) {
                $eligibility_records[] = $row;
            }
            $stmt->close();
        }

        if ($training_table && has_column($training_columns, 'person_id')) {
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search, View, Edit and Delete Personal Record</title>
<style>
*, *::before, *::after{box-sizing:border-box;}
html, body{overflow-x:hidden;}
img{max-width:100%;height:auto;}
input, select, textarea, button{font:inherit;}

body{
  font-family: Arial, sans-serif;
  margin:0;
  background:#e6e6e6;
  color:#222;
}

.page{
  max-width:1300px;
  margin:110px auto 20px;
  padding:0 16px 40px;
}

.top-link{
  display:inline-block;
  margin-bottom:15px;
  text-decoration:none;
  color:#fff;
  background:#22361e;
  padding:10px 14px;
  border-radius:6px;
}

h1{
  margin:0 0 18px;
  font-size:28px;
}

.card-simple{
  background:#fff;
  border:1px solid #cfcfcf;
  border-radius:12px;
  padding:20px;
  margin-bottom:20px;
  box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.simple-grid{
  display:flex;
  align-items:end;
  gap:12px;
  flex-wrap:wrap;
}

.simple-field{
  display:flex;
  align-items:center;
  gap:8px;
}

.simple-field label{
  font-weight:bold;
  white-space:nowrap;
}

.search-name-field input{
  width:320px;
  padding:10px 12px;
}

.sort-field select{
  width:95px;
  padding:10px 12px;
}

.message{
  background:#e7f6e8;
  border:1px solid #98d0a2;
  color:#1f6b2e;
  padding:12px 14px;
  border-radius:8px;
  margin-bottom:15px;
}

.error{
  background:#fdecec;
  border:1px solid #efb3b3;
  color:#a11a1a;
  padding:12px 14px;
  border-radius:8px;
  margin-bottom:15px;
}

.notice{
  background:#fff8df;
  border:1px solid #ead48a;
  color:#7a5d00;
  padding:10px 12px;
  border-radius:8px;
  margin-bottom:15px;
}

.search-actions{
  display:flex;
  align-items:end;
  gap:10px;
}

.btn-primary,
.btn-secondary,
.btn-danger,
.btn-link{
  border:none;
  border-radius:6px;
  padding:10px 16px;
  cursor:pointer;
  font-size:14px;
  text-decoration:none;
  display:inline-block;
}

.btn-primary{
  background:#22361e;
  color:#fff;
}

.btn-secondary{
  background:#6c757d;
  color:#fff;
}

.btn-danger{
  background:#b42318;
  color:#fff;
}

table{
  width:100%;
  border-collapse:collapse;
}

.table-wrap{
  width:100%;
  overflow-x:auto;
}

th, td{
  border:1px solid #d7d7d7;
  padding:10px;
  text-align:left;
  vertical-align:top;
}

th{
  background:#f0f3ef;
}

.muted{
  color:#666;
  font-size:13px;
}

.edit-wrapper{
  margin-top:20px;
}

.container{
  display:flex;
  gap:20px;
  align-items:flex-start;
  min-height:calc(100vh - 50px);
  max-height:calc(90vh - 40px);
  align-items:flex-start;
}

.sidebar{
  width:270px;
  flex:0 0 270px;
  padding:20px 20px 60px;
  position:sticky;
  top:10px;
  align-self:flex-start;
  display:flex;
  flex-direction:column;
  gap:24px;
  min-height:calc(90vh - 80px);
  box-sizing:border-box;
}

.sidebar::before{
  content:"";
  position:absolute;
  left:57px;
  top:65px;
  height:0; 
  width:3px;
  background:#ccc;
  z-index:1;
}

.progress-line{
  position:absolute;
  left:57px;
  top:65px;
  width:3px;
  height:0;
  background:#d6c86f;
  transition:0.4s;
  z-index:2;
}

.nav-item{
  display:flex;
  align-items:center;
  gap:15px;
  cursor:pointer;
  padding:10px 15px;
  border-radius:10px;
  transition:0.2s;
  z-index:3;
  position:relative;
  background:transparent;
}

.nav-item:hover{
  background:#f4f0d3;
}

.nav-icon{
  width:45px;
  height:45px;
  object-fit:cover;
  position:relative;
  z-index:3;
}

.nav-label{
  font-size:13px;
  font-weight:bold;
}

.nav-item.active{
  background:#efe5b6;
  border:2px solid #a5a079;
}

.form-area{
  flex:1 1 auto;
  min-width:0;
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:20px 10px 20px 20px;
  overflow-y:auto;
  overflow-x:hidden;
  max-height:calc(90vh - 80px);
  box-sizing:border-box;
}

.card{
  width:100%;
  min-height:700px;
  max-width:100%;
  background:#c7d1c3;
  padding:20px 32px 30px;
  border-radius:15px;
  border:3px solid black;
  box-sizing:border-box;
  overflow-x:hidden;
}

.title{
  text-align:center;
  font-size:22px;
  font-weight:800;
  margin-bottom:25px;
  margin-top:5px;
}


.simple-grid > *,
.search-actions > *,
.container > *,
.personal-grid > *,
.personal-row > *,
.citizenship-row > *,
.address-house-row > *,
.address-row > *,
.address-two-col > *,
.contact-grid > *,
.contact-row > *,
.education-grid > *,
.eligibility-grid > *,
.training-grid > *{
  min-width:0;
}

/* PERSONAL INFORMATION */
.personal-grid{
  display:grid;
  grid-template-columns:minmax(140px, 160px) minmax(0, 1fr) minmax(140px, 160px) minmax(0, 1fr);
  gap:18px 12px;
  align-items:center;
  width:100%;
}

.personal-grid label,
.personal-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.personal-grid input,
.personal-grid select,
.personal-row input,
.personal-row select{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

.personal-row{
  display:grid;
  grid-template-columns:max-content minmax(110px, 140px) max-content minmax(110px, 140px) max-content minmax(110px, 140px);
  justify-content:start;
  align-items:center;
  gap:15px 12px;
  margin-top:20px;
}

.personal-row.small input,
.personal-row.small select{
  width:140px;
}

.citizenship-row{
  display:grid;
  grid-template-columns:minmax(120px, 150px) minmax(180px, 260px) minmax(180px, 240px) minmax(220px, 1fr);
  gap:18px 12px;
  align-items:center;
  margin-top:25px;
  width:100%;
}

.citizenship-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:normal;
  line-height:1.3;
}

.disabled-look-text{
  display:inline-block;
  width:100%;
  padding:6px 10px;
  background:#cfcfcf;
  border:1px solid #999;
  border-radius:6px;
  color:#666;
}


/* ADDRESS */
.address-section{
  padding-top:10px;
}

.address-title{
  text-align:center;
  font-size:25px;
  font-weight:800;
  margin:0 0 28px 0;
}

.address-block{
  width:100%;
  margin:0 0 28px 0;
}

.address-house-row{
  display:grid;
  grid-template-columns:minmax(140px, 160px) minmax(0, 1fr);
  align-items:center;
  gap:18px 5px;
  margin-bottom:18px;
  width:100%;
}

.address-two-col{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:18px 30px;
  width:100%;
}

.address-col{
  display:flex;
  flex-direction:column;
  gap:18px;
}

.address-row{
  display:grid;
  grid-template-columns:minmax(140px, 160px) minmax(0, 1fr);
  align-items:center;
  gap:18px 5px;
  width:100%;
}

.address-house-row label,
.address-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.address-house-row input,
.address-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* CONTACT */
.contact-section{
  padding-top:110px;
}

.contact-title{
  text-align:center;
  font-size:28px;
  font-weight:800;
  margin:0 0 48px 0;
}

.contact-grid{
  width:390px;
  margin:0 auto;
  display:flex;
  flex-direction:column;
  gap:28px;
}

.contact-row{
  display:grid;
  grid-template-columns:minmax(130px, 145px) minmax(0, 1fr);
  align-items:center;
  column-gap:10px;
}

.contact-row label{
  font-size:14px;
  font-weight:700;
  text-align:right;
  white-space:nowrap;
}

.contact-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* EDUCATION */
.education-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.education-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.education-grid label{
  font-size:13px;
  font-weight:bold;
}

.education-grid input,
.education-grid select{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px; 
  box-sizing:border-box;
}

/* ELIGIBILITY */
.eligibility-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.eligibility-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.eligibility-grid label{
  font-size:13px;
  font-weight:bold;
}

.eligibility-grid input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* TRAINING */
.training-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.training-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.training-grid label{
  font-size:13px;
  font-weight:bold;
}

.training-grid input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* GENERAL */
label{
  font-size:13px;
  font-weight:bold;
}

input, select{
  width:100%;
  padding:8px;
  border:1px solid #666;
  border-radius:4px;
  box-sizing:border-box;
}

button{
  margin-top:20px;
  padding:8px 20px;
  cursor:pointer;
}

.section{
  display:none;
  opacity:0;
  transform:translateY(20px);
  transition:0.3s;
}

.section.active{
  display:block;
  opacity:1;
  transform:translateY(0);
}

.nav-buttons{
  display:flex;
  justify-content:flex-end;
  gap:10px;
  margin-top:20px;
}

.next-btn,
.save-btn,
.add-btn{
  background:#22361e;
  color:#fff;
  border:none;
  padding:10px 22px;
  border-radius:6px;
  font-size:14px;
  font-weight:600;
  cursor:pointer;
  transition:0.2s;
  margin-top:0;
  text-decoration:none;
  display:inline-block;
}

.next-btn:hover,
.save-btn:hover,
.add-btn:hover{
  background:#22361e;
}

.remove-btn{
  background:#8b2c2c;;
  color:#fff;
  border:none;
  padding:8px 16px;
  border-radius:6px;
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  transition:0.2s;
  margin-top:10px;
}

.remove-btn:hover{
  background:#a63a3a;
}

.add-btn{
  margin-top:10px;
}

.sidebar-delete-form{
  margin-top:auto;
  padding:8px 15px 0;
  margin-bottom:12px;
  position:relative;
  z-index:3;
  background:transparent;
}

.sidebar-delete-btn{
  width:100%;
  background:#8b2c2c;
  color:#fff;
  border:none;
  padding:12px 16px;
  border-radius:8px;
  font-size:14px;
  font-weight:700;
  cursor:pointer;
  margin-top:0;
  box-shadow:0 4px 12px rgba(0,0,0,0.15);
}

.sidebar-delete-btn:hover{
  background:#a63a3a;
}

.top-actions{
  margin-bottom:20px;
}

.photo-home-row{
  display:grid;
  grid-template-columns:auto 1fr auto;
  align-items:center;
  margin-bottom:20px;
  gap:15px;
}

.photo-home-row .title{
  text-align:center;
  margin:0;
}

.photo-box{
  width:120px;
  height:120px;
  border:1px solid #555;
  border-radius:6px;
  overflow:hidden;
  cursor:pointer;
  display:flex;
  align-items:center;
  justify-content:center;
  background:#e9e9ee;
}

.photo-box img{
  width:100%;
  height:100%;
  object-fit:cover;
}

.photo-box input{
  display:none;
}

@media (max-width: 992px){
  .page{
    margin:90px auto 20px;
    padding:0 12px 28px;
  }

  .container{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
    flex:1 1 auto;
    max-height:none;
    padding:16px 12px;
    gap:12px;
  }

  .sidebar::before,
  .progress-line{
    display:none;
  }

  .nav-item{
    width:100%;
    padding:12px;
  }

  .card{
    padding:18px 16px 24px;
  }

  .contact-section{
    padding-top:40px;
  }

  .contact-grid{
    width:100%;
    max-width:100%;
  }
}

@media (max-width: 768px){
  .simple-grid{
    flex-direction:column;
    align-items:stretch;
  }

  .simple-field{
    width:100%;
    flex-direction:column;
    align-items:flex-start;
  }

  .search-name-field input,
  .sort-field select{
    width:100%;
  }

  .search-actions{
    width:100%;
    flex-wrap:wrap;
  }

  .search-actions .btn-primary,
  .search-actions .btn-secondary{
    width:100%;
    text-align:center;
  }

  .modal-overlay{
    padding:10px;
  }

  .modal-box{
    width:100%;
    max-width:100%;
    max-height:92vh;
    padding:12px;
  }

  .modal-close{
    top:6px;
    right:6px;
    width:42px;
    height:42px;
    font-size:32px;
  }

  .modal-edit-wrapper{
    margin-top:12px;
  }

  .container{
    flex-direction:column;
    gap:12px;
    min-height:auto;
    max-height:none;
  }

  .sidebar{
    position:relative;
    top:auto;
    width:100%;
    flex:0 0 auto;
    min-height:auto;
    max-height:none;
    flex-direction:row;
    align-items:stretch;
    gap:10px;
    overflow-x:auto;
    overflow-y:hidden;
    padding:12px 12px 6px;
  }

  .sidebar::before,
  .progress-line{
    display:none;
  }

  .nav-item{
    flex:0 0 220px;
    min-height:72px;
    padding:12px;
  }

  .nav-label{
    font-size:12px;
  }

  .sidebar-delete-form{
    margin-top:0;
    margin-bottom:18px;
    padding:0;
    flex:0 0 180px;
    align-self:stretch;
    display:flex;
    align-items:flex-end;
  }

  .sidebar-delete-btn{
    width:100%;
  }

  .form-area{
    width:100%;
    padding:6px 0 0;
    max-height:none;
  }

  .card{
    width:100%;
  }

  .photo-home-row{
    grid-template-columns:1fr;
    justify-items:center;
    text-align:center;
  }

  .personal-grid,
  .personal-row,
  .citizenship-row,
  .education-grid,
  .eligibility-grid,
  .training-grid,
  .address-house-row,
  .address-row,
  .address-two-col,
  .contact-row{
    grid-template-columns:1fr !important;
    row-gap:10px;
    column-gap:0;
  }

  .personal-grid label,
  .personal-row label,
  .citizenship-row label,
  .contact-row label,
  .address-house-row label,
  .address-row label{
    text-align:left;
  }

  .personal-grid input,
  .personal-grid select,
  .personal-row input,
  .personal-row select,
  .citizenship-row input,
  .citizenship-row select,
  .address-house-row input,
  .address-row input,
  .education-grid input,
  .education-grid select,
  .eligibility-grid input,
  .training-grid input,
  .contact-row input{
    width:100%;
    max-width:100%;
    min-width:0;
  }
}

@media (max-width: 480px){
  .page{
    padding:0 10px 24px;
  }

  .card-simple,
  .card{
    padding:12px;
    border-radius:10px;
  }

  .btn-primary,
  .btn-secondary,
  .btn-danger,
  .btn-link,
  .next-btn,
  .save-btn,
  .add-btn,
  .remove-btn,
  .sidebar-delete-btn{
    width:100%;
    text-align:center;
  }

  th, td{
    padding:8px;
    font-size:13px;
  }
}

.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,0.35);
  backdrop-filter:blur(8px);
  -webkit-backdrop-filter:blur(8px);
  display:none;
  align-items:center;
  justify-content:center;
  padding:20px;
  z-index:9999;
}

.modal-overlay.show{
  display:flex;
}

.modal-box{
  background:#fff;
  width:min(1200px, 95vw);
  max-height:90vh;
  overflow:hidden;
  border-radius:16px;
  box-shadow:0 20px 60px rgba(0,0,0,0.25);
  position:relative;
  padding:20px;
}

.modal-close{
  position:absolute;
  top:10px;
  right:10px;
  border:none;
  background:#ffffff;
  font-size:38px;
  line-height:1;
  cursor:pointer;
  color:#111;
  font-weight:700;
  width:48px;
  height:48px;
  border-radius:50%;
  box-shadow:0 4px 12px rgba(0,0,0,0.18);
  display:flex;
  align-items:center;
  justify-content:center;
  z-index:20;
}

.modal-edit-wrapper{
  margin-top:20px;
}

body.modal-open{
  overflow:hidden;
}

.toast{
  position:fixed;
  top:20px;
  right:20px;
  z-index:10050;
  min-width:280px;
  max-width:420px;
  padding:14px 16px;
  border-radius:10px;
  color:#fff;
  font-weight:700;
  box-shadow:0 8px 20px rgba(0,0,0,0.18);
  opacity:0;
  transform:translateY(-10px);
  pointer-events:none;
  transition:all 0.25s ease;
}

.toast.show{
  opacity:1;
  transform:translateY(0);
}

.toast-success{
  background:#1f6b2e;
}

.toast-error{
  background:#a11a1a;
}

.unsaved-banner{
  display:none;
  position:sticky;
  top:0;
  z-index:15;
  background:#fff8df;
  border:1px solid #ead48a;
  color:#7a5d00;
  padding:10px 12px;
  border-radius:8px;
  margin-bottom:15px;
  font-weight:700;
}

.unsaved-banner.show{
  display:block;
}

</style>
</head>
<body>
<?php include "../includes/header.php"; ?>

<div class="page">
    <a href="../dashboard/dashboard.php" class="top-link">🏠︎ Home</a>

    <h1>Search, View, Edit and Delete Personal Record</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo e($message); ?></div>
        <div id="toastSuccess" class="toast toast-success"><?php echo e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo e($error); ?></div>
        <div id="toastError" class="toast toast-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card-simple">
        <h2>Search</h2>
        <form method="GET">
            <div class="simple-grid">
                <div class="simple-field search-name-field">
                    <label for="search">Search Name</label>
                    <input type="text" id="search" name="search" value="<?php echo e($search); ?>" placeholder="Enter first name or surname">
                </div>

                <div class="simple-field sort-field">
                    <label for="sort">Sort</label>
                    <select name="sort" id="sort" onchange="this.form.submit()">
                        <option value="asc" <?php echo ($sort === 'asc') ? 'selected' : ''; ?>>A - Z</option>
                        <option value="desc" <?php echo ($sort === 'desc') ? 'selected' : ''; ?>>Z - A</option>
                    </select>
                </div>

                <div class="search-actions">
                    <button type="submit" class="btn-primary">Search 🔍︎</button>
                    <a href="<?php echo e($_SERVER['PHP_SELF']); ?>" class="btn-link btn-secondary">⟳ Clear</a>
                </div>
            </div>
        </form>
    </div>

    <div class="card-simple table-wrap">
    <h2><?php echo ($search !== "") ? 'Search Results' : 'All Records'; ?></h2>
    <?php if (count($results) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th style="width:100px;">Photo</th>
                    <th>Full Name</th>
                    <th style="width:110px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td>
                            <img
                                src="<?php echo e(make_photo_src($row['photo'] ?? null, $row['photo_type'] ?? null)); ?>"
                                alt="Photo"
                                style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #ccc;"
                            >
                        </td>
                        <td>
                            <?php
                            echo e(trim(
                                ($row['surname'] ?? '') . ", " .
                                ($row['firstname'] ?? '') . " " .
                                ($row['middlename'] ?? '') . " " .
                                ($row['extension'] ?? '')
                            ));
                            ?>
                        </td>
                        <td>
                            <a class="btn-link btn-primary" href="?search=<?php echo urlencode($search); ?>&sort=<?php echo urlencode($sort); ?>&id=<?php echo (int)$row['id']; ?>">
                                ✎ Edit
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No record found.</p>
    <?php endif; ?>
</div>
    <?php if ($person): ?>
    <div id="editModal" class="modal-overlay show">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
            <div class="edit-wrapper modal-edit-wrapper">

        <?php if (!$education_table): ?>
            <div class="notice">Education table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <?php if (!$eligibility_table): ?>
            <div class="notice">Eligibility table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <?php if (!$training_table): ?>
            <div class="notice">Training / Learning and Development table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <div class="container">
            <div class="sidebar">
                <div class="progress-line" id="progressLine"></div>

                <div class="nav-item active" onclick="goToSection(0)">
                    <img src="../assets/profile.png" alt="Profile" class="nav-icon">
                    <div class="nav-label">PERSONAL INFORMATION</div>
                </div>

                <div class="nav-item" onclick="goToSection(1)">
                    <img src="../assets/address.png" alt="Address" class="nav-icon">
                    <div class="nav-label">ADDRESS</div>
                </div>

                <div class="nav-item" onclick="goToSection(2)">
                    <img src="../assets/contact.png" alt="Contact" class="nav-icon">
                    <div class="nav-label">CONTACT INFORMATION</div>
                </div>

                <?php if ($education_table): ?>
                <div class="nav-item" onclick="goToSection(3)">
                    <img src="../assets/education.png" alt="Education" class="nav-icon">
                    <div class="nav-label">EDUCATIONAL BACKGROUND</div>
                </div>
                <?php endif; ?>

                <?php if ($eligibility_table): ?>
                <div class="nav-item" onclick="goToSection(<?php echo $education_table ? '4' : '3'; ?>)">
                    <img src="../assets/service.png" alt="Service" class="nav-icon">
                    <div class="nav-label">SERVICE ELIGIBILITY</div>
                </div>
                <?php endif; ?>

                <?php if ($training_table): ?>
                <div class="nav-item" onclick="goToSection(
                    <?php
                        if ($education_table && $eligibility_table) echo '5';
                        elseif ($education_table || $eligibility_table) echo '4';
                        else echo '3';
                    ?>
                )">
                    <img src="../assets/learning.png" alt="Training" class="nav-icon">
                    <div class="nav-label">LEARNING AND DEVELOPMENT</div>
                </div>
                <?php endif; ?>

                <form method="POST" class="sidebar-delete-form" onsubmit="return confirm('Delete this record? This action cannot be undone.');">
                    <input type="hidden" name="id" value="<?php echo e($person['id']); ?>">
                    <input type="hidden" name="search" value="<?php echo e($search); ?>">
                    <button type="submit" name="delete" class="sidebar-delete-btn">🗑️ Delete Record</button>
                </form>
            </div>

            <div class="form-area">
                <div class="card">
                    <form method="POST" id="editRecordForm" autocomplete="off" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo e($person['id']); ?>">
                        <input type="hidden" name="search" value="<?php echo e($search); ?>">
                        <div id="unsavedBanner" class="unsaved-banner">You have unsaved changes.</div>

                        <div id="personal" class="section active">
                            <div class="photo-home-row">
                                <div class="top-actions">
                                    <a href="../dashboard/dashboard.php" class="top-link">🏠︎ Home</a>
                                </div>

                                <div class="title">PERSONAL INFORMATION</div>
                                 
                                <label class="photo-box">
                                    <?php $photoSrc = make_photo_src($person['photo'] ?? null, $person['photo_type'] ?? null); ?>
                                    <img id="preview" src="<?php echo e($photoSrc); ?>" alt="Profile Preview">
                                    <input type="file" name="photo" id="photo" image/*" onchange="loadImage(event)">
                                </label>
                            </div>

                            <div class="personal-grid">
                                <label>Last Name:</label>
                                <input name="surname" value="<?php echo e($person['surname'] ?? ''); ?>">

                                <label>Name Extension:</label>
                                <select name="extension">
                                    <option value="" <?php echo (($person['extension'] ?? '') === '') ? 'selected' : ''; ?>>None</option>
                                    <option value="Jr." <?php echo (($person['extension'] ?? '') === 'Jr.') ? 'selected' : ''; ?>>Jr.</option>
                                    <option value="Sr." <?php echo (($person['extension'] ?? '') === 'Sr.') ? 'selected' : ''; ?>>Sr.</option>
                                    <option value="II" <?php echo (($person['extension'] ?? '') === 'II') ? 'selected' : ''; ?>>II</option>
                                    <option value="III" <?php echo (($person['extension'] ?? '') === 'III') ? 'selected' : ''; ?>>III</option>
                                    <option value="IV" <?php echo (($person['extension'] ?? '') === 'IV') ? 'selected' : ''; ?>>IV</option>
                                    <option value="V" <?php echo (($person['extension'] ?? '') === 'V') ? 'selected' : ''; ?>>V</option>
                                </select>

                                <label>First Name:</label>
                                <input name="firstname" value="<?php echo e($person['firstname'] ?? ''); ?>">

                                <label>Date of Birth:</label>
                                <input type="date" name="dob" value="<?php echo e($person['dob'] ?? ''); ?>" required>

                                <label>Middle Name:</label>
                                <input name="middlename" value="<?php echo e($person['middlename'] ?? ''); ?>">

                                <label>Place of Birth:</label>
                                <input name="birth_place" value="<?php echo e($person['birth_place'] ?? ''); ?>">
                            </div>

                            <div class="personal-row small">
                                <label>Civil Status:</label>
                                <select name="civil_status">
                                    <option value=""></option>
                                    <option value="Single" <?php echo (($person['civil_status'] ?? '') === 'Single') ? 'selected' : ''; ?>>Single</option>
                                    <option value="Married" <?php echo (($person['civil_status'] ?? '') === 'Married') ? 'selected' : ''; ?>>Married</option>
                                    <option value="Widowed" <?php echo (($person['civil_status'] ?? '') === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                                    <option value="Separated" <?php echo (($person['civil_status'] ?? '') === 'Separated') ? 'selected' : ''; ?>>Separated</option>
                                </select>

                                <label>Sex:</label>
                                <select name="sex">
                                    <option value=""></option>
                                    <option value="Male" <?php echo (($person['sex'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                                    <option value="Female" <?php echo (($person['sex'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                                </select>

                                    <label>Blood Type:</label>
                                        <select name="blood_type">
                                            <option value=""> </option>
                                            <option value="A+" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                            <option value="A-" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                            <option value="B+" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                            <option value="B-" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                            <option value="AB+" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                            <option value="AB-" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                            <option value="O+" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                            <option value="O-" <?php echo (isset($person['blood_type']) && $person['blood_type'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                                        </select>
                            </div>

                            <div class="personal-row small">
                                <label>Height (cm):</label>
                                <input name="height" value="<?php echo e($person['height'] ?? ''); ?>">

                                <label>Weight (kg):</label>
                                <input name="weight" value="<?php echo e($person['weight'] ?? ''); ?>">
                            </div>

                            <div class="personal-grid" style="margin-top:25px;">
                                <label>UMID ID:</label>
                                <input name="umid" value="<?php echo e($person['umid'] ?? ''); ?>">

                                <label>PhilSys No.(PSN):</label>
                                <input name="philsys" value="<?php echo e($person['philsys'] ?? ''); ?>">

                                <label>Pag-IBIG ID No:</label>
                                <input name="pagibig" value="<?php echo e($person['pagibig'] ?? ''); ?>">

                                <label>TIN No:</label>
                                <input name="tin" value="<?php echo e($person['tin'] ?? ''); ?>">

                                <label>PhilHealth No:</label>
                                <input name="philhealth" value="<?php echo e($person['philhealth'] ?? ''); ?>">

                                <label>Agency Employee No:</label>
                                <input name="agency_employee" value="<?php echo e($person['agency_employee'] ?? ''); ?>">
                            </div>

                            <div class="citizenship-row">
                                    <label>Citizenship:</label>
                                    <select name="citizenship" id="citizenship">
                                        <option value=""></option>
                                        <option value="Filipino" <?php echo (($person['citizenship'] ?? '') === 'Filipino') ? 'selected' : ''; ?>>Filipino</option>
                                        <option value="Dual Citizen" <?php echo (($person['citizenship'] ?? '') === 'Dual Citizen') ? 'selected' : ''; ?>>Dual Citizen</option>
                                    </select>

                                    <label>If Dual Citizen (Indicate Country):</label>
                                    <input 
                                        name="dual_country"
                                        id="dual_country"
                                        value="<?php echo e($person['dual_country'] ?? ''); ?>"
                                        class="<?php echo (($person['citizenship'] ?? '') === 'Filipino') ? 'disabled-look' : ''; ?>"
                                        <?php echo (($person['citizenship'] ?? '') === 'Filipino') ? 'disabled' : ''; ?>
                                    >
                            </div>
                        </div>

                        <div id="address" class="section">
                            <div class="address-section">
                                <div class="address-title">RESIDENTIAL ADDRESS</div>
                                <div class="address-block">
                                    <div class="address-house-row">
                                        <label>House / Block / Lot No.</label>
                                        <input name="r_house1" value="<?php echo e($residential['house1']); ?>">
                                    </div>

                                    <div class="address-two-col">
                                        <div class="address-col">
                                            <div class="address-row">
                                                <label>Street:</label>
                                                <input name="r_street" value="<?php echo e($residential['street']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Subdivision / Village:</label>
                                                <input name="r_subdivision" value="<?php echo e($residential['subdivision']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>City / Municipality:</label>
                                                <input name="r_city" value="<?php echo e($residential['city']); ?>">
                                            </div>
                                        </div>

                                        <div class="address-col">
                                            <div class="address-row">
                                                <label>Barangay:</label>
                                                <input name="r_barangay" value="<?php echo e($residential['barangay']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Province:</label>
                                                <input name="r_province" value="<?php echo e($residential['province']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Zip Code:</label>
                                                <input name="r_zip" value="<?php echo e($residential['zip']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div style="display:flex; justify-content:flex-end; margin-top:18px; margin-bottom:5px;">
                                    <label style="display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:red; text-transform:uppercase; white-space:nowrap; cursor:pointer;">
                                        <input type="checkbox" id="sameAsResidentAddress" style="width:14px; height:14px; margin:0;">
                                        SAME AS RESIDENT ADDRESS
                                    </label>
                                </div>

                                <div class="address-title" style="margin-top:0; text-align:center;">
                                    PERMANENT ADDRESS
                                </div>
                                <div class="address-block">

                                    <div class="address-house-row">
                                        <label>House / Block / Lot No.</label>
                                        <input name="p_house1" value="<?php echo e($permanent['house1']); ?>">
                                    </div>

                                    <div class="address-two-col">
                                        <div class="address-col">
                                            <div class="address-row">
                                                <label>Street:</label>
                                                <input name="p_street" value="<?php echo e($permanent['street']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Subdivision / Village:</label>
                                                <input name="p_subdivision" value="<?php echo e($permanent['subdivision']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>City / Municipality:</label>
                                                <input name="p_city" value="<?php echo e($permanent['city']); ?>">
                                            </div>
                                        </div>

                                        <div class="address-col">
                                            <div class="address-row">
                                                <label>Barangay:</label>
                                                <input name="p_barangay" value="<?php echo e($permanent['barangay']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Province:</label>
                                                <input name="p_province" value="<?php echo e($permanent['province']); ?>">
                                            </div>

                                            <div class="address-row">
                                                <label>Zip Code:</label>
                                                <input name="p_zip" value="<?php echo e($permanent['zip']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="contact" class="section">
                            <div class="contact-section">
                                <div class="contact-title">CONTACT INFORMATION</div>

                                <div class="contact-grid">
                                    <div class="contact-row">
                                        <label>Telephone Number:</label>
                                        <input name="telephone" value="<?php echo e($person['telephone'] ?? ''); ?>">
                                    </div>

                                    <div class="contact-row">
                                        <label>Mobile Number:</label>
                                        <input name="mobile" value="<?php echo e($person['mobile'] ?? ''); ?>">
                                    </div>

                                    <div class="contact-row">
                                        <label>E-Mail:</label>
                                        <input type="email" name="email" value="<?php echo e($person['email'] ?? ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($education_table): ?>
                        <div id="education" class="section">
                            <div class="title">EDUCATIONAL BACKGROUND</div>

                            <div id="education-container">
                                <?php if (!empty($education_records)): ?>
                                    <?php foreach ($education_records as $edu): ?>
                                        <div class="education-entry education-box">
                                            <div class="education-grid">
                                                <div>
                                                    <label>Level</label>
                                                    <select name="education_level[]">
                                                        <option value="Elementary" <?php echo (($edu['education_level'] ?? '') === 'Elementary') ? 'selected' : ''; ?>>Elementary</option>
                                                        <option value="Secondary" <?php echo (($edu['education_level'] ?? '') === 'Secondary') ? 'selected' : ''; ?>>Secondary</option>
                                                        <option value="Vocational / Trade Course" <?php echo (($edu['education_level'] ?? '') === 'Vocational / Trade Course') ? 'selected' : ''; ?>>Vocational / Trade Course</option>
                                                        <option value="College" <?php echo (($edu['education_level'] ?? '') === 'College') ? 'selected' : ''; ?>>College</option>
                                                        <option value="Graduate Studies" <?php echo (($edu['education_level'] ?? '') === 'Graduate Studies') ? 'selected' : ''; ?>>Graduate Studies</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label>Name of School</label>
                                                    <input name="school_name[]" value="<?php echo e($edu['school_name'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Basic Education /Degree /Course</label>
                                                    <input name="course[]" value="<?php echo e($edu['course'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Highest Level /Units Earned</label>
                                                    <input name="units[]" value="<?php echo e($edu['units'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Period of Attendance From</label>
                                                    <input type="date" name="edu_from[]" value="<?php echo e($edu['edu_from'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>To</label>
                                                    <input type="date" name="edu_to[]" value="<?php echo e($edu['edu_to'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Year Graduated</label>
                                                    <input name="year_graduated[]" value="<?php echo e($edu['year_graduated'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Scholarship /Academic Honors</label>
                                                    <input name="honors[]" value="<?php echo e($edu['honors'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">✖ Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="education-entry education-box">
                                        <div class="education-grid">
                                            <div>
                                                <label>Level</label>
                                                <select name="education_level[]">
                                                    <option>Elementary</option>
                                                    <option>Secondary</option>
                                                    <option>Vocational / Trade Course</option>
                                                    <option>College</option>
                                                    <option>Graduate Studies</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label>Name of School</label>
                                                <input name="school_name[]">
                                            </div>

                                            <div>
                                                <label>Basic Education /Degree /Course</label>
                                                <input name="course[]">
                                            </div>

                                            <div>
                                                <label>Highest Level /Units Earned</label>
                                                <input name="units[]">
                                            </div>

                                            <div>
                                                <label>Period of Attendance From</label>
                                                <input type="date" name="edu_from[]">
                                            </div>

                                            <div>
                                                <label>To</label>
                                                <input type="date" name="edu_to[]">
                                            </div>

                                            <div>
                                                <label>Year Graduated</label>
                                                <input name="year_graduated[]">
                                            </div>

                                            <div>
                                                <label>Scholarship /Academic Honors</label>
                                                <input name="honors[]">
                                            </div>
                                        </div>

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">✖ Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addEducation()">✚ Add More</button>
                        </div>
                        <?php endif; ?>

                        <?php if ($eligibility_table): ?>
                        <div id="eligibility-section" class="section">
                            <div class="title">SERVICE ELIGIBILITY</div>

                            <div id="eligibility-container">
                                <?php if (!empty($eligibility_records)): ?>
                                    <?php foreach ($eligibility_records as $elig): ?>
                                        <div class="eligibility-entry eligibility-box">
                                            <div class="eligibility-grid">
                                                <div>
                                                    <label>Career Service /CSC /CES</label>
                                                    <input name="career_service[]" value="<?php echo e($elig['career_service'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Rating</label>
                                                    <input name="rating[]" value="<?php echo e($elig['rating'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Exam Date</label>
                                                    <input type="date" name="exam_date[]" value="<?php echo e($elig['exam_date'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Place of Examination</label>
                                                    <input name="exam_place[]" value="<?php echo e($elig['exam_place'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>License</label>
                                                    <input name="license[]" value="<?php echo e($elig['license'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>License Number</label>
                                                    <input name="license_number[]" value="<?php echo e($elig['license_number'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Valid Until</label>
                                                    <input type="date" name="valid_until[]" value="<?php echo e($elig['valid_until'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">✖ Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="eligibility-entry eligibility-box">
                                        <div class="eligibility-grid">
                                            <div>
                                                <label>Career Service /CSC /CES</label>
                                                <input name="career_service[]">
                                            </div>

                                            <div>
                                                <label>Rating</label>
                                                <input name="rating[]">
                                            </div>

                                            <div>
                                                <label>Exam Date</label>
                                                <input type="date" name="exam_date[]">
                                            </div>

                                            <div>
                                                <label>Place of Examination</label>
                                                <input name="exam_place[]">
                                            </div>

                                            <div>
                                                <label>License</label>
                                                <input name="license[]">
                                            </div>

                                            <div>
                                                <label>License Number</label>
                                                <input name="license_number[]">
                                            </div>

                                            <div>
                                                <label>Valid Until</label>
                                                <input type="date" name="valid_until[]">
                                            </div>
                                        </div>

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">✖ Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addEligibility()">✚ Add More</button>
                        </div>
                        <?php endif; ?>

                        <?php if ($training_table): ?>
                        <div id="training-section" class="section">
                            <div class="title">LEARNING AND DEVELOPMENT</div>

                            <div id="training-container">
                                <?php if (!empty($training_records)): ?>
                                    <?php foreach ($training_records as $train): ?>
                                        <div class="training-entry training-box">
                                            <div class="training-grid">
                                                <div>
                                                    <label>Training Title</label>
                                                    <input name="title[]" value="<?php echo e($train['title'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Hours</label>
                                                    <input name="hours[]" value="<?php echo e($train['hours'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>From</label>
                                                    <input type="date" name="training_from[]" value="<?php echo e($train['training_from'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>To</label>
                                                    <input type="date" name="training_to[]" value="<?php echo e($train['training_to'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Type</label>
                                                    <input name="type[]" value="<?php echo e($train['type'] ?? ''); ?>">
                                                </div>

                                                <div>
                                                    <label>Sponsor</label>
                                                    <input name="sponsor[]" value="<?php echo e($train['sponsor'] ?? ''); ?>">
                                                </div>
                                            </div>

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">✖ Remove</button>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="training-entry training-box">
                                        <div class="training-grid">
                                            <div>
                                                <label>Training Title</label>
                                                <input name="title[]">
                                            </div>

                                            <div>
                                                <label>Hours</label>
                                                <input name="hours[]">
                                            </div>

                                            <div>
                                                <label>From</label>
                                                <input type="date" name="training_from[]">
                                            </div>

                                            <div>
                                                <label>To</label>
                                                <input type="date" name="training_to[]">
                                            </div>

                                            <div>
                                                <label>Type</label>
                                                <input name="type[]">
                                            </div>

                                            <div>
                                                <label>Sponsor</label>
                                                <input name="sponsor[]">
                                            </div>
                                        </div>

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">✖ Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addTraining()">✚ Add More</button>
                        </div>
                        <?php endif; ?>

                        <div class="nav-buttons">
                         <button type="button" class="next-btn" id="nextBtn" onclick="nextSection()">Next ➡</button>
                                <button type="submit" name="update" class="save-btn" id="saveBtn">✔ Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <?php endif; ?>
</div>

<script>
function closeEditModal() {
    if (hasUnsavedChanges) {
        const leave = confirm("You have unsaved changes. Close anyway?");
        if (!leave) return;
    }

    const modal = document.getElementById("editModal");
    if (modal) {
        modal.classList.remove("show");
    }

    document.body.classList.remove("modal-open");

    const url = new URL(window.location.href);
    url.searchParams.delete("id");
    url.searchParams.delete("saved");
    window.history.replaceState({}, document.title, url.toString());
}

let currentSection = 0;

function getVisibleSections() {
    return document.querySelectorAll(".section");
}

function getVisibleNavItems() {
    return document.querySelectorAll(".sidebar .nav-item");
}

function updateProgress(index){
    const sections = getVisibleSections();
    const navItems = getVisibleNavItems();

    sections.forEach((sec, i) => {
        sec.classList.toggle("active", i === index);
    });

    navItems.forEach((nav, i) => {
        nav.classList.remove("active", "completed");
        if (i < index) {
            nav.classList.add("completed");
        }
    });

    if (navItems[index]) {
        navItems[index].classList.add("active");
    }

    if (navItems.length > 0 && document.getElementById("progressLine")) {
        const progressLine = document.getElementById("progressLine");
        const firstCenter = navItems[0].offsetTop + (navItems[0].offsetHeight / 2);
        const currentCenter = navItems[index].offsetTop + (navItems[index].offsetHeight / 2);

        progressLine.style.top = firstCenter + "px";
        progressLine.style.height = Math.max(0, currentCenter - firstCenter) + "px";
    }

    currentSection = index;

    const nextBtn = document.getElementById("nextBtn");
    const saveBtn = document.getElementById("saveBtn");

    if (nextBtn && saveBtn) {
        if (index === sections.length - 1) {
            nextBtn.style.display = "none";
        } else {
            nextBtn.style.display = "inline-block";
        }

        saveBtn.style.display = "inline-block";
    }
}

function goToSection(index){
    updateProgress(index);
}

function nextSection(){
    const sections = getVisibleSections();
    const form = document.getElementById("editRecordForm");
    if (!form) return;

    const invalidField = validateSectionFields(sections[currentSection]);
    if (invalidField) {
        invalidField.focus();
        invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
        return;
    }

    if (currentSection < sections.length - 1) {
        updateProgress(currentSection + 1);
    }
}

function loadImage(event) {
    const preview = document.getElementById("preview");
    if (preview && event.target.files && event.target.files[0]) {
        preview.src = URL.createObjectURL(event.target.files[0]);
    }
}

function removeEntry(button, selector) {
    const item = button.closest(selector);
    if (item) {
        item.remove();
        saveFormDraft();
    }
}

function getDraftKey() {
    const form = document.getElementById("editRecordForm");
    if (!form) return null;

    const idInput = form.querySelector('input[name="id"]');
    const personId = idInput ? idInput.value : "new";
    return "personal_record_draft_" + personId;
}

function collectRepeatedEntries(selector, fieldNames) {
    const entries = [];
    document.querySelectorAll(selector).forEach(entry => {
        const row = {};
        fieldNames.forEach(name => {
            const el = entry.querySelector(`[name="${name}[]"]`);
            row[name] = el ? el.value : "";
        });

        const hasValue = Object.values(row).some(v => String(v).trim() !== "");
        if (hasValue) {
            entries.push(row);
        }
    });
    return entries;
}

function saveFormDraft() {
    const form = document.getElementById("editRecordForm");
    if (!form) return;

    const key = getDraftKey();
    if (!key) return;

    const data = {
        simple: {},
        education: collectRepeatedEntries(".education-entry", [
            "education_level", "school_name", "course", "units",
            "edu_from", "edu_to", "year_graduated", "honors"
        ]),
        eligibility: collectRepeatedEntries(".eligibility-entry", [
            "career_service", "rating", "exam_date", "exam_place",
            "license", "license_number", "valid_until"
        ]),
        training: collectRepeatedEntries(".training-entry", [
            "title", "hours", "training_from", "training_to", "type", "sponsor"
        ])
    };

    const fields = form.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([name$="[]"]), select:not([name$="[]"]), textarea:not([name$="[]"])');
    fields.forEach(field => {
        if (!field.name) return;
        data.simple[field.name] = field.value;
    });

    localStorage.setItem(key, JSON.stringify(data));
}

function restoreSimpleFields(form, data) {
    if (!data || !data.simple) return;

    Object.keys(data.simple).forEach(name => {
        const field = form.querySelector(`[name="${name}"]`);
        if (!field) return;

        const currentValue = (field.value || "").trim();
        if (currentValue !== "") return;

        field.value = data.simple[name];
    });
}

function containerHasPopulatedInputs(selector) {
    const container = document.querySelector(selector);
    if (!container) return false;

    const fields = container.querySelectorAll('input, select, textarea');
    for (const field of fields) {
        const value = (field.value || "").trim();
        if (value !== "") {
            return true;
        }
    }
    return false;
}

function clearContainer(selector) {
    const container = document.querySelector(selector);
    if (container) {
        container.innerHTML = "";
    }
}

function addEducation(data = {}) {
    const container = document.getElementById("education-container");
    if (!container) return;

    const div = document.createElement("div");
    div.classList.add("education-entry", "education-box");
    div.innerHTML = `
        <div class="education-grid">
            <div>
                <label>Level</label>
                <select name="education_level[]">
                    <option>Elementary</option>
                    <option>Secondary</option>
                    <option>Vocational / Trade Course</option>
                    <option>College</option>
                    <option>Graduate Studies</option>
                </select>
            </div>

            <div>
                <label>Name of School</label>
                <input name="school_name[]">
            </div>

            <div>
                <label>Basic Education / Degree / Course</label>
                <input name="course[]">
            </div>

            <div>
                <label>Highest Level / Units Earned</label>
                <input name="units[]">
            </div>

            <div>
                <label>From</label>
                <input type="date" name="edu_from[]">
            </div>

            <div>
                <label>To</label>
                <input type="date" name="edu_to[]">
            </div>

            <div>
                <label>Year Graduated</label>
                <input name="year_graduated[]">
            </div>

            <div>
                <label>Honors</label>
                <input name="honors[]">
            </div>
        </div>

        <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">✖ Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="education_level[]"]').value = data.education_level || "";
    div.querySelector('[name="school_name[]"]').value = data.school_name || "";
    div.querySelector('[name="course[]"]').value = data.course || "";
    div.querySelector('[name="units[]"]').value = data.units || "";
    div.querySelector('[name="edu_from[]"]').value = data.edu_from || "";
    div.querySelector('[name="edu_to[]"]').value = data.edu_to || "";
    div.querySelector('[name="year_graduated[]"]').value = data.year_graduated || "";
    div.querySelector('[name="honors[]"]').value = data.honors || "";

    saveFormDraft();
}

function addEligibility(data = {}) {
    const container = document.getElementById("eligibility-container");
    if (!container) return;

    const div = document.createElement("div");
    div.classList.add("eligibility-entry", "eligibility-box");
    div.innerHTML = `
        <div class="eligibility-grid">
            <div>
                <label>Career Service / CSC / CES</label>
                <input name="career_service[]">
            </div>

            <div>
                <label>Rating</label>
                <input name="rating[]">
            </div>

            <div>
                <label>Exam Date</label>
                <input type="date" name="exam_date[]">
            </div>

            <div>
                <label>Place of Examination</label>
                <input name="exam_place[]">
            </div>

            <div>
                <label>License</label>
                <input name="license[]">
            </div>

            <div>
                <label>License Number</label>
                <input name="license_number[]">
            </div>

            <div>
                <label>Valid Until</label>
                <input type="date" name="valid_until[]">
            </div>
        </div>

        <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">✖ Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="career_service[]"]').value = data.career_service || "";
    div.querySelector('[name="rating[]"]').value = data.rating || "";
    div.querySelector('[name="exam_date[]"]').value = data.exam_date || "";
    div.querySelector('[name="exam_place[]"]').value = data.exam_place || "";
    div.querySelector('[name="license[]"]').value = data.license || "";
    div.querySelector('[name="license_number[]"]').value = data.license_number || "";
    div.querySelector('[name="valid_until[]"]').value = data.valid_until || "";

    saveFormDraft();
}

function addTraining(data = {}) {
    const container = document.getElementById("training-container");
    if (!container) return;

    const div = document.createElement("div");
    div.classList.add("training-entry", "training-box");
    div.innerHTML = `
        <div class="training-grid">
            <div>
                <label>Training Title</label>
                <input name="title[]">
            </div>

            <div>
                <label>Hours</label>
                <input name="hours[]">
            </div>

            <div>
                <label>From</label>
                <input type="date" name="training_from[]">
            </div>

            <div>
                <label>To</label>
                <input type="date" name="training_to[]">
            </div>

            <div>
                <label>Type</label>
                <input name="type[]">
            </div>

            <div>
                <label>Sponsor</label>
                <input name="sponsor[]">
            </div>
        </div>

        <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">✖ Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="title[]"]').value = data.title || "";
    div.querySelector('[name="hours[]"]').value = data.hours || "";
    div.querySelector('[name="training_from[]"]').value = data.training_from || "";
    div.querySelector('[name="training_to[]"]').value = data.training_to || "";
    div.querySelector('[name="type[]"]').value = data.type || "";
    div.querySelector('[name="sponsor[]"]').value = data.sponsor || "";

    saveFormDraft();
}

function restoreDraft() {
    const form = document.getElementById("editRecordForm");
    if (!form) return;

    const key = getDraftKey();
    if (!key) return;

    const raw = localStorage.getItem(key);
    if (!raw) return;

    let data = null;
    try {
        data = JSON.parse(raw);
    } catch (e) {
        return;
    }

    restoreSimpleFields(form, data);

    if (document.getElementById("education-container") && Array.isArray(data.education) && !containerHasPopulatedInputs("#education-container")) {
        clearContainer("#education-container");
        if (data.education.length > 0) {
            data.education.forEach(row => addEducation(row));
        } else {
            addEducation();
        }
    }

    if (document.getElementById("eligibility-container") && Array.isArray(data.eligibility) && !containerHasPopulatedInputs("#eligibility-container")) {
        clearContainer("#eligibility-container");
        if (data.eligibility.length > 0) {
            data.eligibility.forEach(row => addEligibility(row));
        } else {
            addEligibility();
        }
    }

    if (document.getElementById("training-container") && Array.isArray(data.training) && !containerHasPopulatedInputs("#training-container")) {
        clearContainer("#training-container");
        if (data.training.length > 0) {
            data.training.forEach(row => addTraining(row));
        } else {
            addTraining();
        }
    }
}

function clearDraft() {
    const key = getDraftKey();
    if (key) {
        localStorage.removeItem(key);
    }
}

/* -------------------------
   VALIDATION
------------------------- */
function addFieldError(input, message){
    input.classList.add("field-error");

    const wrapper = input.parentElement;
    if (!wrapper) return;

    const old = wrapper.querySelector(".field-error-message");
    if (old) old.remove();

    const msg = document.createElement("div");
    msg.className = "field-error-message";
    msg.textContent = message;
    wrapper.appendChild(msg);
}

function clearFieldErrorState(input) {
    if (!input) return;
    input.classList.remove("field-error");
    const wrapper = input.parentElement;
    if (wrapper) {
        const old = wrapper.querySelector(".field-error-message");
        if (old) old.remove();
    }
}

function sanitizeIdNumber(value) {
    return value.replace(/[^\d\- ]/g, '');
}

function isLettersOnly(value) {
    return /^[A-Za-zÑñ\s.'-]+$/.test(value);
}

function isAlphaNumericBasic(value) {
    return /^[A-Za-z0-9Ññ\s./#,-]+$/.test(value);
}

function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function isValidMobile(value) {
    return /^(09\d{9}|\+639\d{9})$/.test(value);
}

function isValidTelephone(value) {
    return /^[0-9()\-\s]{7,15}$/.test(value);
}

function isValidZip(value) {
    return /^\d{4}$/.test(value);
}

function isValidYear(value) {
    return /^(19|20)\d{2}$/.test(value);
}

function isPositiveNumber(value) {
    return /^\d+(\.\d+)?$/.test(value) && parseFloat(value) > 0;
}

function isValidBloodType(value) {
    return /^(A|B|AB|O)[+-]$/i.test(value.trim());
}

function isValidTin(value) {
    return /^(\d{3}-\d{3}-\d{3}|\d{9}|\d{12}|\d{3}-\d{3}-\d{3}-\d{3})$/.test(value);
}

function isValidDateOrder(fromValue, toValue) {
    if (!fromValue || !toValue) return true;
    return new Date(fromValue) <= new Date(toValue);
}

function isPastOrToday(value) {
    if (!value) return true;
    const inputDate = new Date(value);
    const today = new Date();
    today.setHours(0,0,0,0);
    return inputDate <= today;
}

function validateSingleField(input) {
    if (!input || !input.name) return true;

    const form = document.getElementById("editRecordForm");
    const name = input.name;
    const value = (input.value || "").trim();

    clearFieldErrorState(input);

    const markInvalid = (message) => {
        addFieldError(input, message);
        return false;
    };

    switch (name) {
        case "surname":
        case "firstname":
        case "middlename":
            if (value === "") return markInvalid("This field is required.");
            if (!isLettersOnly(value)) return markInvalid("Letters only.");
            return true;

        case "extension":
            if (value !== "" && !["Jr.", "Sr.", "II", "III", "IV", "V"].includes(value)) {
                return markInvalid("Invalid name extension.");
            }
            return true;

        case "birth_place":
        case "r_house1":
        case "r_street":
        case "r_subdivision":
        case "r_city":
        case "r_barangay":
        case "r_province":
        case "p_house1":
        case "p_street":
        case "p_subdivision":
        case "p_city":
        case "p_barangay":
        case "p_province":
            if (value === "") return markInvalid("This field is required.");
            if (!isAlphaNumericBasic(value)) return markInvalid("Invalid characters.");
            return true;

        case "dob":
            if (value === "") return markInvalid("This field is required.");
            if (!isPastOrToday(value)) return markInvalid("Date of birth cannot be in the future.");
            return true;

        case "civil_status":
            if (value === "") return markInvalid("This field is required.");
            if (!["Single", "Married", "Widowed", "Separated"].includes(value)) {
                return markInvalid("Invalid civil status.");
            }
            return true;

        case "sex":
            if (value === "") return markInvalid("This field is required.");
            if (!["Male", "Female"].includes(value)) {
                return markInvalid("Please select a valid sex.");
            }
            return true;

        case "blood_type":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidBloodType(value)) {
                return markInvalid("Use A+, A-, B+, B-, AB+, AB-, O+, or O-.");
            }
            return true;

        case "height":
        case "weight":
            if (value === "") return markInvalid("This field is required.");
            if (!isPositiveNumber(value)) {
                return markInvalid("Must be a positive number.");
            }
            return true;

        case "umid":
        case "philsys":
        case "pagibig":
        case "philhealth":
        case "agency_employee":
            if (value === "") return markInvalid("This field is required.");
            if (!/^[0-9][0-9\- ]{3,29}$/.test(value)) {
                return markInvalid("Numbers only. Hyphen and spaces are allowed.");
            }
            return true;

        case "tin":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidTin(value)) {
                return markInvalid("Invalid TIN format.");
            }
            return true;

        case "citizenship":
            if (value === "") return markInvalid("This field is required.");
            if (!["Filipino", "Dual Citizen"].includes(value)) {
                return markInvalid("Invalid citizenship.");
            }
            return true;

        case "dual_country":
            const citizenship = form.querySelector('[name="citizenship"]')?.value || "";
            if (citizenship === "Dual Citizen" && value === "") {
                return markInvalid("Country is required for dual citizenship.");
            }
            return true;

        case "r_zip":
        case "p_zip":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidZip(value)) {
                return markInvalid("Zip code must be 4 digits.");
            }
            return true;

        case "telephone":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidTelephone(value)) {
                return markInvalid("Invalid telephone number.");
            }
            return true;

        case "mobile":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidMobile(value)) {
                return markInvalid("Use 09XXXXXXXXX or +639XXXXXXXXX.");
            }
            return true;

        case "email":
            if (value === "") return markInvalid("This field is required.");
            if (!isValidEmail(value)) {
                return markInvalid("Invalid email address.");
            }
            return true;
    }

    return true;
}

function validateSectionFields(section) {
    if (!section) return null;

    let firstInvalid = null;

    section.querySelectorAll("input, select, textarea").forEach(input => {
        const ok = validateSingleField(input);
        if (!ok && !firstInvalid) {
            firstInvalid = input;
        }
    });

    if (section.id === "education") {
        section.querySelectorAll(".education-entry").forEach(entry => {
            const from = entry.querySelector('[name="edu_from[]"]');
            const to = entry.querySelector('[name="edu_to[]"]');
            const yearGraduated = entry.querySelector('[name="year_graduated[]"]');

            if (from && to && from.value && to.value && !isValidDateOrder(from.value, to.value)) {
                addFieldError(to, '"To" date must not be earlier than "From" date.');
                if (!firstInvalid) firstInvalid = to;
            }

            if (yearGraduated && yearGraduated.value.trim() !== "" && !isValidYear(yearGraduated.value.trim())) {
                addFieldError(yearGraduated, "Enter a valid 4-digit year.");
                if (!firstInvalid) firstInvalid = yearGraduated;
            }
        });
    }

    if (section.id === "eligibility-section") {
        section.querySelectorAll(".eligibility-entry").forEach(entry => {
            const examDate = entry.querySelector('[name="exam_date[]"]');
            const validUntil = entry.querySelector('[name="valid_until[]"]');
            const rating = entry.querySelector('[name="rating[]"]');

            if (rating && rating.value.trim() !== "" && !/^\d+(\.\d+)?$/.test(rating.value.trim())) {
                addFieldError(rating, "Rating must be numeric.");
                if (!firstInvalid) firstInvalid = rating;
            }

            if (examDate && examDate.value && !isPastOrToday(examDate.value)) {
                addFieldError(examDate, "Exam date cannot be in the future.");
                if (!firstInvalid) firstInvalid = examDate;
            }

            if (examDate && validUntil && examDate.value && validUntil.value && new Date(validUntil.value) < new Date(examDate.value)) {
                addFieldError(validUntil, "Valid until must be after exam date.");
                if (!firstInvalid) firstInvalid = validUntil;
            }
        });
    }

    if (section.id === "training-section") {
        section.querySelectorAll(".training-entry").forEach(entry => {
            const from = entry.querySelector('[name="training_from[]"]');
            const to = entry.querySelector('[name="training_to[]"]');
            const hours = entry.querySelector('[name="hours[]"]');

            if (hours && hours.value.trim() !== "" && !isPositiveNumber(hours.value.trim())) {
                addFieldError(hours, "Hours must be a positive number.");
                if (!firstInvalid) firstInvalid = hours;
            }

            if (from && to && from.value && to.value && !isValidDateOrder(from.value, to.value)) {
                addFieldError(to, '"To" date must not be earlier than "From" date.');
                if (!firstInvalid) firstInvalid = to;
            }
        });
    }

    return firstInvalid;
}

function validateAllSections() {
    const sections = getVisibleSections();
    for (let i = 0; i < sections.length; i++) {
        const invalidField = validateSectionFields(sections[i]);
        if (invalidField) {
            updateProgress(i);
            invalidField.focus();
            invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
            return false;
        }
    }
    return true;
}

let hasUnsavedChanges = false;

function showToast(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add("show");
    setTimeout(() => {
        el.classList.remove("show");
    }, 3000);
}

function setUnsavedState(state) {
    hasUnsavedChanges = state;
    const banner = document.getElementById("unsavedBanner");
    if (banner) {
        banner.classList.toggle("show", state);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("editModal");

    if (modal) {
        document.body.classList.add("modal-open");

        modal.addEventListener("click", function (e) {
            if (e.target === modal) {
                closeEditModal();
            }
        });
    }

    showToast("toastSuccess");
    showToast("toastError");

    const form = document.getElementById("editRecordForm");
    if (!form) return;

    restoreDraft();
    updateProgress(0);
    setUnsavedState(false);

    form.addEventListener("input", function(e) {
        const field = e.target;
        if (!field.matches("input, select, textarea")) return;

        if (["umid", "philsys", "pagibig", "tin", "philhealth", "agency_employee"].includes(field.name)) {
            const cleaned = sanitizeIdNumber(field.value);
            if (field.value !== cleaned) {
                field.value = cleaned;
            }
        }

        validateSingleField(field);

        if (field.name === "citizenship") {
            const dualCountryField = form.querySelector('[name="dual_country"]');
            if (dualCountryField) validateSingleField(dualCountryField);
        }

        saveFormDraft();
        setUnsavedState(true);
    });

    form.addEventListener("change", function(e) {
        const field = e.target;
        if (!field.matches("input, select, textarea")) return;

        validateSingleField(field);

        if (field.name === "citizenship") {
            const dualCountryField = form.querySelector('[name="dual_country"]');
            if (dualCountryField) validateSingleField(dualCountryField);
        }

        saveFormDraft();
        setUnsavedState(true);
    });

    form.addEventListener("submit", function (e) {
        const ok = validateAllSections();
        if (!ok) {
            e.preventDefault();
            const firstError = document.querySelector(".field-error");
            if (firstError) {
                firstError.focus();
                firstError.scrollIntoView({ behavior: "smooth", block: "center" });
            }
            return false;
        }

        setUnsavedState(false);
    });

    form.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && e.target.tagName !== "TEXTAREA") {
            e.preventDefault();
            const saveBtn = document.getElementById("saveBtn");
            if (saveBtn) saveBtn.click();
        }
    });

    window.addEventListener("beforeunload", function (e) {
        if (!hasUnsavedChanges) return;
        e.preventDefault();
        e.returnValue = "";
    });
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closeEditModal();
    }
});

const citizenship = document.getElementById("citizenship");
const dualCountry = document.querySelector('[name="dual_country"]');

function toggleDual() {
  if (citizenship.value === "Dual Citizen") {
    dualCountry.disabled = false;
    dualCountry.classList.remove("disabled-look");
  } else {
    dualCountry.disabled = true;
    dualCountry.value = "";
    dualCountry.classList.add("disabled-look");
  }
}

citizenship.addEventListener("change", toggleDual);
toggleDual(); // run on load

const sameAsResidentAddress = document.getElementById("sameAsResidentAddress");

function copyResidentToPermanent() {
  const pairs = [
    ["r_house1", "p_house1"],
    ["r_street", "p_street"],
    ["r_subdivision", "p_subdivision"],
    ["r_barangay", "p_barangay"],
    ["r_city", "p_city"],
    ["r_province", "p_province"],
    ["r_zip", "p_zip"]
  ];

  pairs.forEach(([residentName, permanentName]) => {
    const residentField = document.querySelector(`[name="${residentName}"]`);
    const permanentField = document.querySelector(`[name="${permanentName}"]`);

    if (residentField && permanentField) {
      permanentField.value = residentField.value;
    }
  });
}

if (sameAsResidentAddress) {
  sameAsResidentAddress.addEventListener("change", function () {
    if (this.checked) {
      copyResidentToPermanent();
    }
  });

  ["r_house1", "r_street", "r_subdivision", "r_barangay", "r_city", "r_province", "r_zip"].forEach(name => {
    const field = document.querySelector(`[name="${name}"]`);
    if (field) {
      field.addEventListener("input", function () {
        if (sameAsResidentAddress.checked) {
          copyResidentToPermanent();
        }
      });
    }
  });
}
</script>
</body>
</html>
<?php ob_end_flush(); ?>