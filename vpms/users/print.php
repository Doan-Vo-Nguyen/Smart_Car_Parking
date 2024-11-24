  <?php
  session_start();
  error_reporting(0);
  include('includes/dbconnection.php');
  if (strlen($_SESSION['vpmsuid'] == 0)) {
    header('location:logout.php');
  } else {


  ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="assets/css/cs-skin-elastic.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <?php
    $cid = $_GET['vid'];
    $ret = mysqli_query($con, "SELECT tbvehlogs.ParkingNumber, tbcat.VehicleCat, tbvehlogs.RegistrationNumber,
                                tbuser.FullName,tbuser.MobileNumber,tbvehlogs.InTime,tbvehlogs.OutTime,tbvehlogs.Status,tbvehlogs.Remark,tbvehlogs.ParkingCharge
                                FROM tblvehiclelogs as tbvehlogs
                                JOIN tblvehicle as tbveh ON tbvehlogs.VehicleID=tbveh.ID
                                JOIN tblregusers as tbuser ON tbuser.ID=tbveh.OwnerID
                                JOIN tblcategory as tbcat ON tbcat.ID=tbveh.CategoryID
                                WHERE tbvehlogs.ID='$cid'");
    $cnt = 1;
    while ($row = mysqli_fetch_array($ret)) {
    ?>

      <div id="exampl">

        <table border="1" class="table table-bordered mg-b-0">
          <tr>
            <th colspan="4" style="text-align: center; font-size:22px;"> Vehicle Parking receipt</th>

          </tr>

          <tr>
            <th>Parking Number</th>
            <td><?php echo $row['ParkingNumber']; ?></td>


            <th>Vehicle Category</th>
            <td><?php echo $row['VehicleCat']; ?></td>
          </tr>
          <tr>
            <th>Registration Number</th>
            <td><?php echo $row['RegistrationNumber']; ?></td>

            <th>Owner Name</th>
            <td><?php echo $row['FullName']; ?></td>
          </tr>
          <tr>
            <th>In Time</th>
            <td><?php echo $row['InTime']; ?></td>

            <th>Out time</th>
              <td><?php echo $row['OutTime']; ?></td>
          </tr>
          <?php if ($row['Remark'] != "") { ?>
            <tr>
            <th>Status</th>
            <td> <?php
                  if ($row['Status'] == "In") {
                    echo "Incoming Vehicle";
                  }
                  if ($row['Status'] == "Out") {
                    echo "Outgoing Vehicle";
                  }; ?></td>
              
              <th>Rarking Charge</th>
              <td><?php echo $row['ParkingCharge']; ?></td>
            </tr>
            <tr>
              <th>Remark</th>
              <td colspan="4"><?php echo $row['Remark']; ?></td>

            </tr>


          <?php } ?>
          <tr>
            <td colspan="4" style="text-align:center; cursor:pointer"><i class="fa fa-print fa-2x" aria-hidden="true" OnClick="CallPrint(this.value)"></i></td>
          </tr>

        </table>
    <?php }
  }  ?>
      </div>
      <script>
        function CallPrint(strid) {
          var prtContent = document.getElementById("exampl");
          var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
          WinPrint.document.write(prtContent.innerHTML);
          WinPrint.document.close();
          WinPrint.focus();
          WinPrint.print();
          WinPrint.close();
        }
      </script>