<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

if(!isset($_SESSION['slogin']) || strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];

// Fetch staff info
$sql = "SELECT StaffName, Role, Photo FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

if(!$staff){ exit("Staff not found."); }

$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

// Handle delete
if(isset($_GET['del'])){
    $id = intval($_GET['del']);
    $delSql = "DELETE FROM tblnotice WHERE id=:id AND postedBy=:postedBy AND role='Staff'";
    $delQuery = $dbh->prepare($delSql);
    $delQuery->bindParam(':id',$id,PDO::PARAM_INT);
    $delQuery->bindParam(':postedBy',$staff['StaffName'],PDO::PARAM_STR);
    $delQuery->execute();
    header("Location: manage-staff-notices.php");
    exit;
}

// Fetch staff's notices
$noticeSql = "SELECT * FROM tblnotice WHERE postedBy=:postedBy AND role='Staff' ORDER BY postingDate DESC";
$noticeQuery = $dbh->prepare($noticeSql);
$noticeQuery->bindParam(':postedBy',$staff['StaffName'],PDO::PARAM_STR);
$noticeQuery->execute();
$notices = $noticeQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Notices | Staff Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/x-icon" href="images/crrengglogo.png" />
<link rel="stylesheet" href="style-side-topbar.css">


</head>
<body>
<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>
<div class="content">
  <h2 class="text-black mb-4">🛠 Manage My Notices</h2>
  <div class="card p-3 shadow-lg">
    <?php if($notices): ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Title</th>
          <th>Details</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach($notices as $notice): ?>
        <tr>
          <td><?php echo htmlentities($notice['noticeTitle']); ?></td>
          <td><?php echo htmlentities(substr($notice['noticeDetails'],0,50)); ?>...</td>
          <td><?php echo date("d M Y",strtotime($notice['postingDate'])); ?></td>
          <td>
            <a href="edit-staff-notice.php?id=<?php echo $notice['id']; ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
            <a href="manage-staff-notices.php?del=<?php echo $notice['id']; ?>" onclick="return confirm('Delete this notice?');" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
      <div class="alert alert-info">No notices posted by you yet.</div>
    <?php endif; ?>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>
</body>
</html>
