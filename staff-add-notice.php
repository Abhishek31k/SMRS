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

// Handle Add Notice form submission
$msg = "";
$error = "";
if(isset($_POST['submit'])){
    $title = $_POST['noticetitle'];
    $details = $_POST['noticedetails'];
    $postedBy = $staff['StaffName'];
    $role = "Staff";

    $insertSql = "INSERT INTO tblnotice (noticeTitle, noticeDetails, postedBy, role) 
                  VALUES (:title, :details, :postedBy, :role)";
    $insertQuery = $dbh->prepare($insertSql);
    $insertQuery->bindParam(':title', $title, PDO::PARAM_STR);
    $insertQuery->bindParam(':details', $details, PDO::PARAM_STR);
    $insertQuery->bindParam(':postedBy', $postedBy, PDO::PARAM_STR);
    $insertQuery->bindParam(':role', $role, PDO::PARAM_STR);

    if($insertQuery->execute()){
        $msg = "Notice added successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Notice | Staff Dashboard</title>

<!-- Bootstrap & FontAwesome -->
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

<!-- Content -->
<div class="content">
    <div class="header">
        <h2>Add Notice 📝</h2>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2 mx-auto">
                <div class="card shadow-lg p-4">
                    <h5 class="mb-3">Notice Form</h5>

                    <?php if($msg): ?>
                        <div class="alert alert-success"><?php echo htmlentities($msg); ?></div>
                    <?php elseif($error): ?>
                        <div class="alert alert-danger"><?php echo htmlentities($error); ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="noticetitle" class="form-label">Notice Title</label>
                            <input type="text" name="noticetitle" id="noticetitle" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label for="noticedetails" class="form-label">Notice Details</label>
                            <textarea name="noticedetails" id="noticedetails" class="form-control" rows="5" required></textarea>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success">
                            <i class="fa fa-check"></i> Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>
</body>
</html>
