<?php
include('includes/config.php');

if(isset($_POST['batch'])){
    list($start,$end) = explode("-", $_POST['batch']);

    $sql = "SELECT DISTINCT Branch 
            FROM tblclasses 
            WHERE BatchStart = :start AND BatchEnd = :end
            ORDER BY Branch";
    $query = $dbh->prepare($sql);
    $query->bindParam(':start', $start, PDO::PARAM_INT);
    $query->bindParam(':end', $end, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    echo "<option value=''>Select Branch</option>";
    foreach($results as $row){
        echo "<option value='".htmlentities($row->Branch)."'>".htmlentities($row->Branch)."</option>";
    }
}
?>
