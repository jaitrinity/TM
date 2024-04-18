<?php
include("dbConfiguration.php");
$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	$output = array('code' => 405, 'message' => 'Invalid method Type');
	echo json_encode($output);
	return;
}

$json = file_get_contents('php://input');
$jsonData=json_decode($json);

$email = $jsonData->email;

$sql = "SELECT `Id`, `EmpId`, `Name`, `Email` FROM `Employees` WHERE `Email`='$email' and `IsActive`=1";
$query = mysqli_query($conn,$sql);
if(mysqli_num_rows($query) != 0){
	$row = mysqli_fetch_assoc($query);
	$id = $row["Id"];
	$empId = $row["EmpId"];
	$empName = $row["Name"];
	$randomOtp = rand(1000,9999);

	require 'SendMailClass.php';

	$msg = "Dear $empName,<br><br>";
	$msg .= "Your One Time Password(OTP) is <b>$randomOtp</b>. Please don't share this OTP to someone. <br>";
	$msg .= "<b>This OTP is only valid for 3 minutes.</b> <br><br>";
	$msg .= "Regards,<br>";
	$msg .= "Trinity automation team.";

	$subject = "Format Password OTP";
	$sendMailObj = new SendMailClass();
	$sendMailStatus = $sendMailObj->sendMail($email, $subject, $msg, null);
	if($sendMailStatus){
		$code = 200;
		$message = "OTP sent to your email id.";

		$t = strtotime("+3 minutes");
		$otpExpDatetime = date("Y-m-d H:i:s",$t);

		$updateOtp = "UPDATE `Employees` set `OTP`=$randomOtp, `IsOTPExpired`=0, `OTPExpDatetime`='$otpExpDatetime' where `Id`=$id";
		mysqli_query($conn,$updateOtp);

		$createEvent = "CREATE EVENT `EV_$t` ON SCHEDULE AT '$otpExpDatetime' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE `Employees` set `IsOTPExpired`=2 where `IsOTPExpired`=0 and `OTPExpDatetime` = CURRENT_TIMESTAMP";
		mysqli_query($conn,$createEvent);
	}
	else{
		$code = 0;
		$message = "Enable to send OTP to mail, please check after some time..";
	}
}
else{
	$code = 204;
	$message = 'Either email incorrect, please try again.';
}
$output = array('code' => $code, 'message' => $message);
echo json_encode($output);
?>