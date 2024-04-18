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
$event = $jsonData->event;

$sql = "SELECT `Name` FROM `Employees` where `EmpId`='$empId'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$query = $stmt->get_result();
$row = mysqli_fetch_assoc($query);
$empName = $row["Name"];

$sql = "INSERT INTO `Logger`(`EmpId`, `Name`, `Event`) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $empId, $empName, $event);
if($stmt->execute()){
	$code = 200;
	$message = "Logout successfully.";
}
else{
	$code = 500;
	$message = "Something went wrong";
}
$output = array('code' => $code, 'message' => $message);
echo json_encode($output);

?>