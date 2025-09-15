<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');  // DB connection

// Check staff login
if(!isset($_SESSION['slogin']) || strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];

// Fetch staff info
$sql = "SELECT StaffName, StaffCode, Role, Department, Designation, Email, Mobile, Photo
        FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

if(!$staff){
    echo "<h3 style='text-align:center; margin-top:50px;'>Staff not found. Please contact admin.</h3>";
    exit;
}

// Profile photo fallback
$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

// Fetch notices from admin
$noticeSql = "SELECT id, noticeTitle, noticeDetails, postingDate FROM tblnotice ORDER BY postingDate DESC";
$noticeQuery = $dbh->prepare($noticeSql);
$noticeQuery->execute();
$notices = $noticeQuery->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard | SRMS</title>

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="icon" type="image/x-icon" href="images/crrengglogo.png" />
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="style-side-topbar.css">

<style>



/* Profile Card */
.profile-card { background:#fff; border-radius:12px; overflow:hidden; max-width:600px; margin:20px auto 40px auto; 
                box-shadow:0 4px 20px rgba(0,0,0,0.08); transition:transform 0.3s ease; }
.profile-card:hover { transform:translateY(-5px); }
.profile-card .header { background:#0077b6; padding:50px 20px 70px 20px; text-align:center; position:relative; }
.profile-card .profile-img { width:120px; height:120px; border-radius:50%; object-fit:cover; border:4px solid #fff; position:absolute; bottom:-60px; left:50%; transform:translateX(-50%); }
.profile-card h4 { margin-top:70px; font-size:1.5rem; font-weight:700; color:#222; text-align:center; }
.role-badge { display:inline-block; background:#06d6a0; color:#fff; padding:6px 16px; border-radius:15px; font-size:0.9rem; margin:10px 0 20px 0; }

.profile-info { padding: 20px 40px 30px 40px; }
.profile-info .info-row { display:flex; align-items:center; margin-bottom:15px; }
.profile-info .info-row i { width:28px; font-size:1.2rem; color:#0077b6; margin-right:12px; }
.profile-info .info-label { font-weight:600; flex:1; color:#333; }
.profile-info .info-value { color:#555; text-align:right; word-break: break-word; }

/* Quick Actions */
.quick-actions { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:20px; margin-bottom:30px; }
.quick-actions .card { color:#fff; border-radius:12px; padding:25px; text-align:center; transition:all 0.3s ease; font-weight:600; cursor:pointer; box-shadow:0 3px 10px rgba(0,0,0,0.1); }
.quick-actions .card i { font-size:2rem; margin-bottom:12px; }
.quick-actions .card:hover { transform:translateY(-5px) scale(1.02); }
.card-blue { background:#0077b6; }
.card-green { background:#06d6a0; }
.card-orange { background:#f4a261; }
.card-pink { background:#e76f51; }
.card-gray { background:#6c757d; }

/* Notices */
.notice-card { background:#fff; border-radius:10px; padding:20px; margin-bottom:15px; box-shadow:0 2px 10px rgba(0,0,0,0.08); }
.notice-card h5 { margin-bottom:8px; font-weight:600; color:#222; }
.notice-card small { color:#777; }


</style>
</head>
<body>

<!-- Topbar -->
<?php include('staff-topbar.php'); ?>


<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>
<div class="content">
    <div class="header">
        <h2>Welcome, <?php echo htmlentities($staff['StaffName']); ?> 👋</h2>
        <div class="alert alert-light text-center border" style="border-radius:10px; padding:12px 20px; margin-bottom:20px;">
            You are logged in as <b><?php echo htmlentities($staff['Role']); ?></b>.
        </div>
    </div>

    <div class="profile-card">
        <div class="header">
            <img src="<?php echo $photo; ?>" class="profile-img" alt="Profile Photo">
        </div>
        <h4><?php echo htmlentities($staff['StaffName']); ?></h4>
        <div class="role-badge"><?php echo htmlentities($staff['Role']); ?></div>

        <div class="profile-info">
            <div class="info-row"><i class="fa fa-id-badge"></i>
                <div class="info-label">Staff Code:</div>
                <div class="info-value"><?php echo htmlentities($staff['StaffCode']); ?></div>
            </div>
            <div class="info-row"><i class="fa fa-building"></i>
                <div class="info-label">Department:</div>
                <div class="info-value"><?php echo htmlentities($staff['Department']); ?></div>
            </div>
            <div class="info-row"><i class="fa fa-user-tie"></i>
                <div class="info-label">Designation:</div>
                <div class="info-value"><?php echo htmlentities($staff['Designation']); ?></div>
            </div>
            <div class="info-row"><i class="fa fa-envelope"></i>
                <div class="info-label">Email:</div>
                <div class="info-value"><?php echo htmlentities($staff['Email']); ?></div>
            </div>
            <div class="info-row"><i class="fa fa-phone"></i>
                <div class="info-label">Mobile:</div>
                <div class="info-value"><?php echo htmlentities($staff['Mobile']); ?></div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <h4 class="mb-3">🚀 Quick Actions</h4>
    <div class="quick-actions">
        <a href="assign-subjects.php" class="card card-blue"><i class="fa fa-book"></i><div>Assign Subjects</div></a>
        <a href="view-students.php" class="card card-green"><i class="fa fa-users"></i><div>View Students</div></a>
        <a href="enter-marks.php" class="card card-orange"><i class="fa fa-pen-to-square"></i><div>Enter Marks</div></a>
        <a href="#viewNoticesSection" class="card card-pink"><i class="fa fa-bell"></i><div>View Notices</div></a>
        <a href="profile-settings.php" class="card card-gray"><i class="fa fa-cog"></i><div>Profile Settings</div></a>
    </div>

    <!-- View Notices Section -->
    <h4 id="viewNoticesSection" class="mb-3">📢 Notices from Admin</h4>
    <?php if(count($notices) > 0): ?>
        <?php foreach($notices as $notice): ?>
            <div class="notice-card">
                <h5><?php echo htmlentities($notice['noticeTitle']); ?></h5>
                <small>Posted on: <?php echo date('d M Y', strtotime($notice['postingDate'])); ?></small>
                <p style="margin-top:10px;"><?php echo nl2br(htmlentities($notice['noticeDetails'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning">No notices available.</div>
    <?php endif; ?>

</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>
</body>
</html>
