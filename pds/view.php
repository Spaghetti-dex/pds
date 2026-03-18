<?php
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
    margin:20px auto;
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
h2{
    margin:0 0 16px;
}
h3{
    margin:28px 0 12px;
    padding-bottom:6px;
    border-bottom:2px solid #b8c3b1;
}
h4{
    margin:16px 0 10px;
}
.card{
    background:#fff;
    border:1px solid #cfcfcf;
    border-radius:12px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.05);
}
.grid{
    display:grid;
    grid-template-columns:180px 1fr 180px 1fr;
    gap:12px 16px;
    align-items:center;
}
.entry-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:12px 16px;
}
label{
    font-size:13px;
    font-weight:bold;
}
input, select, textarea{
    width:100%;
    padding:10px;
    border:1px solid #777;
    border-radius:6px;
    box-sizing:border-box;
    background:#f9f9f9;
}
.actions{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-top:24px;
}
button, .btn-link{
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
.entry-box{
    background:#f7f8f6;
    border:1px solid #cfd6ca;
    border-radius:10px;
    padding:16px;
    margin-bottom:14px;
}
.inline-actions{
    margin-top:12px;
}
.muted{
    color:#666;
    font-size:13px;
}
.search-actions{
    display:flex;
    align-items:end;
    gap:10px;
}
.notice{
    background:#fff8df;
    border:1px solid #ead48a;
    color:#7a5d00;
    padding:10px 12px;
    border-radius:8px;
    margin-bottom:15px;
}
@media (max-width: 950px){
    .grid,
    .entry-grid{
        grid-template-columns:1fr;
    }
}
</style>
</head>
<body>
<div class="page">
    <a href="../dashboard/dashboard.php" class="top-link">← Home</a>

    <h1>Search, View, Edit and Delete Personal Record</h1>

    <?php if ($message): ?>
        <div class="message"><?php echo e($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="card">
        <h2>Search</h2>
        <form method="GET">
            <div class="grid">
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
    <div class="card">
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
    <div class="card">
        <h2>Edit Record</h2>
        <p class="muted">This page safely supports your existing database tables.</p>

        <?php if (!$education_table): ?>
            <div class="notice">Education table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <?php if (!$eligibility_table): ?>
            <div class="notice">Eligibility table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <?php if (!$training_table): ?>
            <div class="notice">Training / Learning and Development table not found. The page will still work without that section.</div>
        <?php endif; ?>

        <form method="POST" id="editRecordForm" autocomplete="off">
            <input type="hidden" name="id" value="<?php echo e($person['id']); ?>">
            <input type="hidden" name="search" value="<?php echo e($search); ?>">

            <h3>Personal Information</h3>
            <div class="grid">
                <div><label>Surname</label></div>
                <div><input name="surname" value="<?php echo e($person['surname'] ?? ''); ?>"></div>

                <div><label>First Name</label></div>
                <div><input name="firstname" value="<?php echo e($person['firstname'] ?? ''); ?>"></div>

                <div><label>Middle Name</label></div>
                <div><input name="middlename" value="<?php echo e($person['middlename'] ?? ''); ?>"></div>

                <div><label>Name Extension</label></div>
                <div><input name="extension" value="<?php echo e($person['extension'] ?? ''); ?>"></div>

                <div><label>Date of Birth</label></div>
                <div><input type="date" name="dob" value="<?php echo e($person['dob'] ?? ''); ?>" required></div>

                <div><label>Place of Birth</label></div>
                <div><input name="birth_place" value="<?php echo e($person['birth_place'] ?? ''); ?>"></div>

                <div><label>Sex</label></div>
                <div>
                    <select name="sex">
                        <option value=""></option>
                        <option value="Male" <?php echo (($person['sex'] ?? '') === 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo (($person['sex'] ?? '') === 'Female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>

                <div><label>Civil Status</label></div>
                <div>
                    <select name="civil_status">
                        <option value=""></option>
                        <option value="Single" <?php echo (($person['civil_status'] ?? '') === 'Single') ? 'selected' : ''; ?>>Single</option>
                        <option value="Married" <?php echo (($person['civil_status'] ?? '') === 'Married') ? 'selected' : ''; ?>>Married</option>
                        <option value="Widowed" <?php echo (($person['civil_status'] ?? '') === 'Widowed') ? 'selected' : ''; ?>>Widowed</option>
                        <option value="Separated" <?php echo (($person['civil_status'] ?? '') === 'Separated') ? 'selected' : ''; ?>>Separated</option>
                    </select>
                </div>

                <div><label>Height</label></div>
                <div><input name="height" value="<?php echo e($person['height'] ?? ''); ?>"></div>

                <div><label>Weight</label></div>
                <div><input name="weight" value="<?php echo e($person['weight'] ?? ''); ?>"></div>

                <div><label>Blood Type</label></div>
                <div><input name="blood_type" value="<?php echo e($person['blood_type'] ?? ''); ?>"></div>

                <div><label>UMID ID</label></div>
                <div><input name="umid" value="<?php echo e($person['umid'] ?? ''); ?>"></div>

                <div><label>Pag-IBIG ID No.</label></div>
                <div><input name="pagibig" value="<?php echo e($person['pagibig'] ?? ''); ?>"></div>

                <div><label>PhilHealth No.</label></div>
                <div><input name="philhealth" value="<?php echo e($person['philhealth'] ?? ''); ?>"></div>

                <div><label>PhilSys No. (PSN)</label></div>
                <div><input name="philsys" value="<?php echo e($person['philsys'] ?? ''); ?>"></div>

                <div><label>TIN No.</label></div>
                <div><input name="tin" value="<?php echo e($person['tin'] ?? ''); ?>"></div>

                <div><label>Agency Employee No.</label></div>
                <div><input name="agency_employee" value="<?php echo e($person['agency_employee'] ?? ''); ?>"></div>

                <div><label>Citizenship</label></div>
                <div>
                    <select name="citizenship">
                        <option value=""></option>
                        <option value="Filipino" <?php echo (($person['citizenship'] ?? '') === 'Filipino') ? 'selected' : ''; ?>>Filipino</option>
                        <option value="Dual Citizen" <?php echo (($person['citizenship'] ?? '') === 'Dual Citizen') ? 'selected' : ''; ?>>Dual Citizen</option>
                    </select>
                </div>

                <div><label>If Dual Citizen (Country)</label></div>
                <div><input name="dual_country" value="<?php echo e($person['dual_country'] ?? ''); ?>"></div>
            </div>

            <h3>Address</h3>
            <h4>Residential Address</h4>
            <div class="grid">
                <div><label>House/Block/Lot No.</label></div>
                <div><input name="r_house1" value="<?php echo e($residential['house1']); ?>"></div>

                <div><label>Street</label></div>
                <div><input name="r_street" value="<?php echo e($residential['street']); ?>"></div>

                <div><label>Subdivision/Village</label></div>
                <div><input name="r_subdivision" value="<?php echo e($residential['subdivision']); ?>"></div>

                <div><label>Barangay</label></div>
                <div><input name="r_barangay" value="<?php echo e($residential['barangay']); ?>"></div>

                <div><label>City/Municipality</label></div>
                <div><input name="r_city" value="<?php echo e($residential['city']); ?>"></div>

                <div><label>Province</label></div>
                <div><input name="r_province" value="<?php echo e($residential['province']); ?>"></div>

                <div><label>Zip Code</label></div>
                <div><input name="r_zip" value="<?php echo e($residential['zip']); ?>"></div>
            </div>

            <h4>Permanent Address</h4>
            <div class="grid">
                <div><label>House/Block/Lot No.</label></div>
                <div><input name="p_house1" value="<?php echo e($permanent['house1']); ?>"></div>

                <div><label>Street</label></div>
                <div><input name="p_street" value="<?php echo e($permanent['street']); ?>"></div>

                <div><label>Subdivision/Village</label></div>
                <div><input name="p_subdivision" value="<?php echo e($permanent['subdivision']); ?>"></div>

                <div><label>Barangay</label></div>
                <div><input name="p_barangay" value="<?php echo e($permanent['barangay']); ?>"></div>

                <div><label>City/Municipality</label></div>
                <div><input name="p_city" value="<?php echo e($permanent['city']); ?>"></div>

                <div><label>Province</label></div>
                <div><input name="p_province" value="<?php echo e($permanent['province']); ?>"></div>

                <div><label>Zip Code</label></div>
                <div><input name="p_zip" value="<?php echo e($permanent['zip']); ?>"></div>
            </div>

            <h3>Contact Information</h3>
            <div class="grid">
                <div><label>Telephone No.</label></div>
                <div><input name="telephone" value="<?php echo e($person['telephone'] ?? ''); ?>"></div>

                <div><label>Mobile No.</label></div>
                <div><input name="mobile" value="<?php echo e($person['mobile'] ?? ''); ?>"></div>

                <div><label>Email</label></div>
                <div><input type="email" name="email" value="<?php echo e($person['email'] ?? ''); ?>"></div>
            </div>

            <?php if ($education_table): ?>
            <h3>Educational Background</h3>
            <div id="education-container">
                <?php if (!empty($education_records)): ?>
                    <?php foreach ($education_records as $edu): ?>
                        <div class="entry-box education-entry">
                            <div class="entry-grid">
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
                                <div><label>Name of School</label><input name="school_name[]" value="<?php echo e($edu['school_name'] ?? ''); ?>"></div>
                                <div><label>Basic Education / Degree / Course</label><input name="course[]" value="<?php echo e($edu['course'] ?? ''); ?>"></div>
                                <div><label>Highest Level / Units Earned</label><input name="units[]" value="<?php echo e($edu['units'] ?? ''); ?>"></div>
                                <div><label>Period of Attendance From</label><input type="date" name="edu_from[]" value="<?php echo e($edu['edu_from'] ?? ''); ?>"></div>
                                <div><label>To</label><input type="date" name="edu_to[]" value="<?php echo e($edu['edu_to'] ?? ''); ?>"></div>
                                <div><label>Year Graduated</label><input name="year_graduated[]" value="<?php echo e($edu['year_graduated'] ?? ''); ?>"></div>
                                <div><label>Scholarship / Academic Honors</label><input name="honors[]" value="<?php echo e($edu['honors'] ?? ''); ?>"></div>
                            </div>
                            <div class="inline-actions">
                                <button type="button" class="btn-danger" onclick="removeEntry(this, '.education-entry')">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="entry-box education-entry">
                        <div class="entry-grid">
                            <div>
                                <label>Level</label>
                                <select name="education_level[]">
                                    <option value="Elementary">Elementary</option>
                                    <option value="Secondary">Secondary</option>
                                    <option value="Vocational / Trade Course">Vocational / Trade Course</option>
                                    <option value="College">College</option>
                                    <option value="Graduate Studies">Graduate Studies</option>
                                </select>
                            </div>
                            <div><label>Name of School</label><input name="school_name[]"></div>
                            <div><label>Basic Education / Degree / Course</label><input name="course[]"></div>
                            <div><label>Highest Level / Units Earned</label><input name="units[]"></div>
                            <div><label>Period of Attendance From</label><input type="date" name="edu_from[]"></div>
                            <div><label>To</label><input type="date" name="edu_to[]"></div>
                            <div><label>Year Graduated</label><input name="year_graduated[]"></div>
                            <div><label>Scholarship / Academic Honors</label><input name="honors[]"></div>
                        </div>
                        <div class="inline-actions">
                            <button type="button" class="btn-danger" onclick="removeEntry(this, '.education-entry')">Remove</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-secondary" onclick="addEducation()">Add More Education</button>
            <?php endif; ?>

            <?php if ($eligibility_table): ?>
            <h3>Service Eligibility</h3>
            <div id="eligibility-container">
                <?php if (!empty($eligibility_records)): ?>
                    <?php foreach ($eligibility_records as $elig): ?>
                        <div class="entry-box eligibility-entry">
                            <div class="entry-grid">
                                <div><label>Career Service / CSC / CES</label><input name="career_service[]" value="<?php echo e($elig['career_service'] ?? ''); ?>"></div>
                                <div><label>Rating</label><input name="rating[]" value="<?php echo e($elig['rating'] ?? ''); ?>"></div>
                                <div><label>Exam Date</label><input type="date" name="exam_date[]" value="<?php echo e($elig['exam_date'] ?? ''); ?>"></div>
                                <div><label>Place of Examination</label><input name="exam_place[]" value="<?php echo e($elig['exam_place'] ?? ''); ?>"></div>
                                <div><label>License</label><input name="license[]" value="<?php echo e($elig['license'] ?? ''); ?>"></div>
                                <div><label>License Number</label><input name="license_number[]" value="<?php echo e($elig['license_number'] ?? ''); ?>"></div>
                                <div><label>Valid Until</label><input type="date" name="valid_until[]" value="<?php echo e($elig['valid_until'] ?? ''); ?>"></div>
                            </div>
                            <div class="inline-actions">
                                <button type="button" class="btn-danger" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="entry-box eligibility-entry">
                        <div class="entry-grid">
                            <div><label>Career Service / CSC / CES</label><input name="career_service[]"></div>
                            <div><label>Rating</label><input name="rating[]"></div>
                            <div><label>Exam Date</label><input type="date" name="exam_date[]"></div>
                            <div><label>Place of Examination</label><input name="exam_place[]"></div>
                            <div><label>License</label><input name="license[]"></div>
                            <div><label>License Number</label><input name="license_number[]"></div>
                            <div><label>Valid Until</label><input type="date" name="valid_until[]"></div>
                        </div>
                        <div class="inline-actions">
                            <button type="button" class="btn-danger" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-secondary" onclick="addEligibility()">Add More Eligibility</button>
            <?php endif; ?>

            <?php if ($training_table): ?>
            <h3>Learning and Development</h3>
            <div id="training-container">
                <?php if (!empty($training_records)): ?>
                    <?php foreach ($training_records as $train): ?>
                        <div class="entry-box training-entry">
                            <div class="entry-grid">
                                <div><label>Training Title</label><input name="title[]" value="<?php echo e($train['title'] ?? ''); ?>"></div>
                                <div><label>Hours</label><input name="hours[]" value="<?php echo e($train['hours'] ?? ''); ?>"></div>
                                <div><label>From</label><input type="date" name="training_from[]" value="<?php echo e($train['training_from'] ?? ''); ?>"></div>
                                <div><label>To</label><input type="date" name="training_to[]" value="<?php echo e($train['training_to'] ?? ''); ?>"></div>
                                <div><label>Type</label><input name="type[]" value="<?php echo e($train['type'] ?? ''); ?>"></div>
                                <div><label>Sponsor</label><input name="sponsor[]" value="<?php echo e($train['sponsor'] ?? ''); ?>"></div>
                            </div>
                            <div class="inline-actions">
                                <button type="button" class="btn-danger" onclick="removeEntry(this, '.training-entry')">Remove</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="entry-box training-entry">
                        <div class="entry-grid">
                            <div><label>Training Title</label><input name="title[]"></div>
                            <div><label>Hours</label><input name="hours[]"></div>
                            <div><label>From</label><input type="date" name="training_from[]"></div>
                            <div><label>To</label><input type="date" name="training_to[]"></div>
                            <div><label>Type</label><input name="type[]"></div>
                            <div><label>Sponsor</label><input name="sponsor[]"></div>
                        </div>
                        <div class="inline-actions">
                            <button type="button" class="btn-danger" onclick="removeEntry(this, '.training-entry')">Remove</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <button type="button" class="btn-secondary" onclick="addTraining()">Add More Training</button>
            <?php endif; ?>

            <div class="actions">
                <button type="submit" name="update" class="btn-primary">Update Record</button>
            </div>
        </form>

        <form method="POST" onsubmit="return confirm('Delete this record? This action cannot be undone.');">
            <input type="hidden" name="id" value="<?php echo e($person['id']); ?>">
            <input type="hidden" name="search" value="<?php echo e($search); ?>">
            <div class="actions">
                <button type="submit" name="delete" class="btn-danger">Delete Record</button>
            </div>
        </form>
    </div>
    <?php endif; ?>
</div>

<script>
function addEducation(data = {}) {
    const container = document.getElementById("education-container");
    if (!container) return;

    const div = document.createElement("div");
    div.className = "entry-box education-entry";
    div.innerHTML = `
        <div class="entry-grid">
            <div>
                <label>Level</label>
                <select name="education_level[]">
                    <option value="Elementary">Elementary</option>
                    <option value="Secondary">Secondary</option>
                    <option value="Vocational / Trade Course">Vocational / Trade Course</option>
                    <option value="College">College</option>
                    <option value="Graduate Studies">Graduate Studies</option>
                </select>
            </div>
            <div><label>Name of School</label><input name="school_name[]"></div>
            <div><label>Basic Education / Degree / Course</label><input name="course[]"></div>
            <div><label>Highest Level / Units Earned</label><input name="units[]"></div>
            <div><label>Period of Attendance From</label><input type="date" name="edu_from[]"></div>
            <div><label>To</label><input type="date" name="edu_to[]"></div>
            <div><label>Year Graduated</label><input name="year_graduated[]"></div>
            <div><label>Scholarship / Academic Honors</label><input name="honors[]"></div>
        </div>
        <div class="inline-actions">
            <button type="button" class="btn-danger" onclick="removeEntry(this, '.education-entry')">Remove</button>
        </div>
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
    div.className = "entry-box eligibility-entry";
    div.innerHTML = `
        <div class="entry-grid">
            <div><label>Career Service / CSC / CES</label><input name="career_service[]"></div>
            <div><label>Rating</label><input name="rating[]"></div>
            <div><label>Exam Date</label><input type="date" name="exam_date[]"></div>
            <div><label>Place of Examination</label><input name="exam_place[]"></div>
            <div><label>License</label><input name="license[]"></div>
            <div><label>License Number</label><input name="license_number[]"></div>
            <div><label>Valid Until</label><input type="date" name="valid_until[]"></div>
        </div>
        <div class="inline-actions">
            <button type="button" class="btn-danger" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
        </div>
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
    div.className = "entry-box training-entry";
    div.innerHTML = `
        <div class="entry-grid">
            <div><label>Training Title</label><input name="title[]"></div>
            <div><label>Hours</label><input name="hours[]"></div>
            <div><label>From</label><input type="date" name="training_from[]"></div>
            <div><label>To</label><input type="date" name="training_to[]"></div>
            <div><label>Type</label><input name="type[]"></div>
            <div><label>Sponsor</label><input name="sponsor[]"></div>
        </div>
        <div class="inline-actions">
            <button type="button" class="btn-danger" onclick="removeEntry(this, '.training-entry')">Remove</button>
        </div>
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

    form.addEventListener('input', saveFormDraft);
    form.addEventListener('change', saveFormDraft);

    form.addEventListener('submit', function () {
        clearDraft();
    });
});
</script>
</body>
</html>