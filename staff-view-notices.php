<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');

// Check staff login
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

if(!$staff){
    echo "<h3 style='text-align:center;margin-top:50px;'>Staff not found.</h3>";
    exit;
}

$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

// Fetch all notices
$noticeSql = "SELECT * FROM tblnotice ORDER BY postingDate DESC";
$noticeQuery = $dbh->prepare($noticeSql);
$noticeQuery->execute();
$notices = $noticeQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Notices | Staff Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/x-icon" href="images/crrengglogo.png" />
<link rel="stylesheet" href="style-side-topbar.css">

<style>

.notice-card{background:#fff;border-radius:12px;padding:20px;margin-bottom:15px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
</style>
</head>
<body>
<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>

<div class="content">
  <h2 class="text-black mb-4">📢 All Notices</h2>
  <?php if($notices): foreach($notices as $notice): ?>
    <div class="notice-card">
      <h5><?php echo htmlentities($notice['noticeTitle']); ?></h5>
      <small>Posted by <?php echo htmlentities($notice['postedBy']); ?> (<?php echo $notice['role']; ?>) on <?php echo date("d M Y", strtotime($notice['postingDate'])); ?></small>
      <p class="mt-2"><?php echo nl2br(htmlentities($notice['noticeDetails'])); ?></p>
    </div>
  <?php endforeach; else: ?>
    <div class="alert alert-warning">No notices available.</div>
  <?php endif; ?>
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
