<?php
session_start();
error_reporting(0);
include('includes/config.php');
if(strlen($_SESSION['alogin'])=="") {
    header("Location: index.php"); 
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin | Dashboard</title>

<!-- CSS Files -->
<link rel="stylesheet" href="css/bootstrap.min.css" media="screen" >
<link rel="stylesheet" href="css/font-awesome.min.css" media="screen" >
<link rel="stylesheet" href="css/animate-css/animate.min.css" media="screen" >
<link rel="stylesheet" href="css/lobipanel/lobipanel.min.css" media="screen" >
<link rel="stylesheet" href="css/toastr/toastr.min.css" media="screen" >
<link rel="stylesheet" href="css/icheck/skins/line/blue.css" >
<link rel="stylesheet" href="css/icheck/skins/line/red.css" >
<link rel="stylesheet" href="css/icheck/skins/line/green.css" >
<link rel="stylesheet" href="css/main.css" media="screen" >
<script src="js/modernizr/modernizr.min.js"></script>

<style>
/* Optional: make chart full width */
#dashboardChart {
    width: 100%;
    height: 400px;
}
</style>

</head>
<body class="top-navbar-fixed">
<div class="main-wrapper">

    <!-- Topbar -->
    <?php include('includes/topbar.php');?>

    <div class="content-wrapper">
        <div class="content-container">

            <!-- Leftbar -->
            <?php include('includes/leftbar.php');?>  

            <!-- Main Page Content -->
            <div class="main-page">
                <div class="container-fluid">
                    <div class="row page-title-div">
                        <div class="col-sm-6">
                            <h2 class="title">Dashboard</h2>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Stats -->
                <section class="section">
                    <div class="container-fluid">
                        <div class="row">
                            <?php
                            $sql1 ="SELECT RegNo FROM tblstudents";
                            $totalstudents = $dbh->query($sql1)->rowCount();

                            $sql2 ="SELECT id FROM tblsubjects";
                            $totalsubjects = $dbh->query($sql2)->rowCount();

                            $sql3 ="SELECT id FROM tblclasses";
                            $totalclasses = $dbh->query($sql3)->rowCount();

                            $sql4 ="SELECT DISTINCT regno FROM tblresult";
                            $totalresults = $dbh->query($sql4)->rowCount();
                            ?>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <a class="dashboard-stat bg-primary" href="manage-students.php">
                                    <span class="number counter"><?php echo htmlentities($totalstudents);?></span>
                                    <span class="name">Registered Students</span>
                                    <span class="bg-icon"><i class="fa fa-users"></i></span>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <a class="dashboard-stat bg-danger" href="manage-subjects.php">
                                    <span class="number counter"><?php echo htmlentities($totalsubjects);?></span>
                                    <span class="name">Subjects Listed</span>
                                    <span class="bg-icon"><i class="fa fa-book"></i></span>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <a class="dashboard-stat bg-warning" href="manage-classes.php">
                                    <span class="number counter"><?php echo htmlentities($totalclasses);?></span>
                                    <span class="name">Total Classes</span>
                                    <span class="bg-icon"><i class="fa fa-building"></i></span>
                                </a>
                            </div>

                            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12">
                                <a class="dashboard-stat bg-success" href="manage-results.php">
                                    <span class="number counter"><?php echo htmlentities($totalresults);?></span>
                                    <span class="name">Results Declared</span>
                                    <span class="bg-icon"><i class="fa fa-file-text"></i></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="section" style="margin-top:20px;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-bolt"></i> Quick Actions</div>
                                    <div class="panel-body">
                                        <a href="manage-students.php" class="btn btn-primary btn-sm"><i class="fa fa-users"></i> Manage Students</a>
                                        <a href="manage-subjects.php" class="btn btn-danger btn-sm"><i class="fa fa-book"></i> Manage Subjects</a>
                                        <a href="manage-classes.php" class="btn btn-warning btn-sm"><i class="fa fa-building"></i> Manage Classes</a>
                                        <a href="manage-results.php" class="btn btn-success btn-sm"><i class="fa fa-file-text"></i> Manage Results</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Pie Chart -->
                        <div class="row" style="margin-top:20px;">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"><i class="fa fa-pie-chart"></i> Analytics</div>
                                    <div class="panel-body">
                                        <div id="dashboardChart"></div>
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

<!-- JS Files -->
<script src="js/jquery/jquery-2.2.4.min.js"></script>
<script src="js/jquery-ui/jquery-ui.min.js"></script>
<script src="js/bootstrap/bootstrap.min.js"></script>
<script src="js/pace/pace.min.js"></script>
<script src="js/lobipanel/lobipanel.min.js"></script>
<script src="js/iscroll/iscroll.js"></script>
<script src="js/prism/prism.js"></script>
<script src="js/waypoint/waypoints.min.js"></script>
<script src="js/counterUp/jquery.counterup.min.js"></script>
<script src="js/amcharts/amcharts.js"></script>
<script src="js/amcharts/pie.js"></script>
<script src="js/amcharts/plugins/export/export.min.js"></script>
<link rel="stylesheet" href="js/amcharts/plugins/export/export.css" type="text/css" media="all" />
<script src="js/amcharts/themes/light.js"></script>
<script src="js/toastr/toastr.min.js"></script>
<script src="js/icheck/icheck.min.js"></script>
<script src="js/main.js"></script>

<script>
$(function(){
    // Counter animation
    $('.counter').counterUp({ delay: 10, time: 1000 });

    // 3D Donut Pie Chart
    AmCharts.makeChart("dashboardChart", {
        "type": "pie",
        "theme": "light",
        "innerRadius": "40%",
        "depth3D": 15,
        "angle": 30,
        "balloonText": "<b>[[title]]: [[value]]</b>",
        "labelText": "[[title]]: [[value]]",
        "legend": {
            "position": "right",
            "marginRight": 0,
            "autoMargins": false
        },
        "export": {
            "enabled": true
        },
        "dataProvider": [
            { "title": "Students", "value": <?php echo $totalstudents;?> },
            { "title": "Subjects", "value": <?php echo $totalsubjects;?> },
            { "title": "Classes", "value": <?php echo $totalclasses;?> },
            { "title": "Results", "value": <?php echo $totalresults;?> }
        ],
        "valueField": "value",
        "titleField": "title"
    });

    toastr.success("Welcome to Student Result Management System!");
});
</script>

</body>
</html>
