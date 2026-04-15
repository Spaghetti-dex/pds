<?php
session_start();

require_once "../includes/auth_check.php";
require_once "../includes/audit_log.php";
require_once "../config/database.php";

/*
|--------------------------------------------------------------------------
| BLOCK DIRECT ACCESS
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    exit('Access denied.');
}

if (!isset($_POST['form_submitted']) || $_POST['form_submitted'] !== '1') {
    http_response_code(403);
    exit('Invalid form submission.');
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/
function clean($data): string {
    if (is_array($data)) return '';
    return trim((string)$data);
}

function cleanArray($data): array {
    if (!is_array($data)) return [];
    return array_map(function ($item) {
        return trim((string)$item);
    }, $data);
}

function normalizeDateValue($value): ?string {
    $value = trim((string)$value);
    return $value === '' ? null : $value;
}

function hasAnyValue(array $row): bool {
    foreach ($row as $value) {
        if ($value !== null && trim((string)$value) !== '') {
            return true;
        }
    }
    return false;
}

function requireField(array &$errors, string $label, $value): void {
    if ($value === null || trim((string)$value) === '') {
        $errors[] = "$label is required.";
    }
}

function validateRegex(array &$errors, string $label, string $value, string $pattern, string $message = 'is invalid.'): void {
    if ($value !== '' && !preg_match($pattern, $value)) {
        $errors[] = "$label $message";
    }
}

function validateDateField(array &$errors, string $label, ?string $value, bool $allowFuture = true): void {
    if ($value === null || $value === '') {
        return;
    }

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

function validateDateRange(array &$errors, string $fromLabel, ?string $from, string $toLabel, ?string $to): void {
    if (!$from || !$to) return;

    $fromDate = DateTime::createFromFormat('Y-m-d', $from);
    $toDate = DateTime::createFromFormat('Y-m-d', $to);

    if ($fromDate && $toDate && $fromDate > $toDate) {
        $errors[] = "$toLabel cannot be earlier than $fromLabel.";
    }
}

function validatePositiveNumber(array &$errors, string $label, string $value): void {
    if ($value !== '' && (!preg_match('/^\d+(\.\d+)?$/', $value) || (float)$value <= 0)) {
        $errors[] = "$label must be a positive number.";
    }
}

function validateYear(array &$errors, string $label, string $value): void {
    if ($value !== '' && !preg_match('/^(19|20)\d{2}$/', $value)) {
        $errors[] = "$label must be a valid 4-digit year.";
    }
}

function rowLabel(string $type, int $index, string $field): string {
    return $type . ' row ' . ($index + 1) . ' - ' . $field;
}

try {
    $conn->begin_transaction();

    /*
    |--------------------------------------------------------------------------
    | PERSONAL INFO
    |--------------------------------------------------------------------------
    */
    $surname         = clean($_POST['surname'] ?? '');
    $firstname       = clean($_POST['firstname'] ?? '');
    $middlename      = clean($_POST['middlename'] ?? '');
    $extension       = clean($_POST['extension'] ?? '');
    $dob             = normalizeDateValue($_POST['dob'] ?? '');
    $birth_place     = clean($_POST['birth_place'] ?? '');

    $sex             = clean($_POST['sex'] ?? '');
    $civil_status    = clean($_POST['civil_status'] ?? '');

    $height          = clean($_POST['height'] ?? '');
    $weight          = clean($_POST['weight'] ?? '');
    $blood_type      = clean($_POST['blood_type'] ?? '');

    $umid            = clean($_POST['umid'] ?? '');
    $pagibig         = clean($_POST['pagibig'] ?? '');
    $philhealth      = clean($_POST['philhealth'] ?? '');
    $philsys         = clean($_POST['philsys'] ?? '');
    $tin             = clean($_POST['tin'] ?? '');
    $agency_employee = clean($_POST['agency_employee'] ?? '');

    $citizenship     = clean($_POST['citizenship'] ?? '');
    $dual_country    = clean($_POST['dual_country'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | ADDRESS
    |--------------------------------------------------------------------------
    */
    $r_house         = clean($_POST['r_house'] ?? '');
    $r_street        = clean($_POST['r_street'] ?? '');
    $r_subdivision   = clean($_POST['r_subdivision'] ?? '');
    $r_barangay      = clean($_POST['r_barangay'] ?? '');
    $r_city          = clean($_POST['r_city'] ?? '');
    $r_province      = clean($_POST['r_province'] ?? '');
    $r_zip           = clean($_POST['r_zip'] ?? '');

    $p_house         = clean($_POST['p_house'] ?? '');
    $p_street        = clean($_POST['p_street'] ?? '');
    $p_subdivision   = clean($_POST['p_subdivision'] ?? '');
    $p_barangay      = clean($_POST['p_barangay'] ?? '');
    $p_city          = clean($_POST['p_city'] ?? '');
    $p_province      = clean($_POST['p_province'] ?? '');
    $p_zip           = clean($_POST['p_zip'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | CONTACT
    |--------------------------------------------------------------------------
    */
    $telephone       = clean($_POST['telephone'] ?? '');
    $mobile          = clean($_POST['mobile'] ?? '');
    $email           = clean($_POST['email'] ?? '');
    $no_middlename   = isset($_POST['no_middlename']) && $_POST['no_middlename'] === '1';
    $no_telephone    = isset($_POST['no_telephone']) && $_POST['no_telephone'] === '1';

    if ($no_middlename) {
        $middlename = '';
    }

    if ($no_telephone) {
        $telephone = '';
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION - MAIN FIELDS
    |--------------------------------------------------------------------------
    */
    $errors = [];

    requireField($errors, 'Surname', $surname);
    requireField($errors, 'First name', $firstname);
    if (!$no_middlename) {
        requireField($errors, 'Middle name', $middlename);
    }
    requireField($errors, 'Date of birth', $dob);
    requireField($errors, 'Birth place', $birth_place);
    requireField($errors, 'Sex', $sex);
    requireField($errors, 'Civil status', $civil_status);
    requireField($errors, 'Blood type', $blood_type);
    requireField($errors, 'Height', $height);
    requireField($errors, 'Weight', $weight);
    requireField($errors, 'UMID', $umid);
    requireField($errors, 'Pag-IBIG', $pagibig);
    requireField($errors, 'PhilHealth', $philhealth);
    requireField($errors, 'PhilSys', $philsys);
    requireField($errors, 'TIN', $tin);
    requireField($errors, 'Agency Employee No.', $agency_employee);
    requireField($errors, 'Citizenship', $citizenship);

    requireField($errors, 'Residential house', $r_house);
    requireField($errors, 'Residential street', $r_street);
    requireField($errors, 'Residential subdivision', $r_subdivision);
    requireField($errors, 'Residential barangay', $r_barangay);
    requireField($errors, 'Residential city', $r_city);
    requireField($errors, 'Residential province', $r_province);
    requireField($errors, 'Residential zip', $r_zip);

    requireField($errors, 'Permanent house', $p_house);
    requireField($errors, 'Permanent street', $p_street);
    requireField($errors, 'Permanent subdivision', $p_subdivision);
    requireField($errors, 'Permanent barangay', $p_barangay);
    requireField($errors, 'Permanent city', $p_city);
    requireField($errors, 'Permanent province', $p_province);
    requireField($errors, 'Permanent zip', $p_zip);

    if (!$no_telephone) {
        requireField($errors, 'Telephone', $telephone);
    }

    requireField($errors, 'Mobile', $mobile);
    requireField($errors, 'Email', $email);

    if ($citizenship === 'Dual Citizen' && $dual_country === '') {
        $errors[] = 'Dual citizenship country is required.';
    }

    validateRegex($errors, 'Surname', $surname, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');
    validateRegex($errors, 'First name', $firstname, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');
    if (!$no_middlename && $middlename !== '') {
        validateRegex($errors, 'Middle name', $middlename, "/^[A-Za-zÑñ\s.'-]+$/", 'contains invalid characters.');
    }

    if ($extension !== '') {
        validateRegex($errors, 'Name extension', $extension, "/^[A-Za-z0-9.\s-]{1,10}$/", 'is invalid.');
    }

    validateDateField($errors, 'Date of birth', $dob, false);

    validateRegex($errors, 'Sex', $sex, "/^(Male|Female)$/", 'must be Male or Female.');
    validateRegex($errors, 'Civil status', $civil_status, "/^(Single|Married|Widowed|Separated)$/", 'is invalid.');
    validateRegex($errors, 'Blood type', $blood_type, "/^(A|B|AB|O)[+-]$/i", 'must be A+, A-, B+, B-, AB+, AB-, O+, or O-.');

    validatePositiveNumber($errors, 'Height', $height);
    validatePositiveNumber($errors, 'Weight', $weight);

    validateRegex($errors, 'UMID', $umid, "/^[A-Za-z0-9\- ]{4,30}$/", 'is invalid.');
    validateRegex($errors, 'PhilSys', $philsys, "/^[A-Za-z0-9\- ]{4,30}$/", 'is invalid.');
    validateRegex($errors, 'Pag-IBIG', $pagibig, "/^[A-Za-z0-9\- ]{4,30}$/", 'is invalid.');
    validateRegex($errors, 'PhilHealth', $philhealth, "/^[A-Za-z0-9\- ]{4,30}$/", 'is invalid.');
    validateRegex($errors, 'Agency Employee No.', $agency_employee, "/^[A-Za-z0-9\- ]{4,30}$/", 'is invalid.');
    validateRegex($errors, 'TIN', $tin, "/^(\d{3}-\d{3}-\d{3}|\d{9}|\d{12}|\d{3}-\d{3}-\d{3}-\d{3})$/", 'is invalid.');

    validateRegex($errors, 'Citizenship', $citizenship, "/^(Filipino|Dual Citizen)$/", 'is invalid.');

    if ($dual_country !== '') {
        validateRegex($errors, 'Dual citizenship country', $dual_country, "/^[A-Za-zÑñ\s.'-]+$/", 'is invalid.');
    }

    validateRegex($errors, 'Residential zip', $r_zip, "/^\d{4}$/", 'must be 4 digits.');
    validateRegex($errors, 'Permanent zip', $p_zip, "/^\d{4}$/", 'must be 4 digits.');

    if (!$no_telephone && $telephone !== '') {
        validateRegex($errors, 'Telephone', $telephone, "/^[0-9()\-\s]{7,15}$/", 'is invalid.');
    }

    validateRegex($errors, 'Mobile', $mobile, "/^(09\d{9}|\+639\d{9})$/", 'must be 09XXXXXXXXX or +639XXXXXXXXX.');

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email is invalid.';
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE PHOTO UPLOAD
    |--------------------------------------------------------------------------
    */
    $photoData = null;
    $photoType = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error uploading photo.");
        }

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

        $photoData = file_get_contents($_FILES['photo']['tmp_name']);
        $photoType = $detectedType;
    }

    /*
    |--------------------------------------------------------------------------
    | EDUCATION INPUTS + VALIDATION
    |--------------------------------------------------------------------------
    */
    $education_level = cleanArray($_POST['education_level'] ?? []);
    $school_name = cleanArray($_POST['school_name'] ?? []);
    $course = cleanArray($_POST['course'] ?? []);
    $units = cleanArray($_POST['units'] ?? []);
    $edu_from = $_POST['edu_from'] ?? [];
    $edu_to = $_POST['edu_to'] ?? [];
    $year_graduated = cleanArray($_POST['year_graduated'] ?? []);
    $honors = cleanArray($_POST['honors'] ?? []);

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
            'edu_from' => normalizeDateValue($edu_from[$i] ?? ''),
            'edu_to' => normalizeDateValue($edu_to[$i] ?? ''),
            'year_graduated' => $year_graduated[$i] ?? '',
            'honors' => $honors[$i] ?? ''
        ];

        if (!hasAnyValue($row)) {
            continue;
        }

        requireField($errors, rowLabel('Education', $i, 'Level'), $row['education_level']);
        requireField($errors, rowLabel('Education', $i, 'School name'), $row['school_name']);
        requireField($errors, rowLabel('Education', $i, 'Course'), $row['course']);
        requireField($errors, rowLabel('Education', $i, 'Units'), $row['units']);
        requireField($errors, rowLabel('Education', $i, 'From date'), $row['edu_from']);
        requireField($errors, rowLabel('Education', $i, 'To date'), $row['edu_to']);
        requireField($errors, rowLabel('Education', $i, 'Year graduated'), $row['year_graduated']);

        validateDateField($errors, rowLabel('Education', $i, 'From date'), $row['edu_from'], true);
        validateDateField($errors, rowLabel('Education', $i, 'To date'), $row['edu_to'], true);
        validateDateRange($errors, rowLabel('Education', $i, 'From date'), $row['edu_from'], rowLabel('Education', $i, 'To date'), $row['edu_to']);
        validateYear($errors, rowLabel('Education', $i, 'Year graduated'), $row['year_graduated']);

        $educationRows[] = $row;
    }

    if (count($educationRows) === 0) {
        $errors[] = 'At least one Educational Background row is required.';
    }

    /*
    |--------------------------------------------------------------------------
    | ELIGIBILITY INPUTS + VALIDATION
    |--------------------------------------------------------------------------
    */
    $career_service = cleanArray($_POST['career_service'] ?? []);
    $rating = cleanArray($_POST['rating'] ?? []);
    $exam_date = $_POST['exam_date'] ?? [];
    $exam_place = cleanArray($_POST['exam_place'] ?? []);
    $license = cleanArray($_POST['license'] ?? []);
    $license_number = cleanArray($_POST['license_number'] ?? []);
    $valid_until = $_POST['valid_until'] ?? [];

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
            'exam_date' => normalizeDateValue($exam_date[$i] ?? ''),
            'exam_place' => $exam_place[$i] ?? '',
            'license' => $license[$i] ?? '',
            'license_number' => $license_number[$i] ?? '',
            'valid_until' => normalizeDateValue($valid_until[$i] ?? '')
        ];

        if (!hasAnyValue($row)) {
            continue;
        }

        requireField($errors, rowLabel('Eligibility', $i, 'Career service'), $row['career_service']);
        requireField($errors, rowLabel('Eligibility', $i, 'Rating'), $row['rating']);
        requireField($errors, rowLabel('Eligibility', $i, 'Exam date'), $row['exam_date']);
        requireField($errors, rowLabel('Eligibility', $i, 'Exam place'), $row['exam_place']);

        if ($row['rating'] !== '') {
            validateRegex($errors, rowLabel('Eligibility', $i, 'Rating'), $row['rating'], "/^\d+(\.\d+)?$/", 'must be numeric.');
        }

        validateDateField($errors, rowLabel('Eligibility', $i, 'Exam date'), $row['exam_date'], false);

        if ($row['valid_until'] !== null) {
            validateDateField($errors, rowLabel('Eligibility', $i, 'Valid until'), $row['valid_until'], true);
            validateDateRange(
                $errors,
                rowLabel('Eligibility', $i, 'Exam date'),
                $row['exam_date'],
                rowLabel('Eligibility', $i, 'Valid until'),
                $row['valid_until']
            );
        }

        if ($row['license_number'] !== '') {
            validateRegex($errors, rowLabel('Eligibility', $i, 'License number'), $row['license_number'], "/^[A-Za-z0-9\- ]{3,30}$/", 'is invalid.');
        }

        $eligibilityRows[] = $row;
    }

    if (count($eligibilityRows) === 0) {
        $errors[] = 'At least one Service Eligibility row is required.';
    }

    /*
    |--------------------------------------------------------------------------
    | TRAINING INPUTS + VALIDATION
    |--------------------------------------------------------------------------
    */
    $title = cleanArray($_POST['title'] ?? []);
    $hours = cleanArray($_POST['hours'] ?? []);
    $training_from = $_POST['training_from'] ?? [];
    $training_to = $_POST['training_to'] ?? [];
    $type = cleanArray($_POST['type'] ?? []);
    $sponsor = cleanArray($_POST['sponsor'] ?? []);

    $trainingRows = [];
    $trainingCount = max(
        count($title),
        count($hours),
        count($training_from),
        count($training_to),
        count($type),
        count($sponsor)
    );

    for ($i = 0; $i < $trainingCount; $i++) {
        $row = [
            'title' => $title[$i] ?? '',
            'hours' => $hours[$i] ?? '',
            'training_from' => normalizeDateValue($training_from[$i] ?? ''),
            'training_to' => normalizeDateValue($training_to[$i] ?? ''),
            'type' => $type[$i] ?? '',
            'sponsor' => $sponsor[$i] ?? ''
        ];

        if (!hasAnyValue($row)) {
            continue;
        }

        requireField($errors, rowLabel('Training', $i, 'Title'), $row['title']);
        requireField($errors, rowLabel('Training', $i, 'Hours'), $row['hours']);
        requireField($errors, rowLabel('Training', $i, 'From date'), $row['training_from']);
        requireField($errors, rowLabel('Training', $i, 'To date'), $row['training_to']);

        validatePositiveNumber($errors, rowLabel('Training', $i, 'Hours'), $row['hours']);
        validateDateField($errors, rowLabel('Training', $i, 'From date'), $row['training_from'], true);
        validateDateField($errors, rowLabel('Training', $i, 'To date'), $row['training_to'], true);
        validateDateRange(
            $errors,
            rowLabel('Training', $i, 'From date'),
            $row['training_from'],
            rowLabel('Training', $i, 'To date'),
            $row['training_to']
        );

        $trainingRows[] = $row;
    }

    if (count($trainingRows) === 0) {
        $errors[] = 'At least one Learning and Development row is required.';
    }

    /*
    |--------------------------------------------------------------------------
    | STOP IF ANY VALIDATION FAILED
    |--------------------------------------------------------------------------
    */
    if (!empty($errors)) {
        throw new Exception(implode("<br>", $errors));
    }

    /*
    |--------------------------------------------------------------------------
    | SAVE PERSONAL INFO
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO personal_info
        (
            surname, firstname, middlename, extension, dob, birth_place, sex, civil_status,
            height, weight, blood_type, umid, pagibig, philhealth, philsys, tin, agency_employee,
            citizenship, dual_country, telephone, mobile, email, photo_type, photo
        )
        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?, ?, ?
        )
    ");

    $emptyBlob = null;

    $stmt->bind_param(
        "sssssssssssssssssssssssb",
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
        $photoType,
        $emptyBlob
    );

    if ($photoData !== null) {
        $stmt->send_long_data(23, $photoData);
    }

    $stmt->execute();
    $person_id = $stmt->insert_id;
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | SAVE ADDRESSES
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO addresses
        (person_id, type, house, street, subdivision, barangay, city, province, zip)
        VALUES
        (?, 'residential', ?, ?, ?, ?, ?, ?, ?),
        (?, 'permanent', ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isssssssisssssss",
        $person_id, $r_house, $r_street, $r_subdivision, $r_barangay, $r_city, $r_province, $r_zip,
        $person_id, $p_house, $p_street, $p_subdivision, $p_barangay, $p_city, $p_province, $p_zip
    );
    $stmt->execute();
    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | SAVE EDUCATIONAL BACKGROUND
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO education
        (person_id, education_level, school_name, course, units, edu_from, edu_to, year_graduated, honors)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($educationRows as $row) {
        $stmt->bind_param(
            "issssssss",
            $person_id,
            $row['education_level'],
            $row['school_name'],
            $row['course'],
            $row['units'],
            $row['edu_from'],
            $row['edu_to'],
            $row['year_graduated'],
            $row['honors']
        );
        $stmt->execute();
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | SAVE ELIGIBILITY
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO eligibility
        (person_id, career_service, rating, exam_date, exam_place, license, license_number, valid_until)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($eligibilityRows as $row) {
        $stmt->bind_param(
            "isssssss",
            $person_id,
            $row['career_service'],
            $row['rating'],
            $row['exam_date'],
            $row['exam_place'],
            $row['license'],
            $row['license_number'],
            $row['valid_until']
        );
        $stmt->execute();
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | SAVE TRAINING
    |--------------------------------------------------------------------------
    */
    $stmt = $conn->prepare("
        INSERT INTO training
        (person_id, title, training_from, training_to, hours, type, sponsor)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($trainingRows as $row) {
        $stmt->bind_param(
            "issssss",
            $person_id,
            $row['title'],
            $row['training_from'],
            $row['training_to'],
            $row['hours'],
            $row['type'],
            $row['sponsor']
        );
        $stmt->execute();
    }

    $stmt->close();

    /*
    |--------------------------------------------------------------------------
    | WRITE AUDIT LOG
    |--------------------------------------------------------------------------
    */
    $full_name = trim($firstname . ' ' . $middlename . ' ' . $surname . ' ' . $extension);
    write_audit_log($conn, $person_id, 'CREATE', "Created PDS record for " . $full_name);

    /*
    |--------------------------------------------------------------------------
    | COMMIT = ACID DURABILITY
    |--------------------------------------------------------------------------
    */
    $conn->commit();

    echo "<script>alert('Operation successful!'); window.location.href = '../dashboard/dashboard.php';</script>";
    exit;

} catch (Throwable $e) {
    if (isset($conn) && $conn instanceof mysqli && $conn->connect_errno === 0) {
        try {
            $conn->rollback();
        } catch (Throwable $rollbackError) {
            // Ignore rollback failure
        }
    }

    http_response_code(500);
    echo "Error: " . $e->getMessage();
    exit;
}
?>