<?php
session_start();
include('includes/config.php');

$regno = $_SESSION['RegNo'] ?? ''; 
$semester = $_POST['semester'] ?? 'All';

$response = ['status' => 'error', 'msg' => 'No results found', 'html' => ''];

if(!$regno){
    $response['msg'] = "Not logged in!";
    echo json_encode($response);
    exit;
}

function gradePoints($grade){
    $points = ['A+'=>10,'A'=>9,'B+'=>8,'B'=>7,'C+'=>6,'C'=>5,'F'=>0];
    return $points[strtoupper($grade)] ?? 0;
}

function gradeBadge($grade){
    if(strtoupper($grade)=='F') return 'danger';
    switch(strtoupper($grade)){
        case 'A+': return 'success';
        case 'A': return 'primary';
        case 'B+': return 'info';
        case 'B': return 'secondary';
        case 'C+': return 'warning';
        case 'C': return 'dark';
        default: return 'dark';
    }
}

// Semester card color based on CGPA
function semCardColor($cgpa){
    if($cgpa>=8) return 'border-success bg-light';
    if($cgpa>=7) return 'border-primary bg-light';
    if($cgpa>=6) return 'border-warning bg-light';
    return 'border-danger bg-light';
}

// Fetch results
$sql = ($semester=='All')
    ? "SELECT Semester, Subject, SubjectCode, Internals, Grade, Credits 
       FROM tblresult WHERE RegNo=:regno ORDER BY Semester, id"
    : "SELECT Semester, Subject, SubjectCode, Internals, Grade, Credits 
       FROM tblresult WHERE RegNo=:regno AND Semester=:sem ORDER BY id";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':regno', $regno, PDO::PARAM_STR);
if($semester != 'All') $stmt->bindParam(':sem', $semester, PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if($results){
    $stmt2 = $dbh->prepare("SELECT StudentName, Photo FROM tblstudents WHERE RegNo=:regno LIMIT 1");
    $stmt2->bindParam(':regno', $regno, PDO::PARAM_STR);
    $stmt2->execute();
    $studentInfo = $stmt2->fetch(PDO::FETCH_ASSOC);
    $studentName = $studentInfo['StudentName'] ?? $regno;
    $studentPhoto = $studentInfo['Photo'] ?? 'default.png';

    $semesterData = [];
    $overallCredits = 0;
    $overallGradePoints = 0;
    $overallMarks = 0;
    $overallMaxMarks = 0;

    foreach($results as $row){
        $semesterData[$row['Semester']][] = $row;
        $overallCredits += $row['Credits'];
        $overallGradePoints += gradePoints($row['Grade'])*$row['Credits'];
        $overallMarks += intval($row['Internals']);
        $overallMaxMarks += 100;
    }

    $overallCGPA = $overallCredits ? $overallGradePoints/$overallCredits : 0;
    $overallPercentage = $overallMaxMarks ? ($overallMarks/$overallMaxMarks)*100 : 0;

    ob_start();
    ?>
    <!-- Export PDF Button -->
    <button id="exportPDF" class="btn btn-primary mb-3">📄 Export as PDF</button>

    <!-- Results Container -->
    <div id="resultContainer">

    <!-- Overall Academic Summary -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex align-items-center">
           
            <div class="flex-grow-1">
                <h5 class="card-title text-success">📊 Overall Academic Summary</h5>
                <p><b>Student Name:</b> <?php echo htmlentities($studentName); ?> | 
                   <b>Reg No:</b> <?php echo htmlentities($regno); ?></p>
                <div class="row mb-2">
                    <div class="col-md-3"><b>Total Credits:</b> <?php echo $overallCredits; ?></div>
                    <div class="col-md-3"><b>Overall CGPA:</b> <?php echo number_format($overallCGPA,2); ?></div>
                    <div class="col-md-3"><b>Overall Percentage:</b> <?php echo number_format($overallPercentage,2); ?>%</div>
                </div>
                <div class="progress" style="height:20px;">
                    <div class="progress-bar bg-info" role="progressbar"
                         style="width: <?php echo number_format($overallPercentage,2); ?>%">
                         <?php echo number_format($overallPercentage,2); ?>%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Semester-wise Results -->
    <?php
    foreach($semesterData as $sem => $subjects){
        $semTotalCredits = array_sum(array_column($subjects,'Credits'));
        $semGradePoints = 0;
        $semMarks = 0;
        $semMaxMarks = count($subjects)*100;
        foreach($subjects as $s){
            $semGradePoints += gradePoints($s['Grade'])*$s['Credits'];
            $semMarks += intval($s['Internals']);
        }
        $semCGPA = $semTotalCredits ? $semGradePoints/$semTotalCredits : 0;
        $semPercentage = $semMaxMarks ? ($semMarks/$semMaxMarks)*100 : 0;

        ?>
        <div class="card mb-4 <?php echo semCardColor($semCGPA); ?> shadow-sm p-3">
            <h5 class="mb-3 text-primary">Semester: <?php echo htmlentities($sem); ?></h5>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Internals</th>
                            <th>Credits</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($subjects as $sub): ?>
                        <tr class="<?php echo strtoupper($sub['Grade'])=='F' ? 'table-danger' : ''; ?>">
                            <td><?php echo htmlentities($sub['Subject']); ?></td>
                            <td><?php echo htmlentities($sub['SubjectCode']); ?></td>
                            <td><?php echo htmlentities($sub['Internals']); ?></td>
                            <td><?php echo htmlentities($sub['Credits']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo gradeBadge($sub['Grade']); ?>">
                                    <?php echo htmlentities($sub['Grade']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <p><b>Semester CGPA:</b> <?php echo number_format($semCGPA,2); ?></p>
            <div class="progress mb-3" style="height:20px;">
                <div class="progress-bar 
                            <?php echo $semPercentage>=75 ? 'bg-success' : ($semPercentage>=60 ? 'bg-info' : ($semPercentage>=50 ? 'bg-warning' : 'bg-danger')); ?>" 
                     role="progressbar" 
                     style="width: <?php echo number_format($semPercentage,2); ?>%">
                     <?php echo number_format($semPercentage,2); ?>%
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div> <!-- End of resultContainer -->

    <!-- Include html2pdf.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
    document.getElementById("exportPDF").addEventListener("click", () => {
        const element = document.getElementById("resultContainer");
        const opt = {
            margin:       0.5,
            filename:     'Sir C R Reddy College of Engineering Student_Result.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };
        html2pdf().set(opt).from(element).save();
    });
    </script>
    <?php

    $html = ob_get_clean();
    $response = ['status'=>'success','html'=>$html];
}

echo json_encode($response);
