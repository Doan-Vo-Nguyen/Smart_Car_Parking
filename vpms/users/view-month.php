<?php
session_start();
include('includes/dbconnection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>View Month Registrations</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
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
                                <form method="GET" action="">
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="monthpicker">Select Month</label>
                                            <input type="text" id="monthpicker" name="month_year" class="form-control" placeholder="Choose month and year" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="num_months">Number of Months</label>
                                            <select name="num_months" id="num_months" class="form-control" required>
                                                <option value="1">1 Month</option>
                                                <option value="2">2 Months</option>
                                                <option value="3">3 Months</option>
                                                <option value="4">4 Months</option>
                                                <option value="5">5 Months</option>
                                                <option value="6">6 Months</option>
                                                <option value="7">7 Months</option>
                                                <option value="8">8 Months</option>
                                                <option value="9">9 Months</option>
                                                <option value="10">10 Months</option>
                                                <option value="11">11 Months</option>
                                                <option value="12">12 Months</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">View Registrations</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                if (isset($_GET['month_year']) && isset($_GET['num_months'])) {
                    // Tách tháng và năm từ input
                    $month_year = explode(" ", $_GET['month_year']);
                    $month = date('m', strtotime($month_year[0]));
                    $year = $month_year[1];
                    $num_months = $_GET['num_months'];

                    // Tính ngày bắt đầu
                    $start_date = date('Y-m-01', strtotime("$year-$month-01"));
                    
                    // Tính ngày đến hạn theo số tháng đã chọn
                    $due_date = date('Y-m-d', strtotime("$start_date +$num_months months -1 day"));
                    
                    echo '<div class="alert alert-info">Ngày đến hạn: <strong>'.$due_date.'</strong></div>';
                ?>
                
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <strong>Registrations for <?php echo date('F Y', strtotime("$year-$month-01")); ?></strong>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Owner Name</th>
                                            <th>Vehicle Category</th>
                                            <th>Registration Number</th>
                                            <th>Date Schedule</th>
                                            <th>Registration Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Truy vấn dữ liệu
                                        $query = "SELECT ts.*, towner.OwnerName, tcat.VehicleCat AS vehicle_category
                                                  FROM tblschedule ts
                                                  JOIN tblowners towner ON ts.OwnerID = towner.ID
                                                  JOIN tblcategory tcat ON ts.CategoryID = tcat.ID
                                                  WHERE ts.DateSchedule BETWEEN '$start_date' AND '$due_date'";
                                        $result = mysqli_query($con, $query);
                                        $cnt = 1;
                                        while ($row = mysqli_fetch_array($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $cnt . "</td>";
                                            echo "<td>" . $row['OwnerName'] . "</td>";
                                            echo "<td>" . $row['vehicle_category'] . "</td>";
                                            echo "<td>" . $row['RegistrationNumber'] . "</td>";
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
                <?php } ?>
            </div>
        </div>
        <?php include_once('includes/footer.php'); ?>
    </div>

    <script>
        $(function() {
            $("#monthpicker").datepicker({
                changeMonth: true,
                changeYear: true,
                showButtonPanel: true,
                dateFormat: 'MM yy',
                onClose: function(dateText, inst) {
                    var month = $("#ui-datepicker-div .ui-datepicker-month option:selected").val();
                    var year = $("#ui-datepicker-div .ui-datepicker-year option:selected").val();
                    $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                }
            });
        });
    </script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
