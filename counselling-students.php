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

// Fetch counselling students
$sql = "SELECT s.RegNo, s.StudentName, s.DOB, s.Gender, s.StudentEmail, s.StudentMobile,
               s.FatherName, s.FatherMobile, s.AdmissionType,
               c.Branch, c.Semester, c.Section, CONCAT(c.BatchStart,'-',c.BatchEnd) AS Batch
        FROM tblstudents s
        JOIN tblclasses c ON c.id = s.ClassId
        WHERE s.CounsellorId = :staffid
        ORDER BY c.BatchStart DESC, c.Branch, c.Semester, c.Section, s.RegNo ASC";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':staffid',$staffid);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_OBJ);

// Fetch filter options
$batches = array_unique(array_map(fn($s)=>$s->Batch, $students));
$branches = array_unique(array_map(fn($s)=>$s->Branch, $students));
$sections = array_unique(array_map(fn($s)=>$s->Section, $students));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Counselling Students | SRMS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap & FontAwesome -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="style-side-topbar.css">

<style>




/* Card */
.card { border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.card-header { background:#0f3ba7; color:#fff; font-weight:600; }
.table thead th { background:#007bff; color:#fff; }

/* Filter */
.filter-row select { width: 100%; }


</style>
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
            My Counselling Students
        </div>
        <div class="card-body">
            <h5>Hello, <?php echo htmlentities($staff['StaffName']); ?> (<?php echo htmlentities($staff['Department']); ?>)</h5>
            <p class="text-muted">Below are the students assigned to you for counselling:</p>

            <?php if($students){ ?>
            <!-- Filters -->
            <div class="row mb-3 filter-row">
                <div class="col-md-3 mb-2">
                    <select id="filterBatch" class="form-select">
                        <option value="">-- Filter by Batch --</option>
                        <?php foreach($batches as $b){ if($b) echo "<option value='".htmlentities($b)."'>$b</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select id="filterBranch" class="form-select">
                        <option value="">-- Filter by Branch --</option>
                        <?php foreach($branches as $br){ if($br) echo "<option value='".htmlentities($br)."'>$br</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select id="filterSection" class="form-select">
                        <option value="">-- Filter by Section --</option>
                        <?php foreach($sections as $sec){ if($sec) echo "<option value='".htmlentities($sec)."'>$sec</option>"; } ?>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <button id="resetFilters" class="btn btn-secondary w-100">Reset Filters</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="studentsTable" class="table table-bordered table-striped nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Reg No</th>
                            <th>Name</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Father Name</th>
                            <th>Father Mobile</th>
                            <th>Admission Type</th>
                            <th>Batch</th>
                            <th>Branch</th>
                            <th>Semester</th>
                            <th>Section</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($students as $s){ ?>
                        <tr>
                            <td><?php echo htmlentities($s->RegNo); ?></td>
                            <td><?php echo htmlentities($s->StudentName); ?></td>
                            <td><?php echo htmlentities($s->DOB); ?></td>
                            <td><?php echo htmlentities($s->Gender); ?></td>
                            <td><?php echo htmlentities($s->StudentEmail); ?></td>
                            <td><?php echo htmlentities($s->StudentMobile); ?></td>
                            <td><?php echo htmlentities($s->FatherName); ?></td>
                            <td><?php echo htmlentities($s->FatherMobile); ?></td>
                            <td><?php echo htmlentities($s->AdmissionType); ?></td>
                            <td><?php echo htmlentities($s->Batch); ?></td>
                            <td><?php echo htmlentities($s->Branch); ?></td>
                            <td><?php echo htmlentities($s->Semester); ?></td>
                            <td><?php echo htmlentities($s->Section); ?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } else { ?>
                <div class="alert alert-info">No students have been assigned to you for counselling yet.</div>
            <?php } ?>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });

    var table = $('#studentsTable').DataTable({
        responsive: true,
        pageLength: 10,
        dom: "<'row'<'col-md-6'B><'col-md-6'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
            { extend: 'excelHtml5', className: 'btn btn-success me-2', text: 'Export Excel', title: 'My Counselling Students' },
            { extend: 'pdfHtml5', className: 'btn btn-danger me-2', text: 'Export PDF', title: 'My Counselling Students' },
            { extend: 'print', className: 'btn btn-info', text: 'Print', title: 'My Counselling Students' }
        ]
    });

    $('#filterBatch').on('change', function(){ table.column(9).search(this.value).draw(); });
    $('#filterBranch').on('change', function(){ table.column(10).search(this.value).draw(); });
    $('#filterSection').on('change', function(){ table.column(12).search(this.value).draw(); });
    $('#resetFilters').click(function(){ 
        $('#filterBatch, #filterBranch, #filterSection').val('');
        table.columns().search('').draw();
    });
});
</script>
</body>
</html>
