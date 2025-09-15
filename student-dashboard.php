<?php
session_start();
include('includes/config.php');

if(!isset($_SESSION['RegNo'])){
    header('Location: login.php');
    exit;
}

$RegNo = $_SESSION['RegNo'];
$StudentName = $_SESSION['StudentName'] ?? '';

/* ==========================
   Logout
========================== */
if(isset($_GET['logout'])){
    session_unset();
    session_destroy();
    setcookie(session_name(), '', time() - 3600, '/');
    header('Location: login.php');
    exit;
}



/* ==========================
   AJAX: Upload Profile Photo
========================== */
if(isset($_POST['ajax_upload_photo'])){
    $sqlCheck = "SELECT Photo FROM tblstudents WHERE RegNo=:RegNo";
    $queryCheck = $dbh->prepare($sqlCheck);
    $queryCheck->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
    $queryCheck->execute();
    $user = $queryCheck->fetch(PDO::FETCH_OBJ);

    if(!empty($user->Photo)){
        echo json_encode(['status'=>'error','msg'=>'Profile photo already uploaded.']);
        exit;
    }

    if(isset($_FILES['profilePhoto']) && $_FILES['profilePhoto']['error'] == 0){
        $file = $_FILES['profilePhoto'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg','jpeg','png','gif'];
        if(!in_array(strtolower($ext), $allowed)) {
            echo json_encode(['status'=>'error','msg'=>'Invalid file type. Only JPG, PNG, GIF allowed.']);
            exit;
        }
        // Basic size check (optional): max ~5MB
        if($file['size'] > 5 * 1024 * 1024){
            echo json_encode(['status'=>'error','msg'=>'File too large (max 5MB).']);
            exit;
        }

        $newName = 'student_'.$RegNo.'_'.time().'.'.$ext;
        $uploadDir = 'uploads/';
        if(!is_dir($uploadDir)){
            @mkdir($uploadDir, 0777, true);
        }

        if(move_uploaded_file($file['tmp_name'], $uploadDir.$newName)){
            $sqlUpdate = "UPDATE tblstudents SET Photo=:photo WHERE RegNo=:RegNo";
            $queryUpdate = $dbh->prepare($sqlUpdate);
            $queryUpdate->bindParam(':photo', $newName, PDO::PARAM_STR);
            $queryUpdate->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
            if($queryUpdate->execute()){
                echo json_encode(['status'=>'success','msg'=>'Profile photo uploaded successfully!','photo'=>'uploads/'.$newName]);
            } else {
                echo json_encode(['status'=>'error','msg'=>'Database update failed.']);
            }
        } else {
            echo json_encode(['status'=>'error','msg'=>'Failed to upload file.']);
        }
    } else {
        echo json_encode(['status'=>'error','msg'=>'No file selected.']);
    }
    exit;
}
/* ==========================
   AJAX: Update Student Settings
========================== */
if(isset($_POST['update_settings'])){
    $name = $_POST['studentName'] ?? '';
    $email = $_POST['studentEmail'] ?? '';
    $mobile = $_POST['studentMobile'] ?? '';

    $sql = "UPDATE tblstudents 
            SET StudentName=:name, StudentEmail=:email, StudentMobile=:mobile 
            WHERE RegNo=:RegNo";

    $query = $dbh->prepare($sql);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $query->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);

    if($query->execute()){
        echo json_encode(['status'=>'success','msg'=>'Details updated successfully!']);
    } else {
        echo json_encode(['status'=>'error','msg'=>'Failed to update details.']);
    }
    exit;
}

/* ==========================
   AJAX: Fetch Results
========================== */
if(isset($_POST['ajax_get_results'])){
    $semester = $_POST['semester'] ?? '';
    $sql = "SELECT r.*
            FROM tblresult r
            WHERE r.RegNo=:RegNo AND r.Semester=:Semester";
    $query = $dbh->prepare($sql);
    $query->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
    $query->bindParam(':Semester', $semester, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0){
        echo '<table class="table table-bordered">';
        echo '<thead class="table-dark"><tr>
                <th>Subject</th>
                <th>Subject Code</th>
                <th>Marks</th>
                <th>Grade</th>
                <th>Credits</th>
              </tr></thead><tbody>';
        foreach($results as $r){
            echo '<tr>
                    <td>'.htmlentities($r->Subject).'</td>
                    <td>'.htmlentities($r->SubjectCode).'</td>
                    <td>'.$r->Internals.'</td>
                    <td>'.$r->Grade.'</td>
                    <td>'.$r->Credits.'</td>
                  </tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p class="text-muted">📢 Results will be updated soon.</p>';
    }
    exit;
}

/* ==========================
   AJAX: Fetch Fees
========================== */
if(isset($_POST['ajax_get_fees'])){
    $sqlFee = "SELECT * FROM tblfees WHERE RegNo=:RegNo ORDER BY id DESC";
    $queryFee = $dbh->prepare($sqlFee);
    $queryFee->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
    $queryFee->execute();
    $fees = $queryFee->fetchAll(PDO::FETCH_OBJ);

    if($queryFee->rowCount() > 0) {
        // Desktop Table
        echo '<div class="d-none d-md-block">';
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="table-dark">
                <tr>
                  <th>Year</th>
                  <th>Tuition Fee</th><th>Ref No</th>
                  <th>Hostel Fee</th><th>Ref No</th>
                  <th>Bus Fee</th><th>Ref No</th>
                  <th>University Fee</th><th>Ref No</th>
                  <th>Actions</th>
                </tr>
              </thead><tbody>';
        foreach($fees as $fee){
            echo '<tr>
                <td class="col-year">'.htmlentities($fee->Year).'</td>
                <td class="col-tuition">'.htmlentities($fee->TuitionFeeAmount).'</td>
                <td class="col-tuitionRef">'.htmlentities($fee->TuitionFeeRef).'</td>
                <td class="col-hostel">'.htmlentities($fee->HostelFeeAmount).'</td>
                <td class="col-hostelRef">'.htmlentities($fee->HostelFeeRef).'</td>
                <td class="col-bus">'.htmlentities($fee->BusFeeAmount).'</td>
                <td class="col-busRef">'.htmlentities($fee->BusFeeRef).'</td>
                <td class="col-univ">'.htmlentities($fee->UniversityFeeAmount).'</td>
                <td class="col-univRef">'.htmlentities($fee->UniversityFeeRef).'</td>
                <td>
                    <button class="btn btn-sm btn-info edit-fee" data-id="'.(int)$fee->id.'">Edit</button>
                    <button class="btn btn-sm btn-danger delete-fee" data-id="'.(int)$fee->id.'">Delete</button>
                </td>
            </tr>';
        }
        echo '</tbody></table></div>';

        // Mobile Cards
        echo '<div class="d-md-none">';
        foreach($fees as $fee){
            echo '<div class="card mb-3 shadow-sm">
                    <div class="card-body">
                      <h6 class="card-title text-primary">'.htmlentities($fee->Year).'</h6>
                      <p><strong>Tuition Fee:</strong> '.htmlentities($fee->TuitionFeeAmount).' <br>
                         <small><strong>Ref:</strong> '.htmlentities($fee->TuitionFeeRef).'</small></p>
                      <p><strong>Hostel Fee:</strong> '.htmlentities($fee->HostelFeeAmount).' <br>
                         <small><strong>Ref:</strong> '.htmlentities($fee->HostelFeeRef).'</small></p>
                      <p><strong>Bus Fee:</strong> '.htmlentities($fee->BusFeeAmount).' <br>
                         <small><strong>Ref:</strong> '.htmlentities($fee->BusFeeRef).'</small></p>
                      <p><strong>University Fee:</strong> '.htmlentities($fee->UniversityFeeAmount).' <br>
                         <small><strong>Ref:</strong> '.htmlentities($fee->UniversityFeeRef).'</small></p>
                      <div class="mt-2">
                        <button class="btn btn-sm btn-info edit-fee" data-id="'.(int)$fee->id.'">Edit</button>
                        <button class="btn btn-sm btn-danger delete-fee" data-id="'.(int)$fee->id.'">Delete</button>
                      </div>
                    </div>
                  </div>';
        }
        echo '</div>';
    } else {
        echo '<p class="text-muted">No fee records found.</p>';
    }
    exit; // <<-- critical!
}

/* ==========================
   AJAX: Save/Edit/Delete Fee
========================== */
if(isset($_POST['feeId']) || isset($_POST['delete_fee'])){
    $feeId = $_POST['feeId'] ?? null;

    // Delete Fee
    if(isset($_POST['delete_fee'])){
        $sqlDel = "DELETE FROM tblfees WHERE id=:id AND RegNo=:regno";
        $queryDel = $dbh->prepare($sqlDel);
        $queryDel->bindParam(':id', $feeId, PDO::PARAM_INT);
        $queryDel->bindParam(':regno', $RegNo, PDO::PARAM_STR);

        if($queryDel->execute()){
            echo json_encode(['status'=>'success','msg'=>'Fee record deleted successfully!']);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Failed to delete fee record.']);
        }
        exit;
    }

    // Common fields
    $academicYear = $_POST['academicYear'] ?? '';
    $tuition      = $_POST['tuitionFee'] ?? 0;
    $tuitionRef   = $_POST['tuitionRef'] ?? '';
    $hostel       = $_POST['hostelFee'] ?? 0;
    $hostelRef    = $_POST['hostelRef'] ?? '';
    $bus          = $_POST['busFee'] ?? 0;
    $busRef       = $_POST['busRef'] ?? '';
    $univ         = $_POST['universityFee'] ?? 0;
    $univRef      = $_POST['univRef'] ?? '';

    if(!empty($feeId)){
        // Update
        $sqlUpd = "UPDATE tblfees 
                   SET Year=:year, 
                       TuitionFeeAmount=:tuition, TuitionFeeRef=:tref,
                       HostelFeeAmount=:hostel, HostelFeeRef=:href,
                       BusFeeAmount=:bus, BusFeeRef=:bref,
                       UniversityFeeAmount=:uni, UniversityFeeRef=:uref
                   WHERE id=:id AND RegNo=:regno";
        $queryUpd = $dbh->prepare($sqlUpd);
        $queryUpd->bindParam(':year',$academicYear);
        $queryUpd->bindParam(':tuition',$tuition);
        $queryUpd->bindParam(':tref',$tuitionRef);
        $queryUpd->bindParam(':hostel',$hostel);
        $queryUpd->bindParam(':href',$hostelRef);
        $queryUpd->bindParam(':bus',$bus);
        $queryUpd->bindParam(':bref',$busRef);
        $queryUpd->bindParam(':uni',$univ);
        $queryUpd->bindParam(':uref',$univRef);
        $queryUpd->bindParam(':id',$feeId);
        $queryUpd->bindParam(':regno',$RegNo);

        if($queryUpd->execute()){
            echo json_encode(['status'=>'success','msg'=>'Fee updated successfully!']);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Failed to update fee.']);
        }
    } else {
    // Check if record already exists for RegNo + Year
    $sqlCheck = "SELECT id FROM tblfees WHERE RegNo=:regno AND Year=:year";
    $queryCheck = $dbh->prepare($sqlCheck);
    $queryCheck->bindParam(':regno',$RegNo);
    $queryCheck->bindParam(':year',$academicYear);
    $queryCheck->execute();

    if($queryCheck->rowCount() > 0){
        // Update existing instead of insert
        $existing = $queryCheck->fetch(PDO::FETCH_OBJ);
        $sqlUpd = "UPDATE tblfees 
                   SET TuitionFeeAmount=:tuition, TuitionFeeRef=:tref,
                       HostelFeeAmount=:hostel, HostelFeeRef=:href,
                       BusFeeAmount=:bus, BusFeeRef=:bref,
                       UniversityFeeAmount=:uni, UniversityFeeRef=:uref
                   WHERE id=:id AND RegNo=:regno";
        $queryUpd = $dbh->prepare($sqlUpd);
        $queryUpd->bindParam(':tuition',$tuition);
        $queryUpd->bindParam(':tref',$tuitionRef);
        $queryUpd->bindParam(':hostel',$hostel);
        $queryUpd->bindParam(':href',$hostelRef);
        $queryUpd->bindParam(':bus',$bus);
        $queryUpd->bindParam(':bref',$busRef);
        $queryUpd->bindParam(':uni',$univ);
        $queryUpd->bindParam(':uref',$univRef);
        $queryUpd->bindParam(':id',$existing->id);
        $queryUpd->bindParam(':regno',$RegNo);

        if($queryUpd->execute()){
            echo json_encode(['status'=>'success','msg'=>'Fee updated (duplicate year merged).']);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Failed to update fee.']);
        }
    } else {
        // Fresh Insert
        $sqlIns = "INSERT INTO tblfees 
                  (RegNo, Year, TuitionFeeAmount, TuitionFeeRef, HostelFeeAmount, HostelFeeRef, BusFeeAmount, BusFeeRef, UniversityFeeAmount, UniversityFeeRef) 
                   VALUES (:regno,:year,:tuition,:tref,:hostel,:href,:bus,:bref,:uni,:uref)";
        $queryIns = $dbh->prepare($sqlIns);
        $queryIns->bindParam(':regno',$RegNo);
        $queryIns->bindParam(':year',$academicYear);
        $queryIns->bindParam(':tuition',$tuition);
        $queryIns->bindParam(':tref',$tuitionRef);
        $queryIns->bindParam(':hostel',$hostel);
        $queryIns->bindParam(':href',$hostelRef);
        $queryIns->bindParam(':bus',$bus);
        $queryIns->bindParam(':bref',$busRef);
        $queryIns->bindParam(':uni',$univ);
        $queryIns->bindParam(':uref',$univRef);

        if($queryIns->execute()){
            echo json_encode(['status'=>'success','msg'=>'Fee added successfully!']);
        } else {
            echo json_encode(['status'=>'error','msg'=>'Failed to add fee.']);
        }
    }
}

    exit;
}

/* ==========================
   AJAX: Attendance JSON
========================== */
if(isset($_POST['ajax_get_attendance_json'])){
    $sqlAtt = "SELECT AttendanceDate, TotalPeriods, PresentPeriods 
               FROM tblattendance 
               WHERE RegNo=:RegNo 
               ORDER BY AttendanceDate ASC";
    $queryAtt = $dbh->prepare($sqlAtt);
    $queryAtt->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
    $queryAtt->execute();
    $att = $queryAtt->fetchAll(PDO::FETCH_ASSOC);

    $totalPeriods = 0;
    $presentPeriods = 0;
    $lastDate = null;
    $attendanceData = [];

    foreach($att as $a){
        $totalPeriods += (int)$a['TotalPeriods'];
        $presentPeriods += (int)$a['PresentPeriods'];
        $lastDate = $a['AttendanceDate']; // last loop = latest date
        $attendanceData[$a['AttendanceDate']] = [
            'total'   => $a['TotalPeriods'],
            'present' => $a['PresentPeriods']
        ];
    }

    $percent = ($totalPeriods > 0) ? round(($presentPeriods / $totalPeriods) * 100, 2) : 0;

    echo json_encode([
        'summary'=>[
            'totalPeriods'=>$totalPeriods,
            'presentPeriods'=>$presentPeriods,
            'percent'=>$percent,
            'untilDate'=>$lastDate
        ],
        'data'=>$attendanceData
    ]);
    exit;
}


/* ==========================
   AJAX: Fetch Notices
========================== */
if(isset($_POST['ajax_get_notices'])){
    $sqlNotice = "SELECT noticeTitle, noticeDetails, postedBy, role, postingDate 
                  FROM tblnotice ORDER BY postingDate DESC";
    $queryNotice = $dbh->prepare($sqlNotice);
    $queryNotice->execute();
    $notices = $queryNotice->fetchAll(PDO::FETCH_OBJ);

    if($queryNotice->rowCount() > 0){
        echo '<ul class="list-group">';
        foreach($notices as $n){
            $by = ($n->role == 'Admin') ? 'Admin' : 'Staff';
            echo '<li class="list-group-item">
                    <strong>'.htmlentities($n->noticeTitle).'</strong><br>'
                    .nl2br(htmlentities($n->noticeDetails)).'
                    <br><small>Posted by '.htmlentities($n->postedBy).' ('.$by.') on '.htmlentities($n->postingDate).'</small>
                  </li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No notices found.</p>';
    }
    exit;
}


/* ==========================
   Fetch Student Bio
========================== */
$sqlBio = "SELECT 
              s.RegNo,
              s.StudentName,
              s.DOB,
              s.Gender,
              s.StudentEmail,
              s.StudentMobile,
              s.FatherName,
              s.FatherMobile,
              s.AdmissionType,
              s.CounsellorName,
              s.CounsellorMobile,
              c.Semester,
              c.Branch,
              c.Section,
              CONCAT(c.BatchStart,'-',c.BatchEnd) AS Batch,
              s.RegDate
           FROM tblstudents s
           LEFT JOIN tblclasses c ON c.id = s.ClassId
           WHERE s.RegNo = :RegNo";
$queryBio = $dbh->prepare($sqlBio);
$queryBio->bindParam(':RegNo', $RegNo, PDO::PARAM_STR);
$queryBio->execute();
$studentBio = $queryBio->fetch(PDO::FETCH_OBJ);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Student Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f0f2f5; }
.sidebar {     width: 300px;
    position: fixed;
    top: 10px;
    left: 7px;
    border-radius: 7px;
    height: 95vh;
    background: #03050ade;
    color: #fff;
    padding-top: 20px;
    transition: 0.3s;
    z-index: 1000; }
.sidebar a { display:block; color:#fff; padding:12px 20px; text-decoration:none; border-radius:5px; margin:3px 10px; }
.sidebar a:hover, .sidebar a.active { background:#ffffff5c; }
.content { margin-left:350px; padding:20px; transition:0.3s; }
.tabcontent { display:none; }
.tabcontent.active { display:block; width: 90%; margin: auto;}
.card-profile { background:#fff; border-radius:12px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.semester-tabs button { margin:3px; }
.att-heatmap { display:flex; flex-wrap: wrap; max-width:700px; }
.day-cell { width: 14.28%; height:50px; border:1px solid #ddd; display:flex; align-items:center; justify-content:center; margin-bottom:2px; font-size:0.85rem; cursor:pointer; border-radius:4px; }
.day-cell.present { background: #28a745; color:#fff; }
.day-cell.absent { background: #dc3545; color:#fff; }
.day-cell.norecord { background: #f0f0f0; color:#888; }
.day-cell:hover { transform: scale(1.05); transition:0.2s; }
@media(max-width:768px){
    .sidebar { left:-290px; }
    .sidebar.active { left: 21px; width: 90%; height: 75%; top: 50px; border-radius: 12px; z-index: 1; }
    .content { margin-left:0; }
    .hamburger { position:fixed; top:20px; left:90%; z-index:1100; font-size:24px; cursor:pointer; color:#000; }
}
@media(max-width:768px){
  #fee-list .card {
    border-radius: 10px;
    background: #fff;
  }
  #fee-list .card-title {
    font-size: 1rem;
    font-weight: 600;
  }
  #fee-list p {
    margin-bottom: 5px;
    font-size: 0.9rem;
  }
  #fee-list p small{
    font-size: 0.9rem;

  }
}

</style>
</head>
<body>

<i class="fa fa-bars hamburger d-md-none"></i>

<div class="sidebar">
    <h5 class="text-center mb-3"><i class="fa fa-user-circle me-2"></i><?php echo htmlentities($StudentName ?: $RegNo); ?></h5>
    <a href="#" class="tablink active" data-tab="profile">Profile</a>
    <a href="#" class="tablink" data-tab="fee">Fee Details</a>
    <a href="#" class="tablink" data-tab="attendance">Attendance</a>
    <a href="#" class="tablink" data-tab="results">Results</a>
 
    <a href="#" class="tablink" data-tab="notice">Notices</a>
    <a href="#" class="tablink" data-tab="settings">Settings</a>
    <a href="?logout=1" class="mt-4 btn btn-danger btn-sm d-block text-center">Logout</a>
</div>

<div class="content">
<!-- Settings Tab -->
<div id="settings" class="tabcontent">
    <div class="card shadow-sm p-4">
        <h4 class="mb-3 text-primary"><i class="fa fa-cog me-2"></i>Settings</h4>
        
        <form id="settings-form">
            <div class="mb-3">
                <label for="studentName" class="form-label">Name</label>
                <input type="text" id="studentName" name="studentName" class="form-control" value="<?php echo htmlentities($studentBio->StudentName ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="studentEmail" class="form-label">Email</label>
                <input type="email" id="studentEmail" name="studentEmail" class="form-control" value="<?php echo htmlentities($studentBio->StudentEmail ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="studentMobile" class="form-label">Mobile</label>
                <input type="text" id="studentMobile" name="studentMobile" class="form-control" value="<?php echo htmlentities($studentBio->StudentMobile ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Details</button>
            <div id="settings-msg" class="mt-2"></div>
        </form>
    </div>
</div>

<!-- Profile Tab -->
<div id="profile" class="tabcontent active">
    <div class="card-profile">
        <div style="display:flex; align-items:center; gap:15px;">
            <img id="profile-img" src="<?php echo !empty($studentBio->Photo) ? 'uploads/'.htmlentities($studentBio->Photo) : 'uploads/default.png'; ?>" 
                 alt="Profile Photo" style="width:70px; height:70px; border-radius:50%; object-fit:cover; border:2px solid #ccc;">
            <h4 style="margin:0;">Student Profile</h4>
        </div>
        <?php if(empty($studentBio->Photo)): ?>
        <form id="upload-photo-form" enctype="multipart/form-data" class="mt-2">
            <input type="file" name="profilePhoto" accept="image/*" required>
            <button type="submit" class="btn btn-sm btn-primary">Upload Photo</button>
        </form>
        <div id="upload-msg" class="mt-2"></div>
        <?php else: ?>
        <p class="text-success mt-2">Profile photo already uploaded.</p>
        <?php endif; ?>

        <hr>
        <div class="card shadow-sm p-4 mb-4">
    <h4 class="mb-4 text-primary"><i class="fa fa-user me-2"></i>Student Profile</h4>
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle mb-0">
            <tbody>
                <tr>
                    <th scope="row">Name</th>
                    <td><?php echo htmlentities($studentBio->StudentName ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Reg No</th>
                    <td><?php echo htmlentities($studentBio->RegNo ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Date of Birth</th>
                    <td><?php echo htmlentities($studentBio->DOB ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Gender</th>
                    <td><?php echo htmlentities($studentBio->Gender ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Semester</th>
                    <td><?php echo htmlentities($studentBio->Semester ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Branch</th>
                    <td><?php echo htmlentities($studentBio->Branch ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Section</th>
                    <td><?php echo htmlentities($studentBio->Section ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Batch</th>
                    <td><?php echo htmlentities($studentBio->Batch ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Email</th>
                    <td><a href="mailto:<?php echo htmlentities($studentBio->StudentEmail ?? ''); ?>"><?php echo htmlentities($studentBio->StudentEmail ?? ''); ?></a></td>
                </tr>
                <tr>
                    <th scope="row">Mobile</th>
                    <td><a href="tel:<?php echo htmlentities($studentBio->StudentMobile ?? ''); ?>"><?php echo htmlentities($studentBio->StudentMobile ?? ''); ?></a></td>
                </tr>
                <tr>
                    <th scope="row">Father Name</th>
                    <td><?php echo htmlentities($studentBio->FatherName ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Father Mobile</th>
                    <td><a href="tel:<?php echo htmlentities($studentBio->FatherMobile ?? ''); ?>"><?php echo htmlentities($studentBio->FatherMobile ?? ''); ?></a></td>
                </tr>
                <tr>
                    <th scope="row">Admission Type</th>
                    <td><?php echo htmlentities($studentBio->AdmissionType ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Counsellor Name</th>
                    <td><?php echo htmlentities($studentBio->CounsellorName ?? ''); ?></td>
                </tr>
                <tr>
                    <th scope="row">Counsellor Mobile</th>
                    <td><a href="tel:<?php echo htmlentities($studentBio->CounsellorMobile ?? ''); ?>"><?php echo htmlentities($studentBio->CounsellorMobile ?? ''); ?></a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    </div>
</div>

<!-- Fee Tab -->
<div id="fee" class="tabcontent">
    <div class="card-profile">
        <button class="btn btn-sm btn-success mb-2" id="add-fee-btn">Add Fee</button>
        <div id="fee-form-container" style="display:none;">
            <form id="fee-form">
                <input type="hidden" name="feeId" value="">
                <p class="text-muted" style="font-size:0.9rem;">
                    📌 <em>If fees are paid in multiple terms, enter all reference numbers separated by commas.  
                    The amount field should contain the <strong>total amount paid</strong>.</em>
                </p>
                <div class="mb-2">
                    <label for="academicYear">Select Academic Year</label>
                    <select id="academicYear" name="academicYear" class="form-control" required>
                        <option value="">Choose Year</option>
                        <option>1st Year</option>
                        <option>2nd Year</option>
                        <option>3rd Year</option>
                        <option>4th Year</option>
                    </select>
                </div>
                
                <h6>Tuition Fee</h6>
                <input type="number" name="tuitionFee" class="form-control mb-2" placeholder="Enter Amount Paid">
                <input type="text" name="tuitionRef" class="form-control mb-3" placeholder="Enter DU / Reference Number(s)">

                <h6>Hostel Fee</h6>
                <input type="number" name="hostelFee" class="form-control mb-2" placeholder="Enter Amount Paid">
                <input type="text" name="hostelRef" class="form-control mb-3" placeholder="Enter DU / Reference Number(s)">

                <h6>Bus Fee</h6>
                <input type="number" name="busFee" class="form-control mb-2" placeholder="Enter Amount Paid">
                <input type="text" name="busRef" class="form-control mb-3" placeholder="Enter DU / Reference Number(s)">

                <h6>University Fee</h6>
                <input type="number" name="universityFee" class="form-control mb-2" placeholder="Enter Amount Paid">
                <input type="text" name="univRef" class="form-control mb-3" placeholder="Enter DU / Reference Number(s)">
                
                <button type="submit" class="btn btn-sm btn-primary">Save Fee</button>
                <button type="button" id="fee-cancel" class="btn btn-sm btn-secondary">Cancel</button>
            </form>

        </div>
        <div id="fee-msg" class="mt-2"></div>
        <div id="fee-list"></div>
    </div>
        </div>

<!-- Attendance Tab -->
<div id="attendance" class="tabcontent">
    <div class="card shadow-sm p-4 mb-4">
        <h4 class="mb-3 text-primary"><i class="fa fa-calendar-check me-2"></i>Attendance</h4>

        <!-- Attendance Summary -->
        <div id="attendance-summary" class="mb-4 p-3 border rounded bg-light">
            <!-- Example dynamic content:
            <p><strong>Total Days:</strong> 120 &nbsp; | &nbsp; <strong>Present:</strong> 110 &nbsp; | &nbsp; <strong>Absent:</strong> 10</p>
            -->
        </div>

        
    </div>
</div>

<style>
/* Attendance heatmap redesign */
.att-heatmap .day-cell {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    font-weight: 500;
    border-radius: 6px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    color: #fff;
}
.att-heatmap .day-cell.present { background-color: #28a745; }
.att-heatmap .day-cell.absent { background-color: #dc3545; }
.att-heatmap .day-cell.norecord { background-color: #d1d1d1; color: #555; }
.att-heatmap .day-cell:hover { transform: scale(1.1); box-shadow: 0 2px 8px rgba(0,0,0,0.2); }
</style>


<!-- Results Tab -->
<div id="results" class="tabcontent">
    <div class="card-profile">
        <h4 class="mb-3 text-primary"><i class="fa fa-graduation-cap me-2"></i>Results</h4>

        <!-- Semester Buttons -->
        <div class="semester-tabs mb-3">
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="All">All</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="1-1">1-1</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="1-2">1-2</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="2-1">2-1</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="2-2">2-2</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="3-1">3-1</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="3-2">3-2</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="4-1">4-1</button>
            <button class="btn btn-outline-primary btn-sm sem-btn" data-sem="4-2">4-2</button>
        </div>

        <div id="results-container">
    <p class="text-muted">📌 Select a semester to view results.</p>
</div>

    </div>
</div>





<!-- Notices Tab -->
<div id="notice" class="tabcontent">
    <div class="card-profile" id="notice-list"></div>
</div>

<script>

    $(document).ready(function(){
    $(".sem-btn").click(function(){
        let sem = $(this).data("sem");
        $("#results-container").html('<div class="text-center p-3"><div class="spinner-border text-primary"></div> Loading...</div>');

        $.post("fetch-results.php", {semester: sem}, function(res){
            let r = JSON.parse(res);
            if(r.status == "success"){
                $("#results-container").html(r.html);
            } else {
                $("#results-container").html(`<div class="alert alert-warning">${r.msg}</div>`);
            }
        });
    });
});

    
$(document).ready(function(){
    // Mobile sidebar toggle
    $('.hamburger').click(function(){ $('.sidebar').toggleClass('active'); });

    // Tabs
    $('.tablink').click(function(e){
        e.preventDefault();
        $('.tablink').removeClass('active');
        $(this).addClass('active');
        $('.tabcontent').removeClass('active');
        $('#' + $(this).data('tab')).addClass('active');
    });

    // Profile photo upload
    $('#upload-photo-form').submit(function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('ajax_upload_photo',1);
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res){
                $('#upload-msg').html(res.msg);
                if(res.status=='success'){
                    $('#profile-img').attr('src', res.photo);
                    $('#upload-photo-form').hide();
                }
            },
            error: function(){ $('#upload-msg').html('Something went wrong!'); }
        });
    });

    // Fees
    function loadFees(){ $.post('',{ajax_get_fees:1},function(data){ $('#fee-list').html(data); }); }
    loadFees();

    $('#add-fee-btn').click(function(){ 
        $('#fee-form')[0].reset(); 
        $('#fee-form [name=feeId]').val('');
        $('#fee-form-container').show(); 
    });
    $('#fee-cancel').click(function(){ $('#fee-form-container').hide(); });

    $('#fee-form').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: '',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(res){
                if(res.status === 'success'){
                    $('#fee-msg').html('<span class="text-success">'+res.msg+'</span>');
                    loadFees();
                    $('#fee-form-container').hide();
                } else {
                    $('#fee-msg').html('<span class="text-danger">'+res.msg+'</span>');
                }
            },
            error: function(){
                $('#fee-msg').html('<span class="text-danger">Something went wrong.</span>');
            }
        });
    });


    $(document).on('click','.edit-fee',function(){
        var row = $(this).closest('tr');
        var feeId = $(this).data('id');

        $('#fee-form-container').show();
        $('#fee-form [name=feeId]').val(feeId);

        $('#fee-form [name=academicYear]').val(row.find('.col-year').text().trim());

        $('#fee-form [name=tuitionFee]').val(row.find('.col-tuition').text().trim());
        $('#fee-form [name=tuitionRef]').val(row.find('.col-tuitionRef').text().trim());

        $('#fee-form [name=hostelFee]').val(row.find('.col-hostel').text().trim());
        $('#fee-form [name=hostelRef]').val(row.find('.col-hostelRef').text().trim());

        $('#fee-form [name=busFee]').val(row.find('.col-bus').text().trim());
        $('#fee-form [name=busRef]').val(row.find('.col-busRef').text().trim());

        $('#fee-form [name=universityFee]').val(row.find('.col-univ').text().trim());
        $('#fee-form [name=univRef]').val(row.find('.col-univRef').text().trim());
    });


    $(document).on('click','.delete-fee',function(){
        if(confirm('Are you sure to delete this fee record?')){
            $.post('', {delete_fee:1, feeId: $(this).data('id')}, function(res){ alert(res); loadFees(); });
        }
    });

    // Attendance
    $.post('', {ajax_get_attendance_json:1}, function(res){
        try {
            var data = JSON.parse(res);
            var s = data.summary;
            $('#attendance-summary').html(
                '<p><strong>Total Periods:</strong> '+s.totalPeriods+
                ' &nbsp; | &nbsp; <strong>Present:</strong> '+s.presentPeriods+
                ' &nbsp; | &nbsp; <strong>Percentage:</strong> '+s.percent+'%'+
                (s.untilDate ? ' &nbsp; | &nbsp; <strong>Until:</strong> '+s.untilDate : '')+
                '</p>'
            );
        } catch(e){
            $('#attendance-summary').html('<p>Could not load attendance.</p>');
        }
    });



    // Notices
    $.post('',{ajax_get_notices:1},function(data){ $('#notice-list').html(data); });

    // Results
    $('.sem-btn').click(function(){
        var sem = $(this).data('sem');
        $('#results-list').html('<p class="text-info">Loading results for ' + sem + '...</p>');
        $.post('', {ajax_get_results:1, semester:sem}, function(data){ 
            $('#results-list').html(data); 
        });
    });

});
</script>
</body>
</html>
