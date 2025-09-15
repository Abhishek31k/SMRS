<?php
if (!isset($staff)) {
    // Fallback: fetch staff if not already set
    $staffid = $_SESSION['slogin'];
    $sql = "SELECT StaffName, Role FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
    $query->execute();
    $staff = $query->fetch(PDO::FETCH_ASSOC);
}
$isHOD = ($staff['Role'] === 'HOD');
?>
<div class="topbar d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
        <img src="includes/crrengglogo.png" alt="College Logo" style="height:40px; margin-right:12px; border-radius:6px;">
        <h4 class="mb-0"><?php echo $isHOD ? "HOD Dashboard" : "Staff Dashboard"; ?></h4>
    </div>
    <a href="staff-logout.php" class="logout-btn">
        <i class="fa fa-sign-out-alt me-1"></i><span>Logout</span>
    </a>
</div>
<i class="fa fa-bars hamburger d-md-none"></i>
