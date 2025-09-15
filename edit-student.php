<?php
session_start();
error_reporting(0);

include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}
$regno = $_GET['regno'];

// Fetch current student
$sql = "SELECT s.*, 
               CONCAT(c.BatchStart, '-', c.BatchEnd) AS Batch, 
               c.Branch, 
               c.Section
        FROM tblstudents s 
        LEFT JOIN tblclasses c ON s.ClassId = c.id 
        WHERE s.RegNo=:regno";
$query = $dbh->prepare($sql);
$query->bindParam(':regno', $regno, PDO::PARAM_STR);
$query->execute();
$student = $query->fetch(PDO::FETCH_OBJ);


// Fetch all classes
$sql2 = "SELECT * FROM tblclasses";
$query2 = $dbh->prepare($sql2);
$query2->execute();
$allClasses = $query2->fetchAll(PDO::FETCH_OBJ);

// Get unique Batches, Branches, Sections
$batches = array_unique(array_map(fn($c) => $c->BatchStart . '-' . $c->BatchEnd, $allClasses));
$branches = array_unique(array_map(fn($c) => $c->Branch, $allClasses));
$sections = array_unique(array_map(fn($c) => $c->Section, $allClasses));

// Fetch all counsellors
$sql3 = "SELECT StaffId, StaffName, Department, Mobile FROM tblstaff WHERE Role='Staff' AND Status='Active'";
$query3 = $dbh->prepare($sql3);
$query3->execute();
$allStaff = $query3->fetchAll(PDO::FETCH_OBJ);

// Handle form submission
if(isset($_POST['submit'])) {
    $newregno       = $_POST['regno'];
    $studentname    = $_POST['studentname'];
    $dob            = $_POST['dob'];
    $gender         = $_POST['gender'];
    $studentemail   = $_POST['studentemail'];
    $studentmobile  = $_POST['studentmobile'];
    $fathername     = $_POST['fathername'];
    $fathermobile   = $_POST['fathermobile'];
    $admissiontype  = $_POST['admissiontype'];
    $batch          = $_POST['batch'];
    $branch         = $_POST['branch'];
    $section        = $_POST['section'];
    
    // Get ClassId from selected Batch, Branch, Section
    $classid = 0;
    foreach($allClasses as $cls){
        if(($cls->BatchStart . '-' . $cls->BatchEnd) == $batch && $cls->Branch==$branch && $cls->Section==$section){
            $classid = $cls->id;
            break;
        }

    }
    $sql = "UPDATE tblstudents 
            SET RegNo=:newregno, StudentName=:studentname, DOB=:dob, Gender=:gender, 
                StudentEmail=:studentemail, StudentMobile=:studentmobile, 
                FatherName=:fathername, FatherMobile=:fathermobile, 
                AdmissionType=:admissiontype, ClassId=:classid
            WHERE RegNo=:oldregno";

    $query = $dbh->prepare($sql);
    $query->bindParam(':newregno',$newregno,PDO::PARAM_STR);
    $query->bindParam(':studentname',$studentname,PDO::PARAM_STR);
    $query->bindParam(':dob',$dob,PDO::PARAM_STR);
    $query->bindParam(':gender',$gender,PDO::PARAM_STR);
    $query->bindParam(':studentemail',$studentemail,PDO::PARAM_STR);
    $query->bindParam(':studentmobile',$studentmobile,PDO::PARAM_STR);
    $query->bindParam(':fathername',$fathername,PDO::PARAM_STR);
    $query->bindParam(':fathermobile',$fathermobile,PDO::PARAM_STR);
    $query->bindParam(':admissiontype',$admissiontype,PDO::PARAM_STR);
    $query->bindParam(':classid',$classid,PDO::PARAM_INT);
    $query->bindParam(':oldregno',$regno,PDO::PARAM_STR);

    if($query->execute()){
        $regno = $newregno;
        $msg = "Student info updated successfully";
    } else {
        $error = "Update failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin | Edit Student</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/font-awesome.min.css">
<link rel="stylesheet" href="css/main.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">


</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">
<?php include('includes/topbar.php');?> 
<div class="content-wrapper">
<div class="content-container">
<?php include('includes/leftbar.php');?>  

<div class="main-page">
<div class="container-fluid">
    <h2 class="title">Edit Student</h2>

    <?php if(isset($msg)){ ?>
        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
    <?php } ?>
    <?php if(isset($error)){ ?>
        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
    <?php } ?>

    <?php if($student){ ?>
<form class="form-horizontal" method="post">

    <div class="form-group">
        <label class="col-sm-2 control-label">Reg No</label>
        <div class="col-sm-10">
            <input type="text" name="regno" class="form-control" value="<?php echo htmlentities($student->RegNo); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Full Name</label>
        <div class="col-sm-10">
            <input type="text" name="studentname" class="form-control" value="<?php echo htmlentities($student->StudentName); ?>" required>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">DOB</label>
        <div class="col-sm-10">
            <input type="date" name="dob" class="form-control" value="<?php echo htmlentities($student->DOB); ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Gender</label>
        <div class="col-sm-10">
            <input type="radio" name="gender" value="Male" <?php if($student->Gender=="Male") echo "checked"; ?>> Male
            <input type="radio" name="gender" value="Female" <?php if($student->Gender=="Female") echo "checked"; ?>> Female
            <input type="radio" name="gender" value="Other" <?php if($student->Gender=="Other") echo "checked"; ?>> Other
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Email</label>
        <div class="col-sm-10">
            <input type="email" name="studentemail" class="form-control" value="<?php echo htmlentities($student->StudentEmail); ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Mobile</label>
        <div class="col-sm-10">
            <input type="text" name="studentmobile" class="form-control" value="<?php echo htmlentities($student->StudentMobile); ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Father Name</label>
        <div class="col-sm-10">
            <input type="text" name="fathername" class="form-control" value="<?php echo htmlentities($student->FatherName); ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Father Mobile</label>
        <div class="col-sm-10">
            <input type="text" name="fathermobile" class="form-control" value="<?php echo htmlentities($student->FatherMobile); ?>">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Admission Type</label>
        <div class="col-sm-10">
            <input type="text" name="admissiontype" class="form-control" value="<?php echo htmlentities($student->AdmissionType); ?>">
        </div>
    </div>

    <!-- Batch, Branch, Section -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Batch</label>
        <div class="col-sm-10">
            <select name="batch" class="form-control" required>
                <option value="">-- Select Batch --</option>
                <?php foreach($batches as $batch){ ?>
                    <option value="<?php echo $batch; ?>" <?php if($student->Batch==$batch) echo "selected"; ?>><?php echo $batch; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Branch</label>
        <div class="col-sm-10">
            <select name="branch" class="form-control" required>
                <option value="">-- Select Branch --</option>
                <?php foreach($branches as $branch){ ?>
                    <option value="<?php echo $branch; ?>" <?php if($student->Branch==$branch) echo "selected"; ?>><?php echo $branch; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Section</label>
        <div class="col-sm-10">
            <select name="section" class="form-control" required>
                <option value="">-- Select Section --</option>
                <?php foreach($sections as $section){ ?>
                    <option value="<?php echo $section; ?>" <?php if($student->Section==$section) echo "selected"; ?>><?php echo $section; ?></option>
                <?php } ?>
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
        <div class="alert alert-danger">No student found.</div>
    <?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/DataTables/datatables.min.js"></script>
<script src="js/main.js"></script>
<?php if(isset($msg)){ ?>
    <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
    <script>
        setTimeout(function(){
            window.location.href = "edit-student.php?regno=<?php echo $regno; ?>";
        }, 1000);
    </script>
<?php } ?>
</body>
</html>
