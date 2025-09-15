<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
}
else {
    if(isset($_POST['Update'])) {
        $sid = intval($_GET['subjectid']);
        $subjectname = $_POST['subjectname'];
        $subjectcode = $_POST['subjectcode']; 

        $sql="UPDATE tblsubjects 
              SET SubjectName=:subjectname, SubjectCode=:subjectcode
              WHERE id=:sid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':subjectname',$subjectname,PDO::PARAM_STR);
        $query->bindParam(':subjectcode',$subjectcode,PDO::PARAM_STR);
        $query->bindParam(':sid',$sid,PDO::PARAM_INT);
        $query->execute();
        $msg="Subject updated successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Subject</title>
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
    <div class="row page-title-div">
        <div class="col-md-6">
            <h2 class="title">Edit Subject</h2>
        </div>
    </div>

    <?php if($msg){?>
        <div class="alert alert-success"><strong>✔</strong> <?php echo htmlentities($msg); ?></div>
    <?php } else if($error){?>
        <div class="alert alert-danger"><strong>✘</strong> <?php echo htmlentities($error); ?></div>
    <?php } ?>

    <div class="panel">
        <div class="panel-body">
            <form method="post" class="form-horizontal">
            <?php
            $sid=intval($_GET['subjectid']);
            $sql = "SELECT * FROM tblsubjects WHERE id=:sid";
            $query = $dbh->prepare($sql);
            $query->bindParam(':sid',$sid,PDO::PARAM_INT);
            $query->execute();
            $results=$query->fetchAll(PDO::FETCH_OBJ);
            if($query->rowCount() > 0) {
                foreach($results as $result) {
            ?>  

            <!-- Subject Name -->
            <div class="form-group">
                <label class="col-sm-2 control-label">Subject Name</label>
                <div class="col-sm-10">
                    <input type="text" name="subjectname" value="<?php echo htmlentities($result->SubjectName);?>" class="form-control" required>
                </div>
            </div>

            <!-- Subject Code -->
            <div class="form-group">
                <label class="col-sm-2 control-label">Subject Code</label>
                <div class="col-sm-10">
                    <input type="text" name="subjectcode" value="<?php echo htmlentities($result->SubjectCode);?>" class="form-control" required>
                </div>
            </div>

            <?php }} ?>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" name="Update" class="btn btn-primary">Update</button>
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
<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
