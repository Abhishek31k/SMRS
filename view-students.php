<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('includes/config.php');  // DB connection

// Check login
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

// Role check
$isHOD = ($staff['Role'] === 'HOD');
// --- ensure variable exists so foreach never throws "undefined variable" ---
$students = [];

try {
    $sqlStudents = "
        SELECT s.RegNo, s.StudentName, s.DOB, s.Gender, s.StudentEmail, s.StudentMobile,
               COALESCE(c.Branch, '') AS Branch,
               COALESCE(c.Semester, '') AS Semester,
               COALESCE(c.Section, '') AS Section,
               CONCAT(COALESCE(c.BatchStart,''), '-', COALESCE(c.BatchEnd,'')) AS Batch
        FROM tblstudents s
        LEFT JOIN tblclasses c ON s.ClassId = c.id
        ORDER BY s.StudentName ASC
    ";
    $stmt = $dbh->prepare($sqlStudents);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_OBJ); // array of objects
} catch (PDOException $e) {
    // log the real error to server logs, but keep page friendly
    error_log("Failed to fetch students: " . $e->getMessage());
    $students = []; // keep it safe
}

// Profile photo fallback
$photo = !empty($staff['Photo']) ? 'uploads/'.$staff['Photo'] : 'uploads/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Students | Staff Dashboard</title>
<link rel="icon" type="image/x-icon" href="images/crrengglogo.png" />

<!-- Bootstrap + Datatables + Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="style-side-topbar.css">

<style>


.card-header { background:#0f3ba7; color:#fff; font-weight:600; display:flex; justify-content:space-between; align-items:center; }
.table thead th { background:#007bff; color:#fff; }


</style>
</head>
<body>

<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>

<!-- Main Content -->
<div class="content">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">View Students</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="studentTable" class="table table-bordered table-striped dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Branch</th>
                            <th>Semester</th>
                            <th>Section</th>
                            <th>Batch</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($students) && is_array($students)): ?>
                        <?php foreach ($students as $s): ?>
                        <tr>
                            <td><?php echo htmlentities($s->RegNo); ?></td>
                            <td><?php echo htmlentities($s->StudentName); ?></td>
                            <td><?php echo htmlentities($s->DOB); ?></td>
                            <td><?php echo htmlentities($s->Gender); ?></td>
                            <td><?php echo htmlentities($s->StudentEmail); ?></td>
                            <td><?php echo htmlentities($s->StudentMobile); ?></td>
                            <td><?php echo htmlentities($s->Branch); ?></td>
                            <td><?php echo htmlentities($s->Semester); ?></td>
                            <td><?php echo htmlentities($s->Section); ?></td>
                            <td><?php echo htmlentities($s->Batch); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="10" class="text-center">No students found.</td></tr>
                    <?php endif; ?>
                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#studentTable').DataTable({ responsive:true, pageLength:10 });
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });
});
</script>
</body>
</html>
