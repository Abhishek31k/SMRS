<?php
include("includes/config.php"); // Your PDO connection

$successMsg = "";
$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $phone   = trim($_POST['phone']);
    $course  = trim($_POST['course']);
    $branch  = trim($_POST['branch']);
    $message = trim($_POST['message']);

    try {
        $sql = "INSERT INTO enquiries (name, email, phone, course, branch, message) 
                VALUES (:name, :email, :phone, :course, :branch, :message)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':name', $name, PDO::PARAM_STR);
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->bindParam(':phone', $phone, PDO::PARAM_STR);
        $query->bindParam(':course', $course, PDO::PARAM_STR);
        $query->bindParam(':branch', $branch, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);

        if ($query->execute()) {
            $successMsg = "✅ Thank you $name! Your enquiry has been submitted. We’ll get back to you soon.";
        } else {
            $errorMsg = "❌ Something went wrong. Please try again.";
        }
    } catch (PDOException $e) {
        $errorMsg = "❌ Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enquiry - SIR CRR College of Engineering</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .enquiry-header {
      background: linear-gradient(to right, #80a3ca, #1435ad);
      color: white;
      padding: 50px 20px;
      text-align: center;
    }
    .form-section { padding: 50px 20px; }
    .form-card {
      background: white;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    .btn-custom {
      background: #dc3545;
      color: white;
      font-weight: 600;
      padding: 10px 25px;
      transition: all 0.3s;
      border-radius: 8px;
    }
    .btn-custom:hover { background: #b02a37; }

    .btn-home {
      background: #6c757d;
      color: white;
      font-weight: 600;
      padding: 8px 20px;
      border-radius: 8px;
      text-decoration: none;
      display: inline-block;
      margin-top: 15px;
    }
    .btn-home:hover { background: #5a6268; }

    .college-logo {
      max-width: 120px;
      height: auto;
      display: block;
      margin: 0 auto 15px auto;
    }
  </style>
</head>
<body>

  <!-- Header -->
<header class="enquiry-header">
  <img src="images/crrengglogo.png" alt="College Logo" class="college-logo mb-3">
  <h1>Enquiry Form Regarding Admission</h1>
  <p>Have questions? Submit your enquiry to SIR CRR College of Engineering</p>
</header>

  <!-- Form Section -->
  <section class="form-section">
    <div class="container">
      <?php if (!empty($successMsg)) : ?>
        <div class="alert alert-success text-center"><?= $successMsg ?></div>
      <?php elseif (!empty($errorMsg)) : ?>
        <div class="alert alert-danger text-center"><?= $errorMsg ?></div>
      <?php endif; ?>

      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="form-card">
            <h3 class="mb-4 text-center">Submit Your Enquiry</h3>
            <form method="POST" action="">
              <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email ID</label>
                <input type="email" name="email" id="email" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" name="phone" id="phone" class="form-control" required>
              </div>

              <div class="mb-3">
                <label for="course" class="form-label">Course Interested</label>
                <select name="course" id="course" class="form-select" required>
                  <option value="">-- Select Course --</option>
                  <option>B.Tech</option>
                  <option>M.Tech</option>
                  <option>MBA</option>
                  <option>MCA</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="branch" class="form-label">Preferred Branch</label>
                <select name="branch" id="branch" class="form-select" required>
                  <option value="">-- Select Branch --</option>
                  <option>Computer Science Engineering</option>
                  <option>Electronics & Communication Engineering</option>
                  <option>Electrical & Electronics Engineering</option>
                  <option>Mechanical Engineering</option>
                  <option>Civil Engineering</option>
                  <option>Information Technology</option>
                </select>
              </div>

              <div class="mb-3">
                <label for="message" class="form-label">Message (Optional)</label>
                <textarea name="message" id="message" class="form-control" rows="4"></textarea>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-custom">Submit Enquiry</button>
                <br>
                <a href="index.php" class="btn-home">⬅ Back to Home</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

</body>
</html>
