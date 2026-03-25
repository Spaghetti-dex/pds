<?php
require_once "../includes/auth_check.php";
require_once "../includes/audit_log.php";
include "../config/database.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

function clean($data){
    if(is_array($data)) return '';
    return htmlspecialchars(trim($data));
}

$surname = clean($_POST['surname'] ?? '');
$firstname = clean($_POST['firstname'] ?? '');
$middlename = clean($_POST['middlename'] ?? '');
$extension = clean($_POST['extension'] ?? '');

$dob = !empty($_POST['dob']) ? $_POST['dob'] : NULL;
$birth_place = clean($_POST['birth_place'] ?? '');

$sex = clean($_POST['sex'] ?? '');
$civil_status = clean($_POST['civil_status'] ?? '');

$height = clean($_POST['height'] ?? '');
$weight = clean($_POST['weight'] ?? '');
$blood_type = clean($_POST['blood_type'] ?? '');

$umid = clean($_POST['umid'] ?? '');
$pagibig = clean($_POST['pagibig'] ?? '');
$philhealth = clean($_POST['philhealth'] ?? '');
$philsys = clean($_POST['philsys'] ?? '');
$tin = clean($_POST['tin'] ?? '');
$agency_employee = clean($_POST['agency_employee'] ?? '');

$citizenship = clean($_POST['citizenship'] ?? '');
$dual_country = clean($_POST['dual_country'] ?? '');
$telephone = clean($_POST['telephone'] ?? '');
$mobile = clean($_POST['mobile'] ?? '');
$email = clean($_POST['email'] ?? '');

/* SAVE PERSONAL INFO */
$sql = "INSERT INTO personal_info
(
    surname, firstname, middlename, extension, dob, birth_place, sex, civil_status,
    height, weight, blood_type, umid, pagibig, philhealth, philsys, tin, agency_employee,
    citizenship, dual_country, telephone, mobile, email
)
VALUES
(
    '$surname','$firstname','$middlename','$extension',".($dob ? "'$dob'" : "NULL").",'$birth_place','$sex','$civil_status',
    '$height','$weight','$blood_type','$umid','$pagibig','$philhealth','$philsys','$tin','$agency_employee',
    '$citizenship','$dual_country','$telephone','$mobile','$email'
)";

if (!$conn->query($sql)) {
    die("Error saving personal info: " . $conn->error);
}

$person_id = $conn->insert_id;

/* SAVE ADDRESSES */
$r_house = clean($_POST['r_house'] ?? '');
$r_street = clean($_POST['r_street'] ?? '');
$r_subdivision = clean($_POST['r_subdivision'] ?? '');
$r_barangay = clean($_POST['r_barangay'] ?? '');
$r_city = clean($_POST['r_city'] ?? '');
$r_province = clean($_POST['r_province'] ?? '');
$r_zip = clean($_POST['r_zip'] ?? '');

$p_house = clean($_POST['p_house'] ?? '');
$p_street = clean($_POST['p_street'] ?? '');
$p_subdivision = clean($_POST['p_subdivision'] ?? '');
$p_barangay = clean($_POST['p_barangay'] ?? '');
$p_city = clean($_POST['p_city'] ?? '');
$p_province = clean($_POST['p_province'] ?? '');
$p_zip = clean($_POST['p_zip'] ?? '');

$sql_address = "INSERT INTO addresses (person_id, type, house, street, subdivision, barangay, city, province, zip)
VALUES
('$person_id', 'residential', '$r_house', '$r_street', '$r_subdivision', '$r_barangay', '$r_city', '$r_province', '$r_zip'),
('$person_id', 'permanent', '$p_house', '$p_street', '$p_subdivision', '$p_barangay', '$p_city', '$p_province', '$p_zip')";

$conn->query($sql_address);

/* SAVE EDUCATION */
if (!empty($_POST['education_level']) && is_array($_POST['education_level'])) {
    for ($i = 0; $i < count($_POST['education_level']); $i++) {
        $education_level = clean($_POST['education_level'][$i] ?? '');
        $school_name = clean($_POST['school_name'][$i] ?? '');
        $course = clean($_POST['course'][$i] ?? '');
        $edu_from = clean($_POST['edu_from'][$i] ?? '');
        $edu_to = clean($_POST['edu_to'][$i] ?? '');
        $units = clean($_POST['units'][$i] ?? '');
        $year_graduated = clean($_POST['year_graduated'][$i] ?? '');
        $hours = clean($_POST['hours'][$i] ?? '');

        if ($education_level !== '' || $school_name !== '' || $course !== '') {
            $sql_education = "INSERT INTO EDUCATION
            (person_id, education_level, school_name, course, edu_from, edu_to, units, year_graduated, hours)
            VALUES
            ('$person_id','$education_level','$school_name','$course','$edu_from','$edu_to','$units','$year_graduated','$hours')";
            $conn->query($sql_education);
        }
    }
}

/* SAVE ELIGIBILITY */
if (!empty($_POST['career_service']) && is_array($_POST['career_service'])) {
    for ($i = 0; $i < count($_POST['career_service']); $i++) {
        $career_service = clean($_POST['career_service'][$i] ?? '');
        $rating = clean($_POST['rating'][$i] ?? '');
        $exam_date = clean($_POST['exam_date'][$i] ?? '');
        $exam_place = clean($_POST['exam_place'][$i] ?? '');
        $license = clean($_POST['license'][$i] ?? '');
        $license_number = clean($_POST['license_number'][$i] ?? '');
        $valid_until = clean($_POST['valid_until'][$i] ?? '');

        if ($career_service !== '' || $rating !== '') {
            $sql_eligibility = "INSERT INTO ELIGIBILITY
            (person_id, career_service, rating, exam_date, exam_place, license, license_number, value_until)
            VALUES
            ('$person_id','$career_service','$rating','$exam_date','$exam_place','$license','$license_number','$valid_until')";
            $conn->query($sql_eligibility);
        }
    }
}

/* SAVE TRAINING */
if (!empty($_POST['title']) && is_array($_POST['title'])) {
    for ($i = 0; $i < count($_POST['title']); $i++) {
        $title = clean($_POST['title'][$i] ?? '');
        $training_from = clean($_POST['training_from'][$i] ?? '');
        $training_to = clean($_POST['training_to'][$i] ?? '');
        $hours = clean($_POST['hours'][$i] ?? '');
        $type = clean($_POST['type'][$i] ?? '');
        $sponsor = clean($_POST['sponsor'][$i] ?? '');

        if ($title !== '' || $hours !== '') {
            $sql_training = "INSERT INTO TRAINING
            (person_id, title, training_from, training_to, hours, type, sponsor)
            VALUES
            ('$person_id','$title','$training_from','$training_to','$hours','$type','$sponsor')";
            $conn->query($sql_training);
        }
    }
}

/* WRITE LOG */
$full_name = trim($firstname . ' ' . $middlename . ' ' . $surname . ' ' . $extension);
write_audit_log($conn, $person_id, 'CREATE', "Created PDS record for " . $full_name);

echo "<script>alert('Operation successful!'); window.location.href = '../dashboard/dashboard.php';</script>";
?>