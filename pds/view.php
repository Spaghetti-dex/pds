<?php
include "../includes/auth_check.php";
include "../config/database.php";

$person = null;
$search = "";

/* DELETE */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM personal_info WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();

    echo "<p>Record deleted.</p>";
}

/* UPDATE */
if(isset($_POST['update'])){

        $stmt = $conn->prepare("UPDATE personal_info SET
        surname=?,firstname=?,middlename=?, extension=?, dob=?, birth_place=?, sex=?, civil_status=?, height=?, weight=?, blood_type=?, umid=?, pagibig=?, philhealth=?,
        philsys=?, tin=?, agency_employee=?, citizenship=?, dual_country=?, r_house=?, r_street=?, r_subdivision=?, r_barangay=?, r_city=?, r_province=?,
        r_zip=?, p_house=?,p_street=?, p_subdivision=?, p_barangay=?,p_city=?, p_province=?, p_zip=?, telephone=?,mobile=?, email=?
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
                        $_POST['r_house'],
                        $_POST['r_street'],
                        $_POST['r_subdivision'],
                        $_POST['r_barangay'],
                        $_POST['r_city'],
                        $_POST['r_province'],
                        $_POST['r_zip'],
                        $_POST['p_house'],
                        $_POST['p_street'],
                        $_POST['p_subdivision'],
                        $_POST['p_barangay'],
                        $_POST['p_city'],
                        $_POST['p_province'],
                        $_POST['p_zip'],
                        $_POST['telephone'],
                        $_POST['mobile'],
                        $_POST['email'],
                        $_POST['id']
);

        $stmt->execute();

        echo "<p>Record updated successfully.</p>";
        }

    /* SEARCH */
    if(isset($_GET['search'])){

    $search = trim($_GET['search']);

    $stmt = $conn->prepare("
    SELECT * FROM personal_info
    WHERE CONCAT(firstname,' ',surname) LIKE ?
    OR CONCAT(surname,' ',firstname) LIKE ?
    LIMIT 1
    ");

    $like = "%".$search."%";

    $stmt->bind_param("ss",$like,$like);
    $stmt->execute();

    $result = $stmt->get_result();
    $person = $result->fetch_assoc();
    }
    ?>

    <a href="../dashboard/dashboard.php">Home</a>

    <h2>Search Personal Record</h2>

    <form method="GET">
    <input type="text" name="search" placeholder="Enter name..." value="<?php echo $search; ?>">
    <button type="submit">Search</button>
    </form>

    <hr>

    <?php if($person){ ?>

    <form method="POST">

    <input type="hidden" name="id" value="<?php echo $person['id']; ?>">

    <h2>Personal Information</h2>

    Surname
    <input name="surname" value="<?php echo $person['surname']; ?>">

    First Name
    <input name="firstname" value="<?php echo $person['firstname']; ?>">

    Middle Name
    <input name="middlename" value="<?php echo $person['middlename']; ?>">

    Name Extension
    <input name="extension" value="<?php echo $person['extension']; ?>">

    Date of Birth
    <input type="date" name="dob" value="<?php echo $person['dob']; ?>">

    Place of Birth
    <input name="birth_place" value="<?php echo $person['birth_place']; ?>">

    Sex
    <select name="sex">
    <option <?php if($person['sex']=="Male") echo "selected"; ?>>Male</option>
    <option <?php if($person['sex']=="Female") echo "selected"; ?>>Female</option>
    </select>

    Civil Status
    <select name="civil_status">
    <option <?php if($person['civil_status']=="Single") echo "selected"; ?>>Single</option>
    <option <?php if($person['civil_status']=="Married") echo "selected"; ?>>Married</option>
    <option <?php if($person['civil_status']=="Widowed") echo "selected"; ?>>Widowed</option>
    <option <?php if($person['civil_status']=="Separated") echo "selected"; ?>>Separated</option>
    </select>

    Height
    <input name="height" value="<?php echo $person['height']; ?>">

    Weight
    <input name="weight" value="<?php echo $person['weight']; ?>">

    Blood Type
    <input name="blood_type" value="<?php echo $person['blood_type']; ?>">

    UMID ID
    <input name="umid" value="<?php echo $person['umid']; ?>">

    Pag-IBIG
    <input name="pagibig" value="<?php echo $person['pagibig']; ?>">

    PhilHealth
    <input name="philhealth" value="<?php echo $person['philhealth']; ?>">

    PhilSys
    <input name="philsys" value="<?php echo $person['philsys']; ?>">

    TIN
    <input name="tin" value="<?php echo $person['tin']; ?>">

    Agency Employee
    <input name="agency_employee" value="<?php echo $person['agency_employee']; ?>">

    <h3>Citizenship</h3>

    <select name="citizenship">
    <option <?php if($person['citizenship']=="Filipino") echo "selected"; ?>>Filipino</option>
    <option <?php if($person['citizenship']=="Dual Citizen") echo "selected"; ?>>Dual Citizen</option>
    </select>

    If Dual Citizen
    <input name="dual_country" value="<?php echo $person['dual_country']; ?>">

    <h3>Residential Address</h3>

    <input name="r_house" value="<?php echo $person['r_house']; ?>">
    <input name="r_street" value="<?php echo $person['r_street']; ?>">
    <input name="r_subdivision" value="<?php echo $person['r_subdivision']; ?>">
    <input name="r_barangay" value="<?php echo $person['r_barangay']; ?>">
    <input name="r_city" value="<?php echo $person['r_city']; ?>">
    <input name="r_province" value="<?php echo $person['r_province']; ?>">
    <input name="r_zip" value="<?php echo $person['r_zip']; ?>">

    <h3>Permanent Address</h3>

    <input name="p_house" value="<?php echo $person['p_house']; ?>">
    <input name="p_street" value="<?php echo $person['p_street']; ?>">
    <input name="p_subdivision" value="<?php echo $person['p_subdivision']; ?>">
    <input name="p_barangay" value="<?php echo $person['p_barangay']; ?>">
    <input name="p_city" value="<?php echo $person['p_city']; ?>">
    <input name="p_province" value="<?php echo $person['p_province']; ?>">
    <input name="p_zip" value="<?php echo $person['p_zip']; ?>">

    <h3>Contact Information</h3>

    Telephone
    <input name="telephone" value="<?php echo $person['telephone']; ?>">

    Mobile
    <input name="mobile" value="<?php echo $person['mobile']; ?>">

    Email
    <input name="email" value="<?php echo $person['email']; ?>">

    <br><br>

    <button type="submit" name="update">Update</button>

    <a href="view.php?delete=<?php echo $person['id']; ?>" onclick="return confirm('Delete this record?')">
    Delete
    </a>

</form>

<?php } ?>

<?php if(isset($_GET['search']) && !$person){ ?>

<p>No record found.</p>

<?php } ?>