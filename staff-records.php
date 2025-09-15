<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit;
}

$msg = "";

if(isset($_GET['del'])) {
    $id=intval($_GET['del']);
    $sql="DELETE FROM tblstaff WHERE StaffId=:id";
    $query=$dbh->prepare($sql);
    $query->bindParam(':id',$id,PDO::PARAM_INT);
    $query->execute();
    $msg="Staff record deleted successfully";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Staff Records</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/main.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="js/DataTables/datatables.min.css">
    <link rel="stylesheet" href="js/DataTables/buttons.dataTables.min.css">

    <style>
        body {
            background-color: #f5f5f5;
        }
        .filter-container {
            margin-bottom: 15px;
        }
        .filter-container select {
            margin-right: 10px;
            display: inline-block;
            width: auto;
        }
        .dt-buttons {
            margin-bottom: 10px;
        }

        /* Table styling */
        table#staffTable {
            width: 100% !important;
            table-layout: auto;
            background-color: #fff;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table#staffTable thead {
            background-color: #dcdcdc;
        }
        table#staffTable thead th {
            color: #333;
            font-weight: 600;
            text-align: center; /* header centered */
            padding: 10px;
        }
        table#staffTable tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table#staffTable tbody td {
            text-align: left; /* content aligned left */
            padding: 10px;
        }

        /* Action buttons */
        .action-btns {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .btn-action {
            padding: 4px 10px;
            font-size: 0.85rem;
        }
        .btn-edit {
            background-color: #337ab7;
            color: #fff;
            border: none;
        }
        .btn-edit:hover {
            background-color: #286090;
        }
        .btn-delete {
            background-color: #d9534f;
            color: #fff;
            border: none;
        }
        .btn-delete:hover {
            background-color: #a71d2a;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">

    <?php include('includes/topbar.php');?> 
    <div class="content-wrapper">
        <div class="content-container">
            <?php include('includes/leftbar.php');?>  

            <div class="main-page">
                <div class="container-fluid">
                    <h2 class="title">Staff Records</h2>

                    <?php if($msg){?>
                        <div class="alert alert-success"><strong>✔</strong> <?php echo htmlentities($msg); ?></div>
                    <?php } ?>

                    <div class="panel">
                        <div class="panel-body">

                            <!-- Filters -->
                            <div class="filter-container">
                                <label><strong>Filter By:</strong></label>
                                <select id="departmentFilter" class="form-control">
                                    <option value="">All Departments</option>
                                    <option value="CSE">CSE</option>
                                    <option value="IT">IT</option>
                                    <option value="MECH">MECH</option>
                                    <option value="CIVIL">CIVIL</option>
                                    <option value="ECE">ECE</option>
                                    <option value="EEE">EEE</option>
                                </select>
                                <select id="specializationFilter" class="form-control" style="display:none;">
                                    <option value="">All CSE Specializations</option>
                                    <option value="CSM">CSM (AI & ML)</option>
                                    <option value="CAD">CAD</option>
                                    <option value="CSC">CSC (Cyber Security)</option>
                                </select>
                                <select id="designationFilter" class="form-control">
                                    <option value="">All Designations</option>
                                    <option value="Professor">Professor</option>
                                    <option value="HOD">HOD</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Assistant Professor">Assistant Professor</option>
                                    <option value="Associate Professor">Associate Professor</option>
                                </select>
                            </div>

                            <!-- Table -->
                            <div class="table-responsive">
                                <table id="staffTable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Action</th>
                                            <th>Staff Code</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql="SELECT * FROM tblstaff";
                                    $query=$dbh->prepare($sql);
                                    $query->execute();
                                    $results=$query->fetchAll(PDO::FETCH_OBJ);
                                    if($query->rowCount() > 0) {
                                        foreach($results as $result) {
                                    ?>
                                        <tr>
                                            <td>
                                                <div class="action-btns">
                                                    <a href="edit-staff.php?id=<?php echo $result->StaffId;?>" 
                                                       class="btn btn-sm btn-action btn-edit" title="Edit">Edit</a>
                                                    <a href="staff-records.php?del=<?php echo $result->StaffId;?>" 
                                                       onclick="return confirm('Do you really want to delete?');" 
                                                       class="btn btn-sm btn-action btn-delete" title="Delete">Delete</a>
                                                </div>
                                            </td>
                                            <td><?php echo htmlentities($result->StaffCode);?></td>
                                            <td><?php echo htmlentities($result->StaffName);?></td>
                                            <td><?php echo htmlentities($result->Gender);?></td>
                                            <td><?php echo htmlentities($result->Email);?></td>
                                            <td><?php echo htmlentities($result->Mobile);?></td>
                                            <td><?php echo htmlentities($result->Role);?></td>
                                            <td><?php echo htmlentities($result->Department);?></td>
                                            <td><?php echo htmlentities($result->Designation);?></td>
                                            <td><?php echo htmlentities($result->Status);?></td>
                                        </tr>
                                    <?php }} ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/DataTables/datatables.min.js"></script>

<script src="js/DataTables/dataTables.buttons.min.js"></script>
<script src="js/DataTables/jszip.min.js"></script>
<script src="js/DataTables/pdfmake.min.js"></script>
<script src="js/DataTables/vfs_fonts.js"></script>
<script src="js/DataTables/buttons.html5.min.js"></script>
<script src="js/DataTables/buttons.print.min.js"></script>

<script src="js/main.js"></script>

<script>
$(document).ready(function() {
    var table = $('#staffTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": false,
        "scrollX": true,
        "autoWidth": false,
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copyHtml5', className: 'btn btn-secondary btn-sm' },
            { extend: 'excelHtml5', className: 'btn btn-success btn-sm' },
            { extend: 'csvHtml5', className: 'btn btn-info btn-sm' },
            { extend: 'pdfHtml5', className: 'btn btn-danger btn-sm' },
            { extend: 'print', className: 'btn btn-primary btn-sm' }
        ],
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ staff members"
        }
    });

    $('#departmentFilter').on('change', function () {
        var val = this.value;
        if(val === "CSE"){
            $('#specializationFilter').show();
            table.column(7).search(val).draw(); 
        } else {
            $('#specializationFilter').hide().val("");
            table.column(7).search(val).draw();
        }
    });

    $('#specializationFilter').on('change', function () {
        table.column(7).search(this.value).draw();
    });

    $('#designationFilter').on('change', function () {
        table.column(8).search(this.value).draw(); 
    });
});
</script>
</body>
</html>
