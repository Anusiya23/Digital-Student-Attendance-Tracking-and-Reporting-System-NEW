<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Student Attendance Tracking and Reporting System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
        }

        /* Top Navbar */
        .topbar {
    width: 100%;
    background: linear-gradient(135deg, #71b7e6, #9b59b6);
    color: #fff;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 25px;
        }


.nav-links {
    margin-left:900px;
    margin-top: 50px;
    
}

.nav-links a {
    color: black;
    text-decoration: none;
    margin-left: 20px;
    font-weight: bold;
    font-size: 22px;
}

.nav-links a:hover {
    text-decoration: underline;
    
}

  /* Hero with slideshow */
        .hero {
            margin-top: 5px;
            height: 80vh;
            position: relative;
            overflow: hidden;
        }

        .slides {
            position: absolute;
            margin-left:180px;
            width: 70%;
            height: 100%;
            animation: slideShow 60s infinite;
            animation-delay: 60s;
            
        }

        .slides img {
            width: 70%;
            height: 100%;
            margin-left:220px;
            margin-top:30px;
            object-fit:inherit;
            position: absolute;
            opacity: 0;
            animation: fade 15s infinite;
        }

        .slides img:nth-child(1) { animation-delay: 0s; }
        .slides img:nth-child(2) { animation-delay: 5s; }
        .slides img:nth-child(3) { animation-delay: 10s; }

        @keyframes fade {
            0%, 100% { opacity: 0; }
            20%, 40% { opacity: 1; }
            60%, 80% { opacity: 0; }
        }

        .hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            top: 30%;
            color: white;
            font-size: 2.5em;
            font-weight: bold;
            text-shadow: 2px 2px 4px #000;
        }

        /* About Section */
        #about {
            padding: 60px 20px;
            background-color: #f4f4f4;
            text-align: center;
            
        }

        #about h2 {
            font-size: 36px;
            margin-bottom: 20px;
           
        }

        #about p {
            font-size: 25px;
            max-width: 900px;
            margin: 0 auto;
           
        }

        /* Footer (Contact Section) */
        footer {
            background: linear-gradient(135deg, #71b7e6, #9b59b6);
            color: white;
            padding: 40px 20px;
            text-align: center;
            font-size: 25px;
          
        }

        footer h2 {
            margin-bottom: 15px;
        }

        /* Buttons for login/register */
        .hero-buttons {
            margin-top: 20px;
        }

        .hero-buttons a {
            text-decoration: none;
            background-color: #fff;
            color: #333;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 1.2em;
            margin: 10px;
            transition: background 0.3s;
        }

        .hero-buttons a:hover {
            background-color: plum;
        }
        /* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 40px;
    right: 30px;
    background-color:#71b7e6;
    color: white;
    font-size: 30px;
    padding: 10px 15px;
    border-radius: 50%;
    text-decoration: none;
    display: none;
    z-index: 1001;
    transition: background 0.3s;
}

.back-to-top:hover {
    background-color:black;
}

        
    </style>
</head>
<body>
  <!-- College Header Section -->
  <div style="display: flex;line-height: 30px; align-items: center; justify-content: center; background: white; padding: 10px 30px; border-bottom: 2px solid #ddd;margin-top: 10px">
    <div style="display: flex; align-items: center;">
        <img src="img/vicas_logo.jpg" alt="VICAS Logo" style="height: 190px; margin-right: 20px;">
        <div style="text-align:center;">
            <h1 style="margin: 0; font-size: 45px; color: #e91e63; font-weight: bold;letter-spacing:5px;">VIVEKANANDHA</h1>
            <p style="margin: 0; font-size: 23px; color: #000;line-height: 35px;">
                College of Arts and Sciences for Women<br>
                (Autonomous)<br></p>
                <p style="margin: 0; font-size: 20px;color:midnightblue">
                'A+' Grade by NAAC || ISO 9001:2015 Certified<br>
                DST-FIST & DST-PG CURIE Sponsored<br>
                Approved by UGC Act 1956 under Section 2(f)&12(B) and AICTE<br>
                Affiliated to Periyar University, Salem
            </p>
        </div>
    </div>
</div>



    <!-- Topbar -->
    <div class="topbar">
    <h1 >Digital Student Attendance Tracking and Reporting System</h1>
</div>
<div class="nav-links">
        <a href="#home">Home</a>
        <a href="#about">About Us</a>
        <a href="login/login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="#contact">Contact </a>
    </div>

    <!-- Hero Section -->
    <div class="hero" id="home">
        <div class="slides">
            <img src="img/home1.jpg" alt="Slide 1" >
            <img src="img/home.jpg" alt="Slide 2">
            <img src="img/home4.jpg" alt="Slide 3">
        </div>
       
    </div>

    <!-- About Us Section -->
    <section id="about">
        <h2>About Us</h2>
        <p>
            The Digital Student Attendance Tracking and Reporting System (DSATRS) is a smart platform that streamlines attendance management. It enables admins to control classes, faculty, and subjects, allows faculty to mark, review attendance and generating reports and lets students monitor their attendance records. The system supports real-time tracking and detailed reports, improving transparency and efficiency in academic environments.
        </p>
    </section>

    <!-- Footer -->
    <footer id="contact">
        <h2>Contact </h2>
        <p>Email: anusiyaarumugam23@gmail.com</p>
        <p>Phone: +91-9360770089</p>
        <p>Address: 3/632,Amman Nagar,Subrayan Nagar,58-Kailasampalayam,Tiruchengode,Namakkal(DT),Tamil Nadu.</p>
    </footer>
    <!-- Back to Top Button -->
<a href="#home" class="back-to-top" title="Back to Top">&#8679;</a>
<script>
    // Show button when user scrolls down
    window.onscroll = function() {
        const topBtn = document.querySelector('.back-to-top');
        if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
            topBtn.style.display = "block";
        } else {
            topBtn.style.display = "none";
        }
    };
</script>

</body>
</html>
