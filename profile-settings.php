<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php'); // DB connection

if(!isset($_SESSION['slogin']) || strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];

// Fetch current staff info
$sql = "SELECT * FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

if(!$staff){
    echo "<h3 style='text-align:center; margin-top:50px;'>Staff not found. Please contact admin.</h3>";
    exit;
}

// Default photo
$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';

$msg_success = '';
$msg_error = '';
$redirect = ($staff['Role'] === 'HOD') ? 'hod-dashboard.php' : 'staff-dashboard.php';

// ===== Handle Profile Photo Upload =====
if(isset($_POST['upload']) && isset($_FILES['photo'])){
    $file = $_FILES['photo'];
    $filename = $file['name'];
    $tmpname = $file['tmp_name'];
    $fileerror = $file['error'];
    $filesize = $file['size'];

    $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];

    if(in_array($fileExt, $allowed)){
        if($fileerror === 0){
            if($filesize <= 5*1024*1024){
                $newFileName = 'staff_'.$staffid.'_'.time().'.'.$fileExt;
                $uploadPath = 'uploads/'.$newFileName;

                if(move_uploaded_file($tmpname, $uploadPath)){
                    $sql = "UPDATE tblstaff SET Photo=:photo WHERE StaffId=:staffid";
                    $query = $dbh->prepare($sql);
                    $query->bindParam(':photo', $newFileName, PDO::PARAM_STR);
                    $query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
                    $query->execute();
                    $photo = 'uploads/'.$newFileName;
                    $msg_success = "Profile photo updated successfully! Redirecting to dashboard...";
                    header("refresh:3;url=$redirect");
                } else {
                    $msg_error = "Failed to upload photo.";
                }
            } else {
                $msg_error = "File size exceeds 5MB.";
            }
        } else {
            $msg_error = "Error uploading file.";
        }
    } else {
        $msg_error = "Invalid file type. Only JPG, PNG, GIF allowed.";
    }
}

// ===== Handle Personal Info Update =====
if(isset($_POST['update'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $sql = "UPDATE tblstaff SET StaffName=:name, Email=:email, Mobile=:mobile WHERE StaffId=:staffid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':staffid', $staffid, PDO::PARAM_INT);

    if($query->execute()){
        $msg_success = "Profile info updated successfully! Redirecting to dashboard...";
        header("refresh:3;url=$redirect");
    } else {
        $msg_error = "Failed to update info.";
    }
}

// ===== Handle Password Change =====
if(isset($_POST['change_pass'])){
    $current = md5($_POST['current_pass']);
    $newpass = $_POST['new_pass'];
    $confpass = $_POST['confirm_pass'];

    if($current !== $staff['Password']){
        $msg_error = "Current password is incorrect.";
    } elseif($newpass !== $confpass){
        $msg_error = "New password and confirm password do not match.";
    } else {
        $sql = "UPDATE tblstaff SET Password=:pass WHERE StaffId=:staffid";
        $query = $dbh->prepare($sql);
        $query->bindParam(':pass',md5($newpass), PDO::PARAM_STR);
        $query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
        if($query->execute()){
            $msg_success = "Password changed successfully! Redirecting to dashboard...";
            header("refresh:3;url=$redirect");
        } else {
            $msg_error = "Failed to change password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile Settings | SRMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style-side-topbar.css">

<style>

/* Container Card */
.container-card { max-width:700px; background:#fff; padding:30px; margin:auto; border-radius:15px; box-shadow:0 10px 25px rgba(0,0,0,0.1); }
.profile-img-preview { width:130px; height:130px; border-radius:50%; object-fit:cover; margin-bottom:15px; border:3px solid #007bff; }

</style>
</head>
<body>
<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>

<!-- Page Content -->
<div class="content">
<div class="container-card">
    <h4>Profile Settings</h4>

    <?php if(!empty($msg_success)){ echo "<div class='alert alert-success'>$msg_success</div>"; } ?>
    <?php if(!empty($msg_error)){ echo "<div class='alert alert-danger'>$msg_error</div>"; } ?>

    <!-- Profile Photo Upload -->
    <form method="post" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3 text-center">
            <img src="<?php echo $photo; ?>" class="profile-img-preview" alt="Profile Photo">
        </div>
        <div class="mb-3">
            <input type="file" name="photo" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" name="upload" class="btn btn-primary w-100">Upload Photo</button>
    </form>

    <!-- Personal Info Update -->
    <form method="post" class="mb-4">
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlentities($staff['StaffName']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlentities($staff['Email']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mobile</label>
            <input type="text" name="mobile" class="form-control" value="<?php echo htmlentities($staff['Mobile']); ?>" required>
        </div>
        <button type="submit" name="update" class="btn btn-success w-100">Update Info</button>
    </form>

    <!-- Change Password -->
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_pass" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_pass" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="confirm_pass" class="form-control" required>
        </div>
        <button type="submit" name="change_pass" class="btn btn-warning w-100">Change Password</button>
    </form>

    
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
