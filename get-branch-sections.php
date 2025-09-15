<?php
include('includes/config.php');

if(isset($_POST['batch']) && isset($_POST['branch'])){
    list($start,$end) = explode("-", $_POST['batch']);
    $branch = $_POST['branch'];

    $sql = "SELECT id, Section, Semester 
            FROM tblclasses 
            WHERE BatchStart=:start AND BatchEnd=:end AND Branch=:branch
            ORDER BY Section";
    $query = $dbh->prepare($sql);
    $query->bindParam(':start', $start, PDO::PARAM_INT);
    $query->bindParam(':end', $end, PDO::PARAM_INT);
    $query->bindParam(':branch', $branch, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    echo "<option value=''>Select Section</option>";
    foreach($results as $row){
        echo "<option value='".$row->id."'>Section ".$row->Section." (Sem ".$row->Semester.")</option>";
    }
}
?>
