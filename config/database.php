<?php 

$conn = new mysqli ("localhost","root","","pds_system");

if ($conn->connect_error){
    die("connection failed")
}
?>