<?php
session_start();
include('includes/config.php'); // DB connection

$error = '';
$results = [];
$studentName = '';
$semesterData = [];
$summary = [
    'totalCredits'=>0,
    'totalGradePoints'=>0,
    'totalMarks'=>0,
    'maxMarks'=>0,
    'failedSubjects'=>0
];

// Define all semesters for dropdown
$allSemesters = ['1-1','1-2','2-1','2-2','3-1','3-2','4-1','4-2'];

if(isset($_POST['submit'])){
    $regno = trim($_POST['regno']);
    $selectedSem = $_POST['semester'] ?? 'All';

    if(empty($regno)){
        $error = "Please enter your registration number.";
    } else {
        // Fetch results
        if($selectedSem == 'All'){
            $sql = "SELECT Semester, Subject, SubjectCode, Internals, Grade, Credits 
                    FROM tblresult WHERE RegNo=:regno ORDER BY Semester, id";
        } else {
            $sql = "SELECT Semester, Subject, SubjectCode, Internals, Grade, Credits 
                    FROM tblresult WHERE RegNo=:regno AND Semester=:sem ORDER BY id";
        }

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':regno', $regno, PDO::PARAM_STR);
        if($selectedSem != 'All') $stmt->bindParam(':sem', $selectedSem, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!$results){
            $error = "No results found for registration number: $regno and selected semester.";
        } else {
            // Fetch student name
            $stmt2 = $dbh->prepare("SELECT StudentName FROM tblstudents WHERE RegNo=:regno LIMIT 1");
            $stmt2->bindParam(':regno', $regno, PDO::PARAM_STR);
            $stmt2->execute();
            $studentName = $stmt2->fetchColumn();

            foreach($results as $row){
                $semester = $row['Semester'];
                $semesterData[$semester][] = $row;

                $credits = intval($row['Credits']);
                $summary['totalCredits'] += $credits;
                $summary['totalGradePoints'] += gradePoints($row['Grade']) * $credits;
                $summary['totalMarks'] += intval($row['Internals']);
                $summary['maxMarks'] += $credits * 25;
                if(strtoupper($row['Grade'])=='F') $summary['failedSubjects']++;
            }

            // Store data in session temporarily to display after redirect
            $_SESSION['semesterData'] = $semesterData;
            $_SESSION['summary'] = $summary;
            $_SESSION['studentName'] = $studentName;
            $_SESSION['regno'] = $regno;
            $_SESSION['error'] = $error;

            // Redirect to avoid form resubmission on refresh
            header("Location: find-result.php");
            exit;
        }
    }
}

// Load session data if redirected
if(isset($_SESSION['semesterData'])){
    $semesterData = $_SESSION['semesterData'];
    $summary = $_SESSION['summary'];
    $studentName = $_SESSION['studentName'];
    $regno = $_SESSION['regno'];
    $error = $_SESSION['error'];

    // Clear session data
    unset($_SESSION['semesterData'], $_SESSION['summary'], $_SESSION['studentName'], $_SESSION['regno'], $_SESSION['error']);
}

function gradePoints($grade){
    $grade = strtoupper($grade);
    $points = ['A+'=>10,'A'=>9,'B+'=>8,'B'=>7,'C+'=>6,'C'=>5,'F'=>0];
    return $points[$grade] ?? 0;
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

$overallPercentage = $summary['maxMarks'] ? ($summary['totalMarks']/$summary['maxMarks'])*100 : 0;
$overallCGPA = $summary['totalCredits'] ? ($summary['totalGradePoints']/$summary['totalCredits']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Result</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/result-style.css">
</head>
<body>

<!-- Topbar -->
<div class="topbar">
    <div class="announcement">
        <marquee behavior="scroll" direction="left" scrollamount="5">
          📢 Today: <?php echo date('l, d M Y'); ?> | B.Tech Result Released. Revaluation and recounting dates will be announced soon.
        </marquee>
    </div>
    <div class="social">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-linkedin-in"></i></a>
    </div>
</div>

<div class="container mt-4">
    <h2 class="text-center mb-4">Check Your Result</h2>

    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <!-- Registration & Semester Form -->
    <form method="post" class="row g-2 justify-content-center mb-4">
        <div class="col-md-3">
            <input type="text" name="regno" class="form-control" placeholder="Enter Registration Number" required>
        </div>
        <div class="col-md-3">
            <select name="semester" class="form-select">
                <option value="All">-- All Semesters --</option>
                <?php foreach($allSemesters as $sem): ?>
                    <option value="<?php echo $sem; ?>" <?php if(isset($_POST['semester']) && $_POST['semester']==$sem) echo 'selected'; ?>>
                        <?php echo $sem; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" name="submit" class="btn btn-primary w-100">View Result</button>
        </div>
    </form>

    <?php if($semesterData): ?>
        <!-- Summary -->
        <div class="summary-card">
            <h5>📊 Academic Summary</h5>
            <div class="row justify-content-center">
                <div class="col-md-3 stat">Total Credits: <?php echo $summary['totalCredits']; ?></div>
                <div class="col-md-3 stat">Overall CGPA: <?php echo number_format($overallCGPA,2); ?></div>
                <div class="col-md-3 stat">Percentage: <?php echo number_format($overallPercentage,2); ?>%</div>
                <div class="col-md-3 stat">Failed Subjects: <?php echo $summary['failedSubjects']; ?></div>
            </div>
        </div>

        <!-- Grade Legend -->
        <div class="result-card grade-legend">
            <h5>Grade Legend</h5>
            <div class="d-flex flex-wrap gap-2">
                <span class="badge bg-success grade-badge">A+ : 10</span>
                <span class="badge bg-primary grade-badge">A : 9</span>
                <span class="badge bg-info grade-badge">B+ : 8</span>
                <span class="badge bg-secondary grade-badge">B : 7</span>
                <span class="badge bg-warning grade-badge text-dark">C+ : 6</span>
                <span class="badge bg-dark grade-badge">C : 5</span>
                <span class="badge bg-danger grade-badge">F : 0</span>
            </div>
        </div>

        <!-- Semester Results -->
        <?php foreach($semesterData as $sem => $subjects): ?>
            <?php
                $semTotalMarks = array_sum(array_column($subjects,'Internals'));
                $semTotalCredits = array_sum(array_column($subjects,'Credits'));
                $semGradePoints = 0;
                foreach($subjects as $s) $semGradePoints += gradePoints($s['Grade'])*$s['Credits'];
                $semCGPA = $semTotalCredits ? $semGradePoints/$semTotalCredits : 0;
                $semMaxMarks = $semTotalCredits*25;
                $semPercentage = $semMaxMarks ? ($semTotalMarks/$semMaxMarks)*100 : 0;
            ?>
            <div class="result-card printable">
                <div class="college-header">
                    <img src="images/crrengglogo.png" alt="College Logo">
                    <div class="college-info">
                        <h4>Sir C.R. Reddy College of Engineering | (Autonomous)</h4>
                        <p>Approved by AICTE | Affiliated to JNTUK | Accredited by NBA & NAAC (A Grade)</p>
                    </div>
                </div>

                <div class="print-student-info">
                    <p><b>Student Name:</b> <?php echo htmlentities($studentName ?: $regno); ?></p>
                    <p><b>Registration Number:</b> <?php echo htmlentities($regno); ?></p>
                </div>

                <h5>Semester: <?php echo htmlentities($sem); ?></h5>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                            <tr class="<?php echo strtoupper($sub['Grade'])=='F' ? 'failed' : ''; ?>">
                                <td><?php echo htmlentities($sub['Subject']); ?></td>
                                <td><?php echo htmlentities($sub['SubjectCode']); ?></td>
                                <td><?php echo htmlentities($sub['Internals']); ?></td>
                                <td><?php echo htmlentities($sub['Credits']); ?></td>
                                <td><span class="badge bg-<?php echo gradeBadge($sub['Grade']); ?> grade-badge"><?php echo htmlentities($sub['Grade']); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="print-summary">
                    <p><b>Semester CGPA:</b> <?php echo number_format($semCGPA,2); ?> | 
                       <b>Percentage:</b> <?php echo number_format($semPercentage,2); ?>%</p>
                </div>

                <button class="btn btn-outline-secondary print-btn" onclick="printCard(this)">
                    <i class="fa fa-print"></i> Print This Semester
                </button>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date('Y'); ?> Sir C.R. Reddy College of Engineering. All Rights Reserved.</p>
    <p>
        <a href="#">Privacy Policy</a> | <a href="#">Terms & Conditions</a>
    </p>
</footer>

<script>
function printCard(btn){
    var card = btn.closest('.printable');
    var printContents = card.outerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
