<?php
session_start();
error_reporting(0);
include('includes/config.php');

// Check if staff is logged in
if(strlen($_SESSION['slogin'])==0){   
    header("Location: staff-login.php"); 
    exit;
} else {
    $staffid = $_SESSION['slogin'];

    // Fetch logged-in staff info
    $sqlStaff = "SELECT StaffName FROM tblstaff WHERE StaffId=:staffid";
    $queryStaff = $dbh->prepare($sqlStaff);
    $queryStaff->bindParam(':staffid', $staffid, PDO::PARAM_INT);
    $queryStaff->execute();
    $staff = $queryStaff->fetch(PDO::FETCH_ASSOC);

    // Fetch notices
    // Adjust column name here: replace 'StaffId' with the actual column in tblnotice linking to staff
    $sqlNotice = "SELECT n.NoticeTitle, n.Description, n.PostingDate, s.StaffName
                  FROM tblnotice n
                  LEFT JOIN tblstaff s ON n.StaffId = s.StaffId
                  ORDER BY n.PostingDate DESC";

    $queryNotice = $dbh->prepare($sqlNotice);
    $queryNotice->execute();
    $notices = $queryNotice->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Staff Notices | SRMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f5f6fa; margin:0; padding:20px; }
h2 { color:#1e40af; margin-bottom:20px; }
.table { background:#fff; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.08); }
.table th, .table td { vertical-align: middle; }
</style>
</head>
<body>

<div class="container">
    <h2>Notices</h2>
    <table class="table table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Posted By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if($notices): ?>
                <?php $count = 1; foreach($notices as $notice): ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo htmlentities($notice['NoticeTitle']); ?></td>
                        <td><?php echo htmlentities($notice['Description']); ?></td>
                        <td><?php echo htmlentities($notice['StaffName'] ?: 'Admin'); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($notice['PostingDate'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No notices found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
