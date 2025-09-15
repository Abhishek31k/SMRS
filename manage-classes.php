<?php 
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
} else {

//Code for Deletion
if(isset($_GET['id'])) { 
    $classid=$_GET['id'];
    $sql="DELETE FROM tblclasses WHERE id = :classid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':classid',$classid,PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Data deleted.")</script>';
    echo "<script>window.location.href ='manage-classes.php'</script>";
}    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Manage Classes</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
    <link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
    <link rel="stylesheet" href="css/prism/prism.css" media="screen" >
    <link rel="stylesheet" type="text/css" href="js/DataTables/datatables.min.css"/>
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <link rel="icon" type="images/images/crrengglogo.png" href="images/crrengglogo.png" />
    <script src="js/modernizr/modernizr.min.js"></script>
    <style>
        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        .succWrap{
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
        }
        thead select {
            width: 100%;
            padding: 2px;
            font-size: 12px;
        }
    </style>
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
                                <h2 class="title">Manage Classes</h2>
                            </div>
                        </div>
                        <div class="row breadcrumb-div">
                            <div class="col-md-6">
                                <ul class="breadcrumb">
            						<li><a href="dashboard.php"><i class="fa fa-home"></i> Home</a></li>
                                    <li> Classes</li>
            						<li class="active">Manage Classes</li>
            					</ul>
                            </div>
                        </div>
                    </div>

                    <section class="section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <div class="panel-title">
                                                <h5>View Classes Info</h5>
                                            </div>
                                        </div>
                                        <div class="panel-body p-20">
                                            <table id="example" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Branch</th>
                                                        <th>Semester</th>
                                                        <th>Section</th>
                                                        <th>Regulation</th>
                                                        <th>Batch</th>
                                                        <th>Creation Date</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    <tr>
                                                        <th></th>
                                                        <th><select><option value="">All</option></select></th>
                                                        <th><select><option value="">All</option></select></th>
                                                        <th><select><option value="">All</option></select></th>
                                                        <th><select><option value="">All</option></select></th>
                                                        <th><select><option value="">All</option></select></th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
<?php 
$sql = "SELECT id, Branch, Semester, Section, Regulation, BatchStart, BatchEnd, CreationDate FROM tblclasses";
$query = $dbh->prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0) {
    foreach($results as $result) {   
        $batch = $result->BatchStart . " - " . $result->BatchEnd;
?>
<tr>
    <td><?php echo htmlentities($cnt);?></td>
    <td><?php echo htmlentities($result->Branch);?></td>
    <td><?php echo htmlentities($result->Semester);?></td>
    <td><?php echo htmlentities($result->Section);?></td>
    <td><?php echo htmlentities($result->Regulation);?></td>
    <td><?php echo htmlentities($batch);?></td>
    <td><?php echo htmlentities($result->CreationDate);?></td>
    <td>
        <a href="edit-class.php?classid=<?php echo htmlentities($result->id);?>" class="btn btn-info btn-xs"> Edit </a> 
        <a href="manage-classes.php?id=<?php echo $result->id;?>&del=delete" 
           onClick="return confirm('Are you sure you want to delete?')" 
           class="btn btn-danger btn-xs">Delete</a>
    </td>
</tr>
<?php $cnt=$cnt+1; } } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <script src="js/bootstrap/bootstrap.min.js"></script>
    <script src="js/pace/pace.min.js"></script>
    <script src="js/lobipanel/lobipanel.min.js"></script>
    <script src="js/iscroll/iscroll.js"></script>
    <script src="js/prism/prism.js"></script>
    <script src="js/DataTables/datatables.min.js"></script>
    <script src="js/main.js"></script>

    <script>
    $(document).ready(function() {
        var table = $('#example').DataTable({
            initComplete: function () {
                this.api().columns().every(function () {
                    var column = this;
                    var select = $('select', column.header());
                    if (select.length > 0) {
                        column.data().unique().sort().each(function (d, j) {
                            if(d){
                                select.append('<option value="'+d+'">'+d+'</option>')
                            }
                        });
                        select.on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^'+val+'$' : '', true, false).draw();
                        });
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
<?php } ?>
