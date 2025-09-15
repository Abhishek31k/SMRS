<?php
include('includes/config.php');

if(isset($_POST['batch']) && isset($_POST['semester'])){
    $batch = $_POST['batch'];
    $semester = $_POST['semester'];
    list($start,$end) = explode("-",$batch);

    $sql="SELECT id, Branch FROM tblclasses 
          WHERE BatchStart=:start AND BatchEnd=:end AND Semester=:semester
          GROUP BY Branch
          ORDER BY Branch";
    $query=$dbh->prepare($sql);
    $query->bindParam(':start',$start,PDO::PARAM_INT);
    $query->bindParam(':end',$end,PDO::PARAM_INT);
    $query->bindParam(':semester',$semester,PDO::PARAM_STR);
    $query->execute();
    $results=$query->fetchAll(PDO::FETCH_OBJ);

    echo "<option value=''>Select Branch</option>";
    foreach($results as $row){
        echo "<option value='".$row->id."'>".$row->Branch."</option>";
    }
}
?>
