<?php
include "../includes/auth_check.php";
include "../config/database.php"; // your DB connection

// Initialize variables
$person = [];
$search_name = "";
$message = "";

// Handle search
if(isset($_GET['search'])){
    $search_name = trim($_GET['search']);

    $stmt = $conn->prepare("SELECT * FROM personal_info WHERE CONCAT(surname,' ',firstname) LIKE ?");
    
    $like = "%".$search_name."%";
    $stmt->bind_param("s", $like);
    
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $person = $result->fetch_assoc();
    } else {
        $message = "No record found.";
    }
}

// Handle update
if(isset($_POST['update'])){
    $id = $_POST['id']; // hidden input in form
    $surname = $_POST['surname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $extension = $_POST['extension'];
    $dob = $_POST['dob'];
    $birth_place = $_POST['birth_place'];
    $sex = $_POST['sex'];
    $civil_status = $_POST['civil_status'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $blood_type = $_POST['blood_type'];
    $umid = $_POST['umid'];
    $pagibig = $_POST['pagibig'];
    $philhealth = $_POST['philhealth'];
    $philsys = $_POST['philsys'];
    $tin = $_POST['tin'];
    $agency_employee = $_POST['agency_employee'];
    $citizenship = $_POST['citizenship'];
    $dual_country = $_POST['dual_country'];

    // Addresses
    $r_house = $_POST['r_house'];
    $r_street = $_POST['r_street'];
    $r_subdivision = $_POST['r_subdivision'];
    $r_barangay = $_POST['r_barangay'];
    $r_city = $_POST['r_city'];
    $r_province = $_POST['r_province'];
    $r_zip = $_POST['r_zip'];

    $p_house = $_POST['p_house'];
    $p_street = $_POST['p_street'];
    $p_subdivision = $_POST['p_subdivision'];
    $p_barangay = $_POST['p_barangay'];
    $p_city = $_POST['p_city'];
    $p_province = $_POST['p_province'];
    $p_zip = $_POST['p_zip'];

    $telephone = $_POST['telephone'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    $education_level = $_POST['education_level'];
    $school_name = $_POST['school_name'];
    $course = $_POST['course'];
    $edu_from = $_POST['edu_from'];
    $edu_to = $_POST['edu_to'];
    $units = $_POST['units'];
    $year_graduated = $_POST['year_graduated'];
    $honors = $_POST['honors'];

    // Update main personal info table
    $stmt = $conn->prepare("
        UPDATE personal_info SET 
            surname=?, firstname=?, middlename=?, extension=?, dob=?, birth_place=?, sex=?, civil_status=?, height=?, weight=?, blood_type=?, 
            umid=?, pagibig=?, philhealth=?, philsys=?, tin=?, agency_employee=?, citizenship=?, dual_country=?,
            r_house=?, r_street=?, r_subdivision=?, r_barangay=?, r_city=?, r_province=?, r_zip=?,
            p_house=?, p_street=?, p_subdivision=?, p_barangay=?, p_city=?, p_province=?, p_zip=?,
            telephone=?, mobile=?, email=?, education_level=?, school_name=?, course=?, edu_from=?, edu_to=?, units=?, year_graduated=?, honors=?
        WHERE id=?
    ");
    $stmt->bind_param(
        "ssssssssddsssssssssssssssssssssssssssssssii",
        $surname,$firstname,$middlename,$extension,$dob,$birth_place,$sex,$civil_status,$height,$weight,$blood_type,
        $umid,$pagibig,$philhealth,$philsys,$tin,$agency_employee,$citizenship,$dual_country,
        $r_house,$r_street,$r_subdivision,$r_barangay,$r_city,$r_province,$r_zip,
        $p_house,$p_street,$p_subdivision,$p_barangay,$p_city,$p_province,$p_zip,
        $telephone,$mobile,$email,$education_level,$school_name,$course,$edu_from,$edu_to,$units,$year_graduated,$honors,
        $id
    );
    if($stmt->execute()){
        $message = "Record updated successfully.";
    } else {
        $message = "Error updating record: ".$conn->error;
    }
}
?>

<h2>Edit / View Personal Information</h2>

<form method="GET">
    Search by Full Name: 
    <input type="text" name="search" value="<?= htmlspecialchars($search_name) ?>">
    <button type="submit">Search</button>
</form>

<?php if($message) echo "<p>$message</p>"; ?>

<?php if($person): ?>
<form method="POST">

<input type="hidden" name="id" value="<?= $person['id'] ?>">

<h2>Personal Information</h2>
Surname
<input name="surname" required value="<?= htmlspecialchars($person['surname']) ?>">
First Name
<input name="firstname" required value="<?= htmlspecialchars($person['firstname']) ?>">
Middle Name
<input name="middlename" value="<?= htmlspecialchars($person['middlename']) ?>">
Name Extension
<input name="extension" value="<?= htmlspecialchars($person['extension']) ?>">
Date of Birth
<input type="date" name="dob" value="<?= $person['dob'] ?>">
Place of Birth
<input name="birth_place" value="<?= htmlspecialchars($person['birth_place']) ?>">
Sex
<select name="sex">
<option value="Male" <?= $person['sex']=='Male'?'selected':'' ?>>Male</option>
<option value="Female" <?= $person['sex']=='Female'?'selected':'' ?>>Female</option>
</select>
Civil Status
<select name="civil_status">
<option <?= $person['civil_status']=='Single'?'selected':'' ?>>Single</option>
<option <?= $person['civil_status']=='Married'?'selected':'' ?>>Married</option>
<option <?= $person['civil_status']=='Widowed'?'selected':'' ?>>Widowed</option>
<option <?= $person['civil_status']=='Separated'?'selected':'' ?>>Separated</option>
</select>
Height (m)
<input name="height" value="<?= $person['height'] ?>">
Weight (kg)
<input name="weight" value="<?= $person['weight'] ?>">
Blood Type
<input name="blood_type" value="<?= $person['blood_type'] ?>">
UMID ID
<input name="umid" value="<?= $person['umid'] ?>">
Pag-IBIG ID No.
<input name="pagibig" value="<?= $person['pagibig'] ?>">
PhilHealth No.
<input name="philhealth" value="<?= $person['philhealth'] ?>">
PhilSys Number (PSN)
<input name="philsys" value="<?= $person['philsys'] ?>">
TIN No.
<input name="tin" value="<?= $person['tin'] ?>">
Agency Employee No.
<input name="agency_employee" value="<?= $person['agency_employee'] ?>">

<h3>Citizenship</h3>
<select name="citizenship">
<option <?= $person['citizenship']=='Filipino'?'selected':'' ?>>Filipino</option>
<option <?= $person['citizenship']=='Dual Citizen'?'selected':'' ?>>Dual Citizen</option>
</select>
If Dual Citizen (Indicate Country)
<input name="dual_country" value="<?= htmlspecialchars($person['dual_country']) ?>">

<h3>Residential Address</h3>
<input name="r_house" placeholder="House/Block/Lot No." value="<?= htmlspecialchars($person['r_house']) ?>">
<input name="r_street" placeholder="Street" value="<?= htmlspecialchars($person['r_street']) ?>">
<input name="r_subdivision" placeholder="Subdivision/Village" value="<?= htmlspecialchars($person['r_subdivision']) ?>">
<input name="r_barangay" placeholder="Barangay" value="<?= htmlspecialchars($person['r_barangay']) ?>">
<input name="r_city" placeholder="City/Municipality" value="<?= htmlspecialchars($person['r_city']) ?>">
<input name="r_province" placeholder="Province" value="<?= htmlspecialchars($person['r_province']) ?>">
<input name="r_zip" placeholder="Zip Code" value="<?= htmlspecialchars($person['r_zip']) ?>">

<h3>Permanent Address</h3>
<input name="p_house" placeholder="House/Block/Lot No." value="<?= htmlspecialchars($person['p_house']) ?>">
<input name="p_street" placeholder="Street" value="<?= htmlspecialchars($person['p_street']) ?>">
<input name="p_subdivision" placeholder="Subdivision/Village" value="<?= htmlspecialchars($person['p_subdivision']) ?>">
<input name="p_barangay" placeholder="Barangay" value="<?= htmlspecialchars($person['p_barangay']) ?>">
<input name="p_city" placeholder="City/Municipality" value="<?= htmlspecialchars($person['p_city']) ?>">
<input name="p_province" placeholder="Province" value="<?= htmlspecialchars($person['p_province']) ?>">
<input name="p_zip" placeholder="Zip Code" value="<?= htmlspecialchars($person['p_zip']) ?>">

<h3>Contact Information</h3>
Telephone No.
<input name="telephone" value="<?= htmlspecialchars($person['telephone']) ?>">
Mobile No.
<input name="mobile" value="<?= htmlspecialchars($person['mobile']) ?>">
Email
<input name="email" value="<?= htmlspecialchars($person['email']) ?>">

<h3>Educational Background</h3>
Level
<select name="education_level">
<option <?= $person['education_level']=='Elementary'?'selected':'' ?>>Elementary</option>
<option <?= $person['education_level']=='Secondary'?'selected':'' ?>>Secondary</option>
<option <?= $person['education_level']=='Vocational / Trade Course'?'selected':'' ?>>Vocational / Trade Course</option>
<option <?= $person['education_level']=='College'?'selected':'' ?>>College</option>
<option <?= $person['education_level']=='Graduate Studies'?'selected':'' ?>>Graduate Studies</option>
</select>
Name of School
<input name="school_name" value="<?= htmlspecialchars($person['school_name']) ?>">
Basic Education / Degree / Course
<input name="course" value="<?= htmlspecialchars($person['course']) ?>">
From
<input type="date" name="edu_from" value="<?= $person['edu_from'] ?>">
To
<input type="date" name="edu_to" value="<?= $person['edu_to'] ?>">
Highest Level / Units Earned
<input name="units" value="<?= htmlspecialchars($person['units']) ?>">
Year Graduated
<input name="year_graduated" value="<?= htmlspecialchars($person['year_graduated']) ?>">
Scholarship / Academic Honors
<input name="honors" value="<?= htmlspecialchars($person['honors']) ?>">

<br><br>
<button type="submit" name="update">Update</button>
</form>
<?php endif; ?>