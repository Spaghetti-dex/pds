<?php

include "../config/database.php";

$id=$_GET['id'];

$conn->query("UPDATE personal_info SET archived=1 WHERE id=$id");

header("Location:view.php");
