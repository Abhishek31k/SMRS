<?php
session_start();
include('includes/config.php');

// Check login
if(!isset($_SESSION['slogin']) || strlen($_SESSION['slogin'])==0){
    header("Location: staff-login.php");
    exit;
}

$staffid = $_SESSION['slogin'];

// Fetch staff info
$sql = "SELECT StaffName, Department, Role FROM tblstaff WHERE StaffId=:staffid";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$staff = $query->fetch(PDO::FETCH_ASSOC);

// Fetch assigned subjects
$sql = "SELECT s.SubjectName, s.SubjectCode, 
               c.Branch, c.Semester, c.Section, 
               CONCAT(c.BatchStart,'-',c.BatchEnd) AS Batch, 
               ss.AssignedDate
        FROM tblstaffsubjects ss
        JOIN tblsubjects s ON ss.SubjectId = s.id
        JOIN tblclasses c ON s.ClassId = c.id
        WHERE ss.StaffId = :staffid
        ORDER BY c.BatchStart DESC, c.Branch, c.Semester, c.Section";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':staffid',$staffid);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Assigned Subjects | SRMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

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

<!-- Page Content -->
<div class="content">
    <div class="card">
        <div class="card-header">
            My Assigned Subjects
        </div>
        <div class="card-body">
            <h5>Hello, <?php echo htmlentities($staff['StaffName']); ?> (<?php echo htmlentities($staff['Department']); ?>)</h5>
            <p class="text-muted">Below are the subjects assigned to you:</p>

            <?php if($subjects){ ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Batch</th>
                            <th>Branch</th>
                            <th>Semester</th>
                            <th>Section</th>
                            <th>Assigned Date</th>
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
                            <td><?php echo htmlentities($sub['AssignedDate']); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="alert alert-info">No subjects have been assigned to you yet.</div>
            <?php } ?>
        </div>
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
