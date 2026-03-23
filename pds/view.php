<?php
require_once "../includes/auth_check.php";
include "../includes/header.php"; 
include "../config/database.php";
//include "../includes/auth_check.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
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

$search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : "";
$selected_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

$message = "";
$error = "";
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

$eligibility_table = table_exists($conn, 'service_eligibility')
    ? 'service_eligibility'
    : (table_exists($conn, 'eligibility') ? 'eligibility' : null);

$training_table = table_exists($conn, 'learning_development')
    ? 'learning_development'
    : (table_exists($conn, 'training') ? 'training' : null);

$education_columns   = $education_table ? get_columns($conn, $education_table) : [];
$eligibility_columns = $eligibility_table ? get_columns($conn, $eligibility_table) : [];
$training_columns    = $training_table ? get_columns($conn, $training_table) : [];
$address_columns     = get_columns($conn, 'addresses');
$address_house_col   = get_address_house_column($address_columns);

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
        $message = "Record deleted successfully.";
        $selected_id = 0;
        $person = null;
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = (int)($_POST['id'] ?? 0);
    $selected_id = $id;

    $surname         = trim($_POST['surname'] ?? '');
    $firstname       = trim($_POST['firstname'] ?? '');
    $middlename      = trim($_POST['middlename'] ?? '');
    $extension       = trim($_POST['extension'] ?? '');
    $dob             = $_POST['dob'] ?? '';
    $birth_place     = trim($_POST['birth_place'] ?? '');
    $sex             = trim($_POST['sex'] ?? '');
    $civil_status    = trim($_POST['civil_status'] ?? '');
    $height          = trim($_POST['height'] ?? '');
    $weight          = trim($_POST['weight'] ?? '');
    $blood_type      = trim($_POST['blood_type'] ?? '');
    $umid            = trim($_POST['umid'] ?? '');
    $pagibig         = trim($_POST['pagibig'] ?? '');
    $philhealth      = trim($_POST['philhealth'] ?? '');
    $philsys         = trim($_POST['philsys'] ?? '');
    $tin             = trim($_POST['tin'] ?? '');
    $agency_employee = trim($_POST['agency_employee'] ?? '');
    $citizenship     = trim($_POST['citizenship'] ?? '');
    $dual_country    = trim($_POST['dual_country'] ?? '');
    $telephone       = trim($_POST['telephone'] ?? '');
    $mobile          = trim($_POST['mobile'] ?? '');
    $email           = trim($_POST['email'] ?? '');

    $r_house1      = trim($_POST['r_house1'] ?? '');
    $r_street      = trim($_POST['r_street'] ?? '');
    $r_subdivision = trim($_POST['r_subdivision'] ?? '');
    $r_barangay    = trim($_POST['r_barangay'] ?? '');
    $r_city        = trim($_POST['r_city'] ?? '');
    $r_province    = trim($_POST['r_province'] ?? '');
    $r_zip         = trim($_POST['r_zip'] ?? '');

    $p_house1      = trim($_POST['p_house1'] ?? '');
    $p_street      = trim($_POST['p_street'] ?? '');
    $p_subdivision = trim($_POST['p_subdivision'] ?? '');
    $p_barangay    = trim($_POST['p_barangay'] ?? '');
    $p_city        = trim($_POST['p_city'] ?? '');
    $p_province    = trim($_POST['p_province'] ?? '');
    $p_zip         = trim($_POST['p_zip'] ?? '');

    $education_level = $_POST['education_level'] ?? [];
    $school_name     = $_POST['school_name'] ?? [];
    $course          = $_POST['course'] ?? [];
    $units           = $_POST['units'] ?? [];
    $edu_from        = $_POST['edu_from'] ?? [];
    $edu_to          = $_POST['edu_to'] ?? [];
    $year_graduated  = $_POST['year_graduated'] ?? [];
    $honors          = $_POST['honors'] ?? [];

    $career_service  = $_POST['career_service'] ?? [];
    $rating          = $_POST['rating'] ?? [];
    $exam_date       = $_POST['exam_date'] ?? [];
    $exam_place      = $_POST['exam_place'] ?? [];
    $license         = $_POST['license'] ?? [];
    $license_number  = $_POST['license_number'] ?? [];
    $valid_until     = $_POST['valid_until'] ?? [];

    $training_title  = $_POST['title'] ?? [];
    $hours           = $_POST['hours'] ?? [];
    $training_from   = $_POST['training_from'] ?? [];
    $training_to     = $_POST['training_to'] ?? [];
    $training_type   = $_POST['type'] ?? [];
    $sponsor         = $_POST['sponsor'] ?? [];

    try {
        $conn->begin_transaction();

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

        if (table_exists($conn, 'addresses')) {
            $resType = "Residential";
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

            $permType = "Permanent";
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

                for ($i = 0; $i < count($education_level); $i++) {
                    $rowData = [
                        'person_id' => $id,
                        'education_level' => trim($education_level[$i] ?? ''),
                        'school_name' => trim($school_name[$i] ?? ''),
                        'course' => trim($course[$i] ?? ''),
                        'units' => trim($units[$i] ?? ''),
                        'edu_from' => trim($edu_from[$i] ?? ''),
                        'edu_to' => trim($edu_to[$i] ?? ''),
                        'year_graduated' => trim($year_graduated[$i] ?? ''),
                        'honors' => trim($honors[$i] ?? '')
                    ];

                    if (
                        $rowData['education_level'] === '' &&
                        $rowData['school_name'] === '' &&
                        $rowData['course'] === '' &&
                        $rowData['units'] === '' &&
                        $rowData['edu_from'] === '' &&
                        $rowData['edu_to'] === '' &&
                        $rowData['year_graduated'] === '' &&
                        $rowData['honors'] === ''
                    ) {
                        continue;
                    }

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

                for ($i = 0; $i < count($career_service); $i++) {
                    $rowData = [
                        'person_id' => $id,
                        'career_service' => trim($career_service[$i] ?? ''),
                        'rating' => trim($rating[$i] ?? ''),
                        'exam_date' => trim($exam_date[$i] ?? ''),
                        'exam_place' => trim($exam_place[$i] ?? ''),
                        'license' => trim($license[$i] ?? ''),
                        'license_number' => trim($license_number[$i] ?? ''),
                        'valid_until' => trim($valid_until[$i] ?? '')
                    ];

                    if (
                        $rowData['career_service'] === '' &&
                        $rowData['rating'] === '' &&
                        $rowData['exam_date'] === '' &&
                        $rowData['exam_place'] === '' &&
                        $rowData['license'] === '' &&
                        $rowData['license_number'] === '' &&
                        $rowData['valid_until'] === ''
                    ) {
                        continue;
                    }

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

                for ($i = 0; $i < count($training_title); $i++) {
                    $rowData = [
                        'person_id' => $id,
                        'title' => trim($training_title[$i] ?? ''),
                        'hours' => trim($hours[$i] ?? ''),
                        'training_from' => trim($training_from[$i] ?? ''),
                        'training_to' => trim($training_to[$i] ?? ''),
                        'type' => trim($training_type[$i] ?? ''),
                        'sponsor' => trim($sponsor[$i] ?? '')
                    ];

                    if (
                        $rowData['title'] === '' &&
                        $rowData['hours'] === '' &&
                        $rowData['training_from'] === '' &&
                        $rowData['training_to'] === '' &&
                        $rowData['type'] === '' &&
                        $rowData['sponsor'] === ''
                    ) {
                        continue;
                    }

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
        $message = "Record updated successfully.";
    } catch (Exception $ex) {
        $conn->rollback();
        $error = "Update failed: " . $ex->getMessage();
    }
}

/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/
if ($search !== "") {
    $stmt = $conn->prepare("
        SELECT id, firstname, middlename, surname, extension, email, mobile
        FROM personal_info
        WHERE CONCAT(firstname, ' ', surname) LIKE ?
           OR CONCAT(surname, ' ', firstname) LIKE ?
           OR firstname LIKE ?
           OR surname LIKE ?
        ORDER BY surname, firstname
    ");
    $like = "%{$search}%";
    $stmt->bind_param("ssss", $like, $like, $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}

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
        if (table_exists($conn, 'addresses')) {
            $stmt = $conn->prepare("SELECT * FROM addresses WHERE person_id = ?");
            $stmt->bind_param("i", $selected_id);
            $stmt->execute();
            $addrResult = $stmt->get_result();

            while ($row = $addrResult->fetch_assoc()) {
                if (strcasecmp($row['type'] ?? '', 'Residential') === 0) {
                    $residential = normalize_address_row($row);
                } elseif (strcasecmp($row['type'] ?? '', 'Permanent') === 0) {
                    $permanent = normalize_address_row($row);
                }
            }
            $stmt->close();
        }

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
  background:#2f402c;
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
  display:grid;
  grid-template-columns:180px 1fr 180px 1fr;
  gap:12px 16px;
  align-items:center;
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
  background:#2f402c;
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
  min-height:calc(100vh - 50px);
}

.sidebar{
  width:270px;
  padding:40px 20px;
  position:relative;
  display:flex;
  flex-direction:column;
  gap:35px;
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
  flex:1;
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:20px;
  overflow-y:auto;
  box-sizing:border-box;
}

.card{
  width:1150px;
  min-height:700px;
  max-width:100%;
  background:#c7d1c3;
  padding:20px 40px 30px;
  border-radius:15px;
  border:3px solid black;
  box-sizing:border-box;
}

.title{
  text-align:center;
  font-size:22px;
  font-weight:800;
  margin-bottom:25px;
  margin-top:5px;
}

/* PERSONAL INFORMATION */
.personal-grid{
  display:grid;
  grid-template-columns:160px 1fr 160px 1fr;
  gap:18px 5px;
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
  grid-template-columns:1fr auto 140px auto 140px auto 140px;
  justify-content:center;
  align-items:center;
  gap:15px 5px;
  margin-top:20px;
}

.personal-row.small input,
.personal-row.small select{
  width:140px;
}

.citizenship-row{
  display:grid;
  grid-template-columns:160px 285px 240px 1fr;
  gap:18px 5px;
  align-items:center;
  margin-top:25px;
  width:100%;
}

.citizenship-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.citizenship-row select,
.citizenship-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
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
  grid-template-columns:160px 1fr;
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
  grid-template-columns:160px 1fr;
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
  grid-template-columns:145px 1fr;
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
  background:#2f402c;
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
  background:#3b5237;
}

.remove-btn{
  background:#8b2c2c;
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
  padding:10px 15px 0;
  position:relative;
  z-index:3;
}

.sidebar-delete-btn{
  width:100%;
  background:#8b2c2c;
  color:#fff;
  border:none;
  padding:10px 16px;
  border-radius:6px;
  font-size:14px;
  font-weight:600;
  cursor:pointer;
  margin-top:0;
}

.sidebar-delete-btn:hover{
  background:#a63a3a;
}

@media (max-width: 950px){
  .simple-grid{
    grid-template-columns:1fr;
  }
}

@media (max-width: 900px){
  .container{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
    max-height:none;
    gap:15px;
  }

  .sidebar::before,
  .progress-line{
    display:none;
  }

  .personal-grid,
  .citizenship-row,
  .education-grid,
  .eligibility-grid,
  .training-grid{
    grid-template-columns:1fr;
  }

  .personal-row{
    grid-template-columns:1fr;
    align-items:stretch;
  }

  .contact-section{
    padding-top:40px;
  }

  .contact-grid{
    width:100%;
    max-width:390px;
  }

  .contact-row{
    grid-template-columns:1fr;
    row-gap:8px;
  }

  .address-house-row,
  .address-row{
    grid-template-columns:1fr;
    row-gap:8px;
  }

  .address-two-col{
    grid-template-columns:1fr;
    column-gap:0;
    row-gap:18px;
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
  }

  .personal-grid label,
  .personal-row label,
  .citizenship-row label,
  .contact-row label,
  .address-house-row label,
  .address-row label{
    text-align:left;
  }

  .sidebar-delete-form{
    padding:0;
  }
}
</style>
</head>
<body>

<div class="page">
    <a href="../dashboard/dashboard.php" class="top-link">Home</a>

    <h1>Search, View, Edit and Delete Personal Record</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card-simple">
        <h2>Search</h2>
        <form method="GET">
            <div class="simple-grid">
                <div><label>Search Name</label></div>
                <div><input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Enter first name or surname"></div>
                <div></div>
                <div class="search-actions">
                    <button type="submit" class="btn-primary">Search</button>
                    <a href="<?php echo e($_SERVER['PHP_SELF']); ?>" class="btn-link btn-secondary">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <?php if ($search !== ""): ?>
    <div class="card-simple">
        <h2>Search Results</h2>
        <?php if (count($results) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th style="width:80px;">ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th style="width:110px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?php echo e($row['id']); ?></td>
                            <td><?php echo e(trim($row['surname'] . ", " . $row['firstname'] . " " . $row['middlename'] . " " . $row['extension'])); ?></td>
                            <td><?php echo e($row['email']); ?></td>
                            <td><?php echo e($row['mobile']); ?></td>
                            <td><a class="btn-link btn-primary" href="?search=<?php echo urlencode($search); ?>&id=<?php echo (int)$row['id']; ?>">Edit</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No record found.</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if ($person): ?>
    <div class="edit-wrapper">

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
                    <button type="submit" name="delete" class="sidebar-delete-btn">Delete Record</button>
                </form>
            </div>

            <div class="form-area">
                <div class="card">
                    <form method="POST" id="editRecordForm" autocomplete="off">
                        <input type="hidden" name="id" value="<?php echo e($person['id']); ?>">
                        <input type="hidden" name="search" value="<?php echo e($search); ?>">

                        <div id="personal" class="section active">
                            <div class="title">PERSONAL INFORMATION</div>

                            <div class="personal-grid">
                                <label>Last Name:</label>
                                <input name="surname" value="<?php echo e($person['surname'] ?? ''); ?>">

                                <label>Name Extension:</label>
                                <input name="extension" value="<?php echo e($person['extension'] ?? ''); ?>">

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
                                <input name="blood_type" value="<?php echo e($person['blood_type'] ?? ''); ?>">
                            </div>

                            <div class="personal-row small">
                                <label>Height:</label>
                                <input name="height" value="<?php echo e($person['height'] ?? ''); ?>">

                                <label>Weight:</label>
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
                                <select name="citizenship">
                                    <option value=""></option>
                                    <option value="Filipino" <?php echo (($person['citizenship'] ?? '') === 'Filipino') ? 'selected' : ''; ?>>Filipino</option>
                                    <option value="Dual Citizen" <?php echo (($person['citizenship'] ?? '') === 'Dual Citizen') ? 'selected' : ''; ?>>Dual Citizen</option>
                                </select>

                                <label>If Dual Citizen(Indicate Country):</label>
                                <input name="dual_country" value="<?php echo e($person['dual_country'] ?? ''); ?>">
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

                                <div class="address-title" style="margin-top:18px;">PERMANENT ADDRESS</div>
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

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">Remove</button>
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

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addEducation()">Add More</button>
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

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
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

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addEligibility()">Add More</button>
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

                                            <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">Remove</button>
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

                                        <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">Remove</button>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <button type="button" class="add-btn" onclick="addTraining()">Add More</button>
                        </div>
                        <?php endif; ?>

                        <div class="nav-buttons">
                            <button type="button" class="next-btn" id="nextBtn" onclick="nextSection()">Next</button>
                            <button type="submit" name="update" class="save-btn" id="saveBtn" style="display:none;">Update Record</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
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
        if(i < index){
            nav.classList.add("completed");
        }
    });

    if (navItems[index]) {
        navItems[index].classList.add("active");
    }

    if (navItems.length > 0 && document.getElementById("progressLine")) {
        const stepHeight = navItems[0].offsetHeight + 35;
        document.getElementById("progressLine").style.height = (index * stepHeight) + "px";
    }

    currentSection = index;

    const nextBtn = document.getElementById("nextBtn");
    const saveBtn = document.getElementById("saveBtn");

    if(nextBtn && saveBtn){
        if(index === sections.length - 1){
            nextBtn.style.display = "none";
            saveBtn.style.display = "inline-block";
        } else {
            nextBtn.style.display = "inline-block";
            saveBtn.style.display = "none";
        }
    }
}

function goToSection(index){
    updateProgress(index);
}

function nextSection(){
    const sections = getVisibleSections();
    if(currentSection < sections.length - 1){
        updateProgress(currentSection + 1);
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
                <input name="school_name[]" placeholder="School Name">
            </div>

            <div>
                <label>Basic Education / Degree / Course</label>
                <input name="course[]" placeholder="Course / Degree">
            </div>

            <div>
                <label>Highest Level / Units Earned</label>
                <input name="units[]" placeholder="Highest Level / Units">
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
                <input name="year_graduated[]" placeholder="Year Graduated">
            </div>

            <div>
                <label>Scholarship / Academic Honors</label>
                <input name="honors[]" placeholder="Scholarship / Honors">
            </div>
        </div>
        <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="education_level[]"]').value = data.education_level || 'Elementary';
    div.querySelector('[name="school_name[]"]').value = data.school_name || '';
    div.querySelector('[name="course[]"]').value = data.course || '';
    div.querySelector('[name="units[]"]').value = data.units || '';
    div.querySelector('[name="edu_from[]"]').value = data.edu_from || '';
    div.querySelector('[name="edu_to[]"]').value = data.edu_to || '';
    div.querySelector('[name="year_graduated[]"]').value = data.year_graduated || '';
    div.querySelector('[name="honors[]"]').value = data.honors || '';

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
                <input name="career_service[]" placeholder="Career Service / CSC / CES">
            </div>

            <div>
                <label>Rating</label>
                <input name="rating[]" placeholder="Rating">
            </div>

            <div>
                <label>Exam Date</label>
                <input type="date" name="exam_date[]">
            </div>

            <div>
                <label>Place of Examination</label>
                <input name="exam_place[]" placeholder="Place of Examination">
            </div>

            <div>
                <label>License</label>
                <input name="license[]" placeholder="License">
            </div>

            <div>
                <label>License Number</label>
                <input name="license_number[]" placeholder="License Number">
            </div>

            <div>
                <label>Valid Until</label>
                <input type="date" name="valid_until[]">
            </div>
        </div>
        <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="career_service[]"]').value = data.career_service || '';
    div.querySelector('[name="rating[]"]').value = data.rating || '';
    div.querySelector('[name="exam_date[]"]').value = data.exam_date || '';
    div.querySelector('[name="exam_place[]"]').value = data.exam_place || '';
    div.querySelector('[name="license[]"]').value = data.license || '';
    div.querySelector('[name="license_number[]"]').value = data.license_number || '';
    div.querySelector('[name="valid_until[]"]').value = data.valid_until || '';

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
                <input name="title[]" placeholder="Training Title">
            </div>

            <div>
                <label>Hours</label>
                <input name="hours[]" placeholder="Hours">
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
                <input name="type[]" placeholder="Managerial / Technical">
            </div>

            <div>
                <label>Sponsor</label>
                <input name="sponsor[]" placeholder="Sponsor">
            </div>
        </div>
        <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">Remove</button>
    `;
    container.appendChild(div);

    div.querySelector('[name="title[]"]').value = data.title || '';
    div.querySelector('[name="hours[]"]').value = data.hours || '';
    div.querySelector('[name="training_from[]"]').value = data.training_from || '';
    div.querySelector('[name="training_to[]"]').value = data.training_to || '';
    div.querySelector('[name="type[]"]').value = data.type || '';
    div.querySelector('[name="sponsor[]"]').value = data.sponsor || '';

    saveFormDraft();
}

function removeEntry(button, selector) {
    const item = button.closest(selector);
    if (item) {
        item.remove();
        saveFormDraft();
    }
}

function getDraftKey() {
    const form = document.getElementById('editRecordForm');
    if (!form) return null;

    const idInput = form.querySelector('input[name="id"]');
    const personId = idInput ? idInput.value : 'new';
    return 'personal_record_draft_' + personId;
}

function collectRepeatedEntries(selector, fieldNames) {
    const entries = [];
    document.querySelectorAll(selector).forEach(entry => {
        const row = {};
        fieldNames.forEach(name => {
            const el = entry.querySelector(`[name="${name}[]"]`);
            row[name] = el ? el.value : '';
        });

        const hasValue = Object.values(row).some(v => String(v).trim() !== '');
        if (hasValue) {
            entries.push(row);
        }
    });
    return entries;
}

function saveFormDraft() {
    const form = document.getElementById('editRecordForm');
    if (!form) return;

    const key = getDraftKey();
    if (!key) return;

    const data = {
        simple: {},
        education: collectRepeatedEntries('.education-entry', [
            'education_level', 'school_name', 'course', 'units',
            'edu_from', 'edu_to', 'year_graduated', 'honors'
        ]),
        eligibility: collectRepeatedEntries('.eligibility-entry', [
            'career_service', 'rating', 'exam_date', 'exam_place',
            'license', 'license_number', 'valid_until'
        ]),
        training: collectRepeatedEntries('.training-entry', [
            'title', 'hours', 'training_from', 'training_to', 'type', 'sponsor'
        ])
    };

    const fields = form.querySelectorAll('input:not([type="hidden"]):not([name$="[]"]), select:not([name$="[]"]), textarea:not([name$="[]"])');
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
        if (field) {
            field.value = data.simple[name];
        }
    });
}

function clearContainer(selector) {
    const container = document.querySelector(selector);
    if (container) {
        container.innerHTML = '';
    }
}

function restoreDraft() {
    const form = document.getElementById('editRecordForm');
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

    if (document.getElementById('education-container') && Array.isArray(data.education)) {
        clearContainer('#education-container');
        if (data.education.length > 0) {
            data.education.forEach(row => addEducation(row));
        } else {
            addEducation();
        }
    }

    if (document.getElementById('eligibility-container') && Array.isArray(data.eligibility)) {
        clearContainer('#eligibility-container');
        if (data.eligibility.length > 0) {
            data.eligibility.forEach(row => addEligibility(row));
        } else {
            addEligibility();
        }
    }

    if (document.getElementById('training-container') && Array.isArray(data.training)) {
        clearContainer('#training-container');
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

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editRecordForm');
    if (!form) return;

    restoreDraft();
    updateProgress(0);

    form.addEventListener('input', saveFormDraft);
    form.addEventListener('change', saveFormDraft);

    form.addEventListener('submit', function () {
        clearDraft();
    });
});
</script>
</body>
</html>