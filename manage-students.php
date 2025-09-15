<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}
if(isset($_GET['regno'])) { 
    $regno = $_GET['regno'];
    $sql = "DELETE FROM tblstudents WHERE RegNo = :regno";
    $query = $dbh->prepare($sql);
    $query->bindParam(':regno',$regno,PDO::PARAM_STR);
    $query->execute();
    echo '<script>alert("Student deleted successfully.")</script>';
    echo "<script>window.location.href ='manage-students.php'</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Manage Students</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="css/main.css"><link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <style>
        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
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
                    <h2 class="title">Manage Students</h2>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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
                    </div>

                    <div class="panel">
                        <div class="panel-body">
                            <table id="studentsTable" class="display table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Actions</th>
                                        <th>Reg No</th>
                                        <th>Student Name</th>
                                        <th>DOB</th>
                                        <th>Gender</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Father Name</th>
                                        <th>Father Mobile</th>
                                        <th>Admission Type</th>
                                        <th>Counsellor</th>
                                        <th>Counsellor Mobile</th>
                                        <th>Batch</th>
                                        <th>Branch</th>
                                        <th>Section</th>
                                        <th>Reg Date</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php
$sql = "SELECT s.RegNo, s.StudentName, s.DOB, s.Gender, s.StudentEmail, s.StudentMobile,
               s.FatherName, s.FatherMobile, s.AdmissionType, s.CounsellorName, s.CounsellorMobile,
               s.RegDate, c.id as classid, c.BatchStart, c.BatchEnd, c.Branch, c.Section
        FROM tblstudents s
        LEFT JOIN tblclasses c ON c.id = s.ClassId
        ORDER BY s.RegNo ASC";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
foreach($results as $row){ 
    $batch = $row->BatchStart && $row->BatchEnd ? $row->BatchStart."-".$row->BatchEnd : "-";
?>
<tr>
    <td>
        <a href="edit-student.php?regno=<?php echo urlencode($row->RegNo); ?>" class="btn btn-primary btn-xs">Edit</a>
        <a href="manage-students.php?regno=<?php echo urlencode($row->RegNo); ?>" 
           class="btn btn-danger btn-xs" 
           onclick="return confirm('Are you sure you want to delete this student?');">
           Delete
        </a>
    <td><?php echo htmlentities($row->RegNo); ?></td>
    <td><?php echo htmlentities($row->StudentName); ?></td>
    <td><?php echo htmlentities($row->DOB); ?></td>
    <td><?php echo htmlentities($row->Gender); ?></td>
    <td><?php echo htmlentities($row->StudentEmail); ?></td>
    <td><?php echo htmlentities($row->StudentMobile); ?></td>
    <td><?php echo htmlentities($row->FatherName); ?></td>
    <td><?php echo htmlentities($row->FatherMobile); ?></td>
    <td><?php echo htmlentities($row->AdmissionType); ?></td>
    <td><?php echo htmlentities($row->CounsellorName); ?></td>
    <td><?php echo htmlentities($row->CounsellorMobile); ?></td>
    <td><?php echo htmlentities($batch); ?></td>
    <td><?php echo htmlentities($row->Branch); ?></td>
    <td><?php echo htmlentities($row->Section); ?></td>
    <td><?php echo htmlentities($row->RegDate); ?></td>
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
    var table = $('#studentsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        "scrollX": true
    });

    // Batch filter (column index 12)
    $('#filterBatch').on('change', function(){
        table.column(12).search(this.value).draw();
    });
    // Branch filter (column index 13)
    $('#filterBranch').on('change', function(){
        table.column(13).search(this.value).draw();
    });
    // Section filter (column index 14)
    $('#filterSection').on('change', function(){
        table.column(14).search(this.value).draw();
    });
});
</script>
</body>
</html>
