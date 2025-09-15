<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
}
else {
if(isset($_POST['submit']))
{
    $classid       = $_POST['classid'];   // from dropdown
    $subjectnames  = $_POST['subjectname'];
    $subjectcodes  = $_POST['subjectcode'];

    $ok = false;
    for($i=0; $i<count($subjectnames); $i++){
        if(!empty($subjectnames[$i]) && !empty($subjectcodes[$i])){
            $sql="INSERT INTO tblsubjects(SubjectName, SubjectCode, ClassId) 
                  VALUES(:subjectname, :subjectcode, :classid)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':subjectname',$subjectnames[$i],PDO::PARAM_STR);
            $query->bindParam(':subjectcode',$subjectcodes[$i],PDO::PARAM_STR);
            $query->bindParam(':classid',$classid,PDO::PARAM_INT);
            $query->execute();
            $ok = true;
        }
    }
    if($ok) $msg="Subjects Created successfully";
    else $error="Something went wrong. Please try again";
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin Subject Creation</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script>
    $(document).ready(function(){

        // Add subject row
        $("#addMore").click(function(){
            var newRow = $(".subjectRow:first").clone();
            newRow.find("input").val("");
            $("#subjectsContainer").append(newRow);
        });

        // When batch is selected
        $("#batchSelect").change(function(){
            $("#semesterSelect").prop("disabled", false);
        });

        // When semester is selected
        $("#semesterSelect").change(function(){
            var batch = $("#batchSelect").val();
            var semester = $(this).val();
            $.ajax({
                type: "POST",
                url: "get-branches.php",
                data: {batch: batch, semester: semester},
                success: function(data){
                    $("#branchSelect").html(data);
                }
            });
        });

    });
    </script>
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
   <div class="col-md-6"><h2 class="title">Subject Creation</h2></div>
  </div>
  <div class="row breadcrumb-div">
   <div class="col-md-6">
    <ul class="breadcrumb">
     <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
     <li> Subjects</li>
     <li class="active">Create Subject</li>
    </ul>
   </div>
  </div>
 </div>

 <div class="container-fluid">
  <div class="row">
   <div class="col-md-12">
    <div class="panel">
     <div class="panel-heading"><div class="panel-title"><h5>Create Subject</h5></div></div>
     <div class="panel-body">
      <?php if($msg){?><div class="alert alert-success"><strong>Well done!</strong> <?php echo htmlentities($msg); ?></div><?php } 
      else if($error){?><div class="alert alert-danger"><strong>Oh snap!</strong> <?php echo htmlentities($error); ?></div><?php } ?>

      <form class="form-horizontal" method="post">
        
        <!-- Select Batch -->
        <div class="form-group">
          <label class="col-sm-2 control-label">Select Batch</label>
          <div class="col-sm-10">
            <select id="batchSelect" class="form-control" required>
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

        <!-- Select Semester -->
        <div class="form-group">
          <label class="col-sm-2 control-label">Select Semester</label>
          <div class="col-sm-10">
            <select id="semesterSelect"  class="form-control" disabled required>
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

        <!-- Select Branch -->
        <div class="form-group">
          <label class="col-sm-2 control-label">Select Branch</label>
          <div class="col-sm-10">
            <select name="classid" id="branchSelect" class="form-control" required>
              <option value="">Select Branch</option>
            </select>
          </div>
        </div>

        <!-- Subject Fields -->
        <div id="subjectsContainer">
          <div class="form-group row subjectRow">
            <div class="col-sm-6">
              <input type="text" name="subjectname[]" class="form-control" placeholder="Subject Name" required>
            </div>
            <div class="col-sm-4">
              <input type="text" name="subjectcode[]" class="form-control" placeholder="Subject Code" required>
            </div>
          </div>
        </div>
        <button type="button" id="addMore" class="btn btn-info">+ Add More</button>
        <br><br>
        
        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-10">
            <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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
</div>
</div>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/DataTables/datatables.min.js"></script>

<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
