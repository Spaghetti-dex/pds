<?php

    include "../config/database.php";

    function clean($data){
        return htmlspecialchars(trim($data));
    }

    //  personal information
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
    
    

    //if(empty($surname)){
        //die("Surname and Firstname are required.");
    //}

    //if(!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)){
        //die("Invalid email format");
    //}


    // INSERT PERSONAL INFO

   // $sql = "INSERT INTO personal_info
       // (surname, firstname, middlename, extension,dob, birth_place, sex, civil_status,
        //height, weight, blood_type, umid, pagibig, philhealth, philsys, tin, agency_employee,
        //citizenship, dual_country, telephone, mobile, email)
  // VALUES
        //('$surname', '$firstname', '$middlename', '$extension', '$dob', '$birth_place', '$sex', '$civil_status',
       // '$height','$weight','$blood_type','$umid','$pagibig','$philhealth','$philsys','$tin','$agency_employee',
        //'$citizenship','$dual_country','$telephone','$mobile','$email')";

    //$conn->query($sql);

    //$person_id = $conn->insert_id;


     //residential
    $r_house = clean($_POST['r_house']);
    $r_street = clean($_POST['r_street']);
    $r_subdivision = clean($_POST['r_subdivision']);
    $r_barangay= clean($_POST['r_barangay']);
    $r_city = clean($_POST['r_city']);
    $r_province = clean($_POST['r_province']);
    $r_zip = clean($_POST['r_zip']);

    //permanent

    $p_house = clean($_POST['p_house']);
    $p_street = clean($_POST['p_street']);
    $p_subdivision = clean($_POST['p_subdivision']);
    $p_barangay= clean($_POST['p_barangay']);
    $p_city = clean($_POST['p_city']);
    $p_province = clean($_POST['p_province']);
    $p_zip = clean($_POST['p_zip']);

    //insert

    $sql = "INSERT INTO ADDRESSES
        (house, street, subdivision, barangay, city, province, zip,)

    VALUES 
        ('$r_house', '$r_street' ,'$r_subdivision', '$r_barangay', '$r_city', '$r_province', '$r_zip',
        , '$p_house, '$p_street', '$p_subdivision', '$p_barangay' , '$p_city', '$p_province', '$r_zip')";  
    
    //education

    $education_level = clean
    $school_name =  clean
    $course = clean
    $edu_from = clean
    $edu_to = clean
    $unit = clean
    $year_graduated = clean
    $hours = clean




?>