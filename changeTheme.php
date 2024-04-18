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

$empId = $jsonData->empId;
$themeOption = $jsonData->themeOption;
$themeColor = $jsonData->themeColor;
$theme = $themeOption.':'.$themeColor;

$code = 0;
$message = "";
$sql="UPDATE `Employees` set `Theme`='$theme' where `EmpId`='$empId'";
if(mysqli_query($conn,$sql)){
	$code = 200;
	$message = "Success";
}
else{
	$code = 500;
	$message = "Something wrong";
}
$output = array(
	'code' => $code,
	'message' => $message
);
echo json_encode($output);

?>