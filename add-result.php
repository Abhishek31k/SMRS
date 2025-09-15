<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}

$msg = $error = "";

// Import Results
if(isset($_POST['import'])){
    $semester = $_POST['semester'];

    if(!empty($_FILES['file']['name'])){
        $filename = $_FILES['file']['tmp_name'];
        $handle = fopen($filename, "r");

        $row = 0;
        while(($data = fgetcsv($handle, 1000, ",")) !== FALSE){
            if($row == 0){ $row++; continue; } // skip header row

            $regno       = trim($data[0]);
            $subject     = trim($data[1]);
            $subjectcode = trim($data[2]);
            $internals   = trim($data[3]);
            $grade       = trim($data[4]);
            $credits     = trim($data[5]);

            try {
                $sql="INSERT INTO tblresult 
                (RegNo, Semester, Subject, SubjectCode, Internals, Grade, Credits) 
                VALUES (:regno, :semester, :subject, :subjectcode, :internals, :grade, :credits)";

                $query=$dbh->prepare($sql);
                $query->bindParam(':regno',$regno,PDO::PARAM_STR);
                $query->bindParam(':semester',$semester,PDO::PARAM_STR);
                $query->bindParam(':subject',$subject,PDO::PARAM_STR);
                $query->bindParam(':subjectcode',$subjectcode,PDO::PARAM_STR);
                $query->bindParam(':internals',$internals,PDO::PARAM_INT);
                $query->bindParam(':grade',$grade,PDO::PARAM_STR);
                $query->bindParam(':credits',$credits,PDO::PARAM_STR);
                $query->execute();

            } catch (PDOException $e) {
                // skip duplicate or invalid rows
                continue;
            }
        }
        fclose($handle);
        $msg="Results imported successfully!";
    } else {
        $error="Please upload a valid CSV file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SRMS Admin | Add Results</title>
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
                    <h2 class="title">Add Results (Bulk Import)</h2>

                    <?php if($msg){?>
                        <div class="alert alert-success"><strong>✔</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } else if($error){?>
                        <div class="alert alert-danger"><strong>✘</strong> <?php echo htmlentities($error); ?></div>
                    <?php } ?>

                    <div class="panel">
                        <div class="panel-body">
<form class="form-horizontal" method="post" enctype="multipart/form-data">

    <!-- Semester -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Semester</label>
        <div class="col-sm-10">
            <select name="semester" class="form-control" required>
                <option value="">Select Semester</option>
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
    </div>


    <!-- Upload CSV -->
    <div class="form-group">
        <label class="col-sm-2 control-label">Upload CSV</label>
        <div class="col-sm-10">
            <input type="file" name="file" class="form-control" accept=".csv" required>
            <small>CSV format: RegNo,Subject,SubjectCode,Internals,Grade,Credits</small>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="import" class="btn btn-primary">Import Results</button>
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
</body>
</html>
