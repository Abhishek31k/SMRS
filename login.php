<?php
session_start();
include('includes/config.php'); // Database connection

// If already logged in, redirect to student-dashboard
if(isset($_SESSION['StudentId'])){
    header('Location: student-dashboard.php');
    exit;
}

// Login
$error = "";
if(isset($_POST['login'])){
    $rollid = trim($_POST['rollid']);
    $password = trim($_POST['password']);

    // Fetch student by RegNo
    $sql = "SELECT * FROM tblstudents WHERE RegNo=:rollid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':rollid', $rollid, PDO::PARAM_STR);
    $query->execute();
    $student = $query->fetch(PDO::FETCH_OBJ);

    if($student){
        // Check if password == RegNo
        if($password === $student->RegNo){
            // Set session
            $_SESSION['StudentId'] = $student->RegNo;   // Use RegNo since no numeric ID
            $_SESSION['StudentName'] = $student->StudentName;
            $_SESSION['RegNo'] = $student->RegNo;
            $_SESSION['ClassId'] = $student->ClassId;

            // Redirect to student-dashboard
            header('Location: student-dashboard.php');
            exit;
        } else {
            $error = "Invalid Password!";
        }
    } else {
        $error = "Invalid Roll ID!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
<style>
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; color: white; }
.card { border-radius:20px; background: rgba(255,255,255,0.15); backdrop-filter: blur(15px); }
.form-control:focus { box-shadow: 0 0 8px rgba(111,66,193,0.4); border-color: #6f42c1; }
</style>
</head>
<body>
  

<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center" style="
    background: linear-gradient(135deg, #000000, #2b8d36);
    border-radius: 15px; width:85%;">
  <div class="row w-100">
    <div class="col-lg-6 d-none d-lg-flex justify-content-center align-items-center p-0">
      <div class="text-center">
        <h1 class="fw-bold mb-3">Welcome to CRR Student Portal</h1>
        <p class="lead mb-4">Login to access your account</p>
        <img src="images/cyber-data-security-online-concept-illustration-internet-security-information-privacy-protection.png" class="img-fluid" style="max-height:400px; border-radius:15px;">
      </div>
    </div>

    <div class="col-lg-6 d-flex justify-content-center align-items-center p-4">
      <div class="card p-5 w-100" style="max-width:400px;">
        <div class="text-center mb-4 text-white">
         <img src="images/crrengglogo.png" alt="CRR Logo" class="mb-3" style="width:80px; height:80px; object-fit:contain; border-radius:50%;">

          <h3 class="fw-bold">Login</h3>
          <p>Enter your Roll ID and Password</p>
        </div>

        <?php if($error){ echo '<div class="alert alert-danger">'.$error.'</div>'; } ?>

        <form method="post" onsubmit="return validateForm();">
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="rollid" name="rollid" placeholder="Roll ID">
            <label for="rollid"><i class="fa fa-id-card me-2"></i>Roll ID</label>
          </div>

          <div class="form-floating mb-3">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            <label for="password"><i class="fa fa-lock me-2"></i>Password</label>
          </div>

          <button type="submit" name="login" class="btn w-100 btn-primary mb-3">
            <i class="fa fa-sign-in-alt me-2"></i>Login
          </button>

          <div class="d-flex gap-2 flex-column flex-md-row">
            <!-- Back to Home Button -->
            <a href="index.php" class="btn btn-outline-light w-100">
              <i class="fa fa-home me-2"></i>Back to Home
            </a>

            <!-- Admin Login Button -->
            <a href="admin-login.php" class="btn btn-outline-warning w-100">
              <i class="fa fa-user-shield me-2"></i>Admin Login
            </a>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

<script>
function validateForm(){
    const roll = document.querySelector('[name="rollid"]').value.trim();
    const pass = document.querySelector('[name="password"]').value.trim();
    if(roll=="" || pass==""){
        alert("Please enter Roll ID and Password!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
