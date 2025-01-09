<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {
    if (isset($_GET['editid'])) {
        $editid = $_GET['editid'];
        $query = mysqli_query($con, "SELECT * FROM tblvehicle WHERE ID='$editid'");
        $row = mysqli_fetch_array($query);
    }

    if (isset($_POST['submit'])) {
        $vehicleregno = $_POST['vehicleregno'];
        $update = mysqli_query($con, "UPDATE tblvehiclelogs SET RegistrationNumber='$vehicleregno' WHERE ID='$editid'");
        if ($update) {
            echo "<script>alert('Record updated successfully');</script>";
            echo "<script>window.location.href='view-vehicle.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again.');</script>";
        }
    }
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Edit Vehicle Details</h2>
        <form method="post">
            <div class="form-group">
                <label for="vehicleregno">Vehicle Registration Number</label>
                <input type="text" class="form-control" id="vehicleregno" name="vehicleregno" value="<?php echo $row['RegistrationNumber']; ?>" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update</button>
            <a href="view-vehicle.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
<?php } ?>
