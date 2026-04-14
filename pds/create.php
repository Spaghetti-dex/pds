<?php 
require_once "../includes/auth_check.php";
include "../includes/header.php"; 
?>

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
  --site-header-height: 90px;
}

/* LAYOUT */
.container{
  display:flex;
  margin-top:var(--site-header-height);
  min-height:calc(100vh - var(--site-header-height));
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

.input::placeholder {
}

/* NAV ITEM */
.nav-item{
  display:flex;
  align-items:center;
  gap:15px;
  cursor:default;
  padding:10px 15px;
  border-radius:10px;
  transition:0.2s;
  z-index:3;
  position:relative;
  background:transparent;
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
  min-width:0;
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
.personal-row label,
.field-pair label,
.citizenship-row label,
.citizenship-pair label{
  font-size:14px;
  font-weight:600;
  text-align:right;
  white-space:nowrap;
}

.personal-grid input,
.personal-grid select,
.personal-row input,
.personal-row select,
.field-pair input,
.field-pair select,
.citizenship-row input,
.citizenship-row select,
.citizenship-pair input,
.citizenship-pair select{
  width:100%;
  min-width:0;
  max-width:100%;
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
  grid-template-columns:repeat(3, minmax(0, 1fr));
  gap:16px;
  align-items:start;
  margin-top:20px;
  width:100%;
}

.personal-row.small{
  grid-template-columns:repeat(3, minmax(0, 1fr));
}

.field-pair,
.citizenship-pair{
  display:grid;
  grid-template-columns:250px minmax(0, 1fr);
  gap:10px;
  align-items:center;
  min-width:0;
}

.citizenship-row{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:16px;
  align-items:start;
  margin-top:25px;
  width:100%;
}

.citizenship-row input:disabled,
.citizenship-pair input:disabled{
  background:#cfcfcf;
  color:#666;
  border:1px solid #999;
  cursor:not-allowed;
  opacity:1;
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
  justify-content:space-between;
  align-items:center;
  gap:10px;
  margin-top:20px;
  flex-wrap:wrap;
}

.nav-left,
.nav-right{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.prev-btn,
.next-btn,
.save-btn,
.add-btn,
.clear-btn{
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

.prev-btn,
.next-btn,
.save-btn,
.add-btn{
  background:#2f402c;
}

.prev-btn:hover,
.next-btn:hover,
.save-btn:hover,
.add-btn:hover{
  background:#3b5237;
}

.clear-btn{
  background:#8b2c2c;
}

.clear-btn:hover{
  background:#a63a3a;
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

.photo-home-row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
  margin-bottom:20px;
}

.photo-home-row .title{
  text-align:center;
  margin:0;
}

.preview-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.photo-box{
  width:120px;
  aspect-ratio:1 / 1;
  border:1px solid #555;
  border-radius:6px;
  overflow:hidden;
  cursor:pointer;
  background:#e9e9ee;
  box-sizing:border-box;
  flex-shrink:0;
  display:block;
  padding:0;
}

.photo-box img{
  width:100%;
  height:100%;
  object-fit:cover;
  display:block;
}

.photo-box input{
  display:none;
}

.photo-upload-btn{
  margin-top:5px;
  padding:4px 8px;
  font-size:11px;
  line-height:1.2;
  white-space:nowrap;
  background:#2f402c;
  color:#fff;
  border:none;
  border-radius:4px;
  cursor:pointer;
}

.photo-upload-btn:hover{
  background:#3b5237;
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
  color:inherit;
  font-weight:700;
}

label.required::after{
  content:" *";
  color:#c62828;
}

@media (max-width: 1200px){
  .card{
    width:100%;
    padding:20px 24px 30px;
    overflow:hidden;
  }

  .form-area{
    padding:16px;
  }

  .personal-row,
  .personal-row.small{
    grid-template-columns:repeat(2, minmax(0, 1fr));
  }

  .field-pair,
  .citizenship-pair{
    grid-template-columns:minmax(0, 1fr);
    gap:8px;
  }

  .field-pair label,
  .citizenship-pair label{
    text-align:left;
    white-space:normal;
  }

  .citizenship-row{
    grid-template-columns:repeat(2, minmax(0, 1fr));
  }
}

@media (max-width: 768px){
  .container{
    margin-top:var(--site-header-height);
    min-height:calc(100vh - var(--site-header-height));
  }

  .photo-home-row{
    flex-wrap:wrap;
    justify-content:center;
  }

  .top-actions{
    width:100%;
    display:flex;
    justify-content:center;
  }

  .photo-home-row .title{
    width:100%;
    order:-1;
  }
}

@media (max-width: 900px){
  .container{
    flex-direction:column;
  }

  .form-area{
    padding:12px;
  }

  .card{
    width:100%;
    padding:20px 16px 30px;
    overflow:hidden;
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
  .training-grid,
  .contact-row,
  .address-house-row,
  .address-row,
  .field-pair,
  .citizenship-pair{
    grid-template-columns:minmax(0, 1fr);
  }

  .personal-row,
  .personal-row.small{
    grid-template-columns:minmax(0, 1fr);
    align-items:stretch;
    gap:8px;
  }

  .personal-grid input,
  .personal-grid select,
  .personal-row input,
  .personal-row select,
  .field-pair input,
  .field-pair select,
  .citizenship-row input,
  .citizenship-row select,
  .citizenship-pair input,
  .citizenship-pair select,
  .address-house-row input,
  .address-row input,
  .education-grid input,
  .education-grid select,
  .eligibility-grid input,
  .training-grid input,
  .contact-row input{
    width:100%;
    min-width:0;
    max-width:100%;
  }

  .contact-section{
    padding-top:40px;
  }

  .contact-grid{
    width:100%;
    max-width:390px;
  }

  .address-two-col{
    grid-template-columns:1fr;
    column-gap:0;
    row-gap:18px;
  }

  .personal-grid label,
  .personal-row label,
  .field-pair label,
  .citizenship-row label,
  .citizenship-pair label,
  .contact-row label,
  .address-house-row label,
  .address-row label{
    text-align:left;
    white-space:normal;
  }

  .nav-buttons{
    flex-direction:column;
    align-items:stretch;
  }

  .nav-left,
  .nav-right{
    width:100%;
    justify-content:center;
  }
}
</style>
</head>
<body>

<div class="container">

  <div class="sidebar">
    <div class="progress-line" id="progressLine"></div>

    <div class="nav-item active" data-index="0" tabindex="0">
      <img src="../assets/profile.png" alt="Profile" class="nav-icon">
      <div class="nav-label">PERSONAL INFORMATION</div>
    </div>

    <div class="nav-item" data-index="1" tabindex="0">
      <img src="../assets/address.png" alt="Address" class="nav-icon">
      <div class="nav-label">ADDRESS</div>
    </div>

    <div class="nav-item" data-index="2" tabindex="0">
      <img src="../assets/contact.png" alt="Contact" class="nav-icon">
      <div class="nav-label">CONTACT INFORMATION</div>
    </div>

    <div class="nav-item" data-index="3" tabindex="0">
      <img src="../assets/education.png" alt="Education" class="nav-icon">
      <div class="nav-label">EDUCATIONAL BACKGROUND</div>
    </div>

    <div class="nav-item" data-index="4" tabindex="0">
      <img src="../assets/service.png" alt="Service" class="nav-icon">
      <div class="nav-label">SERVICE ELIGIBILITY</div>
    </div>

    <div class="nav-item" data-index="5" tabindex="0">
      <img src="../assets/learning.png" alt="Training" class="nav-icon">
      <div class="nav-label">LEARNING AND DEVELOPMENT</div>
    </div>
  </div>

  <div class="form-area">
    <div class="card">

      <form method="POST" action="save.php" id="pdsForm" novalidate enctype="multipart/form-data">
        <input type="hidden" name="form_submitted" value="1">

        <div id="errorSummary" class="error-summary" style="display:none;"></div>

        <!-- PERSONAL INFORMATION -->
        <div id="personal" class="section active">
          <div class="photo-home-row">
            <div class="top-actions">
              <a href="../dashboard/dashboard.php" class="button">🏠︎ Home</a>
            </div>

            <div class="title">PERSONAL INFORMATION</div>

            <div class="preview-container">
              <div class="photo-box" onclick="document.getElementById('photoInput').click()">
                <img id="preview" src="../assets/profile.png" alt="Profile">
                <input type="file" name="photo" id="photoInput" accept="image/*" onchange="loadImage(event)">
              </div>
              <button type="button" class="photo-upload-btn" onclick="document.getElementById('photoInput').click();">🔗 Choose File</button>
            </div>
          </div>

          <div class="personal-grid">
            <label for="surname">Last Name:</label>
            <input id="surname" name="surname" required>

            <label for="extension">Name Extension:</label>
            <input id="extension" name="extension">

            <label for="firstname">First Name:</label>
            <input id="firstname" class="input" name="firstname" placeholder="Enter your first name" required>

            <label for="dob">Date of Birth:</label>
            <input id="dob" type="date" name="dob" required>

            <label for="middlename">Middle Name:</label>
            <input id="middlename" name="middlename" required>

            <label for="birth_place">Place of Birth:</label>
            <input id="birth_place" name="birth_place" placeholder="ex. Pasig City" required>
          </div>

          <div class="personal-row small">
            <div class="field-pair">
              <label for="civil_status">Civil Status:</label>
              <select id="civil_status" name="civil_status" required>
                <option value=""></option>
                <option value="Single">Single</option>
                <option value="Married">Married</option>
                <option value="Widowed">Widowed</option>
                <option value="Separated">Separated</option>
              </select>
            </div>

            <div class="field-pair">
              <label for="sex">Sex:</label>
              <select id="sex" name="sex" required>
                <option value=""></option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
              </select>
            </div>

            <div class="field-pair">
              <label for="blood_type">Blood Type:</label>
              <select id="blood_type" name="blood_type" required>
                <option value=""></option>
                <option value="O+">O+</option>
                <option value="O-">O-</option>
                <option value="A+">A+</option>
                <option value="A-">A-</option>
                <option value="B+">B+</option>
                <option value="B-">B-</option>
                <option value="AB+">AB+</option>
                <option value="AB-">AB-</option>
              </select>
            </div>
          </div>

          <div class="personal-row small">
            <div class="field-pair">
              <label for="height">Height (cm):</label>
              <input id="height" name="height" placeholder="ex. 165" required>
            </div>

            <div class="field-pair">
              <label for="weight">Weight (kg):</label>
              <input id="weight" name="weight" placeholder="ex. 82" required>
            </div>
          </div>

          <div class="personal-grid" style="margin-top:25px;">
            <label for="umid">UMID ID:</label>
            <input id="umid" name="umid" maxlength="30" required>

            <label for="philsys">PhilSys No.(PSN):</label>
            <input id="philsys" name="philsys" maxlength="30" required>

            <label for="pagibig">Pag-IBIG ID No:</label>
            <input id="pagibig" name="pagibig" maxlength="30" required>

            <label for="tin">TIN No:</label>
            <input id="tin" name="tin" maxlength="20" placeholder="000-000-000-000" required>

            <label for="philhealth">PhilHealth No:</label>
            <input id="philhealth" name="philhealth" maxlength="30" required>

            <label for="agency_employee">Agency Employee No:</label>
            <input id="agency_employee" name="agency_employee" maxlength="30" required>
          </div>

          <div class="citizenship-row">
            <div class="citizenship-pair">
              <label for="citizenship">Citizenship:</label>
              <select name="citizenship" id="citizenship" required>
                <option value=""></option>
                <option value="Filipino">Filipino</option>
                <option value="Dual Citizen">Dual Citizen</option>
              </select>
            </div>

            <div class="citizenship-pair">
              <label for="dual_country">If Dual Citizen (Indicate Country):</label>
              <input name="dual_country" id="dual_country" disabled>
            </div>
          </div>
        </div>

        <!-- ADDRESS -->
        <div id="address" class="section">
          <div class="address-section">

            <div class="address-title">RESIDENTIAL ADDRESS</div>
            <div class="address-block">
              <div class="address-house-row">
                <label for="r_house">House / Block / Lot No.</label>
                <input id="r_house" name="r_house" required>
              </div> 

              <div class="address-two-col">
                <div class="address-col">
                  <div class="address-row">
                    <label for="r_street">Street:</label>
                    <input id="r_street" name="r_street" required>
                  </div>

                  <div class="address-row">
                    <label for="r_subdivision">Subdivision / Village:</label>
                    <input id="r_subdivision" name="r_subdivision" required>
                  </div>

                  <div class="address-row">
                    <label for="r_city">City / Municipality:</label>
                    <input id="r_city" name="r_city" required>
                  </div>
                </div>

                <div class="address-col">
                  <div class="address-row">
                    <label for="r_barangay">Barangay:</label>
                    <input id="r_barangay" name="r_barangay" required>
                  </div>

                  <div class="address-row">
                    <label for="r_province">Province:</label>
                    <input id="r_province" name="r_province" required>
                  </div>

                  <div class="address-row">
                    <label for="r_zip">Zip Code:</label>
                    <input id="r_zip" name="r_zip" required>
                  </div>
                </div>
              </div>
            </div>

            <div style="margin:10px 0 15px 0; text-align:center;">
              <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                <input type="checkbox" id="sameAddress" style="width:auto;">
                Same as Residential Address
              </label>
            </div>

            <div class="address-title" style="margin-top:18px;">PERMANENT ADDRESS</div>
            <div class="address-block">
              <div class="address-house-row">
                <label for="p_house">House / Block / Lot No.</label>
                <input id="p_house" name="p_house" required>
              </div>

              <div class="address-two-col">
                <div class="address-col">
                  <div class="address-row">
                    <label for="p_street">Street:</label>
                    <input id="p_street" name="p_street" required>
                  </div>

                  <div class="address-row">
                    <label for="p_subdivision">Subdivision / Village:</label>
                    <input id="p_subdivision" name="p_subdivision" required>
                  </div>

                  <div class="address-row">
                    <label for="p_city">City / Municipality:</label>
                    <input id="p_city" name="p_city" required>
                  </div>
                </div>

                <div class="address-col">
                  <div class="address-row">
                    <label for="p_barangay">Barangay:</label>
                    <input id="p_barangay" name="p_barangay" required>
                  </div>

                  <div class="address-row">
                    <label for="p_province">Province:</label>
                    <input id="p_province" name="p_province" required>
                  </div>

                  <div class="address-row">
                    <label for="p_zip">Zip Code:</label>
                    <input id="p_zip" name="p_zip" required>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- CONTACT -->
        <div id="contact" class="section">
          <div class="contact-section">
            <div class="contact-title">CONTACT INFORMATION</div>

            <div class="contact-grid">
              <div class="contact-row">
                <label for="telephone">Telephone Number:</label>
                <input id="telephone" name="telephone" placeholder="ex. 02-xxxx-xxxx">
              </div>

              <div class="contact-row">
                <label for="mobile">Mobile Number:</label>
                <input id="mobile" name="mobile" placeholder="09XXXXXXXXX" required>
              </div>

              <div class="contact-row">
                <label for="email">E-Mail:</label>
                <input id="email" type="email" name="email" required>
              </div>
            </div>
          </div>
        </div>

        <!-- EDUCATION -->
        <div id="education" class="section">
          <div class="title">EDUCATIONAL BACKGROUND</div>

          <div id="education-container">
            <div class="education-entry education-box">
              <div class="education-grid">
                <div>
                  <label>Level</label>
                  <select name="education_level[]" required>
                    <option>Elementary</option>
                    <option>Secondary</option>
                    <option>Vocational / Trade Course</option>
                    <option>College</option>
                    <option>Graduate Studies</option>
                  </select>
                </div>

                <div>
                  <label>Name of School</label>
                  <input name="school_name[]" placeholder="School Name" required>
                </div>

                <div>
                  <label>Basic Education / Degree / Course</label>
                  <input name="course[]" placeholder="Course / Degree" required>
                </div>

                <div>
                  <label>Highest Level / Units Earned</label>
                  <input name="units[]" placeholder="Highest Level / Units" required>
                </div>

                <div>
                  <label>Period of Attendance From</label>
                  <input type="date" name="edu_from[]" required>
                </div>

                <div>
                  <label>To</label>
                  <input type="date" name="edu_to[]" required>
                </div>

                <div>
                  <label>Year Graduated</label>
                  <input name="year_graduated[]" placeholder="Year Graduated" required>
                </div>

                <div>
                  <label>Scholarship / Academic Honors</label>
                  <input name="honors[]" placeholder="Scholarship / Honors">
                </div>
              </div>

              <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">✖ Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEducation()">✚ Add More</button>
        </div>

        <!-- ELIGIBILITY -->
        <div id="eligibility-section" class="section">
          <div class="title">SERVICE ELIGIBILITY</div>

          <div id="eligibility">
            <div class="eligibility-entry eligibility-box">
              <div class="eligibility-grid">
                <div>
                  <label>Career Service / CSC / CES</label>
                  <input name="career_service[]" placeholder="Career Service / CSC / CES" required>
                </div>

                <div>
                  <label>Rating</label>
                  <input name="rating[]" placeholder="Rating" required>
                </div>

                <div>
                  <label>Exam Date</label>
                  <input type="date" name="exam_date[]" required>
                </div>

                <div>
                  <label>Place of Examination</label>
                  <input name="exam_place[]" placeholder="Place of Examination" required>
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

              <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">✖ Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEligibility()">✚ Add More</button>
        </div>

        <!-- TRAINING -->
        <div id="training-section" class="section">
          <div class="title">LEARNING AND DEVELOPMENT</div>

          <div id="training">
            <div class="training-entry training-box">
              <div class="training-grid">
                <div>
                  <label>Training Title</label>
                  <input name="title[]" placeholder="Training Title" required>
                </div>

                <div>
                  <label>Hours</label>
                  <input name="hours[]" placeholder="Hours" required>
                </div>

                <div>
                  <label>From</label>
                  <input type="date" name="training_from[]" required>
                </div>

                <div>
                  <label>To</label>
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

              <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">✖ Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addTraining()">✚ Add More</button>
        </div>

        <div class="nav-buttons">
          <div class="nav-left">
            <button type="button" class="prev-btn" id="prevBtn" onclick="prevSection()" style="display:none;">⬅ Previous</button>
            <button type="button" class="clear-btn" onclick="clearAllForm()">⟳ Clear All</button>
          </div>

          <div class="nav-right">
            <button type="button" class="next-btn" id="nextBtn" onclick="nextSection()">Next ➡</button>
            <button type="submit" class="save-btn" id="saveBtn" style="display:none;">✔ Save</button>
          </div>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
(function(){
  function updateHeaderOffset(){
    const candidates = Array.from(document.querySelectorAll('header, .header, .main-header, .navbar, .topbar'));
    let headerEl = null;

    for (const el of candidates){
      if (el.closest('.container')) continue;
      const rect = el.getBoundingClientRect();
      if (rect.height > 0 && rect.top <= 5){
        headerEl = el;
        break;
      }
    }

    const headerHeight = headerEl ? Math.ceil(headerEl.getBoundingClientRect().height) : 90;
    document.body.style.setProperty('--site-header-height', (headerHeight + 12) + 'px');
  }

  window.addEventListener('load', updateHeaderOffset);
  window.addEventListener('resize', updateHeaderOffset);
  document.addEventListener('DOMContentLoaded', updateHeaderOffset);
})();
</script>

<script>
let currentSection = 0;
const sections = document.querySelectorAll(".section");
const navItems = document.querySelectorAll(".nav-item");
const form = document.getElementById("pdsForm");
const errorSummary = document.getElementById("errorSummary");

function loadImage(event){
  const preview = document.getElementById("preview");
  if(event.target.files && event.target.files[0]){
    preview.src = URL.createObjectURL(event.target.files[0]);
  }
}

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

  const stepHeight = navItems.length ? (navItems[0].offsetHeight + 35) : 0;
  document.getElementById("progressLine").style.height = (index * stepHeight) + "px";

  currentSection = index;

  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");
  const saveBtn = document.getElementById("saveBtn");

  prevBtn.style.display = index === 0 ? "none" : "inline-block";

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

function prevSection(){
  hideSummary();
  if(currentSection > 0){
    updateProgress(currentSection - 1);
  }
}

function copyResidentialToPermanent(isChecked){
  const fieldPairs = [
    ["r_house", "p_house"],
    ["r_street", "p_street"],
    ["r_subdivision", "p_subdivision"],
    ["r_city", "p_city"],
    ["r_barangay", "p_barangay"],
    ["r_province", "p_province"],
    ["r_zip", "p_zip"]
  ];

  fieldPairs.forEach(([r, p]) => {
    const rField = document.querySelector(`[name="${r}"]`);
    const pField = document.querySelector(`[name="${p}"]`);

    if (!rField || !pField) return;

    if (isChecked) {
      pField.value = rField.value;
    } else {
      pField.value = "";
    }
  });
}

navItems.forEach((item) => {
  item.addEventListener("click", function(e){
    e.preventDefault();
  });

  item.addEventListener("keydown", function(e){
    if(e.key === "Enter"){
      const index = parseInt(this.dataset.index, 10);
      if(!Number.isNaN(index)){
        goToSection(index);
      }
    }
  });
});

const sameAddressCheckbox = document.getElementById("sameAddress");

if (sameAddressCheckbox) {
  sameAddressCheckbox.addEventListener("change", function(){
    copyResidentialToPermanent(this.checked);
    saveFormDraft();
  });

  const residentialFields = [
    "r_house", "r_street", "r_subdivision",
    "r_city", "r_barangay", "r_province", "r_zip"
  ];

  residentialFields.forEach(name => {
    const field = document.querySelector(`[name="${name}"]`);
    if (field) {
      field.addEventListener("input", function(){
        if (sameAddressCheckbox.checked) {
          copyResidentialToPermanent(true);
        }
      });
    }
  });
}

function showSummary(message){
  errorSummary.style.display = "block";
  errorSummary.innerHTML = message;
}

function hideSummary(){
  errorSummary.style.display = "none";
  errorSummary.textContent = "";
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

function clearFieldErrorState(input) {
  if (!input) return;
  input.classList.remove("field-error");
  const wrapper = input.parentElement;
  if (wrapper) {
    const old = wrapper.querySelector(".field-error-message");
    if (old) old.remove();
  }
}

function clearFieldErrors(){
  document.querySelectorAll(".field-error").forEach(el => el.classList.remove("field-error"));
  document.querySelectorAll(".field-error-message").forEach(el => el.remove());
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

function getFieldLabel(input){
  if (!input) return "this field";

  if (input.dataset.label) {
    return input.dataset.label.trim();
  }

  if (input.id) {
    const linkedLabel = document.querySelector(`label[for="${input.id}"]`);
    if (linkedLabel) {
      return linkedLabel.textContent.replace(/\s+/g, " ").replace(/\*/g, "").trim();
    }
  }

  const closestPair = input.closest(".field-pair, .citizenship-pair, .contact-row, .address-row, .address-house-row");
  if (closestPair) {
    const pairLabel = closestPair.querySelector("label");
    if (pairLabel) {
      return pairLabel.textContent.replace(/\s+/g, " ").replace(/\*/g, "").trim();
    }
  }

  const prev = input.previousElementSibling;
  if (prev && prev.tagName === "LABEL") {
    return prev.textContent.replace(/\s+/g, " ").replace(/\*/g, "").trim();
  }

  return input.name || "this field";
}

function sanitizeIdNumber(value) {
  return value.replace(/[^\d\- ]/g, '');
}

function isLettersOnly(value) {
  return /^[A-Za-zÑñ\s.'-]+$/.test(value);
}

function isAlphaNumericBasic(value) {
  return /^[A-Za-z0-9Ññ\s./#,-]+$/.test(value);
}

function isValidEmail(value) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function isValidMobile(value) {
  return /^(09\d{9}|\+639\d{9})$/.test(value);
}

function isValidTelephone(value) {
  return /^[0-9()\-\s]{7,15}$/.test(value);
}

function isValidZip(value) {
  return /^\d{4}$/.test(value);
}

function isValidYear(value) {
  return /^(19|20)\d{2}$/.test(value);
}

function isPositiveNumber(value) {
  return /^\d+(\.\d+)?$/.test(value) && parseFloat(value) > 0;
}

function isValidBloodType(value) {
  return /^(A|B|AB|O)[+-]$/i.test(value.trim());
}

function isValidTin(value) {
  return /^(\d{3}-\d{3}-\d{3}|\d{9}|\d{12}|\d{3}-\d{3}-\d{3}-\d{3})$/.test(value);
}

function isValidDateOrder(fromValue, toValue) {
  if (!fromValue || !toValue) return true;
  return new Date(fromValue) <= new Date(toValue);
}

function isPastOrToday(value) {
  if (!value) return true;
  const inputDate = new Date(value);
  const today = new Date();
  today.setHours(0,0,0,0);
  return inputDate <= today;
}

function shouldSkipValidation(input){
  if (!input) return true;
  if (input.type === "hidden") return true;
  if (input.disabled) return true;
  if (input.closest(".section") && !input.closest(".section").classList.contains("active")) return true;
  return false;
}

function validateSingleField(input) {
  if (!input || !input.name) return true;
  if (input.disabled) {
    clearFieldErrorState(input);
    return true;
  }

  const name = input.name;
  const value = (input.value || "").trim();
  const isRequired = input.hasAttribute("required");

  clearFieldErrorState(input);

  const markInvalid = (message) => {
    addFieldError(input, message);
    return false;
  };

  if (name === "dual_country") {
    const citizenship = form.querySelector('[name="citizenship"]')?.value || "";
    if (citizenship !== "Dual Citizen") {
      return true;
    }
    if (value === "") {
      return markInvalid("Country is required for dual citizenship.");
    }
  } else if (isRequired && value === "") {
    return markInvalid("This field is required.");
  }

  if (value === "") return true;

  switch (name) {
    case "surname":
    case "firstname":
    case "middlename":
      if (!isLettersOnly(value)) return markInvalid("Letters only.");
      return true;

    case "extension":
      if (!/^[A-Za-z0-9.\s-]{1,10}$/.test(value)) {
        return markInvalid("Invalid name extension.");
      }
      return true;

    case "birth_place":
    case "r_house":
    case "r_street":
    case "r_subdivision":
    case "r_city":
    case "r_barangay":
    case "r_province":
    case "p_house":
    case "p_street":
    case "p_subdivision":
    case "p_city":
    case "p_barangay":
    case "p_province":
      if (!isAlphaNumericBasic(value)) return markInvalid("Invalid characters.");
      return true;

    case "dob":
      if (!isPastOrToday(value)) return markInvalid("Date of birth cannot be in the future.");
      return true;

    case "civil_status":
      if (!["Single", "Married", "Widowed", "Separated"].includes(value)) {
        return markInvalid("Invalid civil status.");
      }
      return true;

    case "sex":
      if (!["Male", "Female"].includes(value)) {
        return markInvalid("Please select a valid sex.");
      }
      return true;

    case "blood_type":
      if (!isValidBloodType(value)) {
        return markInvalid("Use A+, A-, B+, B-, AB+, AB-, O+, or O-.");
      }
      return true;

    case "height":
    case "weight":
      if (!isPositiveNumber(value)) {
        return markInvalid("Must be a positive number.");
      }
      return true;

    case "umid":
    case "philsys":
    case "pagibig":
    case "philhealth":
    case "agency_employee":
      if (!/^[0-9][0-9\- ]{3,29}$/.test(value)) {
        return markInvalid("Numbers only. Hyphen and spaces are allowed.");
      }
      return true;

    case "tin":
      if (!isValidTin(value)) {
        return markInvalid("Invalid TIN format.");
      }
      return true;

    case "citizenship":
      if (!["Filipino", "Dual Citizen"].includes(value)) {
        return markInvalid("Invalid citizenship.");
      }
      return true;

    case "dual_country":
      return true;

    case "r_zip":
    case "p_zip":
      if (!isValidZip(value)) {
        return markInvalid("Zip code must be 4 digits.");
      }
      return true;

    case "telephone":
      if (!isValidTelephone(value)) {
        return markInvalid("Invalid telephone number.");
      }
      return true;

    case "mobile":
      if (!isValidMobile(value)) {
        return markInvalid("Use 09XXXXXXXXX or +639XXXXXXXXX.");
      }
      return true;

    case "email":
      if (!isValidEmail(value)) {
        return markInvalid("Invalid email address.");
      }
      return true;
  }

  return true;
}

function validateSection(section){
  let firstInvalid = null;
  const fields = section.querySelectorAll("input, select, textarea");

  const markInvalid = (input, message) => {
    addFieldError(input, message);
    if (!firstInvalid) firstInvalid = input;
  };

  fields.forEach(input => {
    if (shouldSkipValidation(input)) return;

    const name = input.name || "";
    const value = (input.value || "").trim();
    const isRequired = input.hasAttribute("required");

    clearFieldErrorState(input);

    if (name === "dual_country") {
      const citizenship = form.querySelector('[name="citizenship"]')?.value || "";
      if (citizenship === "Dual Citizen" && value === "") {
        markInvalid(input, "Country is required for dual citizenship.");
      } else if (value !== "" && !/^[A-Za-zÑñ\s.'-]+$/.test(value)) {
        markInvalid(input, "Letters only.");
      }
      return;
    }

    if (isRequired && value === "") {
      markInvalid(input, "This field is required.");
      return;
    }

    if (value === "") return;

    switch (name) {
      case "surname":
      case "firstname":
      case "middlename":
        if (!isLettersOnly(value)) markInvalid(input, "Letters only.");
        break;

      case "extension":
        if (!/^[A-Za-z0-9.\s-]{1,10}$/.test(value)) {
          markInvalid(input, "Invalid name extension.");
        }
        break;

      case "birth_place":
      case "r_house":
      case "r_street":
      case "r_subdivision":
      case "r_city":
      case "r_barangay":
      case "r_province":
      case "p_house":
      case "p_street":
      case "p_subdivision":
      case "p_city":
      case "p_barangay":
      case "p_province":
        if (!isAlphaNumericBasic(value)) {
          markInvalid(input, "Invalid characters.");
        }
        break;

      case "dob":
        if (!isPastOrToday(value)) {
          markInvalid(input, "Date of birth cannot be in the future.");
        }
        break;

      case "civil_status":
        if (!["Single", "Married", "Widowed", "Separated"].includes(value)) {
          markInvalid(input, "Invalid civil status.");
        }
        break;

      case "sex":
        if (!["Male", "Female"].includes(value)) {
          markInvalid(input, "Please select a valid sex.");
        }
        break;

      case "blood_type":
        if (!isValidBloodType(value)) {
          markInvalid(input, "Use A+, A-, B+, B-, AB+, AB-, O+, or O-.");
        }
        break;

      case "height":
      case "weight":
        if (!isPositiveNumber(value)) {
          markInvalid(input, "Must be a positive number.");
        }
        break;

      case "umid":
      case "philsys":
      case "pagibig":
      case "philhealth":
      case "agency_employee":
        if (!/^[0-9][0-9\- ]{3,29}$/.test(value)) {
          markInvalid(input, "Numbers only. Hyphen and spaces are allowed.");
        }
        break;

      case "tin":
        if (!isValidTin(value)) {
          markInvalid(input, "Invalid TIN format.");
        }
        break;

      case "citizenship":
        if (!["Filipino", "Dual Citizen"].includes(value)) {
          markInvalid(input, "Invalid citizenship.");
        }
        break;

      case "r_zip":
      case "p_zip":
        if (!isValidZip(value)) {
          markInvalid(input, "Zip code must be 4 digits.");
        }
        break;

      case "telephone":
        if (!isValidTelephone(value)) {
          markInvalid(input, "Invalid telephone number.");
        }
        break;

      case "mobile":
        if (!isValidMobile(value)) {
          markInvalid(input, "Use 09XXXXXXXXX or +639XXXXXXXXX.");
        }
        break;

      case "email":
        if (!isValidEmail(value)) {
          markInvalid(input, "Invalid email address.");
        }
        break;
    }
  });

  if (section.id === "education") {
    section.querySelectorAll(".education-entry").forEach(entry => {
      const from = entry.querySelector('[name="edu_from[]"]');
      const to = entry.querySelector('[name="edu_to[]"]');
      const yearGraduated = entry.querySelector('[name="year_graduated[]"]');

      if (from && to && from.value && to.value && !isValidDateOrder(from.value, to.value)) {
        markInvalid(to, '"To" date must not be earlier than "From" date.');
      }

      if (yearGraduated && yearGraduated.value.trim() !== "" && !isValidYear(yearGraduated.value.trim())) {
        markInvalid(yearGraduated, "Enter a valid 4-digit year.");
      }
    });
  }

  if (section.id === "eligibility-section") {
    section.querySelectorAll(".eligibility-entry").forEach(entry => {
      const examDate = entry.querySelector('[name="exam_date[]"]');
      const validUntil = entry.querySelector('[name="valid_until[]"]');
      const rating = entry.querySelector('[name="rating[]"]');
      const licenseNumber = entry.querySelector('[name="license_number[]"]');

      if (examDate && examDate.value && !isPastOrToday(examDate.value)) {
        markInvalid(examDate, "Exam date cannot be in the future.");
      }

      if (rating && rating.value.trim() !== "" && !/^\d+(\.\d+)?$/.test(rating.value.trim())) {
        markInvalid(rating, "Rating must be numeric.");
      }

      if (licenseNumber && licenseNumber.value.trim() !== "" && !/^[A-Za-z0-9\- ]{3,30}$/.test(licenseNumber.value.trim())) {
        markInvalid(licenseNumber, "Invalid license number.");
      }

      if (examDate && validUntil && examDate.value && validUntil.value) {
        if (new Date(validUntil.value) < new Date(examDate.value)) {
          markInvalid(validUntil, "Valid until must be after exam date.");
        }
      }
    });
  }

  if (section.id === "training-section") {
    section.querySelectorAll(".training-entry").forEach(entry => {
      const from = entry.querySelector('[name="training_from[]"]');
      const to = entry.querySelector('[name="training_to[]"]');
      const hours = entry.querySelector('[name="hours[]"]');

      if (hours && hours.value.trim() !== "" && !isPositiveNumber(hours.value.trim())) {
        markInvalid(hours, "Hours must be a positive number.");
      }

      if (from && to && from.value && to.value && !isValidDateOrder(from.value, to.value)) {
        markInvalid(to, '"To" date must not be earlier than "From" date.');
      }
    });
  }

  return firstInvalid;
}

function nextSection(){
  clearFieldErrors();
  hideSummary();

  const invalidField = validateSection(sections[currentSection]);

  if(invalidField){
    const pageName = getSectionName(sections[currentSection].id);
    const fieldName = getFieldLabel(invalidField);

    showSummary(`⚠ Please fix "<strong>${fieldName}</strong>" in <strong>${pageName}</strong> before proceeding.`);
    invalidField.focus();
    invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
    saveFormDraft();
    return;
  }

  if(currentSection < sections.length - 1){
    updateProgress(currentSection + 1);
  }
}

function validateAllSections(){
  clearFieldErrors();
  hideSummary();

  for(let i = 0; i < sections.length; i++){
    updateProgress(i);
    const invalidField = validateSection(sections[i]);

    if(invalidField){
      const pageName = getSectionName(sections[i].id);
      const fieldName = getFieldLabel(invalidField);

      showSummary(`⚠ Please fix "<strong>${fieldName}</strong>" in <strong>${pageName}</strong> before submitting.`);

      setTimeout(() => {
        invalidField.focus();
        invalidField.scrollIntoView({ behavior: "smooth", block: "center" });
      }, 100);

      return false;
    }
  }

  return true;
}

function getDraftKey() {
  return "personal_record_create_draft_v3";
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

  const fields = form.querySelectorAll('input:not([type="hidden"]):not([type="file"]):not([name$="[]"]), select:not([name$="[]"]), textarea:not([name$="[]"])');
  fields.forEach(field => {
    if (!field.name) return;

    if (field.type === 'checkbox' || field.type === 'radio') {
      if (field.checked) {
        data.simple[field.name] = field.value;
      } else if (field.type === 'checkbox') {
        data.simple[field.name] = '';
      }
    } else {
      data.simple[field.name] = field.value;
    }
  });

  if (sameAddressCheckbox) {
    data.sameAddress = sameAddressCheckbox.checked;
  }

  data.currentSection = currentSection;
  localStorage.setItem(getDraftKey(), JSON.stringify(data));
}

function restoreSimpleFields(data) {
  if (!data || !data.simple) return;

  Object.keys(data.simple).forEach(name => {
    const field = form.querySelector(`[name="${name}"]`);
    if (!field) return;

    if (field.type === 'checkbox') {
      field.checked = !!data.simple[name];
      return;
    }

    const currentValue = (field.value || '').trim();
    if (currentValue !== '') return;

    field.value = data.simple[name];
  });
}

function containerHasPopulatedInputs(selector) {
  const container = document.querySelector(selector);
  if (!container) return false;

  const fields = container.querySelectorAll('input, select, textarea');
  for (const field of fields) {
    const value = (field.value || '').trim();
    if (value !== '') return true;
  }

  return false;
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

  if (document.getElementById('education-container') && Array.isArray(data.education) && !containerHasPopulatedInputs('#education-container')) {
    clearContainer('#education-container');
    if (data.education.length > 0) {
      data.education.forEach(row => addEducation(row));
    } else {
      addEducation();
    }
  }

  if (document.getElementById('eligibility') && Array.isArray(data.eligibility) && !containerHasPopulatedInputs('#eligibility')) {
    clearContainer('#eligibility');
    if (data.eligibility.length > 0) {
      data.eligibility.forEach(row => addEligibility(row));
    } else {
      addEligibility();
    }
  }

  if (document.getElementById('training') && Array.isArray(data.training) && !containerHasPopulatedInputs('#training')) {
    clearContainer('#training');
    if (data.training.length > 0) {
      data.training.forEach(row => addTraining(row));
    } else {
      addTraining();
    }
  }

  if (sameAddressCheckbox && typeof data.sameAddress !== "undefined") {
    sameAddressCheckbox.checked = !!data.sameAddress;
    if (sameAddressCheckbox.checked) {
      copyResidentialToPermanent(true);
    }
  }

  if (typeof data.currentSection === 'number' && data.currentSection >= 0 && data.currentSection < sections.length) {
    updateProgress(data.currentSection);
  } else {
    updateProgress(0);
  }
}

function clearDraft() {
  localStorage.removeItem(getDraftKey());
}

function resetToSingleEntry(containerSelector, addFn) {
  clearContainer(containerSelector);
  addFn();
}

function clearAllForm() {
  const confirmed = window.confirm("Clear all fields in this form?");
  if (!confirmed) return;

  form.reset();
  hideSummary();
  clearFieldErrors();
  clearDraft();

  const preview = document.getElementById("preview");
  if (preview) {
    preview.src = "../assets/profile.png";
  }

  resetToSingleEntry('#education-container', addEducation);
  resetToSingleEntry('#eligibility', addEligibility);
  resetToSingleEntry('#training', addTraining);

  if (sameAddressCheckbox) {
    sameAddressCheckbox.checked = false;
  }

  if (dualCountry) {
    dualCountry.disabled = true;
    dualCountry.value = "";
  }

  updateProgress(0);
}

function addEducation(data = {}) {
  const container = document.getElementById("education-container");
  const div = document.createElement("div");
  div.classList.add("education-entry", "education-box");
  div.innerHTML = `
    <div class="education-grid">
      <div>
        <label>Level</label>
        <select name="education_level[]" required>
          <option>Elementary</option>
          <option>Secondary</option>
          <option>Vocational / Trade Course</option>
          <option>College</option>
          <option>Graduate Studies</option>
        </select>
      </div>

      <div>
        <label>Name of School</label>
        <input name="school_name[]" placeholder="School Name" required>
      </div>

      <div>
        <label>Basic Education / Degree / Course</label>
        <input name="course[]" placeholder="Course / Degree" required>
      </div>

      <div>
        <label>Highest Level / Units Earned</label>
        <input name="units[]" placeholder="Highest Level / Units" required>
      </div>

      <div>
        <label>Period of Attendance From</label>
        <input type="date" name="edu_from[]" required>
      </div>

      <div>
        <label>To</label>
        <input type="date" name="edu_to[]" required>
      </div>

      <div>
        <label>Year Graduated</label>
        <input name="year_graduated[]" placeholder="Year Graduated" required>
      </div>

      <div>
        <label>Scholarship / Academic Honors</label>
        <input name="honors[]" placeholder="Scholarship / Honors">
      </div>
    </div>
    <button type="button" class="remove-btn" onclick="removeEntry(this, '.education-entry')">✖ Remove</button>
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
}

function addEligibility(data = {}) {
  const container = document.getElementById("eligibility");
  const div = document.createElement("div");
  div.classList.add("eligibility-entry", "eligibility-box");
  div.innerHTML = `
    <div class="eligibility-grid">
      <div>
        <label>Career Service / CSC / CES</label>
        <input name="career_service[]" placeholder="Career Service / CSC / CES" required>
      </div>

      <div>
        <label>Rating</label>
        <input name="rating[]" placeholder="Rating" required>
      </div>

      <div>
        <label>Exam Date</label>
        <input type="date" name="exam_date[]" required>
      </div>

      <div>
        <label>Place of Examination</label>
        <input name="exam_place[]" placeholder="Place of Examination" required>
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
    <button type="button" class="remove-btn" onclick="removeEntry(this, '.eligibility-entry')">✖ Remove</button>
  `;
  container.appendChild(div);

  div.querySelector('[name="career_service[]"]').value = data.career_service || '';
  div.querySelector('[name="rating[]"]').value = data.rating || '';
  div.querySelector('[name="exam_date[]"]').value = data.exam_date || '';
  div.querySelector('[name="exam_place[]"]').value = data.exam_place || '';
  div.querySelector('[name="license[]"]').value = data.license || '';
  div.querySelector('[name="license_number[]"]').value = data.license_number || '';
  div.querySelector('[name="valid_until[]"]').value = data.valid_until || '';
}

function addTraining(data = {}) {
  const container = document.getElementById("training");
  const div = document.createElement("div");
  div.classList.add("training-entry", "training-box");
  div.innerHTML = `
    <div class="training-grid">
      <div>
        <label>Training Title</label>
        <input name="title[]" placeholder="Training Title" required>
      </div>

      <div>
        <label>Hours</label>
        <input name="hours[]" placeholder="Hours" required>
      </div>

      <div>
        <label>From</label>
        <input type="date" name="training_from[]" required>
      </div>

      <div>
        <label>To</label>
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
    <button type="button" class="remove-btn" onclick="removeEntry(this, '.training-entry')">✖ Remove</button>
  `;
  container.appendChild(div);

  div.querySelector('[name="title[]"]').value = data.title || '';
  div.querySelector('[name="hours[]"]').value = data.hours || '';
  div.querySelector('[name="training_from[]"]').value = data.training_from || '';
  div.querySelector('[name="training_to[]"]').value = data.training_to || '';
  div.querySelector('[name="type[]"]').value = data.type || '';
  div.querySelector('[name="sponsor[]"]').value = data.sponsor || '';
}

function removeEntry(button, selector) {
  const items = document.querySelectorAll(selector);
  if (items.length <= 1) {
    return;
  }

  const item = button.closest(selector);
  if (item) {
    item.remove();
    saveFormDraft();
  }
}

function markRequiredLabels() {
  document.querySelectorAll("[required]").forEach(input => {
    let label = null;

    if (input.id) {
      label = document.querySelector(`label[for="${input.id}"]`);
    }

    if (!label) {
      const wrapper = input.closest(".field-pair, .citizenship-pair, .contact-row, .address-row, .address-house-row");
      if (wrapper) {
        label = wrapper.querySelector("label");
      }
    }

    if (!label) {
      const prev = input.previousElementSibling;
      if (prev && prev.tagName === "LABEL") {
        label = prev;
      }
    }

    if (label) {
      label.classList.add("required");
    }
  });
}

document.addEventListener("input", function(e){
  if(!e.target.matches("input, select, textarea")) return;

  const field = e.target;

  if (["umid", "philsys", "pagibig", "tin", "philhealth", "agency_employee"].includes(field.name)) {
    const cleaned = sanitizeIdNumber(field.value);
    if (field.value !== cleaned) {
      field.value = cleaned;
    }
  }

  validateSingleField(field);

  if (field.name === "citizenship") {
    const dualCountryField = form.querySelector('[name="dual_country"]');
    if (dualCountryField) validateSingleField(dualCountryField);
  }

  saveFormDraft();
});

document.addEventListener("change", function(e){
  if(!e.target.matches("input, select, textarea")) return;

  validateSingleField(e.target);

  if (e.target.name === "citizenship") {
    const dualCountryField = form.querySelector('[name="dual_country"]');
    if (dualCountryField) validateSingleField(dualCountryField);
  }

  saveFormDraft();
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

  if (typeof currentSection !== "number" || Number.isNaN(currentSection)) {
    currentSection = 0;
  }

  updateProgress(currentSection);

  const citizenshipField = document.getElementById("citizenship");
  const dualCountryField = document.getElementById("dual_country");

  if (citizenshipField && dualCountryField) {
    if (citizenshipField.value === "Dual Citizen") {
      dualCountryField.disabled = false;
    } else {
      dualCountryField.disabled = true;
      dualCountryField.value = "";
      clearFieldErrorState(dualCountryField);
    }
  }
});

const citizenship = document.getElementById("citizenship");
const dualCountry = document.getElementById("dual_country");

if (citizenship && dualCountry) {
  citizenship.addEventListener("change", function () {
    if (this.value === "Dual Citizen") {
      dualCountry.disabled = false;
    } else {
      dualCountry.disabled = true;
      dualCountry.value = "";
      clearFieldErrorState(dualCountry);
    }

    validateSingleField(this);
    validateSingleField(dualCountry);
    saveFormDraft();
  });
}
</script>

</body>
</html>