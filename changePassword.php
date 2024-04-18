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
$otp = $jsonData->otp;
$newPassword = $jsonData->newPassword;

$sql = "SELECT `Id`, `EmpId`, `OTP`, `IsOTPExpired` FROM `Employees` WHERE `Email`='$email' and `IsActive`=1";
$query = mysqli_query($conn,$sql);
if(mysqli_num_rows($query) != 0){
	$row = mysqli_fetch_assoc($query);
	$id = $row["Id"];
	$empId = $row["EmpId"];
	$myOtp = $row["OTP"];
	$isOTPExpired = $row["IsOTPExpired"];
	if($myOtp == $otp && $isOTPExpired == 0){
		$updateOtp = "UPDATE `Employees` set `Password`='$newPassword', `IsOTPExpired`=1 where `id`=$id";
		if(mysqli_query($conn,$updateOtp)){
			// $dd = $conn->affected_rows;
			$code = 200;
			$message = "Password changed successfully.";

			require 'LoggerClass.php';
			$classObj = new LoggerClass();
			$classObj->insertLog($empId, "Password change");
		}
		else{
			$code = 500;
			$message = "Unable to change password.";
		}
	}
	else if($myOtp == $otp && $isOTPExpired == 1){
		$code = 0;
		$message = "Given OTP is already used, plz regeneate OTP";
	}
	else if($myOtp == $otp && $isOTPExpired == 2){
		$code = 0;
		$message = "Given OTP is expired, plz regeneate OTP";
	}
	else{
		$code = 0;
		$message = "Incorrect OTP, plz try again";
	}
}
else{
	$code = 204;
	$message = 'Either email incorrect, please try again.';
}
$output = array('code' => $code, 'message' => $message);
echo json_encode($output);
?>