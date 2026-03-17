<?php
//include "../includes/auth_check.php";
//include "../config/database.php";

$person = null;
$search = "";

if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM personal_info WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();

    echo "<p>Record deleted.</p>";
}

//type, house1, street, subdivision, barangay, city, province, zip

if(isset($_POST['update'])){

        $stmt = $conn->prepare("UPDATE personal_info SET
        surname=?,firstname=?,middlename=?, extension=?, dob=?, birth_place=?, sex=?, civil_status=?, height=?, weight=?, blood_type=?, umid=?, pagibig=?, philhealth=?,
        philsys=?, tin=?, agency_employee=?, citizenship=?, dual_country=?, telephone=?,mobile=?, email=?
            WHERE id=?");

                $stmt->bind_param(
                "ssssssssssssssssssssssssssssssssssssi",

                        $_POST['surname'],
                        $_POST['firstname'],
                        $_POST['middlename'],
                        $_POST['extension'],
                        $_POST['dob'],
                        $_POST['birth_place'],
                        $_POST['sex'],
                        $_POST['civil_status'],
                        $_POST['height'],
                        $_POST['weight'],
                        $_POST['blood_type'],
                        $_POST['umid'],
                        $_POST['pagibig'],
                        $_POST['philhealth'],
                        $_POST['philsys'],
                        $_POST['tin'],
                        $_POST['agency_employee'],
                        $_POST['citizenship'],
                        $_POST['dual_country'],
                        $_POST['telephone'],
                        $_POST['mobile'],
                        $_POST['email'],
                        $_POST['id']
);

        $stmt->execute();

        echo "<p>Record updated successfully.</p>";
        }

    if(isset($_GET['search'])){
       

        $search = trim($_GET['search']);
        

        $stmt = $conn->prepare("
        SELECT 
            p.*, 
            a.*
        FROM personal_info p
        LEFT JOIN addresses a 
            ON p.id = a.person_id
        WHERE CONCAT(p.firstname,' ',p.surname) LIKE ?
        OR CONCAT(p.surname,' ',p.firstname) LIKE ?
        ");

        $like = "%".$search."%";

        $stmt->bind_param("ss",$like,$like);
        $stmt->execute();

        $result = $stmt->get_result();

        $person = [];

        while ($row = $result->fetch_assoc()) {
            $person[] = $row;
        }
        
        foreach ($person as $p) {
            echo "<pre>";
            print_r($p);
            echo "</pre>";
       }
    }   
?>

    <a href="../dashboard/dashboard.php">Home</a>

    <h2>Search Personal Record</h2>

    <form method="GET">
    <input type="text" name="search" placeholder="Enter name..." value="<?php echo $search; ?>">
    <button type="submit">Search</button>
    </form>

    <hr>
     
    
    <?php if(true){ ?>

    <form method="POST">

    <input type="hidden" name="id" value="<?php echo $person['id']; ?>">

    <h2>Personal Information</h2>

    Surname
    <input name="surname" value="Pilapil">

    First Name
    <input name="firstname" value="Yasmin">

    Middle Name
    <input name="middlename" value="Ortaliza">

    Name Extension
    <input name="extension" value="Jr">

    Date of Birth
    <input type="date" name="dob" value="2025-16-08">

    Place of Birth
    <input name="birth_place" value="Taguig City">

    Sex
    <select name="sex">
    <option <?echo "selected"; ?> >Male</option>
    <option <?php if($person['sex']=="Female") echo "selected"; ?>>Female</option>
    </select>

    Civil Status
    <select name="civil_status">
    <option <?php if($person['civil_status']=="Single") echo "selected"; ?>>Single</option>
    <option <?php echo "selected"; ?>>Married</option>
    <option <?php if($person['civil_status']=="Widowed") echo "selected"; ?>>Widowed</option>
    <option <?php if($person['civil_status']=="Separated") echo "selected"; ?>>Separated</option>
    </select>

    Height
    <input name="height" value="168cm">

    Weight
    <input name="weight" value="60kg">
    

    Blood Type
    <input name="blood_type" value="Type A+">

    UMID ID
    <input name="umid" value="0123143">

    Pag-IBIG
    <input name="pagibig" value="1312312">

    PhilHealth
    <input name="philhealth" value="423432">

    PhilSys
    <input name="philsys" value="131231">

    TIN
    <input name="tin" value="3123213">

    Agency Employee
    <input name="agency_employee" value="leslie">

    <h3>Citizenship</h3>

    <select name="citizenship">
    <option <?php echo "selected"; ?>>Filipino</option>
    <option <?php if($person['citizenship']=="Dual Citizen") echo "selected"; ?>>Dual Citizen</option>
    </select>

    If Dual Citizen
    <input name="dual_country" value="dAWDAW>">

    <h3>Residential Address</h3>

    <input name="r_house" value="dawdawdw">
    <input name="r_street" value="dDqsqsQ">
    <input name="r_subdivision" value="DADAWG">
    <input name="r_barangay" value="SDFSEF">
    <input name="r_city" value="GDGDFGD">
    <input name="r_province" value="DAWDAWD">
    <input name="r_zip" value="DASDAWDWA">

    <h3>Permanent Address</h3>

    <input name="p_house" value="DAWDAWDAW">
    <input name="p_street" value="DAWDAWD">
    <input name="p_subdivision" value="DAWDAW">
    <input name="p_barangay" value="DAWDAWDAW">
    <input name="p_city" value="DAWDAW">
    <input name="p_province" value="DAWDAW">
    <input name="p_zip" value="DAWDAWD">

    <h3>Contact Information</h3>

    Telephone
    <input name="telephone" value="0931231231">

    Mobile
    <input name="mobile" value="312312312">

    Email
    <input name="email" value="YASMIN@GMAIL.COM">

    <br><br>

    <button type="submit" name="update">Update</button>

    <a href="view.php?delete=<?php echo $person['id']; ?>" onclick="return confirm('Delete this record?')">
    Delete
    </a>

</form>

<?php } ?>

