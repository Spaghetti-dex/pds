<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About - PDS System</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
:root{
    --primary:#22361e;
    --primary-light:#3d5b35;
    --accent:#8fae8d;
    --bg:#eef3ec;
    --card:rgba(255,255,255,0.72);
    --text:#1f1f1f;
    --muted:#5f6760;
    --border:rgba(34,54,30,0.15);
    --shadow:0 20px 50px rgba(0,0,0,0.15);
    --radius:28px;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins', Arial, Helvetica, sans-serif;
}

html, body{
    width:100%;
    min-height:100%;
}

body{
    position:relative;
    background:
        linear-gradient(135deg, rgba(239,244,238,0.92), rgba(220,235,220,0.88)),
        url("../assets/bg-wave.png") no-repeat center center fixed;
    background-size:cover;
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:30px 18px;
    overflow-x:hidden;
}

/* Decorative blobs */
.bg-orb{
    position:absolute;
    border-radius:50%;
    filter:blur(20px);
    opacity:0.45;
    z-index:0;
    pointer-events:none;
}

.orb-1{
    width:220px;
    height:220px;
    top:8%;
    left:8%;
    background:linear-gradient(135deg, #b7d3b1, #8fae8d);
}

.orb-2{
    width:180px;
    height:180px;
    bottom:10%;
    right:10%;
    background:linear-gradient(135deg, #d8ead1, #9cbc96);
}

.about-container{
    position:relative;
    z-index:1;
    width:100%;
    max-width:920px;
    background:var(--card);
    backdrop-filter:blur(18px);
    -webkit-backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,0.45);
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--shadow);
}

.about-header{
    position:relative;
    padding:34px 28px 26px;
    background:
        linear-gradient(135deg, rgba(34,54,30,0.95), rgba(61,91,53,0.95));
    color:#fff;
    text-align:center;
}

.about-header::after{
    content:"";
    position:absolute;
    inset:auto 0 0 0;
    height:1px;
    background:rgba(255,255,255,0.14);
}

.about-header h1{
    font-size:clamp(1.9rem, 4vw, 2.4rem);
    letter-spacing:2px;
    font-weight:700;
}

.about-header p{
    margin-top:8px;
    font-size:0.95rem;
    color:rgba(255,255,255,0.82);
    font-weight:300;
}

.about-body{
    padding:38px 30px 34px;
    text-align:center;
}

.logo-box{
    width:120px;
    height:120px;
    margin:0 auto 20px;
    border-radius:50%;
    border:4px solid rgba(34,54,30,0.12);
    background:
        #fff url("../assets/rtu_logo.png") no-repeat center center;
    background-size:76%;
    box-shadow:
        0 12px 28px rgba(0,0,0,0.12),
        inset 0 0 0 8px rgba(143,174,141,0.12);
}

.system-badge{
    display:inline-block;
    padding:8px 16px;
    border-radius:999px;
    background:rgba(143,174,141,0.16);
    color:var(--primary);
    font-size:0.84rem;
    font-weight:600;
    margin-bottom:14px;
    border:1px solid rgba(34,54,30,0.08);
}

.system-title{
    font-size:clamp(1.7rem, 3vw, 2.3rem);
    font-weight:700;
    color:var(--primary);
    margin-bottom:14px;
}

.system-desc{
    max-width:700px;
    margin:0 auto;
    font-size:1rem;
    color:var(--muted);
    line-height:1.85;
}

.line{
    width:100%;
    max-width:700px;
    height:1px;
    margin:30px auto;
    background:linear-gradient(to right, transparent, rgba(34,54,30,0.18), transparent);
}

.section-title{
    font-size:1.2rem;
    color:var(--primary);
    margin-bottom:18px;
    font-weight:700;
}

.dev-list{
    list-style:none;
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));
    gap:14px;
    max-width:760px;
    margin:0 auto;
}

.dev-list li{
    background:rgba(233,239,231,0.86);
    padding:16px 14px;
    border-radius:18px;
    font-size:0.97rem;
    font-weight:600;
    color:var(--text);
    border:1px solid rgba(34,54,30,0.08);
    box-shadow:0 8px 18px rgba(34,54,30,0.06);
    transition:transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
}

.dev-list li:hover{
    transform:translateY(-6px);
    box-shadow:0 14px 26px rgba(34,54,30,0.12);
    background:rgba(242,247,241,0.95);
}

.back-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    margin-top:30px;
    padding:14px 28px;
    background:linear-gradient(135deg, var(--accent), #779a75);
    color:#10200f;
    text-decoration:none;
    border-radius:999px;
    font-weight:700;
    font-size:0.95rem;
    box-shadow:0 10px 22px rgba(80,110,75,0.22);
    transition:transform 0.25s ease, box-shadow 0.25s ease, filter 0.25s ease;
}

.back-btn:hover{
    transform:translateY(-3px) scale(1.02);
    box-shadow:0 16px 28px rgba(80,110,75,0.28);
    filter:brightness(1.03);
}

.back-btn:active{
    transform:scale(0.98);
}

.back-btn span{
    font-size:1rem;
}

/* Responsive */
@media (max-width:768px){
    .about-body{
        padding:30px 20px 28px;
    }

    .about-header{
        padding:28px 20px 22px;
    }

    .logo-box{
        width:105px;
        height:105px;
    }

    .system-desc{
        font-size:0.95rem;
        line-height:1.75;
    }
}

@media (max-width:480px){
    body{
        padding:18px 12px;
    }

    .about-container{
        border-radius:22px;
    }

    .dev-list{
        grid-template-columns:1fr;
    }

    .back-btn{
        width:100%;
        max-width:280px;
    }
}
</style>
</head>
<body>

<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>

<div class="about-container">
    <div class="about-header">
        <h1 class="fade-up">ABOUT</h1>
        <p class="fade-up">Personal Data Sheet Management System</p>
    </div>

    <div class="about-body">
        <div class="logo-box pop-in"></div>

        <div class="system-badge fade-up">Rizal Technological University</div>

        <div class="system-title fade-up">PDS System</div>

        <div class="system-desc fade-up">
            This <strong>PDS System</strong> was developed by students of
            <strong>Rizal Technological University</strong>. The platform is designed
            to manage, organize, and maintain personal data sheet records in a more
            efficient, accurate, and user-friendly way.
        </div>

        <div class="line scale-line"></div>

        <div class="section-title fade-up">Developed By</div>

        <ul class="dev-list">
            <li class="card-item">Yasmin Jade Pilapil</li>
            <li class="card-item">Charles Miguel Mayani</li>
            <li class="card-item">Leslie Mangobos</li>
        </ul>

        <a href="../dashboard/dashboard.php" class="back-btn glow-btn">
            <span>←</span> Back to Dashboard
        </a>
    </div>
</div>

<!-- GSAP -->
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const tl = gsap.timeline({ defaults: { ease: "power3.out" } });

    tl.from(".about-container", {
        y: 40,
        opacity: 0,
        duration: 0.9
    })
    .from(".fade-up", {
        y: 24,
        opacity: 0,
        duration: 0.7,
        stagger: 0.12
    }, "-=0.45")
    .from(".pop-in", {
        scale: 0.7,
        opacity: 0,
        duration: 0.7,
        ease: "back.out(1.8)"
    }, "-=0.5")
    .from(".scale-line", {
        scaleX: 0,
        transformOrigin: "center center",
        duration: 0.6
    }, "-=0.35")
    .from(".card-item", {
        y: 30,
        opacity: 0,
        duration: 0.55,
        stagger: 0.12
    }, "-=0.25")
    .from(".glow-btn", {
        y: 20,
        opacity: 0,
        duration: 0.6
    }, "-=0.2");

    gsap.to(".orb-1", {
        x: 20,
        y: -15,
        duration: 5,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut"
    });

    gsap.to(".orb-2", {
        x: -18,
        y: 18,
        duration: 6,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut"
    });

    document.querySelectorAll(".card-item").forEach((item) => {
        item.addEventListener("mouseenter", () => {
            gsap.to(item, {
                scale: 1.03,
                duration: 0.25,
                ease: "power2.out"
            });
        });

        item.addEventListener("mouseleave", () => {
            gsap.to(item, {
                scale: 1,
                duration: 0.25,
                ease: "power2.out"
            });
        });
    });

    const button = document.querySelector(".glow-btn");
    button.addEventListener("mouseenter", () => {
        gsap.to(button, {
            boxShadow: "0 18px 30px rgba(80,110,75,0.35)",
            duration: 0.25
        });
    });

    button.addEventListener("mouseleave", () => {
        gsap.to(button, {
            boxShadow: "0 10px 22px rgba(80,110,75,0.22)",
            duration: 0.25
        });
    });
});
</script>

</body>
</html>