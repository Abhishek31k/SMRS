<?php
session_start();
error_reporting(0);
include('includes/config.php');

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit();
}

// Save attendance submission
if(isset($_POST['save_attendance'])){
    $regno    = $_POST['regno'];
    $date     = $_POST['date'];
    $total    = $_POST['total_periods'];
    $present  = $_POST['present_periods'];
    $semester = $_POST['semester'];

    // Check if already exists for this student + semester
$sql = "SELECT id FROM tblattendance 
        WHERE RegNo=:regno AND Semester=:semester";
$query = $dbh->prepare($sql);
$query->bindParam(':regno',$regno,PDO::PARAM_STR);
$query->bindParam(':semester',$semester,PDO::PARAM_STR);
$query->execute();

if($query->rowCount() > 0){
    // Update existing record
    $sqlUpd = "UPDATE tblattendance 
               SET TotalPeriods=:total, PresentPeriods=:present, AttendanceDate=:date
               WHERE RegNo=:regno AND Semester=:semester";
    $queryUpd = $dbh->prepare($sqlUpd);
    $queryUpd->bindParam(':total',$total,PDO::PARAM_INT);
    $queryUpd->bindParam(':present',$present,PDO::PARAM_INT);
    $queryUpd->bindParam(':date',$date,PDO::PARAM_STR);
    $queryUpd->bindParam(':regno',$regno,PDO::PARAM_STR);
    $queryUpd->bindParam(':semester',$semester,PDO::PARAM_STR);
    $queryUpd->execute();
} else {
    // Insert new record
    $sqlIns = "INSERT INTO tblattendance(RegNo, Semester, AttendanceDate, TotalPeriods, PresentPeriods) 
               VALUES(:regno,:semester,:date,:total,:present)";
    $queryIns = $dbh->prepare($sqlIns);
    $queryIns->bindParam(':regno',$regno,PDO::PARAM_STR);
    $queryIns->bindParam(':semester',$semester,PDO::PARAM_STR);
    $queryIns->bindParam(':date',$date,PDO::PARAM_STR);
    $queryIns->bindParam(':total',$total,PDO::PARAM_INT);
    $queryIns->bindParam(':present',$present,PDO::PARAM_INT);
    $queryIns->execute();
}


    echo "<script>window.location.href='manage-attendance.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Manage Attendance</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="css/main.css"><link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <style>
        .dataTables_wrapper { width: 100%; overflow-x: auto; }
        input[type=number], input[type=date] { width: 120px; }
        form { margin:0; }
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
                    <h2 class="title">Manage Attendance</h2>

                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="filterBatch" class="form-control">
                                <option value="">All Batches</option>
                                <?php 
                                $sql="SELECT DISTINCT BatchStart,BatchEnd FROM tblclasses ORDER BY BatchStart DESC";
                                $query=$dbh->prepare($sql);
                                $query->execute();
                                foreach($query->fetchAll(PDO::FETCH_OBJ) as $row){ 
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
                                foreach($query->fetchAll(PDO::FETCH_OBJ) as $row){ 
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
                                foreach($query->fetchAll(PDO::FETCH_OBJ) as $row){ 
                                    echo "<option value='".$row->Section."'>".$row->Section."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Attendance Table -->
                    <div class="panel">
                        <div class="panel-body">
                            <table id="attendanceTable" class="display table table-striped table-bordered nowrap" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Reg No</th>
                                        <th>Student Name</th>
                                        <th>Batch</th>
                                        <th>Branch</th>
                                        <th>Section</th>
                                        <th>Semester</th>
                                        <th>Date</th>
                                        <th>Total Periods</th>
                                        <th>Present</th>
                                        <th>Percentage</th>
                                        <th>Save</th>
                                    </tr>
                                </thead>
                                <tbody>
<?php
$sql = "SELECT 
            s.RegNo, s.StudentName, 
            c.BatchStart, c.BatchEnd, c.Branch, c.Section, c.Semester,
            a.AttendanceDate, a.TotalPeriods, a.PresentPeriods
        FROM tblstudents s
        LEFT JOIN tblclasses c ON c.id = s.ClassId
        LEFT JOIN tblattendance a ON a.id = (
            SELECT id FROM tblattendance 
            WHERE RegNo = s.RegNo 
            ORDER BY AttendanceDate DESC 
            LIMIT 1
        )
        ORDER BY s.RegNo ASC";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if($query->rowCount() > 0){
    foreach($results as $row){ 
        $batch = $row->BatchStart && $row->BatchEnd ? $row->BatchStart."-".$row->BatchEnd : "-";
        $totalPeriods   = $row->TotalPeriods ?? 0;
        $presentPeriods = $row->PresentPeriods ?? 0;
        $percent        = ($totalPeriods > 0) ? round(($presentPeriods / $totalPeriods) * 100, 2) : 0;
?>
<tr>
<form method="POST">
    <td>
        <?php echo htmlentities($row->RegNo); ?>
        <input type="hidden" name="regno" value="<?php echo htmlentities($row->RegNo); ?>">
        <input type="hidden" name="semester" value="<?php echo htmlentities($row->Semester); ?>">
    </td>
    <td><?php echo htmlentities($row->StudentName); ?></td>
    <td><?php echo htmlentities($batch); ?></td>
    <td><?php echo htmlentities($row->Branch); ?></td>
    <td><?php echo htmlentities($row->Section); ?></td>
    <td><?php echo htmlentities($row->Semester); ?></td>

    <!-- Editable Date -->
    <td>
        <input type="date" name="date" 
               value="<?php echo $row->AttendanceDate ? htmlentities($row->AttendanceDate) : date('Y-m-d'); ?>" 
               required>
    </td>

    <!-- Attendance Inputs -->
    <td><input type="number" name="total_periods" min="0" value="<?php echo $totalPeriods; ?>" required></td>
    <td><input type="number" name="present_periods" min="0" value="<?php echo $presentPeriods; ?>" required></td>

    <!-- Percentage -->
    <td><?php echo $percent; ?>%</td>

    <!-- Save Button -->
    <td><button type="submit" name="save_attendance" class="btn btn-success btn-xs">Save</button></td>
</form>
</tr>
<?php }} ?>
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
    var table = $('#attendanceTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
        "scrollX": true
    });

    // Filters
    $('#filterBatch').on('change', function(){ table.column(2).search(this.value).draw(); });
    $('#filterBranch').on('change', function(){ table.column(3).search(this.value).draw(); });
    $('#filterSection').on('change', function(){ table.column(4).search(this.value).draw(); });
});
</script>
</body>
</html>
