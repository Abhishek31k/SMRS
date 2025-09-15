<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit;
}

$id = intval($_GET['id']); // StaffId from URL

// Handle form submission
if(isset($_POST['submit'])) {
    $StaffCode   = $_POST['StaffCode'];
    $StaffName   = $_POST['StaffName'];
    $Gender      = $_POST['Gender'];
    $Email       = $_POST['Email'];
    $Mobile      = $_POST['Mobile'];
    $Role        = $_POST['Role'];
    $Department  = $_POST['Department'];
    $Designation = $_POST['Designation'];
    $Status      = $_POST['Status'];

    // Update query
    $sql = "UPDATE tblstaff 
            SET StaffCode=:StaffCode, StaffName=:StaffName, Gender=:Gender, 
                Email=:Email, Mobile=:Mobile, Role=:Role, 
                Department=:Department, Designation=:Designation, Status=:Status 
            WHERE StaffId=:id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':StaffCode',$StaffCode,PDO::PARAM_STR);
    $query->bindParam(':StaffName',$StaffName,PDO::PARAM_STR);
    $query->bindParam(':Gender',$Gender,PDO::PARAM_STR);
    $query->bindParam(':Email',$Email,PDO::PARAM_STR);
    $query->bindParam(':Mobile',$Mobile,PDO::PARAM_STR);
    $query->bindParam(':Role',$Role,PDO::PARAM_STR);
    $query->bindParam(':Department',$Department,PDO::PARAM_STR);
    $query->bindParam(':Designation',$Designation,PDO::PARAM_STR);
    $query->bindParam(':Status',$Status,PDO::PARAM_STR);
    $query->bindParam(':id',$id,PDO::PARAM_INT);

    if($query->execute()){
        $msg = "Staff info updated successfully";
    } else {
        $error = "Update failed!";
    }
}

// Fetch staff details
$sql = "SELECT * FROM tblstaff WHERE StaffId=:id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$result = $query->fetch(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin | Edit Staff</title>
<link rel="stylesheet" href="css/bootstrap.min.css" >
<link rel="stylesheet" href="css/font-awesome.min.css" >
<link rel="stylesheet" href="css/main.css" >
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">
<?php include('includes/topbar.php');?> 
<div class="content-wrapper">
<div class="content-container">
<?php include('includes/leftbar.php');?>  

<div class="main-page">
<div class="container-fluid">
    <h2 class="title">Edit Staff</h2>

    <?php if($msg){ ?>
        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
    <?php } elseif($error){ ?>
        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
    <?php } ?>

    <?php if($result){ ?>
<form class="form-horizontal" method="post">

    <div class="form-group">
        <label class="col-sm-2 control-label">Staff Code</label>
        <div class="col-sm-10">
            <input type="text" name="StaffCode" class="form-control" value="<?php echo htmlentities($result->StaffCode); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Full Name</label>
        <div class="col-sm-10">
            <input type="text" name="StaffName" class="form-control" value="<?php echo htmlentities($result->StaffName); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Gender</label>
        <div class="col-sm-10">
            <select name="Gender" class="form-control">
                <option value="Male" <?php if($result->Gender=="Male") echo "selected";?>>Male</option>
                <option value="Female" <?php if($result->Gender=="Female") echo "selected";?>>Female</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" name="Email" class="form-control" value="<?php echo htmlentities($result->Email); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Mobile</label>
        <div class="col-sm-10">
            <input type="text" name="Mobile" class="form-control" value="<?php echo htmlentities($result->Mobile); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Role</label>
        <div class="col-sm-10">
            <select name="Role" class="form-control">
                <option value="Staff" <?php if($result->Role=="Staff") echo "selected";?>>Staff</option>
                <option value="HOD" <?php if($result->Role=="HOD") echo "selected";?>>HOD</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Department</label>
        <div class="col-sm-10">
            <select name="Department" class="form-control">
                <option value="CSE" <?php if($result->Department=="CSE") echo "selected";?>>CSE</option>
                <option value="IT" <?php if($result->Department=="IT") echo "selected";?>>IT</option>
                <option value="MECH" <?php if($result->Department=="MECH") echo "selected";?>>MECH</option>
                <option value="CIVIL" <?php if($result->Department=="CIVIL") echo "selected";?>>CIVIL</option>
                <option value="ECE" <?php if($result->Department=="ECE") echo "selected";?>>ECE</option>
                <option value="EEE" <?php if($result->Department=="EEE") echo "selected";?>>EEE</option>
                <option value="CSM" <?php if($result->Department=="CSM") echo "selected";?>>CSM</option>
                <option value="CSD" <?php if($result->Department=="CSD") echo "selected";?>>CSD</option>
                <option value="CSC" <?php if($result->Department=="CSC") echo "selected";?>>CSC</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Designation</label>
        <div class="col-sm-10">
            <select name="Designation" class="form-control">
                <option value="Professor" <?php if($result->Designation=="Professor") echo "selected";?>>Professor</option>
                <option value="HOD" <?php if($result->Designation=="HOD") echo "selected";?>>HOD</option>
                <option value="Staff" <?php if($result->Designation=="Staff") echo "selected";?>>Staff</option>
                <option value="Assistant Professor" <?php if($result->Designation=="Assistant Professor") echo "selected";?>>Assistant Professor</option>
                <option value="Associate Professor" <?php if($result->Designation=="Associate Professor") echo "selected";?>>Associate Professor</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Status</label>
        <div class="col-sm-10">
            <select name="Status" class="form-control">
                <option value="Active" <?php if($result->Status=="Active") echo "selected";?>>Active</option>
                <option value="Inactive" <?php if($result->Status=="Inactive") echo "selected";?>>Inactive</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
        </div>
    </div>
</form>
    <?php } else { ?>
        <div class="alert alert-danger">No staff found.</div>
    <?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
</body>
</html>
