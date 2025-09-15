<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check admin login
if(strlen($_SESSION['alogin'])==0){
    header("Location: index.php");
    exit;
}

// Assign staff to subject
if(isset($_POST['assign'])){
    $subjectId = intval($_POST['subject_id']);
    $staffId   = intval($_POST['staff_id']);

    $stmt = $dbh->prepare("
        INSERT INTO tblstaffsubjects (SubjectId, StaffId) 
        VALUES (:sid, :stf)
        ON DUPLICATE KEY UPDATE StaffId = :stf, AssignedDate = CURRENT_TIMESTAMP
    ");
    $stmt->bindParam(':sid',$subjectId);
    $stmt->bindParam(':stf',$staffId);
    $stmt->execute();

    $msg = "Staff assigned to subject successfully!";
}

// Filters
$where = [];
$params = [];
if(!empty($_GET['batch'])){
    $where[] = "CONCAT(c.BatchStart,'-',c.BatchEnd)=:batch";
    $params[':batch'] = $_GET['batch'];
}
if(!empty($_GET['branch'])){
    $where[] = "c.Branch=:branch";
    $params[':branch'] = $_GET['branch'];
}
if(!empty($_GET['semester'])){
    $where[] = "c.Semester=:sem";
    $params[':sem'] = $_GET['semester'];
}
if(!empty($_GET['section'])){
    $where[] = "c.Section=:sec";
    $params[':sec'] = $_GET['section'];
}

$subjects=[];
if($where){
    $sql = "SELECT s.id as SubjectId, s.SubjectName, s.SubjectCode, 
               c.Branch, c.Semester, c.Section, CONCAT(c.BatchStart,'-',c.BatchEnd) as Batch,
               ss.StaffId, st.StaffName, st.StaffCode, st.Department
        FROM tblsubjects s
        JOIN tblclasses c ON c.id=s.ClassId
        LEFT JOIN tblstaffsubjects ss ON ss.SubjectId = s.id
        LEFT JOIN tblstaff st ON st.StaffId = ss.StaffId
        WHERE ".implode(" AND ", $where)."
        ORDER BY c.Branch, c.Semester, c.Section, s.SubjectCode";
    $stmt = $dbh->prepare($sql);
    foreach($params as $k=>$v){ $stmt->bindValue($k,$v); }
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch all active staff
$stmt = $dbh->prepare("SELECT StaffId, StaffName, StaffCode, Department 
                       FROM tblstaff 
                       WHERE Role='Staff' AND Status='Active'
                       ORDER BY Department, StaffName");
$stmt->execute();
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch filter options
$batches = $dbh->query("SELECT DISTINCT CONCAT(BatchStart,'-',BatchEnd) as Batch FROM tblclasses ORDER BY BatchStart DESC")->fetchAll(PDO::FETCH_COLUMN);
$branches = $dbh->query("SELECT DISTINCT Branch FROM tblclasses ORDER BY Branch")->fetchAll(PDO::FETCH_COLUMN);
$semesters = $dbh->query("SELECT DISTINCT Semester FROM tblclasses ORDER BY Semester")->fetchAll(PDO::FETCH_COLUMN);
$sections = $dbh->query("SELECT DISTINCT Section FROM tblclasses ORDER BY Section")->fetchAll(PDO::FETCH_COLUMN);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin | Assign Subjects</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

    <link rel="stylesheet" href="css/main.css"><link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">
    <?php include('includes/topbar.php');?> 
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php');?>  

            <div class="main-page">
                <div class="container-fluid">
                    <h2 class="title">Assign Subjects to Staff</h2>
                    <?php if(isset($msg)){ echo "<div class='alert alert-success'>$msg</div>"; } ?>

                    <!-- Filter Form -->
                    <form method="get" class="row mb-4">
                        <div class="col-md-3">
                            <select name="batch" class="form-control">
                                <option value="">-- Select Batch --</option>
                                <?php foreach($batches as $b){ ?>
                                    <option value="<?php echo $b; ?>" <?php if(@$_GET['batch']==$b) echo "selected"; ?>><?php echo $b; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="branch" class="form-control">
                                <option value="">-- Branch --</option>
                                <?php foreach($branches as $br){ ?>
                                    <option value="<?php echo $br; ?>" <?php if(@$_GET['branch']==$br) echo "selected"; ?>><?php echo $br; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="semester" class="form-control">
                                <option value="">-- Semester --</option>
                                <?php foreach($semesters as $sem){ ?>
                                    <option value="<?php echo $sem; ?>" <?php if(@$_GET['semester']==$sem) echo "selected"; ?>><?php echo $sem; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="section" class="form-control">
                                <option value="">-- Section --</option>
                                <?php foreach($sections as $sec){ ?>
                                    <option value="<?php echo $sec; ?>" <?php if(@$_GET['section']==$sec) echo "selected"; ?>><?php echo $sec; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary">Filter</button>
                        </div>
                    </form>

                    <!-- Subjects Table -->
                    <?php if($subjects){ ?>
                    <table id="subjectsTable" class="display table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Batch</th>
                                <th>Branch</th>
                                <th>Semester</th>
                                <th>Section</th>
                                <th>Assigned Staff</th>
                                <th>Assign / Change</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach($subjects as $sub){ ?>
                            <tr>
                                <td><?php echo htmlentities($sub['SubjectCode']); ?></td>
                                <td><?php echo htmlentities($sub['SubjectName']); ?></td>
                                <td><?php echo htmlentities($sub['Batch']); ?></td>
                                <td><?php echo htmlentities($sub['Branch']); ?></td>
                                <td><?php echo htmlentities($sub['Semester']); ?></td>
                                <td><?php echo htmlentities($sub['Section']); ?></td>
                                <td>
                                    <?php if($sub['StaffId']){ ?>
                                        <span class="label label-success">
                                            <?php echo htmlentities($sub['StaffName'])." (".$sub['StaffCode']." - ".$sub['Department'].")"; ?>
                                        </span>
                                    <?php } else { ?>
                                        <span class="label label-default">Not Assigned</span>
                                    <?php } ?>
                                </td>
                                <td>
                                    <form method="post" class="form-inline">
                                        <input type="hidden" name="subject_id" value="<?php echo $sub['SubjectId']; ?>">
                                        <select name="staff_id" class="form-control input-sm">
                                            <option value="">-- Select Staff --</option>
                                            <?php foreach($staffList as $stf): ?>
                                                <option value="<?php echo $stf['StaffId']; ?>"
                                                    <?php if($sub['StaffId']==$stf['StaffId']) echo "selected"; ?>>
                                                    <?php echo htmlentities($stf['StaffName'])." (".$stf['StaffCode']." - ".$stf['Department'].")"; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" name="assign" class="btn btn-success btn-sm">Assign</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } else { ?>
                        <div class="alert alert-info">Please filter to view subjects.</div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>

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
    $('#subjectsTable').DataTable({
        "pageLength": 10,
        "scrollX": true
    });
});
</script>
</body>
</html>
