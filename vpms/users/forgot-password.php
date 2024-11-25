<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];

    // Kiểm tra xem thông tin email có khớp không
    $query = $con->prepare("SELECT ID FROM tblregusers WHERE Email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    $ret = $result->fetch_assoc();

    if ($ret) {
        $userId = $ret['ID'];

        // Tạo mã OTP ngẫu nhiên
        $otp = rand(100000, 999999);

        // Lưu OTP và thời gian tạo vào cơ sở dữ liệu
        $expiryTime = date("Y-m-d H:i:s", strtotime("+5 minutes")); // OTP có hiệu lực 5 phút
        $updateQuery = $con->prepare("UPDATE tblregusers SET OTP = ?, OTPExpiry = ? WHERE ID = ?");
        $updateQuery->bind_param("ssi", $otp, $expiryTime, $userId);
        if ($updateQuery->execute()) {
            // Gửi OTP qua email
            $to = $email;
            $subject = "Your OTP Code";
            $message = "Your OTP code is $otp. This code is valid for 5 minutes.";
            $headers = 'From: vpms@example.com' . "\r\n" .
                        'Reply-To: vpms@example.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();

            if (mail($to, $subject, $message, $headers)) {
                header('location:verify-otp.php?userId=' . $userId);
            } else {
                echo "<script>alert('Failed to send OTP. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Failed to update OTP. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Invalid email. Please try again.');</script>";
    }
}
?>

<!doctype html>
 <html class="no-js" lang="">
<head>
    
    <title>VPMS-Forgot Page</title>
   

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

    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/html5shiv/3.7.3/html5shiv.min.js"></script> -->
</head>
<body class="bg-dark">

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">
                <div class="login-logo">
                    <a href="index.php">
                         <h2 style="color: green">Vehicle Parking Management System</h2>
                    </a>
                </div>
                <div class="login-form">
                    <form method="post">
                         
                        <div class="form-group">
                            <label>Email</label>
                           <input type="text" class="form-control" name="email" placeholder="Email" autofocus required="true">
                        </div>
                        <div class="checkbox">
                            
                            <label class="pull-right">
                                <a href="login.php">Signin</a>
                            </label>

                        </div>
                        <button type="submit" name="submit" class="btn btn-success btn-flat m-b-30 m-t-30">Reset</button>
                    </form>
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
