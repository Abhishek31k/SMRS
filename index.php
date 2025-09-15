<?php
error_reporting(0);
include('includes/config.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Sir CRR College of Engineering - Student Result Management System" />
  <meta name="author" content="SRMS" />
  <title>SRMS - Sir CRR College of Engineering</title>
  <link rel="icon" type="image/x-icon" href="images/crrengglogo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="includes/crrengglogo.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <!-- Google Material Symbols (for arrow icons) -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link rel="stylesheet" href="css/style.css">

</head>
<body>

<!-- Top Social + Announcement Bar -->
<div class="d-flex justify-content-between align-items-center p-2" style="background: #dc3545; color: white;">
  
  <!-- Left: Announcement with Date -->
  <div class="announcement-text" style="flex:1;">
    <marquee behavior="scroll" direction="left" scrollamount="5">
        📢  Today: <?php echo date('l, d M Y'); ?>
    Admission Open 2025-26 | Apply Now for B.Tech, MBA,at SIR CRR College of Engineering
     
    </marquee>
  </div>

  <!-- Right: Social Icons -->
  <div class="top-social">
    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
    <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
    <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
    <a href="#" class="text-white"><i class="fab fa-linkedin-in"></i></a>
  </div>
</div>




  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex flex-column" href="index.php">
        <div class="d-flex align-items-center">
          <img src="images/Sir CRR College of Engineering.png" alt="Logo">
       
        </div>
        
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
        aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 fw-semibold">
          <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="login.php">Students</a></li>
          <li class="nav-item"><a class="nav-link" href="staff-login.php">Staff</a></li>
          <li class="nav-item"><a class="nav-link" href="admin-login.php">Admin</a></li>
        </ul>
        <form class="d-flex ms-3 search-form" action="find-result.php" method="get">
          <input class="form-control" type="search" placeholder="Search..." name="q" aria-label="Search">
          <button type="submit"><i class="fas fa-search"></i></button>
        </form>
      </div>
    </div>
  </nav>

<!-- Hero Section -->
<!-- Hero Section -->
<header class="hero-section">
  <div class="overlay"></div>
  <div class="hero-content text-center">
    <h1>
      Welcome to <br />
      <span id="hero-text">SIR CRR College of Engineering</span>
    </h1>
    <p>Empowering Future Engineers with Knowledge, Innovation & Research</p>

    <div class="hero-cta mt-4">
      <a href="admissions.php" class="btn btn-danger btn-lg me-2 shadow-lg">Apply Now</a>
      <a href="#about-section py-5" class="btn btn-outline-light btn-lg shadow-lg">Learn More</a>
    </div>

    <!-- Stats -->
    <div class="hero-stats mt-5 d-flex justify-content-center flex-wrap gap-4">
      <div class="stat-card">
        <h3 class="counter" data-target="6000">0</h3>
        <p>Students</p>
      </div>
      <div class="stat-card">
        <h3 class="counter" data-target="9">0</h3>
        <p>Branches</p>
      </div>
      <div class="stat-card">
        <h3 class="counter" data-target="150">0</h3>
        <p>Staff</p>
      </div>
      <div class="stat-card">
        <h3 class="counter" data-target="50">0</h3>
        <p>Companies</p>
      </div>
    </div>
  </div>
</header>

<!-- Result Link Section -->
<section class="py-5 text-center" style="background: #22216a;">
    <div class="container" style="
    width: 80%;
    background: cornsilk;
    padding: 20px;
    border-radius: 15px;
">
        <h1 class="mb-3">Sir C.R. Reddy College of Engineering</h1>
        <p class="mb-4">Check your B.Tech/MBA results here. Stay updated with the latest semester outcomes and academic performance.</p>

        <?php
        // Release date (match with JS)
        $releaseDate = "2025-12-15 00:29:00";
        $today = date("Y-m-d H:i:s");

        if ($today >= $releaseDate) {
            // ✅ After release date → Active button
            echo '<a href="find-result.php" target="_blank" class="btn btn-primary btn-lg">
                    📄 Check Your Result
                  </a>';
        } else {
            // ❌ Before release date → Disabled button + countdown placeholder
            echo '<a href="#" class="btn btn-secondary btn-lg disabled" tabindex="-1" aria-disabled="true">
                    📄 Check Your Result (Coming Soon)
                  </a>';
            echo '<p id="countdown" class="mt-3 text-danger fw-bold"></p>';
        }
        ?>
    </div>
</section>


<!-- About Section -->
<section class="about-section py-5">
  <div class="container">
    <div class="row align-items-center">
      
      <!-- Image -->
      <div class="col-md-6 mb-4 mb-md-0">
        <div class="about-image-wrapper">
          <img src="images/C.R.Reddy_College_Aerial_View.jpg" 
               alt="CRR College Campus" 
               class="img-fluid rounded shadow-lg">
        </div>
      </div>

      <!-- Text Content -->
      <div class="col-md-6">
        <div class="about-content">
          <h2>About CRR College of Engineering</h2>
          <p>
            Sir CRR College of Engineering is committed to providing quality 
            technical education and research opportunities. Our college fosters 
            innovation, creativity, and practical knowledge that empowers students 
            to excel in their careers.
          </p>
          <p>
            With modern labs, experienced faculty, and industry collaborations, 
            we ensure holistic development for every student. Our vision is to 
            create leaders who contribute to society and the technological world.
          </p>
          <a href="about.php" class="btn btn-danger mt-3">Learn More</a>
        </div>
      </div>

    </div>
  </div>
</section>



<!-- Vision & Mission Section -->
<section class="vision-mission py-5">
  <div class="container">
    <h2 class="text-center section-title">Our Vision & Mission</h2>
    <div class="timeline">

      <!-- Vision -->
      <div class="timeline-item left">
        <div class="timeline-icon">
          <i class="fas fa-eye"></i>
        </div>
        <div class="timeline-content">
          <h5>Our Vision</h5>
          <p>
            To emerge as a premier institution in the field of technical education 
            and research in the state and as a home for holistic development of the 
            students and contribute to the advancement of society and the region.
          </p>
        </div>
      </div>

      <!-- Mission -->
      <div class="timeline-item right">
        <div class="timeline-icon">
          <i class="fas fa-bullseye"></i>
        </div>
        <div class="timeline-content">
          <h5>Our Mission</h5>
          <p>
            To provide high quality technical education through a creative balance 
            of academic and industry oriented learning; to create an inspiring 
            environment of scholarship and research; to instill high levels of 
            academic and professional discipline; and to establish standards that 
            inculcate ethical and moral values that contribute to growth in career 
            and development of society in general.
          </p>
        </div>
      </div>

      <!-- Example Extra (Core Values) -->
      <div class="timeline-item left">
        <div class="timeline-icon">
          <i class="fas fa-heart"></i>
        </div>
        <div class="timeline-content">
          <h5>Our Core Values</h5>
          <p>
            Integrity, Excellence, Innovation, Teamwork, and Social Responsibility.
          </p>
        </div>
      </div>

    </div>
  </div>
</section>


<!-- Alumni / Testimonial Section -->
<section class="alumni-section py-5">
  <div class="container">
    <h2 class="section-title text-center mb-5">What Our Alumni Say</h2>

    <div id="alumniCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">

        <!-- Alumni 1 -->
        <div class="carousel-item active">
          <div class="testimonial-card text-center">
            <img src="images/Cuttamanchi(Kattamanchi)_Ramalinga_Reddi.jpg" class="alumni-img" alt="Alumni 1">
            <h5 class="alumni-name">Sir Cattamanchi Ramalinga Reddy</h5>
            <p class="alumni-quote">Sir Cattamanchi Ramalinga Reddy (10 December 1880 – 24 February 1951), also popularly known as Sir C. R. Reddy, was an educationist and political thinker, essayist and economist, poet and literary critic.[1] He was a prominent member of the Justice Party[2] and an ardent champion of the non-Brahmin movement,[3] joining the movement to unite the non-Brahmin communities.[4] He wrote his works in Telugu and English; these reveal his deep love for Indian classics and his learning in these texts, as well as the modernity of his outlook.</p>
          </div>
        </div>

        <!-- Alumni 2 -->
        <div class="carousel-item">
          <div class="testimonial-card text-center">
            <img src="images/correspondent1.jpg" class="alumni-img" alt="Alumni 2">
            <h5 class="alumni-name">Sri Jasti Mallikharjunudu</h5>
            <p class="alumni-quote">I deem it a great privilege to be an essential part of this magnanimous institution. As correspondent of Sir C.R.Reddy College of Engineering, I strongly support the cause of professional courses in the technologically advanced world. Engineering, in particular, is a course that demands innovation and logical thinking and subjects are to be learnt at the application level. The ultimate aim of engineering as a study is to make human life easy and pleasurable. So, it is our duty to hone the talents of the young students who join the institution and make them ready for job and ready for life. Every student must be made to remember that in order to survive in the job market governed by cut throat competition, he should be one among the best. The institution must mould the student and give him an edge over others in attaining success.</p>
          </div>
        </div>

        <!-- Alumni 3 -->
        <div class="carousel-item">
          <div class="testimonial-card text-center">
            <img src="images/I-C_PRINCIPAL.jpeg" class="alumni-img" alt="Alumni 3">
            <h5 class="alumni-name">Dr.K.Venkateswa Rao</h5>
            <p class="alumni-quote">I am extremely pleased to write this message for the viewers of our institute's website. Engineers are not only the people who know how to do things right but who know the right things to do. We at 'Sir C.R.Reddy' try to focus on engineering education to keep pace with mighty changes occurring today in industry, business and society.

We do not believe that effective education can be achieved by what we teach but we base on what our students learn. The ideal learning process is the interaction of students with faculty. Hence it is our responsibility to optimise the student faculty interface.

We encourage and facilitate leadership qualities in our faculty and students. For this engineering students shall develop required attributes and knowledge beyond traditional constraints of class rooms. An engineering student shall realize that he has to create connections and stronger partnerships in the global society in which he is embedded.

I am glad to say that the college website is resourceful in numerous ways. Students and visitors who surf through the portal derive lot of benefit and it navigates them to unique educational portals also.</p>
          </div>
        </div>
      </div>

      <!-- Controls -->
      <button class="carousel-control-prev" type="button" data-bs-target="#alumniCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#alumniCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</section>



<!-- Courses Section -->
<section class="courses-section py-5">
  <div class="container">
    <!-- Section Title -->
     <p class="section-titles">Learn more about the courses we offer</p>
    <h2 class="section-title">Our Departments</h2>

    <div class="swiper mt-4">
      <div class="card-wrapper">
        <!-- Card slides container -->
        <ul class="card-list swiper-wrapper">

          <!-- CSE -->
          <li class="card-item swiper-slide" style="margin-right: 0px;">
            <a href="#" class="card-link">
              <img src="images/cse.jpeg" alt="CSE" class="card-image">
              <p class="badge badge-designer">Computer Science Engineering</p>
              <h2 class="card-title">Explore Computer Science Engineering</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

          <!-- IT -->
          <li class="card-item swiper-slide">
            <a href="#" class="card-link">
              <img src="images/developer.jpg" alt="IT" class="card-image">
              <p class="badge badge-developer">Information Technology</p>
              <h2 class="card-title">Discover Information Technology</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

          <!-- EEE -->
          <li class="card-item swiper-slide">
            <a href="#" class="card-link">
              <img src="images/eee.jpeg" alt="EEE" class="card-image">
              <p class="badge badge-marketer">Electrical and Electronics Engineering</p>
              <h2 class="card-title">Learn Electrical & Electronics Engineering</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

          <!-- ECE -->
          <li class="card-item swiper-slide">
            <a href="#" class="card-link">
              <img src="images/Exploring Embedded Systems_ Definition, Applications, and More.jpeg" alt="ECE" class="card-image">
              <p class="badge badge-gamer">Electronics and Communication Engineering</p>
              <h2 class="card-title">Advance in Electronics & Communication</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

          <!-- Mechanical -->
          <li class="card-item swiper-slide">
            <a href="#" class="card-link">
              <img src="images/A Beginner’s Guide to Building the Internet of Things Platform.jpeg" alt="Mechanical" class="card-image">
              <p class="badge badge-editor">Mechanical Engineering</p>
              <h2 class="card-title">Build the Future with Mechanical Engineering</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

          <!-- Civil -->
          <li class="card-item swiper-slide">
            <a href="#" class="card-link">
              <img src="images/civil.jpeg" alt="Civil" class="card-image">
              <p class="badge badge-editor">Civil Engineering</p>
              <h2 class="card-title">Shape the World with Civil Engineering</h2>
              <button class="card-button material-symbols-rounded">arrow_forward</button>
            </a>
          </li>

        </ul>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>

        <!-- Navigation Buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>
    </div>
  </div>
</section>




<!-- Placement / Recruiters Section -->
<section class="placement-section py-5" data-aos="fade-up">
  <div class="container">
    <h2 class="text-center text-danger mb-5">Our MoU's with over 200+ companies</h2>
    <p class="text-center text-success">Join more than 200 companies that have already hired our students.</p>
    <div class="logo-marquee">
      <div class="marquee-track">
        <div class="marquee-item"><img src="images/amazon.png" alt="Company 1"></div>
        <div class="marquee-item"><img src="images/flip.png" alt="Company 2"></div>
        <div class="marquee-item"><img src="images/Walmart_logo_(2008).svg.png" alt="Company 3"></div>
        <div class="marquee-item"><img src="images/Wipro-logo.png" alt="Company 4"></div>
        <div class="marquee-item"><img src="images/capgemini.png" alt="Company 5"></div>
        <div class="marquee-item"><img src="images/Tata_Consultancy_Services_old_logo.svg.png" alt="Company 6"></div>
        <!-- Duplicate logos for seamless scrolling -->
        <div class="marquee-item"><img src="images/png-transparent-infosys-logo.png" alt="Company 1"></div>
        <div class="marquee-item"><img src="images/HCLTECH.NS-a301c3b4.png" alt="Company 2"></div>
        <div class="marquee-item"><img src="images/lt.png" alt="Company 3"></div>
        <div class="marquee-item"><img src="images/Mahindra-Mahindra-Logo-2012.png" alt="Company 4"></div>
        <div class="marquee-item"><img src="images/company5.png" alt="Company 5"></div>
        <div class="marquee-item"><img src="images/company6.png" alt="Company 6"></div>
      </div>
    </div>
  </div>
</section>


<!-- Notice Board Section -->
<section class="notice-section py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-lg border-0">
          <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">📌 Notice Board</h4>
            <span class="small">Latest Updates</span>
          </div>
          <div class="card-body p-0">
            <div class="notice-scroll">
              <ul>
                <?php 
                $sql = "SELECT * from tblnotice ORDER BY id DESC";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                if($query->rowCount() > 0) {
                  foreach($results as $result) { ?>                      
                    <li>
                      <a href="notice-details.php?nid=<?php echo htmlentities($result->id);?>" target="_blank">
                        <i class="bi bi-megaphone-fill text-danger me-2"></i>
                        <?php echo htmlentities($result->noticeTitle); ?>
                        <span class="date text-muted">(<?php echo htmlentities($result->postingDate); ?>)</span>
                      </a>
                    </li>
                <?php }} ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


  <!-- Campus Gallery Section -->
<section class="campus-gallery py-5">
  <div class="container">
    <h2 class="text-center text-danger mb-5">Campus Gallery</h2>
    <div class="row g-3">
      <!-- Gallery Item 1 -->
      <div class="col-sm-6 col-md-4">
        <a href="images/C.R.Reddy_College_Aerial_View.jpg" data-lightbox="campus-gallery" data-title="Campus Building">
          <img src="images/C.R.Reddy_College_Aerial_View.jpg" alt="Campus Building" class="img-fluid rounded shadow-sm gallery-img">
        </a>
      </div>
      
      <!-- Gallery Item 6 -->
      <div class="col-sm-6 col-md-4">
        <a href="images/crr.png" data-lightbox="campus-gallery" data-title="Auditorium">
          <img src="images/crr.png" alt="Auditorium" class="img-fluid rounded shadow-sm gallery-img">
        </a>
      </div>
      <!-- Gallery Item 6 -->
      <div class="col-sm-6 col-md-4">
        <a href="images/campu.jpg" data-lightbox="campus-gallery" data-title="Auditorium">
          <img src="images/campu.jpg" alt="Auditorium" class="img-fluid rounded shadow-sm gallery-img">
        </a>
      </div>
      <!-- Gallery Item 6 -->
      <div class="col-sm-6 col-md-4">
        <a href="images/campu2.jpg" data-lightbox="campus-gallery" data-title="Auditorium">
          <img src="images/campu2.jpg" alt="Auditorium" class="img-fluid rounded shadow-sm gallery-img">
        </a>
      </div>
    </div>
  </div>
</section>

 <!-- Contact Section -->
<section class="contact-section py-5">
  <div class="container">
    <!-- Heading -->
    <div class="row justify-content-center mb-4">
      <div class="col-lg-8 text-center">
        <h2 class="section-title">📍 Contact Us</h2>
        <p class="text-muted">Reach out to Sir CRR College of Engineering for any queries, support, or information.</p>
      </div>
    </div>

    <!-- Contact Info Cards -->
    <div class="row g-4 justify-content-center mb-5">
      <!-- Address -->
      <div class="col-md-4">
        <div class="contact-card shadow-sm p-4 text-center h-100">
          <i class="bi bi-geo-alt-fill text-danger display-5 mb-3"></i>
          <h5 class="fw-bold">Address</h5>
          <p>Sir CRR College of Engineering,<br>Eluru, Andhra Pradesh</p>
        </div>
      </div>
      <!-- Email -->
      <div class="col-md-4">
        <div class="contact-card shadow-sm p-4 text-center h-100">
          <i class="bi bi-envelope-fill text-danger display-5 mb-3"></i>
          <h5 class="fw-bold">Email</h5>
          <p><a href="mailto:info@crrce.edu.in">info@crrce.edu.in</a></p>
        </div>
      </div>
      <!-- Phone -->
      <div class="col-md-4">
        <div class="contact-card shadow-sm p-4 text-center h-100">
          <i class="bi bi-telephone-fill text-danger display-5 mb-3"></i>
          <h5 class="fw-bold">Phone</h5>
          <p><a href="tel:+911234567890">+91-1234567890</a></p>
        </div>
      </div>
    </div>

    <!-- Google Map -->
    <div class="row mb-5">
      <div class="col-12">
        <h2>Street View & 360°</h2>
        <div class="map-container shadow-sm">
          <iframe src="https://www.google.com/maps/embed?pb=!4v1757614453445!6m8!1m7!1sCAoSFkNJSE0wb2dLRUlDQWdJQzRfTkhqSGc.!2m2!1d16.69498723981428!2d81.05049109043836!3f168.31595288476012!4f-2.830598890911091!5f0.7820865974627469" width="1100" height="450" style="border-radius:20px; margin-left:auto; margin-right:auto;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>

   <form action="send-message.php" method="POST">
  <div class="row g-3">
    <div class="col-md-6">
      <input type="text" name="name" class="form-control" placeholder="Your Name" required>
    </div>
    <div class="col-md-6">
      <input type="email" name="email" class="form-control" placeholder="Your Email" required>
    </div>
    <div class="col-12">
      <input type="text" name="subject" class="form-control" placeholder="Subject" required>
    </div>
    <div class="col-12">
      <textarea name="message" rows="5" class="form-control" placeholder="Your Message" required></textarea>
    </div>
    <div class="col-12 text-center">
      <button type="submit" class="btn btn-danger px-5" name="send">Send Message</button>
    </div>
  </div>
</form>


  </div>
</section>


<!-- Footer -->
<footer class="text-center">
  <div class="container">
    <p class="m-0">© 2025 Sir CRR College of Engineering - Student Result Management System</p>
    <p class="m-0 mt-1 text-warning fw-semibold" style="font-size:0.9rem;">
      Officially developed & Designed by the Computer Science and Engineering Students - 2026 Batch
    </p>
    <div class="mt-2">
      <a href="#" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
      <a href="#"><i class="fab fa-facebook-f"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
      <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>
  </div>
</footer>


<!-- Privacy Policy Modal -->
<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-start" style="max-height:70vh; overflow-y:auto;">
        
        <p><strong>Last Updated: September 13, 2025</strong></p>
        <p>
          At <b>Sir C.R. Reddy College of Engineering</b>, we value your privacy and are committed to protecting the personal information of our students, staff, and visitors. This Privacy Policy explains how we collect, use, and safeguard your data.
        </p>

        <h6>1. Information We Collect</h6>
        <p>We may collect personal details such as your name, registration number, contact information, academic records, and other data required for academic and administrative purposes.</p>

        <h6>2. How We Use Your Information</h6>
        <ul>
          <li>To manage student admissions, academic records, and examinations.</li>
          <li>To provide access to results and notifications.</li>
          <li>To communicate important academic and institutional updates.</li>
          <li>To improve our services and maintain smooth operations.</li>
        </ul>

        <h6>3. Data Protection</h6>
        <p>
          We implement strict security measures to protect personal data against unauthorized access, misuse, loss, or alteration. Sensitive information like academic results is displayed securely and only to authorized users.
        </p>

        <h6>4. Sharing of Information</h6>
        <p>
          Your data will never be sold or shared with third parties for commercial purposes. It may only be shared with regulatory authorities, affiliating university, or accreditation bodies when legally required.
        </p>

        <h6>5. Cookies & Tracking</h6>
        <p>
          Our website may use cookies to enhance user experience and analyze traffic. You may disable cookies in your browser settings, but some features may not work properly.
        </p>

        <h6>6. Your Rights</h6>
        <p>
          You have the right to access, update, or request deletion of your personal data. Such requests can be made by contacting the college administration.
        </p>

        <h6>7. Policy Updates</h6>
        <p>
          This Privacy Policy may be revised from time to time. Updates will be posted on our official website.
        </p>

        <h6>8. Contact Us</h6>
        <p>
          For any questions regarding this Privacy Policy, please contact:<br>
          <b>Sir C.R. Reddy College of Engineering</b><br>
          Eluru, Andhra Pradesh, India<br>
          📧 Email: info@scrrengg.ac.in | ☎ Phone: +91-XXXXXXXXXX
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/scripts.js"></script>
<script>
 document.addEventListener("DOMContentLoaded", function () {
    // Release date → 13th Sept 2025, 12:29 AM
    var releaseDate = new Date("2025-12-15T00:29:00").getTime();

    var countdown = setInterval(function () {
        var now = new Date().getTime();
        var distance = releaseDate - now;

        if (distance > 0) {
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            var cd = document.getElementById("countdown");
            if (cd) {
                cd.innerHTML = "⏳Results will be available soons";
            }
        } else {
            clearInterval(countdown);
            var cd = document.getElementById("countdown");
            if (cd) {
                cd.innerHTML = "✅ Results are now live!";
            }
          
        }
    }, 1000);
});
</script>
</body>
</html>
