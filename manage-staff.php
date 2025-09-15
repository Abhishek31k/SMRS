<?php
session_start();
include('includes/config.php');

// Check HOD login
if(!isset($_SESSION['slogin'])){ header("Location: staff-login.php"); exit; }
$staffid = $_SESSION['slogin'];

// Get HOD role + department
$sql = "SELECT StaffName, Role, Department FROM tblstaff WHERE StaffId=:staffid LIMIT 1";
$query = $dbh->prepare($sql);
$query->bindParam(':staffid', $staffid, PDO::PARAM_INT);
$query->execute();
$hod = $query->fetch(PDO::FETCH_ASSOC);

if(!$hod || $hod['Role']!=='HOD'){ echo "Access denied"; exit; }
$hodDept = $hod['Department']; // logged-in HOD’s department

// Add new staff
if(isset($_POST['add'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $designation = $_POST['designation'];
    $staff_code = $_POST['staff_code'];
    $password = md5($_POST['password']); 

    $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblstaff WHERE Email=:email OR StaffCode=:code");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':code', $staff_code);
    $stmt->execute();
    if($stmt->fetchColumn() > 0){
        $error = "Email or Staff Code already exists!";
    } else {
        $stmt2 = $dbh->prepare("INSERT INTO tblstaff 
            (StaffName, StaffCode, Email, Mobile, Department, Designation, Role, Password) 
            VALUES (:name,:code,:email,:mobile,:dept,:desig,'Staff',:pass)");
        $stmt2->bindParam(':name', $name);
        $stmt2->bindParam(':code', $staff_code);
        $stmt2->bindParam(':email', $email);
        $stmt2->bindParam(':mobile', $mobile);
        $stmt2->bindParam(':dept', $hodDept); 
        $stmt2->bindParam(':desig', $designation);
        $stmt2->bindParam(':pass', $password);
        $stmt2->execute();
        $msg = "Staff added successfully!";
    }
}

// Edit staff
if(isset($_POST['edit'])){
    $id = intval($_POST['staff_id']);
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $designation = $_POST['designation'];
    $status = $_POST['status'];

    $stmt = $dbh->prepare("SELECT COUNT(*) FROM tblstaff WHERE (Email=:email) AND StaffId<>:id");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    if($stmt->fetchColumn() > 0){
        $error = "Email already exists!";
    } else {
        $stmt2 = $dbh->prepare("UPDATE tblstaff 
            SET StaffName=:name, Email=:email, Mobile=:mobile, Department=:dept, Designation=:designation, Status=:status
            WHERE StaffId=:id");
        $stmt2->bindParam(':name', $name);
        $stmt2->bindParam(':email', $email);
        $stmt2->bindParam(':mobile', $mobile);
        $stmt2->bindParam(':dept', $hodDept); 
        $stmt2->bindParam(':designation', $designation);
        $stmt2->bindParam(':status', $status);
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();
        $msg = "Staff details updated successfully!";
    }
}

// Activate staff
if(isset($_GET['activate'])){
    $id = intval($_GET['activate']);
    $stmt = $dbh->prepare("UPDATE tblstaff SET Status='Active' WHERE StaffId=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $msg = "Staff activated successfully!";
}

// Deactivate staff
if(isset($_GET['deactivate'])){
    $id = intval($_GET['deactivate']);
    $stmt = $dbh->prepare("UPDATE tblstaff SET Status='Inactive' WHERE StaffId=:id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $msg = "Staff deactivated successfully!";
}

// Fetch staff only from HOD’s department
$stmt = $dbh->prepare("SELECT * FROM tblstaff WHERE Role='Staff' AND Department=:dept ORDER BY StaffId DESC");
$stmt->bindParam(':dept', $hodDept);
$stmt->execute();
$allStaff = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Staff | HOD Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="style-side-topbar.css">

</head>
<body>
<!-- Topbar -->
<?php include('staff-topbar.php'); ?>
<!-- Sidebar -->
<?php include('staff-sidebar.php'); ?>

<!-- Content -->
<div class="content">
    <h2 class="mb-4">Manage Staff (<?php echo htmlentities($hodDept); ?> Department)</h2>

    <?php if(isset($msg)){ echo "<div class='alert alert-success'>$msg</div>"; } ?>
    <?php if(isset($error)){ echo "<div class='alert alert-danger'>$error</div>"; } ?>

    <!-- Add Staff Form -->
    <div class="card mb-4">
        <div class="card-header">Add New Staff</div>
        <div class="card-body">
            <form method="post">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <input type="text" name="staff_code" class="form-control" placeholder="Staff Code" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text" name="mobile" class="form-control" placeholder="Mobile" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="text" class="form-control bg-light" value="<?php echo htmlentities($hodDept); ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-2">
                        <select name="designation" class="form-select" required>
                            <option value="Assistant Professor">Assistant Professor</option>
                            <option value="Lecturer">Lecturer</option>
                            <option value="Professor">Professor</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="col-md-12">
                        <button type="submit" name="add" class="btn btn-primary mt-2">Add Staff</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Staff List -->
    <div class="card">
        <div class="card-header">Staff List</div>
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Staff Code</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Department</th>
                        <th>Designation</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allStaff as $s){ ?>
                    <tr>
                        <td><?php echo $s['StaffId']; ?></td>
                        <td><?php echo htmlentities($s['StaffCode']); ?></td>
                        <td><?php echo htmlentities($s['StaffName']); ?></td>
                        <td><?php echo htmlentities($s['Email']); ?></td>
                        <td><?php echo htmlentities($s['Mobile']); ?></td>
                        <td><?php echo htmlentities($s['Department']); ?></td>
                        <td><?php echo htmlentities($s['Designation']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $s['Status']=='Active' ? 'success' : 'secondary'; ?>">
                                <?php echo htmlentities($s['Status']); ?>
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-info editBtn"
                                data-id="<?php echo $s['StaffId']; ?>"
                                data-name="<?php echo htmlentities($s['StaffName']); ?>"
                                data-email="<?php echo htmlentities($s['Email']); ?>"
                                data-mobile="<?php echo htmlentities($s['Mobile']); ?>"
                                data-dept="<?php echo htmlentities($s['Department']); ?>"
                                data-designation="<?php echo htmlentities($s['Designation']); ?>"
                                data-status="<?php echo $s['Status']; ?>">Edit</button>

                            <?php if($s['Status']=='Active'){ ?>
                                <a href="?deactivate=<?php echo $s['StaffId']; ?>" class="btn btn-sm btn-warning">Deactivate</a>
                            <?php } else { ?>
                                <a href="?activate=<?php echo $s['StaffId']; ?>" class="btn btn-sm btn-success">Activate</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1">
  <div class="modal-dialog">
    <form method="post">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Edit Staff</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="staff_id" id="edit_staff_id">
            <div class="mb-2"><input type="text" name="name" id="edit_name" class="form-control" required></div>
            <div class="mb-2"><input type="email" name="email" id="edit_email" class="form-control" required></div>
            <div class="mb-2"><input type="text" name="mobile" id="edit_mobile" class="form-control" required></div>
            <div class="mb-2"><input type="text" id="edit_department_display" class="form-control bg-light" readonly></div>
            <div class="mb-2">
                <select name="designation" id="edit_designation" class="form-select">
                    <option value="Assistant Professor">Assistant Professor</option>
                    <option value="Lecturer">Lecturer</option>
                    <option value="Professor">Professor</option>
                </select>
            </div>
            <div class="mb-2">
                <select name="status" id="edit_status" class="form-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="edit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });

document.querySelectorAll('.editBtn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.getElementById('edit_staff_id').value=btn.dataset.id;
        document.getElementById('edit_name').value=btn.dataset.name;
        document.getElementById('edit_email').value=btn.dataset.email;
        document.getElementById('edit_mobile').value=btn.dataset.mobile;
        document.getElementById('edit_department_display').value=btn.dataset.dept;
        document.getElementById('edit_designation').value=btn.dataset.designation;
        document.getElementById('edit_status').value=btn.dataset.status;
        new bootstrap.Modal(document.getElementById('editStaffModal')).show();
    });
});
</script>
</body>
</html>
