<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {
    if(isset($_POST['submit'])) {
        $branch=$_POST['branch'];
        if($branch=="other") { 
            $branch=$_POST['newbranch'];
        }
        $semester=$_POST['semester']; 
        $section=$_POST['section'];
        $regulation=$_POST['regulation'];
        $batchstart=$_POST['batchstart'];
        $batchend=$_POST['batchend'];

        $sql="INSERT INTO tblclasses(Branch,Semester,Section,Regulation,BatchStart,BatchEnd) 
            VALUES(:branch,:semester,:section,:regulation,:batchstart,:batchend)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':branch',$branch,PDO::PARAM_STR);
        $query->bindParam(':semester',$semester,PDO::PARAM_STR);
        $query->bindParam(':section',$section,PDO::PARAM_STR);
        $query->bindParam(':regulation',$regulation,PDO::PARAM_STR);
        $query->bindParam(':batchstart',$batchstart,PDO::PARAM_INT);
        $query->bindParam(':batchend',$batchend,PDO::PARAM_INT);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        if($lastInsertId) {
            $msg="Class Created successfully";
        } else { 
            $error="Something went wrong. Please try again";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin Create Class</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
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
                                <h2 class="title">Create Student Class</h2>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <h5>Create Student Class</h5>
                                        </div>
                                        <?php if($msg){?>
                                        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
                                        <?php } else if($error){?>
                                        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
                                        <?php } ?>

                                        <div class="panel-body">
                                            <form method="post">
                                                <!-- Branch Selection -->
                                                <div class="form-group has-success">
                                                    <label class="control-label">Branch</label>
                                                    <select name="branch" id="branch" class="form-control" required>
                                                        <option value="">-- Select Branch --</option>
                                                        <option value="CSE">CSE</option>
                                                        <option value="ECE">ECE</option>
                                                        <option value="EEE">EEE</option>
                                                        <option value="MECH">MECH</option>
                                                        <option value="CIVIL">CIVIL</option>
                                                        <option value="IT">IT</option>
                                                        <option value="other">+ Add New Branch</option>
                                                    </select>
                                                    <input type="text" name="newbranch" id="newbranch" class="form-control" placeholder="Enter new branch" style="margin-top:10px; display:none;">
                                                </div>

                                                <!-- Semester Selection -->
                                                <div class="form-group has-success">
                                                    <label class="control-label">Semester</label>
                                                    <select name="semester" class="form-control" required>
                                                        <option value="">-- Select Semester --</option>
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
                                                    <input type="text" name="section" class="form-control" required>
                                                    <span class="help-block">Eg- A, B, C</span>
                                                </div>

                                                <!-- Regulation -->
                                                <div class="form-group has-success">
                                                    <label class="control-label">Regulation</label>
                                                    <input type="text" name="regulation" class="form-control" required>
                                                    <span class="help-block">Eg- R20, R23</span>
                                                </div>

                                                <!-- Batch Start -->
                                                <div class="form-group has-success">
                                                    <label class="control-label">Batch Start</label>
                                                    <select name="batchstart" class="form-control" required>
                                                        <option value="">-- Select Year --</option>
                                                        <?php for($y=2020;$y<=2050;$y++){ ?>
                                                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <!-- Batch End -->
                                                <div class="form-group has-success">
                                                    <label class="control-label">Batch End</label>
                                                    <select name="batchend" class="form-control" required>
                                                        <option value="">-- Select Year --</option>
                                                        <?php for($y=2020;$y<=2050;$y++){ ?>
                                                            <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <!-- Submit -->
                                                <div class="form-group has-success">
                                                    <button type="submit" name="submit" class="btn btn-success">
                                                        Submit <i class="fa fa-check"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div> <!-- panel-body -->
                                    </div> <!-- panel -->
                                </div>
                            </div>
                        </div>
                    </section>
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
