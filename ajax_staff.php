<?php
session_start();
include('includes/config.php');

if(!isset($_SESSION['StaffId'])){
    echo "Unauthorized access.";
    exit;
}

$StaffId = $_SESSION['StaffId'];

// =====================
// Fetch Assigned Subjects
// =====================
if(isset($_POST['action']) && $_POST['action'] === 'get_subjects') {
    $sql = "SELECT s.SubjectName, c.Branch, c.Semester 
            FROM tblsubjectcombination sc
            JOIN tblsubjects s ON sc.SubjectId = s.id
            JOIN tblclasses c ON sc.ClassId = c.id
            WHERE sc.StaffId = :StaffId
            ORDER BY c.Semester, c.Branch, s.SubjectName";
    $query = $dbh->prepare($sql);
    $query->bindParam(':StaffId', $StaffId, PDO::PARAM_INT);
    $query->execute();
    $subjects = $query->fetchAll(PDO::FETCH_OBJ);

    if($query->rowCount() > 0){
        echo '<table class="table table-bordered">';
        echo '<thead class="table-dark"><tr><th>Subject</th><th>Branch</th><th>Semester</th></tr></thead><tbody>';
        foreach($subjects as $sub){
            echo '<tr>';
            echo '<td>'.htmlentities($sub->SubjectName).'</td>';
            echo '<td>'.htmlentities($sub->Branch).'</td>';
            echo '<td>'.htmlentities($sub->Semester).'</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>No subjects assigned.</p>';
    }
    exit;
}

// =====================
// Fetch Students Under Staff
// =====================
if(isset($_POST['action']) && $_POST['action'] === 'get_students') {
    // Get classes assigned to staff
    $sqlClasses = "SELECT DISTINCT ClassId FROM tblsubjectcombination WHERE StaffId=:StaffId";
    $queryClasses = $dbh->prepare($sqlClasses);
    $queryClasses->bindParam(':StaffId', $StaffId, PDO::PARAM_INT);
    $queryClasses->execute();
    $classes = $queryClasses->fetchAll(PDO::FETCH_COLUMN);

    if(count($classes) > 0){
        $in = str_repeat('?,', count($classes)-1).'?';
        $sqlStudents = "SELECT s.RegNo, s.StudentName, c.Branch, c.Semester 
                        FROM tblstudents s
                        JOIN tblclasses c ON s.ClassId = c.id
                        WHERE s.ClassId IN ($in)
                        ORDER BY c.Semester, c.Branch, s.StudentName";
        $queryStudents = $dbh->prepare($sqlStudents);
        $queryStudents->execute($classes);
        $students = $queryStudents->fetchAll(PDO::FETCH_OBJ);

        if(count($students) > 0){
            echo '<table class="table table-bordered">';
            echo '<thead class="table-dark"><tr><th>RegNo</th><th>Name</th><th>Branch</th><th>Semester</th></tr></thead><tbody>';
            foreach($students as $stu){
                echo '<tr>';
                echo '<td>'.htmlentities($stu->RegNo).'</td>';
                echo '<td>'.htmlentities($stu->StudentName).'</td>';
                echo '<td>'.htmlentities($stu->Branch).'</td>';
                echo '<td>'.htmlentities($stu->Semester).'</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>No students found in your assigned classes.</p>';
        }
    } else {
        echo '<p>No classes assigned to you.</p>';
    }
    exit;
}

// =====================
// Fetch Notices
// =====================
if(isset($_POST['action']) && $_POST['action'] === 'get_notices') {
    $sql = "SELECT * FROM tblnotice ORDER BY postingDate DESC";
    $query = $dbh->prepare($sql);
    $query->execute();
    $notices = $query->fetchAll(PDO::FETCH_OBJ);

    if(count($notices) > 0){
        echo '<ul class="list-group">';
        foreach($notices as $n){
            echo '<li class="list-group-item"><strong>'.htmlentities($n->noticeTitle).'</strong><br>'
                .nl2br(htmlentities($n->noticeDetails))
                .'<br><small>'.htmlentities($n->postingDate).'</small></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No notices found.</p>';
    }
    exit;
}

echo "Invalid action.";
