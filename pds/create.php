<?php include "../includes/auth_check.php"; ?>

<form method="POST" action="save.php">

Surname
<input name="surname" required>

First Name
<input name="firstname" required>

Mobile
<input name="mobile">

Email
<input name="email">

<h3>Residential Address</h3>

<input name="r_house" placeholder="House">
<input name="r_street" placeholder="Street">
<input name="r_city" placeholder="City">

<h3>Permanent Address</h3>

<input name="p_house" placeholder="House">
<input name="p_street" placeholder="Street">
<input name="p_city" placeholder="City">

<h3>Service Eligibility</h3>

<input name="career_service[]">
<input name="rating[]">

<button type="button" onclick="addEligibility()">Add More</button>

<div id="eligibility"></div>

<h3>Learning Development</h3>

<input name="title[]">
<input name="hours[]">

<button type="button" onclick="addTraining()">Add More</button>

<div id="training"></div>

<button type="submit">Save</button>

</form>

<script>

function addEligibility(){

document.getElementById("eligibility").innerHTML +=
'<input name="career_service[]"><input name="rating[]">';

}

function addTraining(){

document.getElementById("training").innerHTML +=
'<input name="title[]"><input name="hours[]">';

}

</script>