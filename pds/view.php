<?php include "../includes/auth_check.php"; ?>
<?php include "../config/database.php"; ?>

<form>

<input name="search">
<button>Search</button>

</form>

<?php

$search=$_GET['search'] ?? "";

$stmt=$conn->prepare("
SELECT * FROM personal_info
WHERE archived=0
AND surname LIKE CONCAT('%',?,'%')
");

$stmt->bind_param("s",$search);
$stmt->execute();

$result=$stmt->get_result();

while($row=$result->fetch_assoc()){

echo $row['surname']." ".$row['first_name'];

echo " <a href='edit.php?id=".$row['id']."'>Edit</a>";

echo " <a href='archive.php?id=".$row['id']."'>Archive</a>";

echo "<br>";

}