<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}

$msg = $error = "";

// Import Students
if(isset($_POST['import'])){
    $classid = $_POST['classid'];

    if(!empty($_FILES['file']['name'])){
        $filename = $_FILES['file']['tmp_name'];
        $handle = fopen($filename, "r");

        $row = 0;
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
            if($row == 0){ $row++; continue; } // skip header row

            $regno          = trim($data[0]);
            $studentname    = trim($data[1]);
            $dob            = trim($data[2]);
            $gender         = trim($data[3]);
            $email          = trim($data[4]);
            $studentmobile  = trim($data[5]);
            $fathername     = trim($data[6]);
            $fathermobile   = trim($data[7]);
            $admissiontype  = trim($data[8]);
            $counsellorname = trim($data[9]);
            $counsellormob  = trim($data[10]);

            // Convert DOB to yyyy-mm-dd if provided
            if($dob != ""){
                $dob = date("Y-m-d", strtotime(str_replace("/","-",$dob)));
            } else {
                $dob = null;
            }

            try {
                $sql="INSERT INTO tblstudents 
                (RegNo, StudentName, DOB, Gender, StudentEmail, StudentMobile, FatherName, FatherMobile, AdmissionType, CounsellorName, CounsellorMobile, ClassId) 
                VALUES (:regno,:studentname,:dob,:gender,:email,:studentmobile,:fathername,:fathermobile,:admissiontype,:counsellorname,:counsellormobile,:classid)";

                $query=$dbh->prepare($sql);
                $query->bindParam(':regno',$regno,PDO::PARAM_STR);
                $query->bindParam(':studentname',$studentname,PDO::PARAM_STR);
                $query->bindParam(':dob',$dob,PDO::PARAM_STR);
                $query->bindParam(':gender',$gender,PDO::PARAM_STR);
                $query->bindParam(':email',$email,PDO::PARAM_STR);
                $query->bindParam(':studentmobile',$studentmobile,PDO::PARAM_STR);
                $query->bindParam(':fathername',$fathername,PDO::PARAM_STR);
                $query->bindParam(':fathermobile',$fathermobile,PDO::PARAM_STR);
                $query->bindParam(':admissiontype',$admissiontype,PDO::PARAM_STR);
                $query->bindParam(':counsellorname',$counsellorname,PDO::PARAM_STR);
                $query->bindParam(':counsellormobile',$counsellormob,PDO::PARAM_STR);
                $query->bindParam(':classid',$classid,PDO::PARAM_INT);
                $query->execute();
            } catch (PDOException $e) {
                // Skip if duplicate RegNo or other errors
                continue;
            }
        }
        fclose($handle);
        $msg="Students imported successfully!";
    } else {
        $error="Please upload a valid CSV file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Add Students</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">

    <?php include('includes/topbar.php');?> 
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php');?>  

            <div class="main-page">
                <div class="container-fluid">
                    <h2 class="title">Add Students (Bulk Import)</h2>

                    <?php if($msg){?>
                        <div class="alert alert-success"><strong>✔</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } else if($error){?>
                        <div class="alert alert-danger"><strong>✘</strong> <?php echo htmlentities($error); ?></div>
                    <?php } ?>

                    <div class="panel">
                        <div class="panel-body">
<form class="form-horizontal" method="post" enctype="multipart/form-data">

    <!-- Batch -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Batch</label>
        <div class="col-sm-10">
            <select id="batchSelect" name="batch" class="form-control" required>
                <option value="">Select Batch</option>
                <?php 
                $sql="SELECT DISTINCT BatchStart,BatchEnd FROM tblclasses ORDER BY BatchStart DESC";
                $query=$dbh->prepare($sql);
                $query->execute();
                $results=$query->fetchAll(PDO::FETCH_OBJ);
                foreach($results as $row){ 
                    $batch = $row->BatchStart."-".$row->BatchEnd;
                ?>
                  <option value="<?php echo $batch; ?>"><?php echo $batch; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <!-- Branch -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Branch</label>
        <div class="col-sm-10">
            <select id="branchSelect" name="branch" class="form-control" required>
                <option value="">Select Branch</option>
            </select>
        </div>
    </div>

    <!-- Section -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Section</label>
        <div class="col-sm-10">
            <select name="classid" id="sectionSelect" class="form-control" required>
                <option value="">Select Section</option>
            </select>
        </div>
    </div>

    <!-- Upload -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Upload CSV</label>
        <div class="col-sm-10">
            <input type="file" name="file" class="form-control" accept=".csv" required>
            <small>CSV format: RegNo,StudentName,DOB,Gender,StudentEmail,StudentMobile,FatherName,FatherMobile,AdmissionType,CounsellorName,CounsellorMobile</small>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="import" class="btn btn-primary">Import Students</button>
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
$(document).ready(function(){
    // Step 1: Batch -> Branch
    $("#batchSelect").change(function(){
        var batch = $(this).val();
        $.ajax({
            url: "get-batch-branches.php",  
            method: "POST",
            data: {batch: batch},
            success: function(data){
                $("#branchSelect").html(data);
                $("#sectionSelect").html('<option value="">Select Section</option>');
            }
        });
    });

    // Step 2: Branch -> Section
    $("#branchSelect").change(function(){
        var batch = $("#batchSelect").val();
        var branch = $(this).val();
        $.ajax({
            url: "get-branch-sections.php",
            method: "POST",
            data: {batch: batch, branch: branch},
            success: function(data){
                $("#sectionSelect").html(data);
            }
        });
    });
});
</script>
</body>
</html>
