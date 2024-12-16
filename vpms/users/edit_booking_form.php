<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
} else {
    // Lấy ID của lịch đặt chỗ để chỉnh sửa
    if (isset($_GET['edit_id'])) {
        $edit_id = intval($_GET['edit_id']);
        $query = mysqli_query($con, "SELECT ts.ID, ts.CategoryID, tc.VehicleCat, ts.RegistrationNumber, ts.DateSchedule, ts.Status 
                                     FROM tblschedule ts
                                     JOIN tblcategory tc ON ts.CategoryID = tc.ID
                                     WHERE ts.ID = '$edit_id'");
        $row = mysqli_fetch_assoc($query);
        if (!$row) {
            echo "<script>alert('Invalid Booking ID.');</script>";
            echo "<script>window.location.href='history_booking.php';</script>";
        }
    } else {
        echo "<script>alert('No Booking ID provided.');</script>";
        echo "<script>window.location.href='history_booking.php';</script>";
    }

    // Cập nhật thông tin lịch đặt chỗ
    if (isset($_POST['update'])) {
        $categoryID = intval($_POST['category']);
        $registrationNumber = mysqli_real_escape_string($con, $_POST['registrationNumber']);
        $dateSchedule = mysqli_real_escape_string($con, $_POST['dateSchedule']);
        $status = mysqli_real_escape_string($con, $_POST['status']);

        $updateQuery = "UPDATE tblschedule SET CategoryID='$categoryID', RegistrationNumber='$registrationNumber', DateSchedule='$dateSchedule', Status='$status' WHERE ID='$edit_id'";

        if (mysqli_query($con, $updateQuery)) {
            echo "<script>alert('Booking updated successfully.');</script>";
            echo "<script>window.location.href='history_booking.php';</script>";
        } else {
            echo "<script>alert('Failed to update booking. Please try again.');</script>";
        }
    }

    // Lấy danh mục phương tiện
    $categories = mysqli_query($con, "SELECT ID, VehicleCat FROM tblcategory");
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <title>Edit Booking</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Booking</h2>
        <form method="post">
            <div class="form-group">
                <label for="category">Vehicle Category</label>
                <select name="category" id="category" class="form-control" required>
                    <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                        <option value="<?php echo $cat['ID']; ?>" <?php if ($row['CategoryID'] == $cat['ID']) echo 'selected'; ?>>
                            <?php echo htmlentities($cat['VehicleCat']); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="registrationNumber">Registration Number</label>
                <input type="text" name="registrationNumber" id="registrationNumber" class="form-control" value="<?php echo htmlentities($row['RegistrationNumber']); ?>" required>
            </div>

            <div class="form-group">
                <label for="dateSchedule">Booking Date</label>
                <input type="date" name="dateSchedule" id="dateSchedule" class="form-control" value="<?php echo htmlentities($row['DateSchedule']); ?>" required>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control" required>
                    <option value="Pending" <?php if ($row['Status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Completed" <?php if ($row['Status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Canceled" <?php if ($row['Status'] == 'Canceled') echo 'selected'; ?>>Canceled</option>
                </select>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update Booking</button>
            <a href="history_booking.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
<?php } ?>
