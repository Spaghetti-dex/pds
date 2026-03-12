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


class address {
    public $person_id;
    public $type;
    public $house1;
    public $street;
    public $subdivision;
    public $barangay;
    public $city;
    public $province;
    public $zip;

    public function _construct($person_id, $type, $house1, $street, $subdivision, $barangay, $city, $province, $zip) {
        $this->person_id = $person_id;
        $this->type = $type;
        $this->house1 = $house1;
        $this->street = $street;
        $this->subdivision = $subdivision;
        $this->barangay = $barangay;
        $this->city = $city;
        $this->province =  $province;
        $this->zip = $zip;
    }
}


$address = [];

$address[] = new address($person_id, 'residential', $_POST['r_house'], $_POST['r_street'], $_POST['r_subdivision'], $_POST['r_barangay'], $_POST['r_city'], $_POST['r_province'], $_POST['r_zip']);
$address[] = new address($person_id, 'permanent', $_POST['p_house'], $_POST['p_street'], $_POST['p_subdivision'], $_POST['p_barangay'], $_POST['p_city'], $_POST['p_province'], $_POST['p_zip']);

// insert residential address
$sql = "INSERT INTO addresses 
(person_id, type, house, street, subdivision, barangay, city, province, zip) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?),
       (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

$stmt->execute([
    $address[0]->person_id,
    $address[0]->type,
    $address[0]->house,
    $address[0]->street,
    $address[0]->subdivision,
    $address[0]->barangay,
    $address[0]->city,
    $address[0]->province,
    $address[0]->zip,

    $address[1]->person_id,
    $address[1]->type,
    $address[1]->house,
    $address[1]->street,
    $address[1]->subdivision,
    $address[1]->barangay,
    $address[1]->city,
    $address[1]->province,
    $address[1]->zip
]);

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