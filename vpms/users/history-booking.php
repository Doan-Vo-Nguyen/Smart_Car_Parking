<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {
    $uid = $_SESSION['vpmsuid'];

    // Default filter value
    $statusFilter = $_POST['statusFilter'] ?? 'All';

    /// Base query for filtering
    $queryBase = "
SELECT tblregusers.FullName, tblschedule.ID, tblcategory.VehicleCat, tblschedule.RegistrationNumber, 
       tblschedule.DateSchedule, tblschedule.ExpectDateOut, tblschedule.Status, tblregusers.FullName AS ActionBy
FROM tblschedule
JOIN tblcategory ON tblschedule.CategoryID = tblcategory.ID
JOIN tblregusers ON tblschedule.OwnerID = tblregusers.ID
JOIN tblschedule_unregistered ON tblschedule_unregistered.ActionBy = tblregusers.ID
WHERE tblschedule.OwnerID = '$uid'
";

    // Add status filter to tblschedule query
    if ($statusFilter !== 'All') {
        $queryBase .= " AND tblschedule.Status = '$statusFilter'";
    }

    // Add data from tblschedule_unregistered
    $queryBase .= "
UNION
SELECT tblschedule_unregistered.OwnerName AS FullName,
        tblschedule_unregistered.ID,
         tblcategory.VehicleCat,
       tblschedule_unregistered.RegistrationNumber, tblschedule_unregistered.DateSchedule,
       tblschedule_unregistered.ExpectDateOut, tblschedule_unregistered.Status,
       tblregusers.FullName AS ActionBy
FROM tblschedule_unregistered
JOIN tblcategory ON tblschedule_unregistered.VehicleCategory = tblcategory.ID
JOIN tblregusers ON tblschedule_unregistered.ActionBy = tblregusers.ID
";

    // Add status filter to tblschedule_unregistered query
    if ($statusFilter !== 'All') {
        $queryBase .= " WHERE tblschedule_unregistered.Status = '$statusFilter'";
    }

    // Order results
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
                                                <option value="Booked" <?php
                                                    // If the status filter is 'Booked', set the default option to 'Booked'
                                                    if ($statusFilter === 'Booked') {
                                                        echo 'selected';
                                                    }
                                                ?>>Booked</option>
                                                <option value="Canceled" <?php if ($statusFilter === 'Canceled') echo 'selected'; ?>>Canceled</option>
                                                <option value="Done" <?php if ($statusFilter === 'Done') echo 'selected'; ?>>Done</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                                </form>

                                <table class="table table-bordered mt-4">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>FullName</th>
                                            <th>Vehicle Type</th>
                                            <th>Registration Number</th>
                                            <th>Booking Date</th>
                                            <th>Expected Date Out</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                            <th>By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_assoc($query)) {
                                        ?>
                                            <tr>
                                                <td><?php echo $cnt; ?></td>
                                                <td><?php echo $row['FullName'];?></td>
                                                <td><?php echo $row['VehicleCat']; ?></td>
                                                <td><?php echo $row['RegistrationNumber']; ?></td>
                                                <td><?php echo $row['DateSchedule']; ?></td>
                                                <td><?php echo $row['ExpectDateOut']; ?></td>
                                                <td><?php echo $row['Status']; ?></td>
                                                <td>
                                                    <form method="post" action="">
                                                        <input type="hidden" name="id" value="<?php echo $row['ID']; ?>">
                                                        <a href="edit_booking_form.php?edit_id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                        <button type="submit" name="delete" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</button>
                                                    </form>
                                                </td>
                                                <td><?php echo $row['ActionBy']; ?></td>
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