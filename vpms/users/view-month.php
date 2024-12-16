<?php
session_start();
include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Month Registrations</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once('includes/sidebar.php'); ?>
    <div id="right-panel" class="right-panel">
        <?php include_once('includes/header.php'); ?>
        <div class="content">
            <div class="animated fadeIn">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>View</strong> <small>Month Registrations</small>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Owner Name</th>
                                            <th>Vehicle Category</th>
                                            <th>Registration Number</th>
                                            <th>Month Schedule</th>
                                            <th>Registration Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = "SELECT * FROM tblschedule";
                                        $result = mysqli_query($con, $query);
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $cnt . "</td>";
                                            echo "<td>" . $row['owner_name'] . "</td>";
                                            echo "<td>" . $row['vehicle_category'] . "</td>";
                                            echo "<td>" . $row['registration_number'] . "</td>";
                                            echo "<td>" . $row['DateSchedule'] . "</td>";
                                            echo "<td>" . $row['created_at'] . "</td>";
                                            echo "</tr>";
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
        <?php include_once('includes/footer.php'); ?>
    </div>
</body>
</html>
