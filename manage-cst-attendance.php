<?php
session_start();
include('includes/config.php');

// ✅ Check staff login
if(!isset($_SESSION['slogin']) || strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];

// ✅ Fetch staff info
$sql = "SELECT StaffName, Department, Role FROM tblstaff WHERE StaffId=:staffid";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

if(!$staff){
    echo "<h3>Staff not found</h3>";
    exit;
}

// ✅ Save attendance
if(isset($_POST['save_attendance'])){
    foreach($_POST['attendance'] as $regno=>$att){
        $sem   = $_POST['semester'][$regno];
        $date  = $_POST['date'][$regno];
        $total = intval($att['total']);
        $present = intval($att['present']);

        // Insert or Update (Unique Key: RegNo + Semester + Date)
        $sql = "INSERT INTO tblattendance (RegNo, Semester, AttendanceDate, TotalPeriods, PresentPeriods)
                VALUES (:regno,:sem,:dt,:total,:present)
                ON DUPLICATE KEY UPDATE 
                    TotalPeriods=:total, PresentPeriods=:present, AttendanceDate=:dt";
        $stmt = $dbh->prepare($sql);
        $stmt->execute([
            ':regno'=>$regno, ':sem'=>$sem, ':dt'=>$date,
            ':total'=>$total, ':present'=>$present
        ]);
    }
    $msg = "✅ Attendance updated successfully!";
}

// ✅ Fetch counselling students with attendance (latest if exists)
$sql = "SELECT s.RegNo, s.StudentName, c.Semester, c.Section, c.Branch,
               a.AttendanceDate, a.TotalPeriods, a.PresentPeriods
        FROM tblstudents s
        JOIN tblclasses c ON c.id = s.ClassId
        LEFT JOIN tblattendance a 
          ON a.RegNo = s.RegNo AND a.Semester=c.Semester
        WHERE s.CounsellorId=:staffid
        ORDER BY c.Semester, s.RegNo ASC";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':staffid',$staffid);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Counselling Students Attendance | SRMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style-side-topbar.css">

<style>

/* Card */
.card { border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.card-header{ background:#0f3ba7; color:#fff; font-weight:600; }

</style>
</head>
<body>

<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>
<!-- Page Content -->
<div class="content">
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
      <span>Manage Counselling Students Attendance</span>
  </div>
  <div class="card-body">
    <h5>Hello, <?php echo htmlentities($staff['StaffName']); ?> (<?php echo htmlentities($staff['Department']); ?>)</h5>

    <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

    <?php if($students){ ?>
    <form method="post">
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Reg No</th>
              <th>Name</th>
              <th>Branch</th>
              <th>Semester</th>
              <th>Section</th>
              <th>Date</th>
              <th>Total Periods</th>
              <th>Present Periods</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($students as $s){ ?>
            <tr>
              <td><?php echo htmlentities($s->RegNo); ?></td>
              <td><?php echo htmlentities($s->StudentName); ?></td>
              <td><?php echo htmlentities($s->Branch); ?></td>
              <td>
                <?php echo htmlentities($s->Semester); ?>
                <input type="hidden" name="semester[<?php echo $s->RegNo; ?>]" value="<?php echo $s->Semester; ?>">
              </td>
              <td><?php echo htmlentities($s->Section); ?></td>
              <td>
                <input type="date" name="date[<?php echo $s->RegNo; ?>]" 
                       value="<?php echo $s->AttendanceDate ?: date('Y-m-d'); ?>" class="form-control" required>
              </td>
              <td>
                <input type="number" name="attendance[<?php echo $s->RegNo; ?>][total]" 
                       value="<?php echo $s->TotalPeriods ?: 0; ?>" class="form-control" min="0" required>
              </td>
              <td>
                <input type="number" name="attendance[<?php echo $s->RegNo; ?>][present]" 
                       value="<?php echo $s->PresentPeriods ?: 0; ?>" class="form-control" min="0" required>
              </td>
            </tr>
          <?php } ?>
          </tbody>
        </table>
      </div>
      <button type="submit" name="save_attendance" class="btn btn-primary mt-3">Save Attendance</button>
    </form>
    <?php } else { ?>
      <div class="alert alert-info">No counselling students found.</div>
    <?php } ?>
  </div>
</div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>

</body>
</html>
