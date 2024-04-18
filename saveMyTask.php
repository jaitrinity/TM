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
$project = $jsonData->project;
$week = $jsonData->week;
if($week == "Yesterday"){
	$yesterday = date('d-M-Y', strtotime('-1 day'));
	$week .= ' ('.$yesterday.')';
	// $week = $yesterday;
}
else if($week == "Today"){
	$currentDate = date('d-M-Y');
	$week .= ' ('.$currentDate.')';
	// $week = $currentDate;
}
$noOfTask = $jsonData->noOfTask;
$taskList = $jsonData->taskList;
$taskId = 0;

$rmSuSql = "SELECT  e.RMId, e1.RMId as SUId FROM Employees e left join Employees e1 on e.RMId = e1.EmpId where e.EmpId = '$empId'";
$rmSuQuery = mysqli_query($conn,$rmSuSql);
$rowCount=mysqli_num_rows($rmSuQuery);
$rmId="";
$suId="";
if($rowCount != 0){
	$rmSuRow = mysqli_fetch_assoc($rmSuQuery);
	$rmId = $rmSuRow["RMId"];
	$suId = $rmSuRow["SUId"];
}
$sql = "INSERT INTO `MyTask`(`EmpId`, `ProjectName`, `Week`, `NoOfTask`, `RM_EmpId`, `SU_EmpId`) VALUES ('$empId', '$project', '$week', $noOfTask, '$rmId', '$suId')";
$code = 0;
$message = "";
if(mysqli_query($conn,$sql)){
	$taskId = $conn->insert_id;

	$status = false;
	$table="INSERT INTO `SubTask`(`TaskId`, `SrNo`, `Subject`, `TaskDescription`) ";
	for($i=0;$i<count($taskList);$i++){
		$taskObj = $taskList[$i];
		$srNo = $taskObj->srNo;
		$subject = $taskObj->subject;
		$task = $taskObj->task;
		$data = "($taskId, $srNo, '$subject', ?)";
		$insertSubTask = $table." VALUES ".$data;

		$stmt = $conn->prepare($insertSubTask);
		$stmt->bind_param("s", $task);
		if($stmt->execute()){
			$status = true;
		}
		else{
			$status = false;
			$message = "Something wrong while insert data of task $srNo in `SubTask` table";
			break;
		}
	}

	// echo $insertSubTask;
	if($status){
		$code = 200;
		$message = "Success";

		// $taskRep = "UPDATE `TaskReport` SET `$week`=1 WHERE `EmpId`='$empId'";
		// $taskRepStmt = $conn->prepare($taskRep);
		// $taskRepStmt->execute();

		require 'LoggerClass.php';
		$classObj = new LoggerClass();
		$classObj->insertLog($empId, "Task Submit");
	}
	else{
		$code = 500;
		$message = $message;

		$delSubTask = "DELETE FROM `SubTask` WHERE `TaskId`='$taskId'";
		mysqli_query($conn,$delSubTask);

		$delTask = "DELETE FROM `MyTask` WHERE `TaskId`='$taskId'";
		mysqli_query($conn,$delTask);

		$taskId = 0;

	}
}
else{
	$code = 500;
	$message = "Something wrong while insert data in `MyTask` table";
}
$output = array(
	'code' => $code,
	'message' => $message,
	'taskId' => $taskId
);
echo json_encode($output);
?>