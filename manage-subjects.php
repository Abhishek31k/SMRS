<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {

// ====== Delete subject ======
if(isset($_GET['id'])) { 
    $subid = $_GET['id'];
    $sql = "DELETE FROM tblsubjects WHERE id = :subid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':subid',$subid,PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Data deleted.")</script>';
    echo "<script>window.location.href ='manage-subjects.php'</script>";
} 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Manage Subjects</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <style>
        .errorWrap { padding:10px; margin:0 0 20px 0; background:#fff; border-left:4px solid #dd3d36; }
        .succWrap { padding:10px; margin:0 0 20px 0; background:#fff; border-left:4px solid #5cb85c; }
    </style>
    <script>
    $(document).ready(function(){

        // Enable semester when batch selected
        $("#batchSelect").change(function(){
            $("#semesterSelect").prop("disabled", false);
        });

        // Load branches for selected batch + semester
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
   <div class="col-md-6"><h2 class="title">Manage Subjects</h2></div>
  </div>
  <div class="row breadcrumb-div">
   <div class="col-md-6">
    <ul class="breadcrumb">
     <li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
     <li> Subjects</li>
     <li class="active">Manage Subjects</li>
    </ul>
   </div>
  </div>
 </div>

 <section class="section">
 <div class="container-fluid">
  <div class="panel">
   <div class="panel-heading"><div class="panel-title"><h5>View Subjects Info</h5></div></div>
   <div class="panel-body p-20">

    <!-- Filter Form -->
    <form method="post" class="form-inline">
      <!-- Batch -->
      <div class="form-group">
        <label>Batch</label>
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
            <option value="<?php echo $batch; ?>" 
              <?php if(isset($_POST['batch']) && $_POST['batch']==$batch) echo "selected"; ?>>
              <?php echo $batch; ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <!-- Semester -->
      <div class="form-group">
        <label>Semester</label>
        <select id="semesterSelect" name="semester" class="form-control" 
                <?php if(!isset($_POST['semester'])) echo "disabled"; ?> required>
          <option value="">Select Semester</option>
          <?php 
          $sems = ["1-1","1-2","2-1","2-2","3-1","3-2","4-1","4-2"];
          foreach($sems as $s){ ?>
            <option value="<?php echo $s; ?>" 
              <?php if(isset($_POST['semester']) && $_POST['semester']==$s) echo "selected"; ?>>
              <?php echo $s; ?>
            </option>
          <?php } ?>
        </select>
      </div>

      <!-- Branch -->
      <div class="form-group">
        <label>Branch</label>
        <select id="branchSelect" name="classid" class="form-control" required>
          <option value="">Select Branch</option>
          <?php 
          if(isset($_POST['batch']) && isset($_POST['semester'])){
              list($start,$end) = explode("-",$_POST['batch']);
              $sql="SELECT id, Branch FROM tblclasses 
                    WHERE BatchStart=:start AND BatchEnd=:end AND Semester=:sem 
                    GROUP BY Branch ORDER BY Branch";
              $query=$dbh->prepare($sql);
              $query->bindParam(':start',$start,PDO::PARAM_INT);
              $query->bindParam(':end',$end,PDO::PARAM_INT);
              $query->bindParam(':sem',$_POST['semester'],PDO::PARAM_STR);
              $query->execute();
              $results=$query->fetchAll(PDO::FETCH_OBJ);
              foreach($results as $row){ ?>
                <option value="<?php echo $row->id; ?>" 
                  <?php if(isset($_POST['classid']) && $_POST['classid']==$row->id) echo "selected"; ?>>
                  <?php echo htmlentities($row->Branch); ?>
                </option>
          <?php } } ?>
        </select>
      </div>

      <button type="submit" name="filter" class="btn btn-primary">Filter</button>
    </form>
    <br>

    <!-- Table -->
    <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
        <tr>
          <th>#</th>
          <th>Subject Name</th>
          <th>Subject Code</th>
          <th>Creation Date</th>
          <th>Updation Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php 
      if(isset($_POST['filter']) && !empty($_POST['classid'])){
          $classid = $_POST['classid'];
          $sql = "SELECT * FROM tblsubjects WHERE ClassId=:cid";
          $query = $dbh->prepare($sql);
          $query->bindParam(':cid',$classid,PDO::PARAM_INT);
      } else {
          $sql = "SELECT * FROM tblsubjects";
          $query = $dbh->prepare($sql);
      }
      $query->execute();
      $results=$query->fetchAll(PDO::FETCH_OBJ);
      $cnt=1;
      if($query->rowCount() > 0) {
        foreach($results as $result) { ?>  
          <tr>
            <td><?php echo htmlentities($cnt);?></td>
            <td><?php echo htmlentities($result->SubjectName);?></td>
            <td><?php echo htmlentities($result->SubjectCode);?></td>
            <td><?php echo htmlentities($result->Creationdate);?></td>
            <td><?php echo htmlentities($result->UpdationDate);?></td>
            <td>
              <a href="edit-subject.php?subjectid=<?php echo htmlentities($result->id);?>" 
                 class="btn btn-info btn-xs">Edit</a> 
              <a href="manage-subjects.php?id=<?php echo $result->id;?>&del=delete" 
                 onClick="return confirm('Are you sure you want to delete?')" 
                 class="btn btn-danger btn-xs">Delete</a>
            </td>
          </tr>
      <?php $cnt++; } } ?>
      </tbody>
    </table>

   </div>
  </div>
 </div>
 </section>

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
<script>
  $(function($){ $('#example').DataTable(); });
</script>
</body>
</html>
<?php } ?>
