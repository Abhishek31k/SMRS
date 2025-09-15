<?php
session_start();
error_reporting(0);
include('includes/config.php');

if($_SESSION['alogin']!=''){
    $_SESSION['alogin']='';
}

if(isset($_POST['login']))
{
    $uname=$_POST['username'];
    $password=md5($_POST['password']);
    $sql ="SELECT UserName,Password FROM admin WHERE UserName=:uname and Password=:password";
    $query= $dbh -> prepare($sql);
    $query-> bindParam(':uname', $uname, PDO::PARAM_STR);
    $query-> bindParam(':password', $password, PDO::PARAM_STR);
    $query-> execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);
    if($query->rowCount() > 0)
    {
        $_SESSION['alogin']=$_POST['username'];
        echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
    } else{
        echo "<script>alert('Invalid Details');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login | SRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
  <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
<style>
body {
     background: #243d62;
    font-family: 'Segoe UI', sans-serif;
    /* height: 100vh; */
    padding: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-wrapper {
    display: flex;
    width: 85%;
    max-width: 1000px;
 
    box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    border-radius: 15px;
    overflow: hidden;
    animation: fadeIn 1s ease-in-out;
}

@keyframes fadeIn {
    from {opacity:0; transform: translateY(30px);}
    to {opacity:1; transform: translateY(0);}
}

/* Animated Gradient Background with Floating Circles */
.login-left {
    flex: 1;
    position: relative;
  
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    color: #f2f2f2ff;
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
    align-items: center;
}

@keyframes gradientBG {
    0% {background-position: 0% 50%;}
    50% {background-position: 100% 50%;}
    100% {background-position: 0% 50%;}
}



@keyframes float {
    0% {transform: translateY(0) translateX(0);}
    50% {transform: translateY(-20px) translateX(20px);}
    100% {transform: translateY(0) translateX(0);}
}

.login-left h2 {
    font-size: 38px;
    margin-bottom: 20px;
    font-weight: bold;
    z-index: 1;
    position: relative;
}

.login-left p {
    font-size: 18px;
    line-height: 1.5;
    z-index: 1;
    position: relative;
}

.login-right {
    flex: 1;
    background: #000000b5;
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
}

.login-right h3 {
    margin-bottom: 25px;
    font-weight: bold;
    color: #333;
    text-align: center;
}

.form-control {
       border-radius: 50px;
    padding-left: 40px;
    height: 50px;
    font-size: 16px;
    width: 50%;
    margin-left: auto;
    margin-right: auto;
}

.form-control:focus {
    box-shadow: 0 0 10px rgba(123,44,191,0.3);
    border-color: #7b2cbf;
}

.input-icon {
        position: absolute;
    top: 16px;
    left: 27%;
    color: #3220d8;
}

.form-group {
    position: relative;
    margin-bottom: 25px;
}

.btn-primary {
       background-color: #2c35bf;
    border: none;
    border-radius: 11px;
    height: 50px;
    width: 50%;
    margin-right: auto;
    margin-left: auto;
    font-size: 18px;
    transition: 0.3s;
}

.btn-primary:hover {
    background-color: #5a1a99;
}

.login-footer {
    text-align: center;
    margin-top: 20px;
    color: #aaa;
}

.back-home {
    text-align: center;
    margin-top: 15px;
}

.back-home a {
    color: #7b2cbf;
    font-weight: bold;
    text-decoration: none;
    transition: 0.3s;
}

.back-home a:hover {
    color: #5a1a99;
}
.admin-logo {
       width: 155px;
    height: 155px;
    object-fit: contain;
    border-radius: 50%;
    margin-bottom: 15px;

}
.REDDY-ENGINEERING{
       background: white;
    font-size: 22px;
    padding: 10px;
    font-weight: 900;
    right: 25px;
    color: crimson;
    /* margin-left: auto; */
    position: relative;
    bottom: 50px;
    border-radius: 0px 0px 29px 32px;
}

@media(max-width: 768px){
    .login-wrapper {
        flex-direction: column;
        height: auto;
    }
    .login-left {
      
        padding: 20px;
        text-align: center;
    }
    .REDDY-ENGINEERING {
          background: white;
        font-size: 10px;
        padding: 5px;
        font-weight: 900;
        right: 31px;
        color: crimson;
        position: relative;
        bottom: 42px;
        border-radius: 0;
}
    .login-right {
        padding: 40px 20px;
    }
}
</style>
</head>
<body>

<div class="login-wrapper">
    <!-- Left Animated Background & Floating Circles -->

    <!-- Right Login Form -->
    <div class="login-right">
        <div class="text-center mb-3">
         <span class="REDDY-ENGINEERING">SIR CR REDDY COLLEGE OF ENGINEERING | AUTONOMOUS</span>
    <div class="login-left">
   

        <img src="images/crrengglogo.png" alt="CRR Logo" class="admin-logo">
        <h2>Welcome Back!</h2>
        <p>Manage student results efficiently and securely with the Student Result Management System. Login to access your admin dashboard and control everything.</p>
    </div>
</div>
        <h3>Admin Login</h3>
        <form method="post">
            <div class="form-group">
                <i class="fa fa-user input-icon"></i>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="form-group">
                <i class="fa fa-lock input-icon"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <div class="d-grid mt-4">
                <button type="submit" name="login" class="btn btn-primary btn-lg">Sign In</button>
            </div>
        </form>

        <!-- Back to Home Link -->
        <div class="back-home">
            <a href="index.php"><i class="fa fa-home"></i> Back to Home</a>
        </div>

        <div class="login-footer mt-4">
            <small>© 2025 Student Result Management System</small>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
