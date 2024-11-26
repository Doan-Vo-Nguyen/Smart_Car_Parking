<?php
session_start();
include('includes/dbconnection.php');

if (isset($_POST['verify'])) {
    $adminId = $_GET['adminId'];
    $inputOtp = $_POST['otp'];

    // Kiểm tra OTP và thời gian hết hạn
    $query = $con->prepare("SELECT OTP, OTPExpiry FROM tbladmin WHERE ID = ?");
    $query->bind_param("i", $adminId);
    $query->execute();
    $result = $query->get_result();
    $ret = $result->fetch_assoc();

    if ($ret) {
        $storedOtp = $ret['OTP'];
        $otpExpiry = $ret['OTPExpiry'];
        $currentTime = date("Y-m-d H:i:s");

        if ($storedOtp === $inputOtp && $currentTime <= $otpExpiry) {
            // OTP hợp lệ, cho phép truy cập trang reset mật khẩu
            header('location:reset-password.php?adminId=' . $adminId);
        } else {
            echo "<script>alert('Invalid or expired OTP. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('Admin not found.');</script>";
    }
}
?>
<form method="post">
    <label>Enter OTP</label>
    <input type="text" name="otp" required>
    <button type="submit" name="verify">Verify</button>
</form>
