<?php
session_start();
include('includes/config.php');

// Check login (only HOD/Admin should assign)
if(strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

// Get logged-in staff role
$staffid = $_SESSION['slogin'];
$sql = "SELECT StaffName, Role, Department, Photo FROM tblstaff WHERE StaffId=:staffid";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);
if(!$staff || $staff['Role']!=='HOD'){ echo "Access denied"; exit; }
$hodDept = $staff['Department'];
$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

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

// Fetch filter options
$batches = $dbh->query("SELECT DISTINCT CONCAT(BatchStart,'-',BatchEnd) as Batch FROM tblclasses ORDER BY BatchStart DESC")->fetchAll(PDO::FETCH_COLUMN);
$branches = $dbh->query("SELECT DISTINCT Branch FROM tblclasses ORDER BY Branch")->fetchAll(PDO::FETCH_COLUMN);
$semesters = $dbh->query("SELECT DISTINCT Semester FROM tblclasses ORDER BY Semester")->fetchAll(PDO::FETCH_COLUMN);
$sections = $dbh->query("SELECT DISTINCT Section FROM tblclasses ORDER BY Section")->fetchAll(PDO::FETCH_COLUMN);

// Apply filters
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
        WHERE ".implode(" AND ", $where);

    $stmt = $dbh->prepare($sql);
    foreach($params as $k=>$v){ $stmt->bindValue($k,$v); }
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ✅ Fetch all active staff
$stmt = $dbh->prepare("SELECT StaffId, StaffName, StaffCode, Department 
                       FROM tblstaff 
                       WHERE Role='Staff' AND Status='Active'
                       ORDER BY Department, StaffName");
$stmt->execute();
$staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Subjects | SRMS</title>
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

<!-- Content -->
<div class="content">
  <h2>Assign Subjects to Staff (<?php echo htmlentities($hodDept); ?> Dept)</h2>
  <?php if(isset($msg)){ echo "<div class='alert alert-success'>$msg</div>"; } ?>

  <!-- Filter -->
  <form method="get" class="row mb-4">
    <div class="col-md-3">
      <select name="batch" class="form-select">
        <option value="">-- Select Batch --</option>
        <?php foreach($batches as $b){ ?>
          <option value="<?php echo $b; ?>" <?php if(@$_GET['batch']==$b) echo "selected"; ?>><?php echo $b; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="branch" class="form-select">
        <option value="">-- Branch --</option>
        <?php foreach($branches as $br){ ?>
          <option value="<?php echo $br; ?>" <?php if(@$_GET['branch']==$br) echo "selected"; ?>><?php echo $br; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="semester" class="form-select">
        <option value="">-- Semester --</option>
        <?php foreach($semesters as $sem){ ?>
          <option value="<?php echo $sem; ?>" <?php if(@$_GET['semester']==$sem) echo "selected"; ?>><?php echo $sem; ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="section" class="form-select">
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

  <!-- Subjects -->
  <?php if($subjects){ ?>
  <table class="table table-bordered bg-white">
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
            <span class="badge bg-success">
              <?php echo htmlentities($sub['StaffName'])." (".$sub['StaffCode']." - ".$sub['Department'].")"; ?>
            </span>
          <?php } else { ?>
            <span class="badge bg-secondary">Not Assigned</span>
          <?php } ?>
        </td>
        <td>
          <form method="post" class="d-flex">
            <input type="hidden" name="subject_id" value="<?php echo $sub['SubjectId']; ?>">
            <select name="staff_id" class="form-select staff-select">
              <option value="">-- Select Staff --</option>
              <?php foreach($staffList as $stf): ?>
                <option value="<?php echo $stf['StaffId']; ?>" <?php if($sub['StaffId']==$stf['StaffId']) echo "selected"; ?>>
                  <?php echo htmlentities($stf['StaffName'])." (".$stf['StaffCode']." - ".$stf['Department'].")"; ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" name="assign" class="btn btn-success btn-sm ms-2">Assign</button>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function(){
  $('.staff-select').select2({ placeholder:"Search staff...", allowClear:true, width:'100%' });
});
</script>
</body>
</html>
