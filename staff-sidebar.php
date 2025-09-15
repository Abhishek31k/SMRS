<?php
// Fetch staff info if not already set
if (!isset($staff)) {
    $staffid = $_SESSION['slogin'];
    $sql = "SELECT StaffName, Role FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
    $query = $dbh->prepare($sql);
    $query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
    $query->execute();
    $staff = $query->fetch(PDO::FETCH_ASSOC);
}

// Determine role
$isHOD = ($staff['Role'] === 'HOD');

// Get current page filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <h3><?php echo htmlentities($staff['StaffName']); ?></h3>

    <?php if ($isHOD): ?>
        <!-- HOD Menu -->
        <a href="hod-dashboard.php" class="<?= ($currentPage=='hod-dashboard.php')?'active':'' ?>"><i class="fa fa-user"></i> Profile</a>
        <a href="manage-staff.php" class="<?= ($currentPage=='manage-staff.php')?'active':'' ?>"><i class="fa fa-users"></i> Manage Staff</a>
        <a href="assign-subjects-to-staff.php" class="<?= ($currentPage=='assign-subjects-to-staff.php')?'active':'' ?>"><i class="fa fa-book"></i> Assign Subjects</a>
        <a href="assign-counselling-students.php" class="<?= ($currentPage=='assign-counselling-students.php')?'active':'' ?>"><i class="fa fa-user-graduate"></i> Assign Counsellors</a>
        <a href="view-students.php" class="<?= ($currentPage=='view-students.php')?'active':'' ?>"><i class="fa fa-users"></i> View Students</a>

        <?php
        // Notice submenu for HOD
        $hodNoticePages = ['notice.php','manage-hod-notices.php'];
        $hodNoticeActive = in_array($currentPage, $hodNoticePages);
        ?>
        <a class="d-flex align-items-center justify-content-between <?= $hodNoticeActive?'active':'' ?>" 
        data-bs-toggle="collapse" href="#noticeSubmenu" role="button" 
        aria-expanded="<?= $hodNoticeActive?'true':'false' ?>" aria-controls="noticeSubmenu">
            <span><i class="fa fa-bell"></i> Notice</span>
            <i class="fa fa-chevron-down"></i>
        </a>
        <div class="collapse <?= $hodNoticeActive?'show':'' ?>" id="noticeSubmenu">
            <a href="notice.php" class="ms-4 <?= ($currentPage=='notice.php')?'active':'' ?>"><i class="fa fa-eye"></i> View Notices</a>
            <a href="manage-hod-notices.php" class="ms-4 <?= ($currentPage=='manage-hod-notices.php')?'active':'' ?>"><i class="fa fa-edit"></i> Manage Notices</a>
        </div>

        <a href="profile-settings.php" class="<?= ($currentPage=='profile-settings.php')?'active':'' ?>"><i class="fa fa-cog"></i> Profile Settings</a>
    <?php else: ?>


        <!-- Staff Menu -->
        <a href="staff-dashboard.php" class="<?= ($currentPage=='staff-dashboard.php')?'active':'' ?>"><i class="fa fa-user"></i> Profile</a>
        <a href="assigned-subjects.php" class="<?= ($currentPage=='assigned-subjects.php')?'active':'' ?>"><i class="fa fa-book"></i> Assigned Subjects</a>
        <a href="view-students.php" class="<?= ($currentPage=='view-students.php')?'active':'' ?>"><i class="fa fa-users"></i> View Students</a>

        <?php
        // Counselling submenu logic
        $counsellingPages = ['counselling-students.php','manage-cst-attendance.php'];
        $counsellingActive = in_array($currentPage, $counsellingPages);
        ?>
        <a class="d-flex align-items-center justify-content-between <?= $counsellingActive?'active':'' ?>" data-bs-toggle="collapse" href="#counsellingMenu">
            <span><i class="fa fa-pen-to-square"></i> Counselling</span> 
            <i class="fa fa-chevron-down"></i>
        </a>
        <div class="collapse <?= $counsellingActive?'show':'' ?>" id="counsellingMenu">
            <a href="counselling-students.php" class="ms-4 <?= ($currentPage=='counselling-students.php')?'active':'' ?>"><i class="fa fa-users"></i> View Students</a>
            <a href="manage-cst-attendance.php" class="ms-4 <?= ($currentPage=='manage-cst-attendance.php')?'active':'' ?>"><i class="fa fa-calendar-check"></i> Manage Attendance</a>
        </div>

        <?php
        // Notice submenu logic
        $noticePages = ['staff-view-notices.php','staff-add-notice.php','manage-staff-notices.php'];
        $noticeActive = in_array($currentPage, $noticePages);
        ?>
        <a class="d-flex align-items-center justify-content-between <?= $noticeActive?'active':'' ?>" data-bs-toggle="collapse" href="#noticeMenu">
            <span><i class="fa fa-bell"></i> Notice</span> 
            <i class="fa fa-chevron-down"></i>
        </a>
        <div class="collapse <?= $noticeActive?'show':'' ?>" id="noticeMenu">
            <a href="staff-view-notices.php" class="ms-4 <?= ($currentPage=='staff-view-notices.php')?'active':'' ?>"><i class="fa fa-eye"></i> View Notices</a>
            <a href="staff-add-notice.php" class="ms-4 <?= ($currentPage=='staff-add-notice.php')?'active':'' ?>"><i class="fa fa-plus-circle"></i> Add Notice</a>
            <a href="manage-staff-notices.php" class="ms-4 <?= ($currentPage=='manage-staff-notices.php')?'active':'' ?>"><i class="fa fa-tasks"></i> Manage Notices</a>
        </div>

        <!-- Settings -->
        <a href="profile-settings.php" class="<?= ($currentPage=='profile-settings.php')?'active':'' ?>"><i class="fa fa-cog"></i> Profile Settings</a>
    <?php endif; ?>
</div>
