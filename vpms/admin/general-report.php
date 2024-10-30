<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsaid']==0)) {
  header('location:logout.php');
  } else{
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <title>VPMS - Reports</title>
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

</head>
<body>
    <!-- Left Panel -->

  <?php include_once('includes/sidebar.php');?>

    <!-- Left Panel -->

    <!-- Right Panel -->

     <?php include_once('includes/header.php');?>

        <div class="breadcrumbs">
            <div class="breadcrumbs-inner">
                <div class="row m-0">
                    <div class="col-sm-4">
                        <div class="page-header float-left">
                            <div class="page-title">
                                <h1>Dashboard</h1>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="page-header float-right">
                            <div class="page-title">
                                <ol class="breadcrumb text-right">
                                    <li><a href="dashboard.php">Dashboard</a></li>
                                    <li><a href="general-report.php">General</a></li>
                                    <li class="active">General Reports</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">General Reports</strong>
                        </div>
                        <div class="card-body">
                            <div class="chart-toggle">
                                <button id="barChartBtn" class="btn btn-primary">Show Vehicle Count</button>
                                <button id="wheelChartBtn" class="btn btn-secondary">Show Financial Report</button>
                            </div>
                            <?php
                            // Fetching vehicle parking data by date
                            $query = "SELECT DATE(InTime) as date, COUNT(*) as vehicle_count FROM tblvehicle GROUP BY DATE(InTime)";
                            $result = mysqli_query($con, $query);

                            $dataPoints = array();
                            while ($row = mysqli_fetch_assoc($result)) {
                                $vehicleDataPoints[] = array("label" => $row['date'], "y" => $row['vehicle_count']);
                            }
                            // Fetching financial data (sum of parking charges) by date
                            $financialQuery = "SELECT DATE(InTime) as date, SUM(ParkingCharge) as total_charge FROM tblvehicle GROUP BY DATE(InTime)";
                            $financialResult = mysqli_query($con, $financialQuery);

                            $financialDataPoints = array();
                            while ($row = mysqli_fetch_assoc($financialResult)) {
                                $financialDataPoints[] = array("label" => $row['date'], "y" => (float)$row['total_charge']);
                            }
                            ?>
                            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div><!-- .animated -->
</div><!-- .content -->

<div class="clearfix"></div>

<?php include_once('includes/footer.php');?>

</div><!-- /#right-panel -->

<!-- Right Panel -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script src="assets/js/main.js"></script>
<script>
    // Prepare the data from PHP
    var vehicleDataPoints = <?php echo json_encode($vehicleDataPoints, JSON_NUMERIC_CHECK); ?>;
    var financialDataPoints = <?php echo json_encode($financialDataPoints, JSON_NUMERIC_CHECK); ?>;
    // Initial chart load (Bar Chart)
    window.onload = function () {
            renderBarChart();
    }
    // Function to render Bar Chart (Vehicle Count)
    function renderBarChart() {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                theme: "light1",
                title:{
                    text: "Number of Vehicles Parked Per Day"
                },
                axisY:{
                    includeZero: true,
                    title: "Number of Vehicles"
                },
                axisX:{
                    title: "Date",
                    labelAngle: -50
                },
                data: [{
                    type: "column",
                    indexLabelFontColor: "#5A5757",
                    indexLabelPlacement: "outside",
                    dataPoints: vehicleDataPoints
                }]
            });
            chart.render();
        }
    // Wheel Finicial loading
    // Function to render Wheel Chart (Financial Report)
    function renderWheelChart() {
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                exportEnabled: true,
                title:{
                    text: "Total Parking Charges Per Day"
                },
                subtitles: [{
                    text: "Currency Used: VietNam Dong (VND)"
                }],
                data: [{
                    type: "pie",
                    showInLegend: "true",
                    legendText: "{label}",
                    indexLabelFontSize: 16,
                    indexLabel: "{label} - #percent%",
                    yValueFormatString: "#,##0 VND",
                    dataPoints: financialDataPoints
                }]

            });
            chart.render();
        }
        // Event listeners for buttons
        document.getElementById('barChartBtn').addEventListener('click', function() {
            renderBarChart();
            // Update button styles
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
            document.getElementById('wheelChartBtn').classList.remove('btn-primary');
            document.getElementById('wheelChartBtn').classList.add('btn-secondary');
        });

        document.getElementById('wheelChartBtn').addEventListener('click', function() {
            renderWheelChart();
            // Update button styles
            this.classList.remove('btn-secondary');
            this.classList.add('btn-primary');
            document.getElementById('barChartBtn').classList.remove('btn-primary');
            document.getElementById('barChartBtn').classList.add('btn-secondary');
        });
    </script>

</body>
</html>
<?php }  ?>