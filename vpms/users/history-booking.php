<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {
    $uid = $_SESSION['vpmsuid'];

    // Default filter values
    $statusFilter = $_POST['statusFilter'] ?? 'All';
    $customerTypeFilter = $_POST['customerTypeFilter'] ?? 'All';

    // Base query for filtering
    $queryBase = "
        SELECT tblschedule.ID, tblcategory.VehicleCat, tblschedule.RegistrationNumber, 
               tblschedule.DateSchedule, tblschedule.ExpectDateOut, tblschedule.Status, 'Registered' AS CustomerType
        FROM tblschedule
        JOIN tblcategory ON tblschedule.CategoryID = tblcategory.ID
        WHERE tblschedule.OwnerID = '$uid'
    ";

    if ($customerTypeFilter === 'Unregistered' || $customerTypeFilter === 'All') {
        $queryBase .= "
            UNION
            SELECT tblschedule_unregistered.ID, tblschedule_unregistered.VehicleCategory AS VehicleCat, 
                   tblschedule_unregistered.RegistrationNumber, tblschedule_unregistered.DateSchedule, 
                   tblschedule_unregistered.ExpectDateOut, tblschedule_unregistered.Status, 'Unregistered' AS CustomerType
            FROM tblschedule_unregistered
            WHERE tblschedule_unregistered.CCCD IN
                (SELECT CCCD FROM tblregusers WHERE ID = '$uid')
        ";
    }

    // Add status filter
    if ($statusFilter !== 'All') {
        $queryBase .= " HAVING Status = '$statusFilter'";
    }

    $queryBase .= " ORDER BY DateSchedule DESC";

    // Execute the query
    $query = mysqli_query($con, $queryBase);

    // Handle Edit button
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        header("Location: edit_booking.php?id=$id");
        exit();
    }

    // Handle Delete button
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $query_delete = "DELETE FROM tblschedule WHERE ID = '$id'";
        $result = mysqli_query($con, $query_delete);
        if ($result) {
            echo "<script>alert('Booking deleted successfully!');</script>";
            echo "<script>window.location.href = window.location.href;</script>"; // Refresh the page
        } else {
            echo "<script>alert('Error deleting booking.');</script>";
        }
    }
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <title>History Booking</title>
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
    <?php include_once('includes/sidebar.php'); ?>
    <?php include_once('includes/header.php'); ?>

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong class="card-title">History Booking</strong>
                        </div>
                        <div class="card-body">
                            <form method="post" action="">
                                <div class="form-group row">
                                    <label for="statusFilter" class="col-sm-2 col-form-label">Status Filter</label>
                                    <div class="col-sm-4">
                                        <select name="statusFilter" id="statusFilter" class="form-control">
                                            <option value="All" <?php if ($statusFilter === 'All') echo 'selected'; ?>>All</option>
                                            <option value="Booked" <?php if ($statusFilter === 'Booked') echo 'selected'; ?>>Booked</option>
                                            <option value="Canceled" <?php if ($statusFilter === 'Canceled') echo 'selected'; ?>>Canceled</option>
                                            <option value="Done" <?php if ($statusFilter === 'Done') echo 'selected'; ?>>Done</option>
                                        </select>
                                    </div>

                                    <label for="customerTypeFilter" class="col-sm-2 col-form-label">Customer Type</label>
                                    <div class="col-sm-4">
                                        <select name="customerTypeFilter" id="customerTypeFilter" class="form-control">
                                            <option value="All" <?php if ($customerTypeFilter === 'All') echo 'selected'; ?>>All</option>
                                            <option value="Registered" <?php if ($customerTypeFilter === 'Registered') echo 'selected'; ?>>Registered</option>
                                            <option value="Unregistered" <?php if ($customerTypeFilter === 'Unregistered') echo 'selected'; ?>>Unregistered</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                            </form>

                            <table class="table table-bordered mt-4">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Vehicle Type</th>
                                        <th>Registration Number</th>
                                        <th>Booking Date</th>
                                        <th>Expected Date Out</th>
                                        <th>Status</th>
                                        <th>Customer Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $cnt = 1;
                                    while ($row = mysqli_fetch_assoc($query)) {
                                    ?>
                                        <tr>
                                            <td><?php echo $cnt; ?></td>
                                            <td><?php echo $row['VehicleCat']; ?></td>
                                            <td><?php echo $row['RegistrationNumber']; ?></td>
                                            <td><?php echo $row['DateSchedule']; ?></td>
                                            <td><?php echo $row['ExpectDateOut']; ?></td>
                                            <td><?php echo $row['Status']; ?></td>
                                            <td><?php echo $row['CustomerType']; ?></td>
                                            <td>
                                                <form method="post" action="">
                                                    <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                    <button type="submit" name="edit" class="btn btn-sm btn-primary">Edit</button>
                                                    <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php
                                        $cnt++;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php } ?>
