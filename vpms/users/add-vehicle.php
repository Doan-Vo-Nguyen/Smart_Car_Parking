<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {

    if (isset($_POST['submit'])) {
        $uid=$_SESSION['vpmsuid'];
        $catename = $_POST['catename'];
        $vehreno = $_POST['vehreno'];

        $cat_query = mysqli_query($con, "SELECT ID FROM tblcategory WHERE VehicleCat='$catename'");
        $cat_id = mysqli_fetch_assoc($cat_query);
        $ownercontno = $cat_id['ID'];

        $query = mysqli_query($con, "INSERT INTO tblvehicle(OwnerID, RegistrationNumber,CategoryID) VALUE ('$uid','$vehreno','$ownercontno')");
        if ($query) {
            echo "<script>alert('Vehicle has been added');</script>";
            // echo "<script>window.location.href ='manage-incomingvehicle.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again.');</script>";
        }
    }
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <title>VPMS - Add Vehicle</title>
    <link rel="apple-touch-icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="shortcut icon" href="https://i.imgur.com/QRAUqs9.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="../admin/assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>
</head>

<body>
    <?php include_once('includes/sidebar.php'); ?>
    <?php include_once('includes/header.php'); ?>

    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mt-4">
                        <div class="card-header">
                            <strong>Add</strong> Vehicle
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="form-horizontal">
                                <div class="form-group row">
                                    <label for="catename" class="col-md-3 col-form-label">Select Category</label>
                                    <div class="col-md-9">
                                        <select name="catename" id="catename" class="form-control" required>
                                            <option value="">Select Category</option>
                                            <?php
                                            $query = mysqli_query($con, "SELECT * FROM tblcategory");
                                            while ($row = mysqli_fetch_array($query)) {
                                                echo "<option value='" . $row['VehicleCat'] . "'>" . $row['VehicleCat'] . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="vehreno" class="col-md-3 col-form-label">Registration Number</label>
                                    <div class="col-md-9">
                                        <input type="text" id="vehreno" name="vehreno" class="form-control" placeholder="Registration Number" required>
                                    </div>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary" name="submit">Add Vehicle</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="../admin/assets/js/main.js"></script>
</body>

</html>
<?php } ?>