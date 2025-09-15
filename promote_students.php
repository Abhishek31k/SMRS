<?php
session_start();

error_reporting(0);
include('includes/config.php');
// ---- 1. Get Branches ----
if (isset($_GET['getBranches']) && isset($_GET['batch'])) {
    $batch = explode("-", $_GET['batch']);
    $bs = $batch[0];
    $be = $batch[1];

    $sql = "SELECT DISTINCT Branch FROM tblclasses WHERE BatchStart=:bs AND BatchEnd=:be ORDER BY Branch";
    $query = $dbh->prepare($sql);
    $query->bindParam(':bs',$bs,PDO::PARAM_INT);
    $query->bindParam(':be',$be,PDO::PARAM_INT);
    $query->execute();
    $branches = $query->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode($branches);
    exit;
}

// ---- 2. Get Promotion Info (from → to semester) ----
if (isset($_GET['getPromotionInfo']) && isset($_GET['batch']) && isset($_GET['branch'])) {
    $batch = explode("-", $_GET['batch']);
    $bs = $batch[0];
    $be = $batch[1];
    $branch = $_GET['branch'];

    $sql = "SELECT Semester FROM tblclasses WHERE BatchStart=:bs AND BatchEnd=:be";
    if ($branch != "all") $sql .= " AND Branch=:branch";
    $sql .= " ORDER BY Semester";

    $q = $dbh->prepare($sql);
    $q->bindParam(':bs',$bs,PDO::PARAM_INT);
    $q->bindParam(':be',$be,PDO::PARAM_INT);
    if ($branch != "all") $q->bindParam(':branch',$branch,PDO::PARAM_STR);
    $q->execute();
    $sems = $q->fetchAll(PDO::FETCH_COLUMN);

    $order = ["1-1","1-2","2-1","2-2","3-1","3-2","4-1","4-2"];
    $from = null; $to = null;

    // find the lowest semester that exists for this batch (i.e., current one)
    foreach ($order as $sem) {
        if (in_array($sem, $sems)) {
            $from = $sem;
            $idx = array_search($sem, $order);
            if ($idx !== false && isset($order[$idx+1])) {
                $to = $order[$idx+1];
            }
            break;
        }
    }




    echo json_encode(["from"=>$from,"to"=>$to]);
    exit;
}

if(strlen($_SESSION['alogin'])=="") {   
    header("Location: index.php"); 
    exit;
} else {
    $msg = "";
    $error = "";

    if (isset($_POST['promote'])) {
    $batch = $_POST['batch'];
    $branch = $_POST['branch'];

    // Batch range split
    list($bs, $be) = explode('-', $batch);

    // Get all classes in this batch (and branch if not "all")
    $sql = "SELECT * FROM tblclasses WHERE BatchStart=:bs AND BatchEnd=:be";
    if ($branch != "all") {
        $sql .= " AND Branch=:branch";
    }
    $sql .= " ORDER BY Semester";

    $q = $dbh->prepare($sql);
    $q->bindParam(':bs', $bs, PDO::PARAM_INT);
    $q->bindParam(':be', $be, PDO::PARAM_INT);
    if ($branch != "all") $q->bindParam(':branch', $branch, PDO::PARAM_STR);
    $q->execute();
    $classes = $q->fetchAll(PDO::FETCH_OBJ);

    if ($classes) {
    $order = ["1-1","1-2","2-1","2-2","3-1","3-2","4-1","4-2"];

    // Pick the current class (highest semester for this batch)
    foreach ($classes as $current) {
    $idx = array_search($current->Semester, $order);

    if ($idx !== false && isset($order[$idx+1])) {
        $nextSem = $order[$idx+1];

        // Check if next semester class exists
        $sqlNext = "SELECT id FROM tblclasses
                    WHERE BatchStart=:bs AND BatchEnd=:be
                      AND Branch=:branch AND Section=:section
                      AND Semester=:sem LIMIT 1";
        $q2 = $dbh->prepare($sqlNext);
        $q2->bindParam(':bs',$bs,PDO::PARAM_INT);
        $q2->bindParam(':be',$be,PDO::PARAM_INT);
        $q2->bindParam(':branch',$current->Branch,PDO::PARAM_STR);
        $q2->bindParam(':section',$current->Section,PDO::PARAM_STR);
        $q2->bindParam(':sem',$nextSem,PDO::PARAM_STR);
        $q2->execute();
        $target = $q2->fetch(PDO::FETCH_OBJ);

        if (!$target) {
            // create new class if missing
            $sqlIns = "INSERT INTO tblclasses(Branch,Semester,Section,Regulation,BatchStart,BatchEnd)
                       VALUES(:branch,:sem,:section,:regulation,:bs,:be)";
            $ins = $dbh->prepare($sqlIns);
            $ins->bindParam(':branch',$current->Branch,PDO::PARAM_STR);
            $ins->bindParam(':sem',$nextSem,PDO::PARAM_STR);
            $ins->bindParam(':section',$current->Section,PDO::PARAM_STR);
            $ins->bindParam(':regulation',$current->Regulation,PDO::PARAM_STR);
            $ins->bindParam(':bs',$bs,PDO::PARAM_INT);
            $ins->bindParam(':be',$be,PDO::PARAM_INT);
            $ins->execute();
            $targetId = $dbh->lastInsertId();
        } else {
            $targetId = $target->id;
        }

        // Promote students
        $upd = $dbh->prepare("UPDATE tblstudents SET ClassId=:toId WHERE ClassId=:fromId");
        $upd->bindParam(':toId',$targetId,PDO::PARAM_INT);
        $upd->bindParam(':fromId',$current->id,PDO::PARAM_INT);
        $upd->execute();
    }
}


}


}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SMS Admin | Promote Batch</title>
    <link rel="stylesheet" href="css/bootstrap.css" media="screen" >
    <link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
    <link rel="stylesheet" href="css/main.css" media="screen" >
    <script src="js/modernizr/modernizr.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function(){
    const batchSelect = document.getElementById("batch");
    const branchBox   = document.getElementById("branch-box");
    const branchSelect= document.getElementById("branch");
    const promoMsg    = document.getElementById("promo-info");
    const promoteBtn  = document.getElementById("promote-btn");

    batchSelect.addEventListener("change", function(){
        let batch = this.value;
        if(batch){
            fetch(window.location.href + "?getBranches=1&batch=" + batch)
            .then(res => res.json())
            .then(data => {
                branchSelect.innerHTML = '<option value="all">All Branches</option>';
                data.forEach(b => {
                    branchSelect.innerHTML += `<option value="${b}">${b}</option>`;
                });
                branchBox.style.display = "block";

                // default to "All Branches"
                branchSelect.value = "all";
                branchSelect.dispatchEvent(new Event("change"));
            });
        } else {
            branchBox.style.display = "none";
            promoMsg.style.display = "none";
            promoteBtn.style.display = "none";
        }
    });

    branchSelect.addEventListener("change", function(){
        var batch = batchSelect.value;
        var branch = branchSelect.value;

        document.getElementById("batch-input").value = batch;
        document.getElementById("branch-input").value = branch;

        if(batch && branch){
            promoMsg.style.display = "block";
            promoteBtn.style.display = "inline-block";
            fetch(window.location.href + "?getPromotionInfo=1&batch=" + batch + "&branch=" + branch)
            .then(res => res.json())
            .then(data => {
                promoMsg.innerHTML = "Promoting " + batch + " batch - " 
                    + (branch=="all" ? "All Branches" : branch) 
                    + " students from " + data.from + " → " + data.to;
            });
        } else {
            promoMsg.style.display = "none";
            promoteBtn.style.display = "none";
        }
    });
});

</script>

</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">

    <?php include('includes/topbar.php');?>   
    <div class="content-wrapper">
        <div class="content-container">

            <?php include('includes/leftbar.php');?>                   

            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-md-6">
                            <h2 class="title">Promote Batch</h2>
                        </div>
                    </div>
                </div>

                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h5>Promote Students to Next Semester</h5>
                                    </div>
                                    <div class="panel-body">

            

                                        <form method="post" onsubmit="return confirm('Are you sure you want to promote this batch?');">
                                            <!-- Step 1: Select Batch -->
                                            <div class="form-group has-success">
                                                <label class="control-label">Select Batch</label>
                                                <select id="batch" class="form-control" required>
                                                    <option value="">-- Select Batch --</option>
                                                    <?php 
                                                    $sql = "SELECT DISTINCT BatchStart, BatchEnd FROM tblclasses ORDER BY BatchStart DESC";
                                                    $query = $dbh->prepare($sql);
                                                    $query->execute();
                                                    $batches = $query->fetchAll(PDO::FETCH_OBJ);
                                                    foreach($batches as $b){
                                                        echo "<option value='{$b->BatchStart}-{$b->BatchEnd}'>{$b->BatchStart}-{$b->BatchEnd}</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="batch" id="batch-input">
                                            </div>

                                            <!-- Step 2: Select Branch -->
                                            <div class="form-group has-success" id="branch-box" style="display:none;">
                                                <label class="control-label">Select Branch</label>
                                                <select id="branch" class="form-control"></select>
                                                <input type="hidden" name="branch" id="branch-input">
                                            </div>

                                            <!-- Auto Promotion Info -->
                                            <div id="promo-info" class="alert alert-info" style="display:none; font-weight:bold;"></div>

                                            <button type="submit" name="promote" class="btn btn-primary mt-2" style="display:none;" id="promote-btn">
                                                Promote <i class="fa fa-arrow-up"></i>
                                            </button>
                                        </form>



                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/main.js"></script>
</body>
</html>
<?php } ?>
