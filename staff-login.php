<?php
session_start();
error_reporting(0);
include('includes/config.php');  // DB connection

if(isset($_POST['login'])) {
    $staffcode = $_POST['staffcode'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];  // From radio button

    if ($role === 'HOD') {
        // Strict HOD login
        $sql = "SELECT StaffId, StaffName, Role FROM tblstaff 
                WHERE StaffCode=:staffcode 
                  AND Password=:password 
                  AND Role='HOD' 
                  AND Status='Active' LIMIT 1";
    } else {
        // Staff login (can also allow HODs here)
        $sql = "SELECT StaffId, StaffName, Role FROM tblstaff 
                WHERE StaffCode=:staffcode 
                  AND Password=:password 
                  AND Role IN ('Staff','HOD') 
                  AND Status='Active' LIMIT 1";
    }

    $query = $dbh->prepare($sql);
    $query->bindParam(':staffcode', $staffcode, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();

    if($query->rowCount() > 0) {
        $row = $query->fetch(PDO::FETCH_ASSOC);
        $_SESSION['slogin'] = $row['StaffId'];
        $_SESSION['staffname'] = $row['StaffName'];
        $_SESSION['staffrole'] = $row['Role'];

        if($row['Role'] === 'HOD' && $role === 'HOD'){
            header("Location: hod-dashboard.php");
        } else {
            header("Location: staff-dashboard.php");
        }
        exit;
    } else {
        $msg = "Invalid credentials for selected role.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Login | SRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

body, html {
    height:100%;
    margin:0;
    font-family:'Inter', sans-serif;
}

/* Particles background */
#particles-js {
    position:fixed;
    width:100%;
    height:100%;
    top:0;
    left:0;
    z-index:0;
    background: linear-gradient(135deg, #4742ad, #1f582c);
}

/* Login card */
.login-container {
    position:relative;
    z-index:1;
    width:100%;
    max-width:400px;
    background:#fff;
    border-radius:20px;
    padding:60px 30px 40px 30px;
    box-shadow:0 15px 40px rgba(0,0,0,0.2);
   
    margin:auto;
    top:50%;
    transform:translateY(-50%);
}

/* Bounce animation */
@keyframes bounceIn {
    0% { transform: scale(0.3); opacity:0; }
    50% { transform: scale(1.05); opacity:1; }
    70% { transform: scale(0.9); }
    100% { transform: scale(1); }
}

/* Floating logo */
.logo-container {
    position:absolute;
    top:-40px;
    left:50%;
    transform:translateX(-50%);
    z-index:2;
   width: 100px;
    height: 100px;
    border-radius:50%;
    background:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    animation:float 3s ease-in-out infinite;
    box-shadow:0 8px 20px rgba(0,0,0,0.15);
}
.logo-container img { width: 100px;
    height: 100px; object-fit:contain; }

@keyframes float {
    0%,100% { transform:translateX(-50%) translateY(0); }
    50% { transform:translateX(-50%) translateY(-10px); }
}

.login-container h3 {
    text-align:center;
    margin-bottom:30px;
    position:relative;
    z-index:1;
    color:#003366;
    margin-top:60px;
}

.error {
    color:#e74c3c;
    text-align:center;
    margin-bottom:15px;
    position:relative;
    z-index:1;
}

.login-container form { position:relative; z-index:1; }
.login-container input {
    border-radius:10px;
    border:1px solid #ddd;
    padding:12px 15px;
    margin-bottom:20px;
    width:100%;
    transition:0.3s;
}
.login-container input:focus {
    border-color:#2563eb;
    box-shadow:0 0 10px rgba(37,99,235,0.2);
    outline:none;
}

.btn-login {
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(45deg,#3b82f6,#2563eb);
    color:#fff;
    font-weight:600;
    transition:0.3s;
    cursor:pointer;
}
.btn-login:hover {
    background:linear-gradient(45deg,#2563eb,#3b82f6);
    transform:scale(1.03);
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}

.btn-back {
    display:block;
    width:100%;
    margin-top:15px;
    background:#6c757d;
    color:#fff;
    border:none;
    padding:10px;
    border-radius:10px;
    text-align:center;
    text-decoration:none;
    transition:0.3s;
    position:relative;
    z-index:1;
}
.btn-back:hover {
    background:#5a6268;
    color:#fff;
    text-decoration:none;
    transform:scale(1.02);
}
.radio-staff{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}
.radio-staff label{
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: center;
}
.radio-staff label input{
    margin: 5px;
}
</style>
</head>
<body>

<div id="particles-js"></div>

<div class="login-container">
    <div class="alert alert-info text-center" style="max-width:500px; margin:0 auto 20px auto; font-size:0.95rem; border-radius:10px;">
    Welcome to the SRMS Portal! Both <b>HODs</b> and <b>Staff</b> can log in here to access their dashboards.
</div>

    <!-- Floating Logo -->
    <div class="logo-container">
        <img src="includes/crrengglogo.png" alt="College Logo">
    </div>

    <h3>HOD||StaffLogin</h3>
    <?php if(!empty($msg)){ echo '<p class="error">'.htmlentities($msg).'</p>'; } ?>
    <form method="post">
        <!-- Role Selection -->
        <div class="mb-3 text-center radio-staff">
            <label class="me-3">
                <input type="radio" name="role" value="Staff" checked> Staff
            </label>
            <label>
                <input type="radio" name="role" value="HOD"> HOD
            </label>
        </div>

        <input type="text" name="staffcode" id="staffcode" placeholder="Staff Code" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login" class="btn-login">Login</button>
    </form>
    <a href="index.php" class="btn-back"><i class="fa fa-home me-1"></i> Back to Home</a>
</div>

<script>
/* Initialize Particles.js */
particlesJS('particles-js',
{
  "particles": {
    "number": {"value":60,"density":{"enable":true,"value_area":800}},
    "color":{"value":"#ffffff"},
    "shape":{"type":"circle"},
    "opacity":{"value":0.5,"random":true},
    "size":{"value":3,"random":true},
    "line_linked":{"enable":true,"distance":120,"color":"#ffffff","opacity":0.3,"width":1},
    "move":{"enable":true,"speed":2,"direction":"none","random":true,"straight":false,"bounce":false}
  },
  "interactivity": {
    "detect_on":"canvas",
    "events":{"onhover":{"enable":true,"mode":"grab"},"onclick":{"enable":true,"mode":"push"}}
  },
  "retina_detect":true
});
document.addEventListener("DOMContentLoaded", function(){
    const staffCodeInput = document.getElementById("staffcode");
    const roleRadios = document.querySelectorAll("input[name='role']");

    roleRadios.forEach(radio => {
        radio.addEventListener("change", function(){
            if(this.value === "HOD"){
                staffCodeInput.placeholder = "HOD ID";
            } else {
                staffCodeInput.placeholder = "Staff Code";
            }
        });
    });
});
</script>
</body>
</html>
