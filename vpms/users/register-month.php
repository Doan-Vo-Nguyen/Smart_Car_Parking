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
    <title>VPMS - Register Month</title>
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
                            <h1>Register Month</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="dashboard.php">Dashboard</a></li>
                                <li class="active">Register Month</li>
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
                            <strong class="card-title">Register Month Form</strong>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="registrationNumber" class="form-control-label">Registration Number</label>
                                    <select name="registrationNumber" id="registrationNumber" class="form-control" required>
                                        <option value="">Select Registration Number</option>
                                        <?php
                                        $userId = $_SESSION['vpmsuid'];
                                        $query = mysqli_query($con, "SELECT * FROM tblvehicle WHERE OwnerID = '$userId'");
                                        while ($row = mysqli_fetch_array($query)) {
                                        ?>
                                            <option value="<?php echo $row['RegistrationNumber']; ?>"><?php echo $row['RegistrationNumber']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="startDate" class="form-control-label">Start Date</label>
                                    <input type="date" name="startDate" id="startDate" class="form-control" required onchange="calculateEndDate()">
                                </div>

                                <div class="form-group">
                                    <label for="monthsToAdd" class="form-control-label">Additional Months</label>
                                    <select name="monthsToAdd" id="monthsToAdd" class="form-control" required onchange="calculateEndDate()">
                                        <?php
                                        for ($i = 1; $i <= 12; $i++) {
                                            echo "<option value='$i'>$i Month(s)</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group" id="endDateContainer" style="display: none;">
                                    <label for="endDate" class="form-control-label">End Date:</label>
                                    <span id="endDate" class="form-control"></span>
                                </div>

                                <!-- Hidden Input for End Date -->
                                <input type="hidden" name="endDateHidden" id="endDateHidden">

                                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
                            </form>

                            <?php
                            if (isset($_POST['submit'])) {
                                $owner = $_SESSION['vpmsuid'];
                                $registrationNumber = $_POST['registrationNumber'];
                                $startDate = $_POST['startDate'];
                                $endDate = $_POST['endDateHidden'];
                                $status = 'Booked'; // Default status
                                $note = 'Month';

                                // Take the ID of the Category
                                $queryCatID =  mysqli_query($con, "SELECT ID FROM tblcategory WHERE ID IN (SELECT CategoryID FROM tblvehicle WHERE RegistrationNumber = '$registrationNumber')");
                                $resultCatID = mysqli_fetch_array($queryCatID);
                                $catID = $resultCatID['ID'];

                                if ($owner) {
                                    // Insert the new registration month into tblschedule
                                    $queryInsert = "INSERT INTO tblschedule (OwnerID, CategoryID, RegistrationNumber, DateSchedule, ExpectDateOut, Status, Note)
                                    VALUES ('$owner', '$catID', '$registrationNumber', '$startDate', '$endDate', '$status', '$note')";

                                    $insertResult = mysqli_query($con, $queryInsert);

                                    if ($insertResult) {
                                        echo "<script>alert('Registration for the month has been successfully created.');</script>";
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
    function calculateEndDate() {
        var startDate = document.getElementById('startDate').value;
        var monthsToAdd = document.getElementById('monthsToAdd').value;

        if (startDate && monthsToAdd) {
            var date = new Date(startDate);
            // Add the selected number of months to the start date
            date.setMonth(date.getMonth() + parseInt(monthsToAdd));

            // Format the end date to 'yyyy-mm-dd'
            var formattedEndDate = date.toISOString().split('T')[0];

            // Show the end date and display it in the span
            document.getElementById('endDate').innerText = formattedEndDate;
            document.getElementById('endDateContainer').style.display = 'block';

            // Set the hidden input's value
            document.getElementById('endDateHidden').value = formattedEndDate;
        }
    }
</script>

</body>

</html>

<?php } ?>
