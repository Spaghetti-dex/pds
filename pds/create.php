<?php include "../includes/auth_check.php"; ?>

<form method="POST" action="save.php">

<h2>Personal Information</h2>

Surname
<input name="surname" required>

First Name
<input name="firstname" required>

Middle Name
<input name="middlename">

Name Extension
<input name="extension">

Date of Birth
<input type="date" name="dob">

Place of Birth
<input name="birth_place">

Sex
<select name="sex">
  <option value="Male">Male</option>
  <option value="Female">Female</option>
</select>

Civil Status
<select name="civil_status">
  <option>Single</option>
  <option>Married</option>
  <option>Widowed</option>
  <option>Separated</option>
</select>

Height (m)
<input name="height">

Weight (kg)
<input name="weight">

Blood Type
<input name="blood_type">

UMID ID
<input name="umid">

Pag-IBIG ID No.
<input name="pagibig">

PhilHealth No.
<input name="philhealth">

PhilSys Number (PSN)
<input name="philsys">

TIN No.
<input name="tin">

Agency Employee No.
<input name="agency_employee">

<h3>Citizenship</h3>

<select name="citizenship">
  <option>Filipino</option>
  <option>Dual Citizen</option>
</select>

If Dual Citizen (Indicate Country)
<input name="dual_country">

<h3>Residential Address</h3>

<input name="r_house" placeholder="House/Block/Lot No.">
<input name="r_street" placeholder="Street">
<input name="r_subdivision" placeholder="Subdivision/Village">
<input name="r_barangay" placeholder="Barangay">
<input name="r_city" placeholder="City/Municipality">
<input name="r_province" placeholder="Province">
<input name="r_zip" placeholder="Zip Code">

<h3>Permanent Address</h3>

<input name="p_house" placeholder="House/Block/Lot No.">
<input name="p_street" placeholder="Street">
<input name="p_subdivision" placeholder="Subdivision/Village">
<input name="p_barangay" placeholder="Barangay">
<input name="p_city" placeholder="City/Municipality">
<input name="p_province" placeholder="Province">
<input name="p_zip" placeholder="Zip Code">

<h3>Contact Information</h3>

Telephone No.
<input name="telephone">

Mobile No.
<input name="mobile">

Email
<input name="email">

<!-- EDUCATIONAL BACKGROUND -->
<h3>Educational Background</h3>
<div id="education-container">
  <div class="education-entry">
    Level
    <select name="education_level[]">
      <option>Elementary</option>
      <option>Secondary</option>
      <option>Vocational / Trade Course</option>
      <option>College</option>
      <option>Graduate Studies</option>
    </select>

    Name of School
    <input name="school_name[]" placeholder="School Name">

    Basic Education / Degree / Course
    <input name="course[]" placeholder="Course / Degree">

    Period of Attendance
    From <input type="date" name="edu_from[]">
    To <input type="date" name="edu_to[]">

    Highest Level / Units Earned
    <input name="units[]" placeholder="Highest Level / Units">

    Year Graduated
    <input name="year_graduated[]" placeholder="Year Graduated">

    Scholarship / Academic Honors
    <input name="honors[]" placeholder="Scholarship / Honors">

    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  </div>
</div>
<button type="button" onclick="addEducation()">Add More</button>

<!-- SERVICE ELIGIBILITY -->
<h3>Service Eligibility</h3>
<div id="eligibility">
  <div class="eligibility-entry">
    <input name="career_service[]" placeholder="Career Service / CSC / CES">
    <input name="rating[]" placeholder="Rating">
    <input name="exam_date[]" placeholder="Exam Date">
    <input name="exam_place[]" placeholder="Place of Examination">
    <input name="license[]" placeholder="License">
    <input name="license_number[]" placeholder="License Number">
    <input name="valid_until[]" placeholder="Valid Until">
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  </div>
</div>
<button type="button" onclick="addEligibility()">Add More</button>

<!-- LEARNING & DEVELOPMENT -->
<h3>Learning and Development</h3>
<div id="training">
  <div class="training-entry">
    <input name="title[]" placeholder="Training Title">
    <input name="training_from[]" placeholder="From">
    <input name="training_to[]" placeholder="To">
    <input name="hours[]" placeholder="Hours">
    <input name="type[]" placeholder="Managerial / Technical">
    <input name="sponsor[]" placeholder="Sponsor">
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  </div>
</div>
<button type="button" onclick="addTraining()">Add More</button>

<br><br>
<button type="submit">Save</button>
</form>

<button></button>

<script>
function addEducation() {
  const container = document.getElementById("education-container");
  const div = document.createElement("div");
  div.classList.add("education-entry");
  div.innerHTML = `
    <br>
    Level
    <select name="education_level[]">
      <option>Elementary</option>
      <option>Secondary</option>
      <option>Vocational / Trade Course</option>
      <option>College</option>
      <option>Graduate Studies</option>
    </select>

    Name of School
    <input name="school_name[]" placeholder="School Name">

    Basic Education / Degree / Course
    <input name="course[]" placeholder="Course / Degree">

    Period of Attendance
    From <input type="date" name="edu_from[]">
    To <input type="date" name="edu_to[]">

    Highest Level / Units Earned
    <input name="units[]" placeholder="Highest Level / Units">

    Year Graduated
    <input name="year_graduated[]" placeholder="Year Graduated">

    Scholarship / Academic Honors
    <input name="honors[]" placeholder="Scholarship / Honors">

    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}

function addEligibility() {
  const container = document.getElementById("eligibility");
  const div = document.createElement("div");
  div.classList.add("eligibility-entry");
  div.innerHTML = `
    <br>
    <input name="career_service[]" placeholder="Career Service / CSC / CES">
    <input name="rating[]" placeholder="Rating">
    <input name="exam_date[]" placeholder="Exam Date">
    <input name="exam_place[]" placeholder="Place of Examination">
    <input name="license[]" placeholder="License">
    <input name="license_number[]" placeholder="License Number">
    <input name="valid_until[]" placeholder="Valid Until">
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}

function addTraining() {
  const container = document.getElementById("training");
  const div = document.createElement("div");
  div.classList.add("training-entry");
  div.innerHTML = `
    <br>
    <input name="title[]" placeholder="Training Title">
    <input name="training_from[]" placeholder="From">
    <input name="training_to[]" placeholder="To">
    <input name="hours[]" placeholder="Hours">
    <input name="type[]" placeholder="Managerial / Technical">
    <input name="sponsor[]" placeholder="Sponsor">
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}
</script>