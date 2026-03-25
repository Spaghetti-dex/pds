<?php 
require_once "../includes/auth_check.php";
include "../includes/header.php"; ?>

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
  height:0;
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

/* =========================
   PERSONAL INFORMATION
========================= */
.personal-grid{
  display:grid;
  grid-template-columns:160px 1fr 160px 1fr;
  gap:18px 5px;
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

.personal-row{
  display:grid;
  grid-template-columns:1fr auto 140px auto 140px auto 140px;
  justify-content:center;
  align-items:center;
  gap:15px 5px;
  margin-top:20px;
}

.personal-row.small input,
.personal-row.small select{
  width:140px;
}

.citizenship-row{
  display:grid;
  grid-template-columns:160px 285px 240px 1fr;
  gap:18px 5px;
  align-items:center;
  margin-top:25px;
  width:100%;
}

.citizenship-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.citizenship-row select,
.citizenship-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* =========================
   ADDRESS
========================= */
.address-section{
  padding-top:10px;
}

.address-title{
  text-align:center;
  font-size:25px;
  font-weight:800;
  margin:0 0 28px 0;
}

.address-block{
  width:100%;
  margin:0 0 28px 0;
}

.address-house-row{
  display:grid;
  grid-template-columns:160px 1fr;
  align-items:center;
  gap:18px 5px;
  margin-bottom:18px;
  width:100%;
}

.address-two-col{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:18px 30px;
  width:100%;
}

.address-col{
  display:flex;
  flex-direction:column;
  gap:18px;
}

.address-row{
  display:grid;
  grid-template-columns:160px 1fr;
  align-items:center;
  gap:18px 5px;
  width:100%;
}

.address-house-row label,
.address-row label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.address-house-row input,
.address-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* =========================
   CONTACT
========================= */
.contact-section{
  padding-top:110px;
}

.contact-title{
  text-align:center;
  font-size:28px;
  font-weight:800;
  margin:0 0 48px 0;
}

.contact-grid{
  width:390px;
  margin:0 auto;
  display:flex;
  flex-direction:column;
  gap:28px;
}

.contact-row{
  display:grid;
  grid-template-columns:145px 1fr;
  align-items:center;
  column-gap:10px;
}

.contact-row label{
  font-size:14px;
  font-weight:700;
  text-align:right;
  white-space:nowrap;
}

.contact-row input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* =========================
   EDUCATION
========================= */
.education-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.education-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.education-grid label{
  font-size:13px;
  font-weight:bold;
}

.education-grid input,
.education-grid select{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px; 
  box-sizing:border-box;
}

/* =========================
   ELIGIBILITY
========================= */
.eligibility-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.eligibility-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.eligibility-grid label{
  font-size:13px;
  font-weight:bold;
}

.eligibility-grid input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* =========================
   TRAINING
========================= */
.training-box{
  background:#d7dfd3;
  border:1px solid #777;
  border-radius:8px;
  padding:15px;
  margin-bottom:15px;
}

.training-grid{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:10px 15px;
}

.training-grid label{
  font-size:13px;
  font-weight:bold;
}

.training-grid input{
  width:100%;
  height:36px;
  padding:6px 10px;
  border:1px solid #555;
  border-radius:6px;
  background:#e9e9ee;
  font-size:14px;
  box-sizing:border-box;
}

/* =========================
   GENERAL
========================= */
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
  justify-content:flex-end;
  gap:10px;
  margin-top:20px;
}

.next-btn,
.save-btn,
.add-btn{
  background:#2f402c;
  color:#fff;
  border:none;
  padding:10px 22px;
  border-radius:6px;
  font-size:14px;
  font-weight:600;
  cursor:pointer;
  transition:0.2s;
  margin-top:0;
}

.next-btn:hover,
.save-btn:hover,
.add-btn:hover{
  background:#3b5237;
}

.remove-btn{
  background:#8b2c2c;
  color:#fff;
  border:none;
  padding:8px 16px;
  border-radius:6px;
  font-size:13px;
  font-weight:600;
  cursor:pointer;
  transition:0.2s;
  margin-top:10px;
}

.remove-btn:hover{
  background:#a63a3a;
}

.add-btn{
  margin-top:10px;
}

.section-subtitle{
  font-size:25px;
  font-weight:bold;
  margin:20px 0 10px;
  text-align:center;
  padding:25px;
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

  .personal-grid,
  .citizenship-row,
  .education-grid,
  .eligibility-grid,
  .training-grid{
    grid-template-columns:1fr;
  }

  .personal-row{
    grid-template-columns:1fr;
    align-items:stretch;
  }

  .contact-section{
    padding-top:40px;
  }

  .contact-grid{
    width:100%;
    max-width:390px;
  }

  .contact-row{
    grid-template-columns:1fr;
    row-gap:8px;
  }

  .address-house-row,
  .address-row{
    grid-template-columns:1fr;
    row-gap:8px;
  }

  .address-two-col{
    grid-template-columns:1fr;
    column-gap:0;
    row-gap:18px;
  }

  .personal-grid input,
  .personal-grid select,
  .personal-row input,
  .personal-row select,
  .citizenship-row input,
  .citizenship-row select,
  .address-house-row input,
  .address-row input,
  .education-grid input,
  .education-grid select,
  .eligibility-grid input,
  .training-grid input,
  .contact-row input{
    width:100%;
  }

  .personal-grid label,
  .personal-row label,
  .citizenship-row label,
  .contact-row label,
  .address-house-row label,
  .address-row label{
    text-align:left;
  }
.error-summary{
  display:none;
  background:#ffe3e3;
  color:#8b1e1e;
  border:1px solid #d99;
  padding:12px 15px;
  border-radius:8px;
  margin-bottom:15px;
  font-size:14px;
  font-weight:600;
}

.field-error{
  border:2px solid #c62828 !important;
  background:#fff3f3 !important;
}

.field-error-message{
  color:#c62828;
  font-size:12px;
  font-weight:600;
  margin-top:4px;
}

label.required::after {
  content: "*";
  color: #c62828;
  font-weight: bold;
}

label.required {
  color: #c62828;
}
.error-summary{
  display:none;
  background:#ffe3e3;
  color:#b71c1c;
  border:1px solid #e0a4a4;
  padding:12px 14px;
  border-radius:8px;
  margin-bottom:15px;
  font-weight:700;
}

.field-error{
  border:2px solid #c62828 !important;
  background:#fff4f4 !important;
}

.field-error-message{
  color:#c62828;
  font-size:12px;
  font-weight:700;
  margin-top:4px;
}

label.required{
  color:#c62828;
  font-weight:700;
}

label.required::after{
  content:" *";
  color:#c62828;
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

      <form method="POST" action="save.php" id="pdsForm" novalidate>

      <div id="errorSummary" class="error-summary" style="display:none;"></div>

        <!-- PERSONAL INFORMATION -->
        <div id="personal" class="section active">
          <div class="title">PERSONAL INFORMATION</div>

          <div class="personal-grid">
            <label class="required">Last Name: *</label>
            <input name="surname">

            <label>Name Extension:</label>
            <input name="extension">

            <label class="required">First Name: *</label>
            <input name="firstname">

            <label class="required">Date of Birth: *</label>
            <input type="date" name="dob">

            <label>Middle Name:</label>
            <input name="middlename">

            <label class="required">Place of Birth: *</label>
            <input name="birth_place"required>
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

            <label>PhilSys No.(PSN):</label>
            <input name="philsys">

            <label>Pag-IBIG ID No:</label>
            <input name="pagibig">

            <label>TIN No:</label>
            <input name="tin">

            <label>PhilHealth No:</label>
            <input name="philhealth">

            <label>Agency Employee No:</label>
            <input name="agency_employee">
          </div>

          <div class="citizenship-row">
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

        
        <div id="address" class="section">
          <div class="address-section">

            <div class="address-title">RESIDENTIAL ADDRESS</div>
            <div class="address-block">
              <div class="address-house-row">
                <label>House / Block / Lot No.</label>
                <input name="r_house">
              </div>

              <div class="address-two-col">
                <div class="address-col">
                  <div class="address-row">
                    <label>Street:</label>
                    <input name="r_street">
                  </div>

                  <div class="address-row">
                    <label>Subdivision / Village:</label>
                    <input name="r_subdivision">
                  </div>

                  <div class="address-row">
                    <label>City / Municipality:</label>
                    <input name="r_city">
                  </div>
                </div>

                <div class="address-col">
                  <div class="address-row">
                    <label>Barangay:</label>
                    <input name="r_barangay">
                  </div>

                  <div class="address-row">
                    <label>Province:</label>
                    <input name="r_province">
                  </div>

                  <div class="address-row">
                    <label>Zip Code:</label>
                    <input name="r_zip">
                  </div>
                </div>
              </div>
            </div>

            <div class="address-title" style="margin-top:18px;">PERMANENT ADDRESS</div>
            <div class="address-block">
              <div class="address-house-row">
                <label>House / Block / Lot No.</label>
                <input name="p_house">
              </div>

              <div class="address-two-col">
                <div class="address-col">
                  <div class="address-row">
                    <label>Street:</label>
                    <input name="p_street">
                  </div>

                  <div class="address-row">
                    <label>Subdivision / Village:</label>
                    <input name="p_subdivision">
                  </div>

                  <div class="address-row">
                    <label>City / Municipality:</label>
                    <input name="p_city">
                  </div>
                </div>

                <div class="address-col">
                  <div class="address-row">
                    <label>Barangay:</label>
                    <input name="p_barangay">
                  </div>

                  <div class="address-row">
                    <label>Province:</label>
                    <input name="p_province">
                  </div>

                  <div class="address-row">
                    <label>Zip Code:</label>
                    <input name="p_zip">
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        
        <div id="contact" class="section">
          <div class="contact-section">
            <div class="contact-title">CONTACT INFORMATION</div>

            <div class="contact-grid">
              <div class="contact-row">
                <label>Telephone Number:</label>
                <input name="telephone">
              </div>

              <div class="contact-row">
                <label>Mobile Number:</label>
                <input name="mobile">
              </div>

              <div class="contact-row">
                <label>E-Mail:</label>
                <input type="email" name="email">
              </div>
            </div>
          </div>
        </div>

        
        <div id="education" class="section">
          <div class="title">EDUCATIONAL BACKGROUND</div>

          <div id="education-container">
            <div class="education-entry education-box">
              <div class="education-grid">
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
                  <label>Basic Education /Degree /Course</label>
                  <input name="course[]" placeholder="Course / Degree">
                </div>

                <div>
                  <label>Highest Level /Units Earned</label>
                  <input name="units[]" placeholder="Highest Level / Units">
                </div>

                <div>
                  <label class="required">Period of Attendance From *</label>
                  <input type="date" name="edu_from[]"required>
                </div>

                <div>
                  <label class="required">To *</label>
                  <input type="date" name="edu_to[]"required>
                </div>

                <div>
                  <label class="required">Year Graduated *</label>
                  <input name="year_graduated[]" placeholder="Year Graduated"required>
                </div>

                <div>
                  <label>Scholarship /Academic Honors</label>
                  <input name="honors[]" placeholder="Scholarship / Honors">
                </div>
              </div>

              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEducation()">Add More</button>
        </div>

       
        <div id="eligibility-section" class="section">
          <div class="title">SERVICE ELIGIBILITY</div>

          <div id="eligibility">
            <div class="eligibility-entry eligibility-box">
              <div class="eligibility-grid">
                <div>
                  <label>Career Service /CSC /CES</label>
                  <input name="career_service[]" placeholder="Career Service / CSC / CES">
                </div>

                <div>
                  <label>Rating</label>
                  <input name="rating[]" placeholder="Rating">
                </div>

                <div>
                  <label class="required">Exam Date *</label>
                  <input type="date" name="exam_date[]"required>
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
                  <label class="required">Valid Until *</label>
                  <input type="date" name="valid_until[]">
                </div>
              </div>

              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEligibility()">Add More</button>
        </div>

        
        <div id="training-section" class="section">
          <div class="title">LEARNING AND DEVELOPMENT</div>

          <div id="training">
            <div class="training-entry training-box">
              <div class="training-grid">
                <div>
                  <label>Training Title</label>
                  <input name="title[]" placeholder="Training Title">
                </div>

                <div>
                  <label class="required">Hours *</label>
                  <input name="hours[]" placeholder="Hours"required>
                </div>

                <div>
                  <label class="required">From *</label>
                  <input type="date" name="training_from[]"required>
                </div>

                <div>
                  <label class="required">To *</label>
                  <input type="date" name="training_to[]"required>
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

              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addTraining()">Add More</button>
        </div>

        <div class="nav-buttons">
          <button type="button" class="next-btn" id="nextBtn" onclick="nextSection()">Next</button>
          <button type="submit" class="save-btn" id="saveBtn" style="display:none;">Save</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
        let currentSection = 0;
        const sections = document.querySelectorAll(".section");
        const navItems = document.querySelectorAll(".nav-item");
        const form = document.getElementById("createRecordForm");
        const errorSummary = document.getElementById("errorSummary");

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

          if (navItems[index]) {
            navItems[index].classList.add("active");
          }

          const stepHeight = navItems[0].offsetHeight + 35;
          document.getElementById("progressLine").style.height = (index * stepHeight) + "px";

          currentSection = index;

          const nextBtn = document.getElementById("nextBtn");
          const saveBtn = document.getElementById("saveBtn");

          if(index === sections.length - 1){
            nextBtn.style.display = "none";
            saveBtn.style.display = "inline-block";
          } else {
            nextBtn.style.display = "inline-block";
            saveBtn.style.display = "none";
          }
        }

        function goToSection(index){
          updateProgress(index);
        }

        function showSummary(message){
          errorSummary.style.display = "block";
          errorSummary.textContent = message;
        }

        function hideSummary(){
          errorSummary.style.display = "none";
          errorSummary.textContent = "";
        }

        function clearFieldErrors(){
          document.querySelectorAll(".field-error").forEach(el => {
            el.classList.remove("field-error");
          });

          document.querySelectorAll(".field-error-message").forEach(el => {
            el.remove();
          });
        }

        function addFieldError(input, message){
          input.classList.add("field-error");

          const wrapper = input.parentElement;
          if (!wrapper) return;

          const old = wrapper.querySelector(".field-error-message");
          if (old) old.remove();

          const msg = document.createElement("div");
          msg.className = "field-error-message";
          msg.textContent = message;
          wrapper.appendChild(msg);
        }

        function getSectionName(sectionId){
          const names = {
            personal: "Personal Information",
            address: "Address",
            contact: "Contact Information",
            education: "Educational Background",
            "eligibility-section": "Service Eligibility",
            "training-section": "Learning and Development"
          };
          return names[sectionId] || "this section";
        }

        function validateSection(section){
          let firstInvalid = null;
          const requiredFields = section.querySelectorAll("[required]");

          requiredFields.forEach(input => {
            const value = (input.value || "").trim();
            if(value === ""){
              addFieldError(input, "This field is required.");
              if(!firstInvalid){
                firstInvalid = input;
              }
            }
          });

          return firstInvalid;
        }

        function validateAllSections(){
          clearFieldErrors();
          hideSummary();

          for(let i = 0; i < sections.length; i++){
            const invalidField = validateSection(sections[i]);

            if(invalidField){
              updateProgress(i);
              showSummary("Please complete the required field(s) in " + getSectionName(sections[i].id) + ".");
              setTimeout(() => {
                invalidField.focus();
                invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
              }, 100);
              return false;
            }
          }

          return true;
        }

        function nextSection(){
          clearFieldErrors();
          hideSummary();

          const invalidField = validateSection(sections[currentSection]);
          if(invalidField){
            showSummary("You forgot a required field in " + getSectionName(sections[currentSection].id) + ".");
            invalidField.focus();
            invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
            saveFormDraft();
            return;
          }

          if(currentSection < sections.length - 1){
            updateProgress(currentSection + 1);
          }
        }

        /* -------------------------
          DRAFT SAVE / RESTORE
        ------------------------- */
        function getDraftKey() {
          return "personal_record_create_draft";
        }

        function collectRepeatedEntries(selector, fieldNames) {
          const entries = [];
          document.querySelectorAll(selector).forEach(entry => {
            const row = {};
            fieldNames.forEach(name => {
              const el = entry.querySelector(`[name="${name}[]"]`);
              row[name] = el ? el.value : '';
            });

            const hasValue = Object.values(row).some(v => String(v).trim() !== '');
            if (hasValue) {
              entries.push(row);
            }
          });
          return entries;
        }

        function saveFormDraft() {
          if (!form) return;

          const data = {
            simple: {},
            education: collectRepeatedEntries('.education-entry', [
              'education_level', 'school_name', 'course', 'units',
              'edu_from', 'edu_to', 'year_graduated', 'honors'
            ]),
            eligibility: collectRepeatedEntries('.eligibility-entry', [
              'career_service', 'rating', 'exam_date', 'exam_place',
              'license', 'license_number', 'valid_until'
            ]),
            training: collectRepeatedEntries('.training-entry', [
              'title', 'hours', 'training_from', 'training_to', 'type', 'sponsor'
            ])
          };

          const fields = form.querySelectorAll('input:not([type="hidden"]):not([name$="[]"]), select:not([name$="[]"]), textarea:not([name$="[]"])');
          fields.forEach(field => {
            if (!field.name) return;

            if (field.type === 'checkbox' || field.type === 'radio') {
              if (field.checked) {
                data.simple[field.name] = field.value;
              }
            } else {
              data.simple[field.name] = field.value;
            }
          });

          data.currentSection = currentSection;

          localStorage.setItem(getDraftKey(), JSON.stringify(data));
        }

        function restoreSimpleFields(data) {
          if (!data || !data.simple) return;

          Object.keys(data.simple).forEach(name => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field) {
              field.value = data.simple[name];
            }
          });
        }

        function clearContainer(selector) {
          const container = document.querySelector(selector);
          if (container) {
            container.innerHTML = '';
          }
        }

        function restoreDraft() {
          if (!form) return;

          const raw = localStorage.getItem(getDraftKey());
          if (!raw) return;

          let data = null;
          try {
            data = JSON.parse(raw);
          } catch (e) {
            return;
          }

          restoreSimpleFields(data);

          if (document.getElementById('education-container') && Array.isArray(data.education)) {
            clearContainer('#education-container');
            if (data.education.length > 0) {
              data.education.forEach(row => addEducation(row));
            } else {
              addEducation();
            }
          }

          if (document.getElementById('eligibility') && Array.isArray(data.eligibility)) {
            clearContainer('#eligibility');
            if (data.eligibility.length > 0) {
              data.eligibility.forEach(row => addEligibility(row));
            } else {
              addEligibility();
            }
          }

          if (document.getElementById('training') && Array.isArray(data.training)) {
            clearContainer('#training');
            if (data.training.length > 0) {
              data.training.forEach(row => addTraining(row));
            } else {
              addTraining();
            }
          }

          if (typeof data.currentSection === 'number') {
            updateProgress(data.currentSection);
          }
        }

        function clearDraft() {
          localStorage.removeItem(getDraftKey());
        }

        /* -------------------------
          DYNAMIC SECTIONS
        ------------------------- */
        function addEducation(data = {}) {
          const container = document.getElementById("education-container");
          const div = document.createElement("div");
          div.classList.add("education-entry", "education-box");
          div.innerHTML = `
            <div class="education-grid">
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
                <label class="required">Period of Attendance From</label>
                <input type="date" name="edu_from[]" required>
              </div>

              <div>
                <label class="required">To</label>
                <input type="date" name="edu_to[]" required>
              </div>

              <div>
                <label class="required">Year Graduated</label>
                <input name="year_graduated[]" placeholder="Year Graduated" required>
              </div>

              <div>
                <label>Scholarship / Academic Honors</label>
                <input name="honors[]" placeholder="Scholarship / Honors">
              </div>
            </div>
            <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">Remove</button>
          `;
          container.appendChild(div);

          div.querySelector('[name="education_level[]"]').value = data.education_level || 'Elementary';
          div.querySelector('[name="school_name[]"]').value = data.school_name || '';
          div.querySelector('[name="course[]"]').value = data.course || '';
          div.querySelector('[name="units[]"]').value = data.units || '';
          div.querySelector('[name="edu_from[]"]').value = data.edu_from || '';
          div.querySelector('[name="edu_to[]"]').value = data.edu_to || '';
          div.querySelector('[name="year_graduated[]"]').value = data.year_graduated || '';
          div.querySelector('[name="honors[]"]').value = data.honors || '';

          saveFormDraft();
        }

        function addEligibility(data = {}) {
          const container = document.getElementById("eligibility");
          const div = document.createElement("div");
          div.classList.add("eligibility-entry", "eligibility-box");
          div.innerHTML = `
            <div class="eligibility-grid">
              <div>
                <label>Career Service / CSC / CES</label>
                <input name="career_service[]" placeholder="Career Service / CSC / CES">
              </div>

              <div>
                <label>Rating</label>
                <input name="rating[]" placeholder="Rating">
              </div>

              <div>
                <label class="required">Exam Date</label>
                <input type="date" name="exam_date[]" required>
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
            <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">Remove</button>
          `;
          container.appendChild(div);

          div.querySelector('[name="career_service[]"]').value = data.career_service || '';
          div.querySelector('[name="rating[]"]').value = data.rating || '';
          div.querySelector('[name="exam_date[]"]').value = data.exam_date || '';
          div.querySelector('[name="exam_place[]"]').value = data.exam_place || '';
          div.querySelector('[name="license[]"]').value = data.license || '';
          div.querySelector('[name="license_number[]"]').value = data.license_number || '';
          div.querySelector('[name="valid_until[]"]').value = data.valid_until || '';

          saveFormDraft();
        }

        function addTraining(data = {}) {
          const container = document.getElementById("training");
          const div = document.createElement("div");
          div.classList.add("training-entry", "training-box");
          div.innerHTML = `
            <div class="training-grid">
              <div>
                <label>Training Title</label>
                <input name="title[]" placeholder="Training Title">
              </div>

              <div>
                <label class="required">Hours</label>
                <input name="hours[]" placeholder="Hours" required>
              </div>

              <div>
                <label class="required">From</label>
                <input type="date" name="training_from[]" required>
              </div>

              <div>
                <label class="required">To</label>
                <input type="date" name="training_to[]" required>
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
            <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">Remove</button>
          `;
          container.appendChild(div);

          div.querySelector('[name="title[]"]').value = data.title || '';
          div.querySelector('[name="hours[]"]').value = data.hours || '';
          div.querySelector('[name="training_from[]"]').value = data.training_from || '';
          div.querySelector('[name="training_to[]"]').value = data.training_to || '';
          div.querySelector('[name="type[]"]').value = data.type || '';
          div.querySelector('[name="sponsor[]"]').value = data.sponsor || '';

          saveFormDraft();
        }

        function removeEntry(button, selector) {
          const item = button.closest(selector);
          if (item) {
            item.remove();
            saveFormDraft();
          }
        }

        /* -------------------------
          AUTO MARK REQUIRED LABELS
        ------------------------- */
        function markRequiredLabels() {
          document.querySelectorAll("[required]").forEach(input => {
            const wrapper = input.parentElement;
            if (!wrapper) return;
            const label = wrapper.querySelector("label");
            if (label) {
              label.classList.add("required");
            }
          });
        }

        /* -------------------------
          EVENTS
        ------------------------- */
        document.addEventListener("input", function(e){
          if(e.target.matches("input, select, textarea")){
            if((e.target.value || "").trim() !== ""){
              e.target.classList.remove("field-error");
              const wrapper = e.target.parentElement;
              if (wrapper) {
                const msg = wrapper.querySelector(".field-error-message");
                if(msg) msg.remove();
              }
            }
            saveFormDraft();
          }
        });

        document.addEventListener("change", function(e){
          if(e.target.matches("input, select, textarea")){
            saveFormDraft();
          }
        });

        form.addEventListener("submit", function(e){
          saveFormDraft();

          if(!validateAllSections()){
            e.preventDefault();
            return false;
          }

          clearDraft();
        });

        document.addEventListener("DOMContentLoaded", function () {
          restoreDraft();
          markRequiredLabels();
          if (localStorage.getItem(getDraftKey())) {
            const raw = JSON.parse(localStorage.getItem(getDraftKey()));
            if (typeof raw.currentSection === "number") {
              updateProgress(raw.currentSection);
            } else {
              updateProgress(0);
            }
          } else {
            updateProgress(0);
          }
        });
</script>

</body>
</html>