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
$roleId = $jsonData->roleId;
$taskId = $jsonData->taskId;
$rating = $jsonData->rating;
$remark = $jsonData->remark;
$roleType = $jsonData->roleType;

$code = 0;
$message = "";
if($roleType == "RM"){
	$sql = "UPDATE `MyTask` SET `RM_Rating`=$rating, `RM_Remark`='$remark', `RM_RatingDate`=current_timestamp where `TaskId` = $taskId and `RM_Rating` is null";	
}
else if($roleType == "SU"){
	$sql = "UPDATE `MyTask` SET `SU_Rating`=$rating, `SU_Remark`='$remark', `SU_RatingDate`=current_timestamp where `TaskId` = $taskId and `SU_Rating` is null";	
}
// echo $sql;
if(mysqli_query($conn,$sql)){
	$code = 200;
	$message = "Successfully update rating";

	require 'LoggerClass.php';
	$classObj = new LoggerClass();
	$classObj->insertLog($empId, "$roleType rating updated");
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