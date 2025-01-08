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
                                        <label for="weekNumber" class="form-control-label">Week Number</label>
                                        <input type="date" name="week" id="week" class="form-control" required onchange="calculateNextDay()">
                                    </div>

                                    <div class="form-group" id="nextWeekContainer" style="display: none;">
                                        <label for="nextWeek" class="form-control-label">Next Week:</label>
                                        <span id="nextWeek" class="form-control"></span>
                                    </div>

                                    <!-- Hidden Input for Next Week -->
                                    <input type="hidden" name="nextWeek" id="nextWeekInput">

                                    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                                </form>

                                <?php
                                if (isset($_POST['submit'])) {
                                    $owner = $_SESSION['vpmsuid'];
                                    $registrationNumber = $_POST['registrationNumber'];
                                    $weekNumber = $_POST['week'];
                                    $dateOut = $_POST['nextWeek'];
                                    $status = 'Booked'; // Default status
                                    $note = 'Week';

                                    // Take the ID of the Categoty if user have sign up
                                    $queryCatID =  mysqli_query($con, "SELECT tbcat.ID, tbsche.RegistrationNumber FROM tblcategory as tbcat
                                JOIN tblvehicle as tbve
                                ON tbcat.ID = tbve.CategoryID
                                JOIN tblschedule as tbsche ON tbsche.RegistrationNumber = tbve.RegistrationNumber
                                WHERE tbsche.RegistrationNumber = '$registrationNumber'");
                                    $resultCatID = mysqli_fetch_array($queryCatID);
                                    $catID = $resultCatID['ID'];

                                    if ($owner) {
                                        // Insert the new registration week into tblregister_week
                                        $queryInsert = "INSERT INTO tblschedule (OwnerID, CategoryID, RegistrationNumber, DateSchedule, ExpectDateOut, Status, Note)
                                    VALUES ('$owner', '$catID', '$registrationNumber', '$weekNumber', '$dateOut', '$status', '$note')";

                                        $insertResult = mysqli_query($con, $queryInsert);

                                        if ($insertResult) {
                                            echo "<script>alert('Registration for the week has been successfully created.');</script>";
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

                    // Format the new date to 'yyyy-mm-dd' for compatibility with databases
                    var nextDay = date.toISOString().split('T')[0];

                    // Show the next day and display it in the span
                    document.getElementById('nextWeek').innerText = nextDay;
                    document.getElementById('nextWeekContainer').style.display = 'block';

                    // Set the hidden input's value
                    document.getElementById('nextWeekInput').value = nextDay;
                }
            }
        </script>

    </body>

    </html>

<?php } ?>