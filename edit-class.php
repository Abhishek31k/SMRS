<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {

if(isset($_POST['update'])) {
    $branch=$_POST['branch'];
    if($branch=="other") { // add new branch
        $branch=$_POST['newbranch'];
    }
    $semester=$_POST['semester'];
    $section=$_POST['section'];
    $regulation=$_POST['regulation'];
    $batchstart=$_POST['batchstart'];
    $batchend=$_POST['batchend'];
    $cid=intval($_GET['classid']);

    $sql="UPDATE tblclasses 
          SET Branch=:branch, Semester=:semester, Section=:section, Regulation=:regulation,
              BatchStart=:batchstart, BatchEnd=:batchend 
          WHERE id=:cid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':branch',$branch,PDO::PARAM_STR);
    $query->bindParam(':semester',$semester,PDO::PARAM_STR);
    $query->bindParam(':section',$section,PDO::PARAM_STR);
    $query->bindParam(':regulation',$regulation,PDO::PARAM_STR);
    $query->bindParam(':batchstart',$batchstart,PDO::PARAM_INT);
    $query->bindParam(':batchend',$batchend,PDO::PARAM_INT);
    $query->bindParam(':cid',$cid,PDO::PARAM_INT);
    $query->execute();
    $msg="Data has been updated successfully";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin Update Class</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
    <script src="js/modernizr/modernizr.min.js"></script>
</head>
<body class="top-navbar-fixed">
    <div class="main-wrapper">
        <?php include('includes/topbar.php');?>   
        <div class="content-wrapper">
            <div class="content-container">
                <?php include('includes/leftbar.php');?>                   

                <div class="main-page">
                    <div class="container-fluid">
                        <div class="row page-title-div">
                            <div class="col-md-6">
                                <h2 class="title">Update Student Class</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
            						<li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
            						<li><a href="#">Classes</a></li>
            						<li class="active">Update Class</li>
            					</ul>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h5>Update Student Class info</h5>
                                        </div>
<?php if($msg){?>
<div class="alert alert-success"><strong>Well done!</strong> <?php echo htmlentities($msg); ?> </div>
<?php } else if($error){?>
<div class="alert alert-danger"><strong>Oh snap!</strong> <?php echo htmlentities($error); ?> </div>
<?php } ?>

<form method="post">
<?php 
$cid=intval($_GET['classid']);
$sql = "SELECT * FROM tblclasses WHERE id=:cid";
$query = $dbh->prepare($sql);
$query->bindParam(':cid',$cid,PDO::PARAM_INT);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0) {
foreach($results as $result) {
?>

<!-- Branch -->
<div class="form-group has-success">
    <label class="control-label">Branch</label>
    <select name="branch" id="branch" class="form-control" required>
        <option value="<?php echo htmlentities($result->Branch);?>" selected>
            <?php echo htmlentities($result->Branch);?>
        </option>
        <option value="CSE">CSE</option>
        <option value="ECE">ECE</option>
        <option value="EEE">EEE</option>
        <option value="MECH">MECH</option>
        <option value="CIVIL">CIVIL</option>
        <option value="IT">IT</option>
        <option value="other">+ Add New Branch</option>
    </select>
    <input type="text" name="newbranch" id="newbranch" class="form-control" 
           placeholder="Enter new branch" style="margin-top:10px; display:none;">
</div>

<!-- Semester -->
<div class="form-group has-success">
    <label class="control-label">Semester</label>
    <select name="semester" class="form-control" required>
        <option value="<?php echo htmlentities($result->Semester);?>" selected>
            <?php echo htmlentities($result->Semester);?>
        </option>
        <option value="1-1">1-1</option>
        <option value="1-2">1-2</option>
        <option value="2-1">2-1</option>
        <option value="2-2">2-2</option>
        <option value="3-1">3-1</option>
        <option value="3-2">3-2</option>
        <option value="4-1">4-1</option>
        <option value="4-2">4-2</option>
    </select>
</div>

<!-- Section -->
<div class="form-group has-success">
    <label class="control-label">Section</label>
    <input type="text" name="section" value="<?php echo htmlentities($result->Section);?>" 
           class="form-control" required>
</div>

<!-- Regulation -->
<div class="form-group has-success">
    <label class="control-label">Regulation</label>
    <input type="text" name="regulation" value="<?php echo htmlentities($result->Regulation);?>" 
           class="form-control" required>
</div>

<!-- Batch Start -->
<div class="form-group has-success">
    <label class="control-label">Batch Start</label>
    <select name="batchstart" class="form-control" required>
        <option value="<?php echo htmlentities($result->BatchStart);?>" selected>
            <?php echo htmlentities($result->BatchStart);?>
        </option>
        <?php for($y=2020;$y<=2050;$y++){ ?>
            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
        <?php } ?>
    </select>
</div>

<!-- Batch End -->
<div class="form-group has-success">
    <label class="control-label">Batch End</label>
    <select name="batchend" class="form-control" required>
        <option value="<?php echo htmlentities($result->BatchEnd);?>" selected>
            <?php echo htmlentities($result->BatchEnd);?>
        </option>
        <?php for($y=2020;$y<=2050;$y++){ ?>
            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
        <?php } ?>
    </select>
</div>

<?php }} ?>

<div class="form-group has-success">
    <button type="submit" name="update" class="btn btn-success">
        Update <i class="fa fa-check"></i>
    </button>
</div>
</form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script>
    // Show "Add New Branch" textbox if selected
    document.addEventListener("DOMContentLoaded", function(){
        var branchSelect = document.getElementById("branch");
        var newBranchInput = document.getElementById("newbranch");
        branchSelect.addEventListener("change", function(){
            if(this.value === "other"){
                newBranchInput.style.display = "block";
                newBranchInput.required = true;
            } else {
                newBranchInput.style.display = "none";
                newBranchInput.required = false;
            }
        });
    });
    </script>
</body>
</html>
<?php } ?>
