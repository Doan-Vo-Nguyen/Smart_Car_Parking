<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {



?>
    <!doctype html>

    <html class="no-js" lang="">

    <head>

        <title>VPMS - Booking</title>


        <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
        <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
        <link rel="stylesheet" href="../admin/assets/css/cs-skin-elastic.css">
        <link rel="stylesheet" href="../admin/assets/css/style.css">

        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    </head>

    <body>
        <!-- Left Panel -->

        <?php include_once('includes/sidebar.php'); ?>

        <!-- Left Panel -->

        <!-- Right Panel -->

        <?php include_once('includes/header.php'); ?>

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
                                    <li class="active">Booking</li>
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
                                <strong class="card-title">Booking Form</strong>
                            </div>
                            <div class="card-body">
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="vehicleCategory" class="form-control-label">Vehicle Category</label>
                                        <select name="vehicleCategory" id="vehicleCategory" class="form-control" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            $query = mysqli_query($con, "SELECT * FROM tblcategory");
                                            while ($row = mysqli_fetch_array($query)) {
                                            ?>
                                                <option value="<?php echo $row['VehicleCat']; ?>"><?php echo $row['VehicleCat']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="registrationNumber" class="form-control-label">Registration Number</label>
                                        <input type="text" name="registrationNumber" id="registrationNumber" class="form-control" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="dateSchedule" class="form-control-label">Date Schedule</label>
                                        <input type="date" name="dateSchedule" id="dateSchedule" class="form-control" required>
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                </form>
                                <?php
                                if (isset($_POST['submit'])) {
                                    $uid = $_SESSION['vpmsuid'];
                                    $vehicleCategory = $_POST['vehicleCategory'];
                                    $registrationNumber = $_POST['registrationNumber'];
                                    $dateSchedule = $_POST['dateSchedule'];
                                    $status = 'Done'; // Default status

                                    $queryCatID =  mysqli_query($con, "SELECT ID FROM tblcategory WHERE VehicleCat like '$vehicleCategory'");
                                    $resultCatID = mysqli_fetch_array($queryCatID);
                                    $catID = $resultCatID['ID'];
                                    if ($catID > 0) {
                                        // Insert the new schedule into tblschedule
                                        $queryInsert = "INSERT INTO tblschedule (OwnerID, CategoryID, RegistrationNumber, DateSchedule, Status)
                                         VALUES ('$uid', '$catID', '$registrationNumber', '$dateSchedule', '$status')";

                                        $insertResult = mysqli_query($con, $queryInsert);

                                        if ($insertResult) {
                                            echo "<script>alert('Booking schedule created successfully.');</script>";
                                            echo "<script>window.location.href = 'dashboard.php';</script>";
                                        } else {
                                            echo "<script>alert('Something went wrong. Please try again.');</script>";
                                        }
                                    } else {
                                        echo "<script>alert('Owner not found. Please check the Owner Name.');</script>";
                                    }
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- .animated -->
        </div><!-- .content -->

        <div class="clearfix"></div>

        <?php include_once('includes/footer.php'); ?>

        </div><!-- /#right-panel -->

        <!-- Right Panel -->

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
        <script src="../admin/assets/js/main.js"></script>


    </body>

    </html>
<?php }  ?>