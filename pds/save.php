<?php

    include "../config/database.php";

    function clean($data){
        return htmlspecialchars(trim($data));
    }

    $surname = clean($_POST['surname']);
    $firstname = clean($_POST['firstname']);
    $middlename = clean($_POST['middlename']);
    $extension = clean($_POST['extension']);

    $dob = $_POST['dob'];
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


    // VALIDATION

    if(empty($surname) || empty($firstname)){
        die("Surname and Firstname are required.");
    }

    if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        die("Invalid email format");
    }


    // INSERT PERSONAL INFO

    $sql = "INSERT INTO personal_info
        (surname,firstname,middlename,extension,dob,birth_place,sex,civil_status,
        height,weight,blood_type,umid,pagibig,philhealth,philsys,tin,agency_employee,
        citizenship,dual_country,telephone,mobile,email)
    VALUES
        ('$surname','$firstname','$middlename','$extension','$dob','$birth_place','$sex','$civil_status',
        '$height','$weight','$blood_type','$umid','$pagibig','$philhealth','$philsys','$tin','$agency_employee',
        '$citizenship','$dual_country','$telephone','$mobile','$email')";

    $conn->query($sql);

    $person_id = $conn->insert_id;



    // RESIDENTIAL ADDRESS

    $conn->query("INSERT INTO addresses
    (person_id,type,house,street,subdivision,barangay,city,province,zip)
    VALUES
        ('$person_id','residential',
        '".$_POST['r_house']."',
        '".$_POST['r_street']."',
        '".$_POST['r_subdivision']."',
        '".$_POST['r_barangay']."',
        '".$_POST['r_city']."',
        '".$_POST['r_province']."',
        '".$_POST['r_zip']."')
        ");



    // PERMANENT ADDRESS

    $conn->query("INSERT INTO addresses
        (person_id,type,house,street,subdivision,barangay,city,province,zip)
    VALUES
        ('$person_id','permanent',
        '".$_POST['p_house']."',
        '".$_POST['p_street']."',
        '".$_POST['p_subdivision']."',
        '".$_POST['p_barangay']."',
        '".$_POST['p_city']."',
        '".$_POST['p_province']."',
        '".$_POST['p_zip']."')
        ");



    // EDUCATION

    $conn->query("INSERT INTO education
    (person_id,education_level,school_name,course,edu_from,edu_to,units,year_graduated,honors)

    VALUES
        ('$person_id',
        '".$_POST['education_level']."',
        '".$_POST['school_name']."',
        '".$_POST['course']."',
        '".$_POST['edu_from']."',
        '".$_POST['edu_to']."',
        '".$_POST['units']."',
        '".$_POST['year_graduated']."',
        '".$_POST['honors']."')
        ");



    // ELIGIBILITY LOOP

    if(isset($_POST['career_service'])){

    for($i=0;$i<count($_POST['career_service']);$i++){

    $career = $_POST['career_service'][$i];

    if(empty($career)) continue;

    $conn->query("INSERT INTO eligibility
    (person_id,career_service,rating,exam_date,exam_place,license,license_number,valid_until)

    VALUES
        ('$person_id',
        '".$career."',
        '".$_POST['rating'][$i]."',
        '".$_POST['exam_date'][$i]."',
        '".$_POST['exam_place'][$i]."',
        '".$_POST['license'][$i]."',
        '".$_POST['license_number'][$i]."',
        '".$_POST['valid_until'][$i]."')
        ");

    }

    }



    // TRAINING LOOP

    if(isset($_POST['title'])){

    for($i=0;$i<count($_POST['title']);$i++){

    $title = $_POST['title'][$i];

    if(empty($title)) continue;

    $conn->query("INSERT INTO training
    (person_id,title,training_from,training_to,hours,type,sponsor)

    VALUES
    ('$person_id',
    '".$title."',
    '".$_POST['training_from'][$i]."',
    '".$_POST['training_to'][$i]."',
    '".$_POST['hours'][$i]."',
    '".$_POST['type'][$i]."',
    '".$_POST['sponsor'][$i]."')
    ");

    }

    }



    echo "Data Saved Successfully";

?>