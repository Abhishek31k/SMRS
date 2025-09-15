<?php
session_start();
include('includes/config.php');

// Check login
if(strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];
$sql = "SELECT StaffName, Role, Department, Photo FROM tblstaff WHERE StaffId=:staffid";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid',$staffid,PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

if(!$staff || $staff['Role']!=='HOD'){ 
    echo "<h3 style='text-align:center;margin-top:50px;'>Access denied. Only HOD allowed.</h3>";
    exit; 
}
$hodDept = $staff['Department'];

// Profile photo fallback
$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

// ✅ Assign counsellor
if (isset($_POST['assign'])) {
    $staffId = intval($_POST['counsellor_id']);  
    $students = $_POST['students'] ?? [];
    if ($staffId && !empty($students)) {
        $stmt = $dbh->prepare("SELECT StaffName, Mobile FROM tblstaff WHERE StaffId=:stf");
        $stmt->bindParam(':stf', $staffId);
        $stmt->execute();
        $staffRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($staffRow) {
            foreach ($students as $regno) {
                $sql = "UPDATE tblstudents 
                           SET CounsellorId=:stf,
                               CounsellorName=:cname,
                               CounsellorMobile=:cmobile
                         WHERE RegNo=:regno";
                $stmt2 = $dbh->prepare($sql);
                $stmt2->bindParam(':stf', $staffId);
                $stmt2->bindParam(':cname', $staffRow['StaffName']);
                $stmt2->bindParam(':cmobile', $staffRow['Mobile']);
                $stmt2->bindParam(':regno', $regno);
                $stmt2->execute();
            }
            $msg = "Counsellor assigned successfully to selected students!";
        }
    }
}

// ✅ Fetch batches and sections
$sql = "SELECT DISTINCT CONCAT(BatchStart,'-',BatchEnd) as Batch 
        FROM tblclasses WHERE Branch=:branch ORDER BY BatchStart DESC";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':branch',$hodDept);
$stmt->execute();
$batches = $stmt->fetchAll(PDO::FETCH_COLUMN);

$sql = "SELECT DISTINCT Section FROM tblclasses WHERE Branch=:branch ORDER BY Section";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':branch',$hodDept);
$stmt->execute();
$sections = $stmt->fetchAll(PDO::FETCH_COLUMN);

// ✅ Fetch students based on filter
$students = [];
if(!empty($_GET['batch']) && !empty($_GET['section'])){
    $batch = $_GET['batch'];
    $section = $_GET['section'];

    $sql = "SELECT s.RegNo, s.StudentName, c.Semester, c.Section, c.Branch,
                   CONCAT(c.BatchStart,'-',c.BatchEnd) as Batch,
                   st.StaffName as CounsellorName
            FROM tblstudents s
            JOIN tblclasses c ON c.id = s.ClassId
            LEFT JOIN tblstaff st ON st.StaffId = s.CounsellorId
            WHERE c.Branch=:branch AND c.Section=:section 
                  AND CONCAT(c.BatchStart,'-',c.BatchEnd)=:batch
            ORDER BY s.RegNo ASC";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':branch',$hodDept);
    $stmt->bindParam(':section',$section);
    $stmt->bindParam(':batch',$batch);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Fetch active staff from the same department
$stmt = $dbh->prepare("SELECT StaffId, StaffName, StaffCode, Department 
                       FROM tblstaff 
                       WHERE Role='Staff' AND Status='Active' 
                             AND Department=:dept
                       ORDER BY StaffName");
$stmt->bindParam(':dept',$hodDept);
$stmt->execute();
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Counsellors | HOD Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="style-side-topbar.css">
</head>
<body>

<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>

<div class="content">
    <a href="hod-dashboard.php" class="btn btn-secondary mb-3">&larr; Back to Dashboard</a>
    <h3>Assign Counsellors (<?php echo htmlentities($hodDept); ?> Dept)</h3>
    <?php if(isset($msg)){ echo "<div class='alert alert-success'>$msg</div>"; } ?>

    <!-- Filter Form -->
    <form method="get" class="row mb-4">
        <div class="col-md-3">
            <select name="batch" class="form-select" required>
                <option value="">-- Select Batch --</option>
                <?php foreach($batches as $b){ ?>
                    <option value="<?php echo $b; ?>" <?php if(@$_GET['batch']==$b) echo "selected"; ?>><?php echo $b; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" value="<?php echo htmlentities($hodDept); ?>" readonly>
        </div>
        <div class="col-md-3">
            <select name="section" class="form-select" required>
                <option value="">-- Select Section --</option>
                <?php foreach($sections as $sec){ ?>
                    <option value="<?php echo $sec; ?>" <?php if(@$_GET['section']==$sec) echo "selected"; ?>><?php echo $sec; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary">Fetch Students</button>
        </div>
    </form>

    <!-- Students Table -->
    <?php if($students){ ?>
    <form method="post">
        <div class="card">
            <div class="card-header">Students (<?php echo count($students); ?> found)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>Semester</th>
                            <th>Section</th>
                            <th>Batch</th>
                            <th>Current Counsellor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($students as $s){ ?>
                        <tr>
                            <td><input type="checkbox" name="students[]" value="<?php echo $s['RegNo']; ?>"></td>
                            <td><?php echo htmlentities($s['RegNo']); ?></td>
                            <td><?php echo htmlentities($s['StudentName']); ?></td>
                            <td><?php echo htmlentities($s['Semester']); ?></td>
                            <td><?php echo htmlentities($s['Section']); ?></td>
                            <td><?php echo htmlentities($s['Batch']); ?></td>
                            <td>
                                <?php echo $s['CounsellorName'] ? "<span class='badge bg-success'>".htmlentities($s['CounsellorName'])."</span>" : "<span class='badge bg-secondary'>Not Assigned</span>"; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assign Block -->
        <div class="row mt-3">
            <div class="col-md-6">
                <select name="counsellor_id" class="form-select staff-select" required>
                    <option value="">-- Select Counsellor Staff --</option>
                    <?php foreach($staffList as $stf){ ?>
                        <option value="<?php echo $stf['StaffId']; ?>">
                            <?php echo htmlentities($stf['StaffName'])." (".$stf['StaffCode']." - ".$stf['Department'].")"; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" name="assign" class="btn btn-success">Assign Selected</button>
            </div>
        </div>
    </form>

    <?php } elseif(isset($_GET['batch'])) { ?>
        <div class="alert alert-warning">No students found for selected filter.</div>
    <?php } ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
    $('.staff-select').select2({ placeholder: "Search staff...", allowClear: true, width:'100%' });

    // Select/Deselect all
    $("#selectAll").on('click', function(){
        $("input[name='students[]']").prop('checked', this.checked);
    });

    // Hamburger toggle
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>
</body>
</html>
