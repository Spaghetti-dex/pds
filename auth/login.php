<form method="POST" action="login_process.php">

</form>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<!-- FONT AWESOME FOR EYE ICON -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-image: url('../assets/background.jpg');
    background-size: cover;
    font-family: Arial, Helvetica, sans-serif;
}

/* LOGIN CARD */

.login-container{
    width:420px;
    background:#d9d9dd;
    border:3px solid black;
    border-radius:25px;
    overflow:hidden;
    text-align:center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
}

.login-container:hover {
    transform: translateY(-5px);
}

/* TOP HEADER */

.login-header{
    background:#2f3f28;
    color:white;
    padding:40px 0 60px 0;
    font-size:30px;
    font-weight:bold;
    letter-spacing:2px;
    position:relative;
    border-bottom: 3px solid black;
}

/* curved shape */

.login-header::after{
    content:"";
    position:absolute;
    bottom:-25px;
    left:0;
    width:100%;
    height:60px;
    background:#d9d9dd;
    border-top-left-radius:50% 40px;
    border-top-right-radius:50% 40px;
}

/* FORM AREA */

.form-area{
    padding:40px;
}

.form-group{
    margin-bottom:20px;
    display:flex;
    align-items:center;
    justify-content:center;
}

label{
    width:100px;
    font-weight:bold;
    text-align:right;
    margin-right:10px;
}

input{
    width:200px;
    padding:10px;
    border-radius:8px;
    border:2px solid #888;
    background:#efe8c2;
    transition: border-color 0.3s;
}

input:focus {
    border-color:#2f3f28;
    outline:none;
}

/* LOGIN BUTTON */

button{
    margin-top:10px;
    padding:10px 25px;
    border:none;
    border-radius:20px;
    background:#8fae8d;
    font-weight:bold;
    cursor:pointer;
    transition: background 0.3s, transform 0.2s;
}

button:hover{
    background:#789c78;
    transform: scale(1.05);
}

/* FORGOT PASSWORD */

.forgot{
    margin-top:8px;
    font-size:12px;
}

.forgot a{
    color:black;
    text-decoration:underline;
    transition: color 0.3s;
}

.forgot a:hover{
    color:red;
}

.forgot a:active{
    color:red;
}

/* PASSWORD FIELD */

.password-box{
    position: relative;
    width: 200px;
}

.password-box input{
    width:100%;
    padding-right:35px; /* space for the eye */
}

.eye{
    position:absolute;
    right:10px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    font-size:16px;
    color:#333;
    transition: color 0.3s;
}

.eye:hover {
    color:#2f3f28;
}

</style>
</head>

<body>

<div class="login-container">

    <div class="login-header">
        LOGIN
    </div>

    <div class="form-area">

        <form method="POST" action="login_process.php">

        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group password-group">
            <label>Password:</label>

            <div class="password-box">
                <input type="password" id="id_password" name="password" required>
                <i class="fa-solid fa-eye-slash eye" id="togglePassword"></i>
            </div>
        </div>

        <button type="submit">Login</button>

        <div class="forgot">
            <a href="forgot.php">Forgot Password?</a>
        </div>

        </form>

    </div>

</div>

<script>
const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("id_password");

togglePassword.addEventListener("click", function () {

    if (password.type === "password") {
        password.type = "text";
        this.classList.remove("fa-eye-slash");
        this.classList.add("fa-eye");
    } 
    else {
        password.type = "password";
        this.classList.remove("fa-eye");
        this.classList.add("fa-eye-slash");
    }

});
</script>

</body>
</html>