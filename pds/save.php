<?php

include "../config/database.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

function clean($data){
    if(is_array($data)) return '';
    return htmlspecialchars(trim($data));
}

// personal information
$surname = clean($_POST['surname']);
$firstname = clean($_POST['firstname']);
$middlename = clean($_POST['middlename']);
$extension = clean($_POST['extension']);

$dob = !empty($_POST['dob']) ? $_POST['dob'] : NULL;
$birth_place = clean($_POST['birth_place']);

$sex = $_POST['sex'];
$civil_status = $_POST['civil_status'];

$height = clean($_POST['height']);
$weight = clean($_POST['weight']);
$blood_type = clean($_POST['blood_type']);

$umid = clean($_POST['umid']);
$pagibig = clean($_POST['pagibig']);
$philhealth = clean($_POST['philhealth']);
$philsys = clean($_POST['philsys']);
$tin = clean($_POST['tin']);
$agency_employee = clean($_POST['agency_employee']);

$citizenship = $_POST['citizenship'];
$dual_country = clean($_POST['dual_country']);
$telephone = clean($_POST['telephone']);
$mobile = clean($_POST['mobile']);
$email = clean($_POST['email']);

// INSERT PERSONAL INFO
$sql = "INSERT INTO personal_info
(surname, firstname, middlename, extension, dob, birth_place, sex, civil_status,
height, weight, blood_type, umid, pagibig, philhealth, philsys, tin, agency_employee,
citizenship, dual_country, telephone, mobile, email)
VALUES
('$surname','$firstname','$middlename','$extension',".($dob ? "'$dob'" : "NULL").",'$birth_place','$sex','$civil_status',
'$height','$weight','$blood_type','$umid','$pagibig','$philhealth','$philsys','$tin','$agency_employee',
'$citizenship','$dual_country','$telephone','$mobile','$email')";

$conn->query($sql);

$person_id = $conn->insert_id;


// residential
$r_house = clean($_POST['r_house']);
$r_street = clean($_POST['r_street']);
$r_subdivision = clean($_POST['r_subdivision']);
$r_barangay = clean($_POST['r_barangay']);
$r_city = clean($_POST['r_city']);
$r_province = clean($_POST['r_province']);
$r_zip = clean($_POST['r_zip']);

// permanent
$p_house = clean($_POST['p_house']);
$p_street = clean($_POST['p_street']);
$p_subdivision = clean($_POST['p_subdivision']);
$p_barangay = clean($_POST['p_barangay']);
$p_city = clean($_POST['p_city']);
$p_province = clean($_POST['p_province']);
$p_zip = clean($_POST['p_zip']);

// insert residential address
$sql = "INSERT INTO ADDRESSES
(houseq1, street, subdivision, barangay, city, province, zip)
VALUES 
($r_house','$r_street','$r_subdivision','$r_barangay','$r_city','$r_province','$r_zip')";


// education (form sends arrays so we take first value)
$education_level = clean($_POST['education_level'][0] ?? '');
$school_name = clean($_POST['school_name'][0] ?? '');
$course = clean($_POST['course'][0] ?? '');
$edu_from = clean($_POST['edu_from'][0] ?? '');
$edu_to = clean($_POST['edu_to'][0] ?? '');
$units = clean($_POST['units'][0] ?? '');
$year_graduated = clean($_POST['year_graduated'][0] ?? '');
$hours = clean($_POST['hours'][0] ?? '');

$sql = "INSERT INTO EDUCATION
(education_level, school_name, course, edu_from, edu_to, units, year_graduated, hours)
VALUES
('$education_level','$school_name','$course','$edu_from','$edu_to','$units','$year_graduated','$hours')";



// eligibility
$career_service = clean($_POST['career_service'][0] ?? '');
$rating = clean($_POST['rating'][0] ?? '');
$exam_date = clean($_POST['exam_date'][0] ?? '');
$exam_place = clean($_POST['exam_place'][0] ?? '');
$license = clean($_POST['license'][0] ?? '');
$license_number = clean($_POST['license_number'][0] ?? '');
$value_until = clean($_POST['valid_until'][0] ?? '');

$sql = "INSERT INTO ELIGIBILITY
(career_service, rating, exam_date, exam_place, license, license_number, value_until)
VALUES
('$career_service','$rating','$exam_date','$exam_place','$license','$license_number','$value_until')";



// learning and development
$title = clean($_POST['title'][0] ?? '');
$hours = clean($_POST['hours'][0] ?? '');

$sql = "INSERT INTO LEARNING_DEVELOPMENT
(title, hours)
VALUES
('$title','$hours')";




// service eligibility
$career_service = clean($_POST['career_service'][0] ?? '');
$rating = clean($_POST['rating'][0] ?? '');

$sql = "INSERT INTO SERVICE_ELIGIBILITY
(career_service, rating)
VALUES
('$career_service','$rating')";



// trainings
$title = clean($_POST['title'][0] ?? '');
$training_from = clean($_POST['training_from'][0] ?? '');
$training_to = clean($_POST['training_to'][0] ?? '');
$hours = clean($_POST['hours'][0] ?? '');
$type = clean($_POST['type'][0] ?? '');
$sponsor = clean($_POST['sponsor'][0] ?? '');

$sql = "INSERT INTO TRAINING
(title, training_from, training_to, hours, type, sponsor)
VALUES 
('$title','$training_from','$training_to','$hours','$type','$sponsor')";
   
    echo "<script>alert('Operation successful!'); window.location.href = '../dashboard/dashboard.php';</script>";



?>  