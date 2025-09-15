<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}

// Delete result record
if(isset($_GET['id'])) { 
    $id = $_GET['id'];
    $sql = "DELETE FROM tblresult WHERE id = :id";
    $query = $dbh->prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_INT);
    $query->execute();
    echo '<script>alert("Result deleted successfully.")</script>';
    echo "<script>window.location.href ='manage-results.php'</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Manage Results</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="css/main.css"><link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css">
    <style>
        .dataTables_wrapper { width: 100%; overflow-x: auto; }
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
                    <h2 class="title">Manage Results</h2>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <select id="filterBatch" class="form-control">
                                <option value="">All Batches</option>
                                <?php 
                                $sql="SELECT DISTINCT BatchStart,BatchEnd FROM tblclasses ORDER BY BatchStart DESC";
                                $query=$dbh->prepare($sql);
                                $query->execute();
                                $batches=$query->fetchAll(PDO::FETCH_OBJ);
                                foreach($batches as $row){ 
                                    $batch = $row->BatchStart."-".$row->BatchEnd;
                                    echo "<option value='".$batch."'>".$batch."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterBranch" class="form-control">
                                <option value="">All Branches</option>
                                <?php 
                                $sql="SELECT DISTINCT Branch FROM tblclasses ORDER BY Branch ASC";
                                $query=$dbh->prepare($sql);
                                $query->execute();
                                $branches=$query->fetchAll(PDO::FETCH_OBJ);
                                foreach($branches as $row){ 
                                    echo "<option value='".$row->Branch."'>".$row->Branch."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterSemester" class="form-control">
                                <option value="">All Semesters</option>
                                <?php 
                                $sql="SELECT DISTINCT Semester FROM tblclasses ORDER BY Semester ASC";
                                $query=$dbh->prepare($sql);
                                $query->execute();
                                $semesters=$query->fetchAll(PDO::FETCH_OBJ);
                                foreach($semesters as $row){ 
                                    echo "<option value='".$row->Semester."'>".$row->Semester."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="filterSection" class="form-control">
                                <option value="">All Sections</option>
                                <?php 
                                $sql="SELECT DISTINCT Section FROM tblclasses ORDER BY Section ASC";
                                $query=$dbh->prepare($sql);
                                $query->execute();
                                $sections=$query->fetchAll(PDO::FETCH_OBJ);
                                foreach($sections as $row){ 
                                    echo "<option value='".$row->Section."'>".$row->Section."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="filterRegNo" class="form-control" placeholder="Filter by RegNo">
                        </div>
                    </div>

                    <div class="panel">
                        <div class="panel-body">
                            <table id="resultsTable" class="display table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Reg No</th>
                                        <th>Student Name</th>
                                        <th>Batch</th> 
                                        <th>Branch</th>
                                        <th>Section</th>
                                        <th>Semester</th>
                                        <th>Subject</th>
                                        <th>Subject Code</th>
                                        <th>Internals</th>
                                        <th>Grade</th>
                                        <th>Credits</th>
                                    </tr>
                                </thead>

                                <tbody>
<?php
$sql = "SELECT r.id, r.RegNo, s.StudentName, r.Semester, r.Subject, r.SubjectCode, 
               r.Internals, r.Grade, r.Credits, c.BatchStart, c.BatchEnd, c.Branch, c.Section
        FROM tblresult r
        LEFT JOIN tblstudents s ON s.RegNo = r.RegNo
        LEFT JOIN tblclasses c ON s.ClassId = c.id
        ORDER BY r.RegNo ASC";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
foreach($results as $row){ 
    $batch = $row->BatchStart && $row->BatchEnd ? $row->BatchStart."-".$row->BatchEnd : "-";
?>
<tr>
    <td>
        <a href="edit-result.php?id=<?php echo urlencode($row->id); ?>" class="btn btn-primary btn-xs">Edit</a>
        <a href="manage-results.php?id=<?php echo urlencode($row->id); ?>" 
           class="btn btn-danger btn-xs" 
           onclick="return confirm('Are you sure you want to delete this result?');">
           Delete
        </a>
    </td>
    <td><?php echo htmlentities($row->RegNo); ?></td>
    <td><?php echo htmlentities($row->StudentName); ?></td>
    <td><?php echo htmlentities($batch); ?></td> <!-- Show Batch -->
    <td><?php echo htmlentities($row->Branch); ?></td>
    <td><?php echo htmlentities($row->Section); ?></td>
    <td><?php echo htmlentities($row->Semester); ?></td>
    <td><?php echo htmlentities($row->Subject); ?></td>
    <td><?php echo htmlentities($row->SubjectCode); ?></td>
    <td><?php echo htmlentities($row->Internals); ?></td>
    <td><?php echo htmlentities($row->Grade); ?></td>
    <td><?php echo htmlentities($row->Credits); ?></td>
</tr>

<?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap.min.js"></script>
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
    var table = $('#resultsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        "scrollX": true
    });

    // Batch filter (BatchStart-BatchEnd is not directly shown, we use regex match on Branch+Section if needed)
    $('#filterBatch').on('change', function(){
    table.column(3).search(this.value).draw(); 
    });
    $('#filterBranch').on('change', function(){
        table.column(4).search(this.value).draw();
    });
    $('#filterSemester').on('change', function(){
        table.column(6).search(this.value).draw();
    });
    $('#filterSection').on('change', function(){
        table.column(5).search(this.value).draw();
    });
    $('#filterRegNo').on('keyup', function(){
        table.column(1).search(this.value).draw();
    });

});
</script>
</body>
</html>
