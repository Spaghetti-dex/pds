<?php include "../includes/auth_check.php"; ?>
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
<<<<<<< HEAD
  height:calc(100% - 200px);
=======
  height:calc(100% - 140px);
>>>>>>> main
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

<<<<<<< HEAD
.profile {
  height: 50px;
}

.learning {
  height: 50px;
  margin-left: 5px;
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
=======
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
>>>>>>> main
}

.personal-row{
  display:grid;
<<<<<<< HEAD
  grid-template-columns:1fr auto 140px auto 140px auto 140px;
  justify-content:center;
  align-items:center;
  gap:15px 5px;
  margin-top:20px;
=======
  grid-template-columns:auto 140px auto 140px auto 140px;
  justify-content:center;
  align-items:center;
  gap:15px 20px;
  margin-top:18px;
>>>>>>> main
}

.personal-row.small input,
.personal-row.small select{
  width:140px;
}

<<<<<<< HEAD
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

=======
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

>>>>>>> main
  .personal-row{
    grid-template-columns:1fr;
    align-items:stretch;
  }

<<<<<<< HEAD
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
=======
  .personal-grid input,
  .personal-grid select,
  .personal-row input,
  .personal-row select{
>>>>>>> main
    width:100%;
  }

  .personal-grid label,
  .personal-row label,
<<<<<<< HEAD
  .citizenship-row label,
  .contact-row label,
  .address-house-row label,
  .address-row label{
    text-align:left;
  }
=======
  .grid label{
    text-align:left;
  }

  input[name="dual_country"]{
    min-width:100%;
  }
>>>>>>> main
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
<<<<<<< HEAD
      <img src="../assets/profile.png" alt="Profile" class="nav-icon profile">
=======
      <img src="../assets/profile.png" alt="Profile" class="nav-icon">
>>>>>>> main
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
<<<<<<< HEAD
      <img src="../assets/learning.png" alt="Training" class="nav-icon learning">
=======
      <img src="../assets/learning.png" alt="Training" class="nav-icon">
>>>>>>> main
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

<<<<<<< HEAD
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
=======
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

>>>>>>> main
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
<<<<<<< HEAD
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

=======
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
>>>>>>> main
          </div>
        </div>

        <!-- CONTACT -->
        <div id="contact" class="section">
<<<<<<< HEAD
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
=======
          <div class="title">CONTACT INFORMATION</div>

          <div class="grid">
            <label>Telephone No.:</label>
            <input name="telephone">

            <label>Mobile No.:</label>
            <input name="mobile">

            <label>Email:</label>
            <input type="email" name="email">
>>>>>>> main
          </div>
        </div>

        <!-- EDUCATION -->
        <div id="education" class="section">
          <div class="title">EDUCATIONAL BACKGROUND</div>

          <div id="education-container">
<<<<<<< HEAD
            <div class="education-entry education-box">
              <div class="education-grid">
=======
            <div class="education-entry entry-box">
              <div class="entry-grid">
>>>>>>> main
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
<<<<<<< HEAD
                  <label>Basic Education /Degree /Course</label>
=======
                  <label>Basic Education / Degree / Course</label>
>>>>>>> main
                  <input name="course[]" placeholder="Course / Degree">
                </div>

                <div>
<<<<<<< HEAD
                  <label>Highest Level /Units Earned</label>
=======
                  <label>Highest Level / Units Earned</label>
>>>>>>> main
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
<<<<<<< HEAD
                  <label>Scholarship /Academic Honors</label>
=======
                  <label>Scholarship / Academic Honors</label>
>>>>>>> main
                  <input name="honors[]" placeholder="Scholarship / Honors">
                </div>
              </div>

<<<<<<< HEAD
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEducation()">Add More</button>
=======
              <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" onclick="addEducation()">Add More</button>
>>>>>>> main
        </div>

        <!-- ELIGIBILITY -->
        <div id="eligibility-section" class="section">
          <div class="title">SERVICE ELIGIBILITY</div>

          <div id="eligibility">
<<<<<<< HEAD
            <div class="eligibility-entry eligibility-box">
              <div class="eligibility-grid">
                <div>
                  <label>Career Service /CSC /CES</label>
=======
            <div class="eligibility-entry entry-box">
              <div class="entry-grid">
                <div>
                  <label>Career Service / CSC / CES</label>
>>>>>>> main
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

<<<<<<< HEAD
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addEligibility()">Add More</button>
=======
              <button type="button" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" onclick="addEligibility()">Add More</button>
>>>>>>> main
        </div>

        <!-- TRAINING -->
        <div id="training-section" class="section">
          <div class="title">LEARNING AND DEVELOPMENT</div>

          <div id="training">
<<<<<<< HEAD
            <div class="training-entry training-box">
              <div class="training-grid">
=======
            <div class="training-entry entry-box">
              <div class="entry-grid">
>>>>>>> main
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

<<<<<<< HEAD
              <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
            </div>
          </div>

          <button type="button" class="add-btn" onclick="addTraining()">Add More</button>
        </div>

        <div class="nav-buttons">
          <button type="button" class="next-btn" id="nextBtn" onclick="nextSection()">Next</button>
          <button type="submit" class="save-btn" id="saveBtn" style="display:none;">Save</button>
        </div>
=======
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
>>>>>>> main
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
<<<<<<< HEAD

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
=======
  document.getElementById("progressLine").style.height = (index * 80) + "px";
  currentSection = index;
>>>>>>> main
}

function goToSection(index){
  updateProgress(index);
}

function nextSection(){
  if(currentSection < sections.length - 1){
    updateProgress(currentSection + 1);
  }
}

<<<<<<< HEAD
function addEducation() {
  const container = document.getElementById("education-container");
  const div = document.createElement("div");
  div.classList.add("education-entry", "education-box");
  div.innerHTML = `
    <div class="education-grid">
=======
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
>>>>>>> main
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
<<<<<<< HEAD
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
=======
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
>>>>>>> main
  `;
  container.appendChild(div);
}

function addEligibility() {
  const container = document.getElementById("eligibility");
  const div = document.createElement("div");
<<<<<<< HEAD
  div.classList.add("eligibility-entry", "eligibility-box");
  div.innerHTML = `
    <div class="eligibility-grid">
=======
  div.classList.add("eligibility-entry", "entry-box");
  div.innerHTML = `
    <div class="entry-grid">
>>>>>>> main
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
<<<<<<< HEAD
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
=======
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
>>>>>>> main
  `;
  container.appendChild(div);
}

function addTraining() {
  const container = document.getElementById("training");
  const div = document.createElement("div");
<<<<<<< HEAD
  div.classList.add("training-entry", "training-box");
  div.innerHTML = `
    <div class="training-grid">
=======
  div.classList.add("training-entry", "entry-box");
  div.innerHTML = `
    <div class="entry-grid">
>>>>>>> main
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
<<<<<<< HEAD
    <button type="button" class="remove-btn" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}

updateProgress(0);
=======
    <button type="button" onclick="this.parentElement.remove()">Remove</button>
  `;
  container.appendChild(div);
}
>>>>>>> main
</script>

</body>
</html>