<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}

$msg = $error = "";

if(isset($_POST['submit'])) {
    $StaffCode   = $_POST['StaffCode'];
    $StaffName   = $_POST['StaffName'];
    $Gender      = $_POST['Gender'];
    $Email       = $_POST['Email'];
    $Mobile      = $_POST['Mobile'];
    $Password    = md5($_POST['Password']); 
    $Role        = $_POST['Role'];
    $Department  = $_POST['Department'];
    $Designation = $_POST['Designation'];
    $Status      = $_POST['Status'];

    try {
        $sql="INSERT INTO tblstaff(StaffCode,StaffName,Gender,Email,Mobile,Password,Role,Department,Designation,Status)
              VALUES(:StaffCode,:StaffName,:Gender,:Email,:Mobile,:Password,:Role,:Department,:Designation,:Status)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':StaffCode',$StaffCode,PDO::PARAM_STR);
        $query->bindParam(':StaffName',$StaffName,PDO::PARAM_STR);
        $query->bindParam(':Gender',$Gender,PDO::PARAM_STR);
        $query->bindParam(':Email',$Email,PDO::PARAM_STR);
        $query->bindParam(':Mobile',$Mobile,PDO::PARAM_STR);
        $query->bindParam(':Password',$Password,PDO::PARAM_STR);
        $query->bindParam(':Role',$Role,PDO::PARAM_STR);
        $query->bindParam(':Department',$Department,PDO::PARAM_STR);
        $query->bindParam(':Designation',$Designation,PDO::PARAM_STR);
        $query->bindParam(':Status',$Status,PDO::PARAM_STR);
        $query->execute();
        $msg="Staff added successfully!";
    } catch (PDOException $e) {
        $error="Error: Could not add staff (maybe duplicate StaffCode or Email)";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Add Staff</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/main.css">

    <!-- Font Awesome 6 Free CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen">


</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">

    <?php include('includes/topbar.php');?> 
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php');?>  

            <div class="main-page">
                <div class="container-fluid">
                    <h2 class="title">Add Staff</h2>

                    <?php if($msg){?>
                        <div class="alert alert-success"><strong>✔</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } else if($error){?>
                        <div class="alert alert-danger"><strong>✘</strong> <?php echo htmlentities($error); ?></div>
                    <?php } ?>

                    <div class="panel">
                        <div class="panel-body">
<form class="form-horizontal" method="post">

    <!-- Staff Code -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Staff Code</label>
        <div class="col-sm-10">
            <input type="text" name="StaffCode" class="form-control" placeholder="Unique Staff Code" required>
        </div>
    </div>

    <!-- Staff Name -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Staff Name</label>
        <div class="col-sm-10">
            <input type="text" name="StaffName" class="form-control" placeholder="Enter Full Name" required>
        </div>
    </div>

    <!-- Gender -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Gender</label>
        <div class="col-sm-10">
            <select name="Gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
    </div>

    <!-- Email -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" name="Email" class="form-control" placeholder="Enter Email" required>
        </div>
    </div>

    <!-- Mobile -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Mobile</label>
        <div class="col-sm-10">
            <input type="text" name="Mobile" class="form-control" placeholder="10-digit Mobile No" required>
        </div>
    </div>

    <!-- Password -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Password</label>
        <div class="col-sm-10">
            <input type="password" name="Password" class="form-control" placeholder="Enter Password" required>
        </div>
    </div>

    <!-- Role -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Role</label>
        <div class="col-sm-10">
            <select name="Role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="Staff">Staff</option>
                <option value="HOD">HOD</option>
            </select>
        </div>
    </div>

    <!-- Department -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Department</label>
        <div class="col-sm-10">
            <select id="department" name="Department" class="form-control" required>
                <option value="">Select Department</option>
                <option value="CSE">CSE</option>
                <option value="IT">IT</option>
                <option value="MECH">MECH</option>
                <option value="CIVIL">CIVIL</option>
                <option value="ECE">ECE</option>
                <option value="EEE">EEE</option>
            </select>

            <!-- Specialization (only if CSE) -->
            <div id="cse-options" class="mt-2" style="display:none;">
              <select name="Department" class="form-control">
                <option value="">Select Specialization</option>
                <option value="CSM">CSM (AI & ML)</option>
                <option value="CSD">CSD (Data Science)</option>
                <option value="CSC">CSC (Cyber Security)</option>
              </select>
            </div>
        </div>
    </div>

    <!-- Designation -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Designation</label>
        <div class="col-sm-10">
            <input type="text" name="Designation" class="form-control" placeholder="Assistant Professor, Professor etc." required>
        </div>
    </div>

    <!-- Status -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Status</label>
        <div class="col-sm-10">
            <select name="Status" class="form-control" required>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Submit -->
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">Add Staff</button>
        </div>
    </div>
</form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/DataTables/datatables.min.js"></script>
<script src="js/main.js"></script>

<script>
  // Toggle specialization options for CSE
  $("#department").change(function(){
    if($(this).val() === "CSE"){
      $("#cse-options").show();
    } else {
      $("#cse-options").hide();
    }
  });
</script>

</body>
</html>
