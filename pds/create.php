<?php //include "../includes/auth_check.php"; ?>
<?php include "../includes/header.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PCGG Employee Form</title>

<style>
body{
  font-family: Arial, sans-serif;
  margin:0;
  background:#e6e6e6;
}

/* HEADER */
.header{
  background:#2f402c;
  color:white;
  padding:12px 20px;
  font-size:14px;
}

/* LAYOUT */
.container{
  display:flex;
  min-height:calc(100vh - 50px);
}

/* SIDEBAR */
.sidebar{
  width:270px;
  padding:40px 20px;
  position:relative;
  display:flex;
  flex-direction:column;
  gap:35px;
  overflow-y:auto;
  max-height:100vh;
  box-sizing:border-box;
}

/* TIMELINE LINE */
.sidebar::before{
  content:"";
  position:absolute;
  left:57px;
  top:65px;
  height:calc(100% - 140px);
  width:3px;
  background:#ccc;
  z-index:1;
}

/* PROGRESS LINE */
.progress-line{
  position:absolute;
  left:57px;
  top:65px;
  width:3px;
  height:0;
  background:#d6c86f;
  transition:0.4s;
  z-index:2;
}

/* NAV ITEM */
.nav-item{
  display:flex;
  align-items:center;
  gap:15px;
  cursor:pointer;
  padding:10px 15px;
  border-radius:10px;
  transition:0.2s;
  z-index:3;
  position:relative;
  background:transparent;
}

.nav-item:hover{
  background:#f4f0d3;
}

/* SIDEBAR ICON */
.nav-icon{
  width:45px;
  height:45px;
  object-fit:cover;
  position:relative;
  z-index:3;
}

/* COMPLETED STEP CHECKMARK */
.nav-item.completed::after{
  content:"✓";
  position:absolute;
  left:28px;
  top:50%;
  transform:translateY(-50%);
  color:green;
  font-size:18px;
  font-weight:bold;
  z-index:4;
  background:rgba(255,255,255,0.85);
  border-radius:50%;
  width:20px;
  height:20px;
  display:flex;
  align-items:center;
  justify-content:center;
}

/* TEXT */
.nav-label{
  font-size:13px;
  font-weight:bold;
}

/* ACTIVE SIDEBAR */
.nav-item.active{
  background:#efe5b6;
  border:2px solid #a5a079;
}

/* FORM AREA */
.form-area{
  flex:1;
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:20px;
  overflow-y:auto;
  box-sizing:border-box;
}

/* FORM CARD */
.card{
  width:1150px;
  min-height:700px;
  max-width:100%;
  background:#c7d1c3;
  padding:20px 40px 30px;
  border-radius:15px;
  border:3px solid black;
  box-sizing:border-box;
}

/* TITLE */
.title{
  text-align:center;
  font-size:22px;
  font-weight:800;
  margin-bottom:25px;
  margin-top:5px;
}

/* PERSONAL INFO GRID */
.personal-grid{
  display:grid;
  grid-template-columns:160px 1fr 160px 1fr;
  gap:18px 22px;
  align-items:center;
  width:100%;
}

.personal-grid label,
.personal-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.personal-grid input,
.personal-grid select,
.personal-row input,
.personal-row select{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* make only dual_country wider */
input[name="dual_country"]{
  min-width:280px;
}

.personal-row{
  display:grid;
  grid-template-columns:auto 140px auto 140px auto 140px;
  justify-content:center;
  align-items:center;
  gap:15px 20px;
  margin-top:18px;
}

.personal-row.small input,
.personal-row.small select{
  width:140px;
}

.grid{
  display:grid;
  grid-template-columns:180px 1fr 180px 1fr;
  gap:12px 18px;
  align-items:center;
  width:100%;
}

label{
  font-size:13px;
  font-weight:bold;
}

input, select{
  width:100%;
  padding:8px;
  border:1px solid #666;
  border-radius:4px;
  box-sizing:border-box;
}

button{
  margin-top:20px;
  padding:8px 20px;
  cursor:pointer;
}

.section{
  display:none;
  opacity:0;
  transform:translateY(20px);
  transition:0.3s;
}

.section.active{
  display:block;
  opacity:1;
  transform:translateY(0);
}

.nav-buttons{
  display:flex;
  justify-content:space-between;
  gap:10px;
  margin-top:20px;
}

.entry-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.entry-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.section-subtitle{
  font-size:17px;
  font-weight:bold;
  margin:20px 0 10px;
}

.top-actions{
  margin-bottom:20px;
}

.top-actions a{
  display:inline-block;
  text-decoration:none;
  background:#2f402c;
  color:#fff;
  padding:8px 15px;
  border-radius:6px;
}

.save-btn{
  display:block;
  margin-left:auto;
  margin-right:auto;
  margin-top:20px;
}

@media (max-width: 900px){
  .container{
    flex-direction:column;
  }

  .sidebar{
    width:100%;
    max-height:none;
    gap:15px;
  }

  .sidebar::before,
  .progress-line{
    display:none;
  }

  .grid,
  .personal-grid,
  .entry-grid{
    grid-template-columns:1fr;
  }

  .personal-row{
    grid-template-columns:1fr;
    align-items:stretch;
  }

  .personal-grid input,
  .personal-grid select,
  .personal-row input,
  .personal-row select{
    width:100%;
  }

  .personal-grid label,
  .personal-row label,
  .grid label{
    text-align:left;
  }

  input[name="dual_country"]{
    min-width:100%;
  }
}
</style>
</head>
<body>

<div class="header">
  REPUBLIC OF THE PHILIPPINES
  <br>
  PRESIDENTIAL COMMISSION ON GOOD GOVERNANCE
  <br>
  The People's Commission
</div>

<div class="container">

  <div class="sidebar">
    <div class="progress-line" id="progressLine"></div>

    <div class="nav-item active" onclick="goToSection(0)">
      <img src="../assets/profile.png" alt="Profile" class="nav-icon">
      <div class="nav-label">PERSONAL INFORMATION</div>
    </div>

    <div class="nav-item" onclick="goToSection(1)">
      <img src="../assets/address.png" alt="Address" class="nav-icon">
      <div class="nav-label">ADDRESS</div>
    </div>

    <div class="nav-item" onclick="goToSection(2)">
      <img src="../assets/contact.png" alt="Contact" class="nav-icon">
      <div class="nav-label">CONTACT INFORMATION</div>
    </div>

    <div class="nav-item" onclick="goToSection(3)">
      <img src="../assets/education.png" alt="Education" class="nav-icon">
      <div class="nav-label">EDUCATIONAL BACKGROUND</div>
    </div>

    <div class="nav-item" onclick="goToSection(4)">
      <img src="../assets/service.png" alt="Service" class="nav-icon">
      <div class="nav-label">SERVICE ELIGIBILITY</div>
    </div>

    <div class="nav-item" onclick="goToSection(5)">
      <img src="../assets/learning.png" alt="Training" class="nav-icon">
      <div class="nav-label">LEARNING AND DEVELOPMENT</div>
    </div>
  </div>

  <div class="form-area">
    <div class="card">

      <div class="top-actions">
        <a href="../dashboard/dashboard.php" class="button">Home</a>
      </div>

      <form method="POST" action="save.php">

        <!-- PERSONAL INFORMATION -->
        <div id="personal" class="section active">
          <div class="title">PERSONAL INFORMATION</div>

          <div class="personal-grid">
            <label>Last Name:</label>
            <input name="surname">

            <label>Name Extension:</label>
            <input name="extension">

            <label>First Name:</label>
            <input name="firstname">

            <label>Date of Birth:</label>
            <input type="date" name="dob">

            <label>Middle Name:</label>
            <input name="middlename">

            <label>Place of Birth:</label>
            <input name="birth_place">
          </div>

          <div class="personal-row small">
            <label>Civil Status:</label>
            <select name="civil_status">
              <option value=""></option>
              <option>Single</option>
              <option>Married</option>
              <option>Widowed</option>
              <option>Separated</option>
            </select>

            <label>Sex:</label>
            <select name="sex">
              <option value=""></option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>

            <label>Blood Type:</label>
            <input name="blood_type">
          </div>

          <div class="personal-row small">
            <label>Height:</label>
            <input name="height">

            <label>Weight:</label>
            <input name="weight">
          </div>

          <div class="personal-grid" style="margin-top:25px;">
            <label>UMID ID:</label>
            <input name="umid">

            <label>PhilSys No. (PSN):</label>
            <input name="philsys">

            <label>Pag-IBIG ID No.:</label>
            <input name="pagibig">

            <label>TIN No.:</label>
            <input name="tin">

            <label>PhilHealth No.:</label>
            <input name="philhealth">

            <label>Agency Employee No.:</label>
            <input name="agency_employee">

            <label>Citizenship:</label>
            <select name="citizenship">
              <option value=""></option>
              <option>Filipino</option>
              <option>Dual Citizen</option>
            </select>

            <label>If Dual Citizen(Indicate Country):</label>
            <input name="dual_country">
          </div>
        </div>

        <!-- ADDRESS -->
        <div id="address" class="section">
          <div class="title">ADDRESS</div>

          <div class="section-subtitle">Residential Address</div>
          <div class="grid">
            <label>House/Block/Lot No.:</label>
            <input name="r_house" placeholder="House/Block/Lot No.">

            <label>Street:</label>
            <input name="r_street" placeholder="Street">

            <label>Subdivision/Village:</label>
            <input name="r_subdivision" placeholder="Subdivision/Village">

            <label>Barangay:</label>
            <input name="r_barangay" placeholder="Barangay">

            <label>City/Municipality:</label>
            <input name="r_city" placeholder="City/Municipality">

            <label>Province:</label>
            <input name="r_province" placeholder="Province">

            <label>Zip Code:</label>
            <input name="r_zip" placeholder="Zip Code">
          </div>

          <div class="section-subtitle">Permanent Address</div>
          <div class="grid">
            <label>House/Block/Lot No.:</label>
            <input name="p_house" placeholder="House/Block/Lot No.">

            <label>Street:</label>
            <input name="p_street" placeholder="Street">

            <label>Subdivision/Village:</label>
            <input name="p_subdivision" placeholder="Subdivision/Village">

            <label>Barangay:</label>
            <input name="p_barangay" placeholder="Barangay">

            <label>City/Municipality:</label>
            <input name="p_city" placeholder="City/Municipality">

            <label>Province:</label>
            <input name="p_province" placeholder="Province">

            <label>Zip Code:</label>
            <input name="p_zip" placeholder="Zip Code">
          </div>
        </div>

        <!-- CONTACT -->
        <div id="contact" class="section">
          <div class="title">CONTACT INFORMATION</div>

          <div class="grid">
            <label>Telephone No.:</label>
            <input name="telephone">

            <label>Mobile No.:</label>
            <input name="mobile">

            <label>Email:</label>
            <input type="email" name="email">
          </div>
        </div>

        <!-- EDUCATION -->
        <div id="education" class="section">
          <div class="title">EDUCATIONAL BACKGROUND</div>

          <div id="education-container">
            <div class="education-entry entry-box">
              <div class="entry-grid">
                <div>
                  <label>Level</label>
                  <select name="education_level[]">
                    <option>Elementary</option>
                    <option>Secondary</option>
                    <option>Vocational / Trade Course</option>
                    <option>College</option>
                    <option>Graduate Studies</option>
                  </select>
                </div>

                <div>
                  <label>Name of School</label>
                  <input name="school_name[]" placeholder="School Name">
                </div>

                <div>
                  <label>Basic Education / Degree / Course</label>
                  <input name="course[]" placeholder="Course / Degree">
                </div>

                <div>
                  <label>Highest Level / Units Earned</label>
                  <input name="units[]" placeholder="Highest Level / Units">
                </div>

                <div>
                  <label>Period of Attendance From</label>
                  <input type="date" name="edu_from[]">
                </div>

                <div>
                  <label>To</label>
                  <input type="date" name="edu_to[]">
                </div>

                <div>
                  <label>Year Graduated</label>
                  <input name="year_graduated[]" placeholder="Year Graduated">
                </div>

                <div>
                  <label>Scholarship / Academic Honors</label>
                  <input name="honors[]" placeholder="Scholarship / Honors">
                </div>
              </div>

              <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" onclick="addEducation()">Add More</button>
        </div>

        <!-- ELIGIBILITY -->
        <div id="eligibility-section" class="section">
          <div class="title">SERVICE ELIGIBILITY</div>

          <div id="eligibility">
            <div class="eligibility-entry entry-box">
              <div class="entry-grid">
                <div>
                  <label>Career Service / CSC / CES</label>
                  <input name="career_service[]" placeholder="Career Service / CSC / CES">
                </div>

                <div>
                  <label>Rating</label>
                  <input name="rating[]" placeholder="Rating">
                </div>

                <div>
                  <label>Exam Date</label>
                  <input type="date" name="exam_date[]">
                </div>

                <div>
                  <label>Place of Examination</label>
                  <input name="exam_place[]" placeholder="Place of Examination">
                </div>

                <div>
                  <label>License</label>
                  <input name="license[]" placeholder="License">
                </div>

                <div>
                  <label>License Number</label>
                  <input name="license_number[]" placeholder="License Number">
                </div>

                <div>
                  <label>Valid Until</label>
                  <input type="date" name="valid_until[]">
                </div>
              </div>

              <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" onclick="addEligibility()">Add More</button>
        </div>

        <!-- TRAINING -->
        <div id="training-section" class="section">
          <div class="title">LEARNING AND DEVELOPMENT</div>

          <div id="training">
            <div class="training-entry entry-box">
              <div class="entry-grid">
                <div>
                  <label>Training Title</label>
                  <input name="title[]" placeholder="Training Title">
                </div>

                <div>
                  <label>Hours</label>
                  <input name="hours[]" placeholder="Hours">
                </div>

                <div>
                  <label>From</label>
                  <input type="date" name="training_from[]">
                </div>

                <div>
                  <label>To</label>
                  <input type="date" name="training_to[]">
                </div>

                <div>
                  <label>Type</label>
                  <input name="type[]" placeholder="Managerial / Technical">
                </div>

                <div>
                  <label>Sponsor</label>
                  <input name="sponsor[]" placeholder="Sponsor">
                </div>
              </div>

              <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" onclick="addTraining()">Add More</button>
        </div>

        <div class="nav-buttons">
          <button type="button" onclick="prevSection()">Previous</button>
          <button type="button" onclick="nextSection()">Next</button>
        </div>

        <button type="submit" class="save-btn">Save</button>
      </form>

    </div>
  </div>
</div>

<script>
let currentSection = 0;
const sections = document.querySelectorAll(".section");
const navItems = document.querySelectorAll(".nav-item");

function updateProgress(index){
  sections.forEach((sec, i) => {
    sec.classList.toggle("active", i === index);
  });

  navItems.forEach((nav, i) => {
    nav.classList.remove("active", "completed");
    if(i < index){
      nav.classList.add("completed");
    }
  });

  navItems[index].classList.add("active");
  document.getElementById("progressLine").style.height = (index * 80) + "px";
  currentSection = index;
}

function goToSection(index){
  updateProgress(index);
}

function nextSection(){
  if(currentSection < sections.length - 1){
    updateProgress(currentSection + 1);
  }
}

function prevSection(){
  if(currentSection > 0){
    updateProgress(currentSection - 1);
  }
}

function addEducation() {
  const container = document.getElementById("education-container");
  const div = document.createElement("div");
  div.classList.add("education-entry", "entry-box");
  div.innerHTML = `
    <div class="entry-grid">
      <div>
        <label>Level</label>
        <select name="education_level[]">
          <option>Elementary</option>
          <option>Secondary</option>
          <option>Vocational / Trade Course</option>
          <option>College</option>
          <option>Graduate Studies</option>
        </select>
      </div>

      <div>
        <label>Name of School</label>
        <input name="school_name[]" placeholder="School Name">
      </div>

      <div>
        <label>Basic Education / Degree / Course</label>
        <input name="course[]" placeholder="Course / Degree">
      </div>

      <div>
        <label>Highest Level / Units Earned</label>
        <input name="units[]" placeholder="Highest Level / Units">
      </div>

      <div>
        <label>Period of Attendance From</label>
        <input type="date" name="edu_from[]">
      </div>

      <div>
        <label>To</label>
        <input type="date" name="edu_to[]">
      </div>

      <div>
        <label>Year Graduated</label>
        <input name="year_graduated[]" placeholder="Year Graduated">
      </div>

      <div>
        <label>Scholarship / Academic Honors</label>
        <input name="honors[]" placeholder="Scholarship / Honors">
      </div>
    </div>
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}

function addEligibility() {
  const container = document.getElementById("eligibility");
  const div = document.createElement("div");
  div.classList.add("eligibility-entry", "entry-box");
  div.innerHTML = `
    <div class="entry-grid">
      <div>
        <label>Career Service / CSC / CES</label>
        <input name="career_service[]" placeholder="Career Service / CSC / CES">
      </div>

      <div>
        <label>Rating</label>
        <input name="rating[]" placeholder="Rating">
      </div>

      <div>
        <label>Exam Date</label>
        <input type="date" name="exam_date[]">
      </div>

      <div>
        <label>Place of Examination</label>
        <input name="exam_place[]" placeholder="Place of Examination">
      </div>

      <div>
        <label>License</label>
        <input name="license[]" placeholder="License">
      </div>

      <div>
        <label>License Number</label>
        <input name="license_number[]" placeholder="License Number">
      </div>

      <div>
        <label>Valid Until</label>
        <input type="date" name="valid_until[]">
      </div>
    </div>
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}

function addTraining() {
  const container = document.getElementById("training");
  const div = document.createElement("div");
  div.classList.add("training-entry", "entry-box");
  div.innerHTML = `
    <div class="entry-grid">
      <div>
        <label>Training Title</label>
        <input name="title[]" placeholder="Training Title">
      </div>

      <div>
        <label>Hours</label>
        <input name="hours[]" placeholder="Hours">
      </div>

      <div>
        <label>From</label>
        <input type="date" name="training_from[]">
      </div>

      <div>
        <label>To</label>
        <input type="date" name="training_to[]">
      </div>

      <div>
        <label>Type</label>
        <input name="type[]" placeholder="Managerial / Technical">
      </div>

      <div>
        <label>Sponsor</label>
        <input name="sponsor[]" placeholder="Sponsor">
      </div>
    </div>
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}
</script>

</body>
</html>