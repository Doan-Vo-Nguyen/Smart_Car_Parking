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
        <title>VPMS - Booking by time</title>
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
        <script>
            // JavaScript function to toggle visibility of the forms
            function toggleForm(type) {
                if (type === 'registered') {
                    document.getElementById('registeredForm').style.display = 'block';
                    document.getElementById('unregisteredForm').style.display = 'none';
                } else {
                    document.getElementById('unregisteredForm').style.display = 'block';
                    document.getElementById('registeredForm').style.display = 'none';
                }
            }
        </script>
    </head>

    <body>
        <?php include_once('includes/sidebar.php'); ?>
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
                                    <li class="active">Booking by time</li>
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
                                <!-- Buttons to toggle forms -->
                                <button type="button" class="btn btn-primary" onclick="toggleForm('registered')">The person registered</button>
                                <button type="button" class="btn btn-secondary" onclick="toggleForm('unregistered')">The person unregistered</button>

                                <!-- Registered Form -->
                                <form id="registeredForm" action="" method="post" style="display:none;">
                                    <div class="form-group">
                                        <label for="registrationNumber" class="form-control-label">Registration Number (As format XXXX-XXXXX)</label>
                                        <select name="registrationNumber" id="registrationNumber" class="form-control" required>
                                            <option value="">Select Registration Number</option>
                                            <?php
                                            $userId = $_SESSION['vpmsuid'];
                                            $query = mysqli_query($con, "SELECT * FROM tblvehicle as tbve
                                            JOIN tblregusers as tbuser ON tbve.OwnerID = tbuser.ID  WHERE tbve.OwnerID = '$userId'");
                                            while ($row = mysqli_fetch_array($query)) {
                                            ?>
                                                <option value="<?php echo $row['RegistrationNumber']; ?>"><?php echo $row['RegistrationNumber']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateSchedule" class="form-control-label">Date Schedule</label>
                                        <input type="date" name="dateSchedule" id="dateSchedule" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateOut" class="form-control-label">Date Out</label>
                                        <input type="date" name="dateOut" id="dateOut" class="form-control" required>
                                    </div>
                                    <button type="submit" name="submitRegistered" class="btn btn-primary">Submit</button>
                                </form>


                                <!-- Unregistered Form -->
                                <form id="unregisteredForm" action="" method="post" style="display:none;">
                                    <div class="form-group">
                                        <label for="ownerNameUnregistered" class="form-control-label">Owner Name</label>
                                        <input type="text" name="ownerNameUnregistered" id="ownerNameUnregistered" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="vehicleCategoryUnregistered" class="form-control-label">Vehicle Category</label>
                                        <select name="vehicleCategoryUnregistered" id="vehicleCategoryUnregistered" class="form-control" required>
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
                                        <label for="cccdUnregistered" class="form-control-label">CCCD (Citizen ID)</label>
                                        <input type="text" name="cccdUnregistered" id="cccdUnregistered" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="phoneNumberUnregistered" class="form-control-label">Phone Number</label>
                                        <input type="text" name="phoneNumberUnregistered" id="phoneNumberUnregistered" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="registrationNumberUnregistered" class="form-control-label">Registration Number</label>
                                        <input type="text" name="registrationNumberUnregistered" id="registrationNumberUnregistered" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateScheduleUnregistered" class="form-control-label">Date Schedule</label>
                                        <input type="date" name="dateScheduleUnregistered" id="dateScheduleUnregistered" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dateOutUnregistered" class="form-control-label">Date Out</label>
                                        <input type="date" name="dateOutUnregistered" id="dateOutUnregistered" class="form-control" required>
                                    </div>
                                    <button type="submit" name="submitUnregistered" class="btn btn-primary">Submit</button>
                                </form>

                                <?php
                                // Handle form submissions
                                if (isset($_POST['submitRegistered']) || isset($_POST['submitUnregistered'])) {
                                    $registrationNumber = $_POST['registrationNumber'];
                                    $dateSchedule = $_POST['dateSchedule'];
                                    $dateOut = $_POST['dateOut'];
                                    $status = 'Booked'; // Default status

                                    // Unregistered
                                    $ownerName = $_POST['ownerNameUnregistered'];
                                    $cccd = $_POST['cccdUnregistered'];
                                    $vehicleCategory = $_POST['vehicleCategoryUnregistered'];
                                    $registrationNumberUnregistered = $_POST["registrationNumberUnregistered"];
                                    $phoneUnregistered = $_POST["phoneNumberUnregistered"];
                                    $dateScheduleUnregistered = $_POST["dateScheduleUnregistered"];
                                    $dateOutUnregistered = $_POST["dateOutUnregistered"];


                                    // Take the ID of the Categoty if user have sign up
                                    $queryCatID =  mysqli_query($con, "SELECT tbcat.ID, tbsche.RegistrationNumber FROM tblcategory as tbcat
                                    JOIN tblvehicle as tbve
                                    ON tbcat.ID = tbve.CategoryID
                                    JOIN tblschedule as tbsche ON tbsche.RegistrationNumber = tbve.RegistrationNumber
                                    WHERE tbsche.RegistrationNumber = '$registrationNumber'");
                                    $resultCatID = mysqli_fetch_array($queryCatID);
                                    $catID = $resultCatID['ID'];

                                    // Take the ID of the Category if user have not sign up
                                    $queryCatIDUnregistered =  mysqli_query($con, "SELECT ID FROM tblcategory WHERE VehicleCat = '$vehicleCategory'");
                                    $resultCatIDUnregistered = mysqli_fetch_array($queryCatIDUnregistered);
                                    $catIDUnregistered = $resultCatIDUnregistered['ID'];

                                    // Check if the user exists in the system
                                    $checkUserQuery = mysqli_query($con, "SELECT tblregusers.ID FROM tblregusers JOIN tblvehicle ON tblregusers.ID = tblvehicle.OwnerID WHERE tblvehicle.RegistrationNumber = '$registrationNumber'");
                                    $userResult = mysqli_fetch_array($checkUserQuery);
                                    echo $userResult;
                                    if ($userResult == null) {
                                        // User does not exist
                                        $queryInsertUnregistered = "INSERT INTO tblschedule_unregistered (OwnerName, CCCD, PhoneNumber, VehicleCategory, RegistrationNumber, DateSchedule, ExpectDateOut, Status, ActionBy)
                                                                VALUES ('$ownerName', '$cccd', '$phoneUnregistered', '$catIDUnregistered', '$registrationNumberUnregistered', '$dateScheduleUnregistered', '$dateOutUnregistered', '$status', '$userId')";
                                        if (mysqli_query($con, $queryInsertUnregistered)) {
                                            echo "<script>alert('Booking schedule created for unregistered user.');</script>";
                                            echo "<script>window.location.href = 'dashboard.php';</script>";
                                        } else {
                                            echo "<script>alert('Something went wrong. Please try again.');</script>";
                                        }
                                    } else {
                                        // Insert into tblschedule
                                        $queryInsert = "INSERT INTO tblschedule (OwnerID, CategoryID, RegistrationNumber, DateSchedule, ExpectDateOut, Status)
                                                    VALUES ('$userId', '$catID', '$registrationNumber', '$dateSchedule', '$dateOut', '$status')";
                                        if (mysqli_query($con, $queryInsert)) {
                                            echo "<script>alert('Booking schedule created successfully.');</script>";
                                            echo "<script>window.location.href = 'dashboard.php';</script>";
                                        } else {
                                            echo "<script>alert('Something went wrong. Please try again.');</script>";
                                        }
                                        
                                    }
                                }

                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once('includes/footer.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
        <script src="../admin/assets/js/main.js"></script>
    </body>

    </html>
<?php } ?>