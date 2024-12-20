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
    <title>VPMS - Register Week</title>
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

    <!-- Right Panel -->
    <?php include_once('includes/header.php'); ?>

    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>Book Week</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="dashboard.php">Dashboard</a></li>
                                <li class="active">Book Week</li>
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
                            <strong class="card-title">Register Week Form</strong>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="ownerName" class="form-control-label">Owner Name</label>
                                    <input type="text" name="ownerName" id="ownerName" class="form-control" required>
                                </div>

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
                                    <label for="cccd" class="form-control-label">CCCD (Citizen ID)</label>
                                    <input type="text" name="cccd" id="cccd" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="phoneNumber" class="form-control-label">Phone Number</label>
                                    <input type="text" name="phoneNumber" id="phoneNumber" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label for="registrationNumber" class="form-control-label">Registration Number(As format XXXX-XXXXX)</label>
                                    <input type="text" name="registrationNumber" id="registrationNumber" class="form-control" required>
                                </div>

                                <!-- Week Number as Select Dropdown -->
                                <div class="form-group">
                                    <label for="weekNumber" class="form-control-label">Week Number</label>
                                    <input type="date" name="week" id="week" class="form-control" required onchange="calculateNextDay()">
                                </div>

                                <!-- Display Next Day Under Week Number -->
                                <div class="form-group" id="nextWeekContainer" style="display: none;">
                                    <label for="nextWeek" class="form-control-label">Next Week:</label>
                                    <span id="nextWeek" class="form-control" name="nextWeek"></span>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            </form>

                            <?php
                            if (isset($_POST['submit'])) {
                                $actionBy = $_SESSION['vpmsuid'];
                                $ownerName = $_POST['ownerName'];
                                $cccd = $_POST['cccd'];
                                $phoneNumber = $_POST['phoneNumber'];
                                $registrationNumber = $_POST['registrationNumber'];
                                $weekNumber = $_POST['weekNumber'];
                                $dateOut = $_POST['nextWeek'];
                                $status = 'Booked'; // Default status

                                // Take the ID of the Categoty
                                $queryCatID =  mysqli_query($con, "SELECT ID FROM tblcategory WHERE VehicleCat like '$vehicleCategory'");
                                $resultCatID = mysqli_fetch_array($queryCatID);
                                $catID = $resultCatID['ID'];
                                // Query to get the OwnerID from tblreguser by OwnerName
                                $queryOwner = mysqli_query($con, "SELECT ID FROM tblreguser WHERE FullName='$ownerName'");
                                $resultOwner = mysqli_fetch_array($queryOwner);
                                $ownerID = $resultOwner['ID'];

                                if ($ownerID) {
                                    // Insert the new registration week into tblregister_week
                                    $queryInsert = "INSERT INTO tblschedule (OwnerID, CategoryID, PhoneNumber, CCCD, RegistrationNumber, DateSchedule, ExpectDateOut, Status)
                                    VALUES ('$ownerID', '$catID', '$phoneNumber', '$cccd', '$registrationNumber', '$weekNumber', '$dateOut', '$status')";

                                    $insertResult = mysqli_query($con, $queryInsert);

                                    if ($insertResult) {
                                        echo "<script>alert('Registration for the week has been successfully created.');</script>";
                                        echo "<script>window.location.href = 'dashboard.php';</script>";
                                    } else {
                                        echo "<script>alert('Something went wrong. Please try again.');</script>";
                                    }
                                } else {
                                    // User does not exist
                                    $queryInsertUnregistered = "INSERT INTO tblschedule_unregistered (OwnerName, CCCD, PhoneNumber, VehicleCategory, RegistrationNumber, DateSchedule, ExpectDateOut, Status, ActionBy)
                                                                VALUES ('$ownerName', '$cccd', '$phoneNumber', '$catID', '$registrationNumber', '$dateSchedule', '$dateOut', '$status', '$actionBy')";
                                    if (mysqli_query($con, $queryInsertUnregistered)) {
                                        echo "<script>alert('Booking schedule created for unregistered user.');</script>";
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

<script>
    function calculateNextDay() {
        var selectedDate = document.getElementById('week').value;
        
        if (selectedDate) {
            var date = new Date(selectedDate);
            // Add 7 days to the selected date
            date.setDate(date.getDate() + 7);
            
            // Format the new date to 'mm/dd/yyyy'
            var nextDay = (date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear();
            
            // Show the next day and display it in the span
            document.getElementById('nextWeek').innerText = nextDay;
            document.getElementById('nextWeekContainer').style.display = 'block'; // Make the next day visible
        }
    }
</script>

</body>

</html>

<?php } ?>
