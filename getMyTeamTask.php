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
$filterEmpId = $jsonData->filterEmpId;
$filterWeek = $jsonData->filterWeek;

$filterSql = "";
if($roleId != 1 && $filterEmpId == ""){
	$filterSql .= "and (mt.RM_EmpId='$empId' or mt.SU_EmpId='$empId') ";

	$empIdArr = array();
	getNlevelRm($empId, $empIdArr);

	if(count($empIdArr) !=0){
		$empIdImp = implode("','", $empIdArr);
		$filterSql .= "or mt.EmpId in ('$empIdImp') ";
	}
}
if($filterEmpId != ""){
	$filterSql .= "and mt.EmpId = '$filterEmpId' ";
}
if($filterWeek != ""){
	$filterSql .= "and mt.Week = '$filterWeek' ";
}

// $sql="SELECT mt.TaskId, mt.EmpId, e.Name, mt.ProjectName, mt.Week, mt.NoOfTask, mt.RM_EmpId, e1.Name as RM_Name, mt.RM_Rating, mt.RM_Remark, mt.RM_RatingDate, mt.SU_EmpId, e2.Name as SU_Name, mt.SU_Rating, mt.SU_Remark, mt.SU_RatingDate, mt.CreateDate FROM MyTask mt join Employees e on mt.EmpId = e.EmpId left join Employees e1 on mt.RM_EmpId = e1.EmpId left join Employees e2 on mt.SU_EmpId = e2.EmpId where 1=1 $filterSql order by mt.TaskId desc";
$sql="SELECT mt.TaskId, mt.EmpId, e.Name, mt.ProjectName, mt.Week, mt.NoOfTask, mt.RM_EmpId, e1.Name as RM_Name, mt.RM_Rating, mt.RM_Remark, mt.RM_RatingDate, mt.SU_EmpId, e2.Name as SU_Name, mt.SU_Rating, mt.SU_Remark, mt.SU_RatingDate, mt.CreateDate FROM MyTask mt join Employees e on mt.EmpId = e.EmpId left join Employees e1 on mt.RM_EmpId = e1.EmpId left join Employees e2 on mt.SU_EmpId = e2.EmpId where 1=1 $filterSql order by e.Name";
// echo $sql;
$query = mysqli_query($conn,$sql);
$taskList = array();
while($row = mysqli_fetch_assoc($query)){
	$roleType = "";
	$oneTask = "";
	$rmEmpId = $row["RM_EmpId"];
	$rmRating = $row["RM_Rating"] == null ? "" : $row["RM_Rating"];
	if($rmEmpId == $empId && $rmRating == ''){
		$roleType = "RM";
	}
	$suEmpId = $row["SU_EmpId"];
	$suRating = $row["SU_Rating"] == null ? "" : $row["SU_Rating"];
	if($rmRating != '' && $suEmpId == $empId && $suRating == ''){
		$roleType = "SU";
	}

	// if($roleType != ""){
		$taskId = $row["TaskId"];

		$subTaskList = array();
		$subTaskSql = "SELECT * FROM `SubTask` where `TaskId` = $taskId";
		$subTaskQuery = mysqli_query($conn,$subTaskSql);
		while($subTaskRow = mysqli_fetch_assoc($subTaskQuery)){
			$srNo = $subTaskRow["SrNo"];
			$subject = $subTaskRow["Subject"];
			$testDescription = $subTaskRow["TaskDescription"];
			if($srNo == 1) $oneTask = $testDescription;
			$subTaskObj = array(
				'srNo' => $srNo,
				'subject' => $subject,
				'task' => $testDescription
			);

			array_push($subTaskList, $subTaskObj);
		}

		$rmEmpId = $row["RM_EmpId"] == null ? "" : $row["RM_EmpId"];
		$rmRating = $row["RM_Rating"] == null ? "" : $row["RM_Rating"];
		if($rmEmpId == ""){
			$rmRating = "NA";
		}
		else if($rmEmpId != "" && $rmRating == ""){
			$rmRating = "No";
		}

		$suEmpId = $row["SU_EmpId"] == null ? "" : $row["SU_EmpId"];
		$suRating = $row["SU_Rating"] == null ? "" : $row["SU_Rating"];
		if($suEmpId == ""){
			$suRating = "NA";
		}
		else if($suEmpId != "" && $suRating == ""){
			$suRating = "No";
		}

		$taskObj = array(
			'taskId' => $row["TaskId"],
			'empId' => $row["EmpId"],
			'name' => $row["Name"],
			'project' => $row["ProjectName"],
			'week' => $row["Week"],
			'oneTask' => $oneTask,
			'rmRating' => $rmRating,
			'rmRemark' => $row["RM_Remark"] == null ? "" : $row["RM_Remark"],
			'rmName' => $row["RM_Name"],
			'rmRatingDate' => $row["RM_RatingDate"] == null ? "" : changeDateFormat($row["RM_RatingDate"]),
			'suRating' => $suRating,
			'suRemark' => $row["SU_Remark"] == null ? "" : $row["SU_Remark"],
			'suName' => $row["SU_Name"],
			'suRatingDate' => $row["SU_RatingDate"] == null ? "" : changeDateFormat($row["SU_RatingDate"]),
			'roleType' => $roleType,
			'submitDate' => changeDateFormat($row["CreateDate"]),
			'taskList' => $subTaskList
		);

		array_push($taskList, $taskObj);
	// }
}
echo json_encode($taskList);
?>

<?php
function changeDateFormat($myDate){
	$date = date_create($myDate);
	return date_format($date, 'd-M-Y H:i:s');
}
function getNlevelRm($empId){
	global $conn;
	global $empIdArr;
	$sql = "SELECT e.EmpId FROM Employees e left join Employees e1 on e.RMId=e1.EmpId where e.RMId='$empId'";
	$query = mysqli_query($conn,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$loopEmpId = $row["EmpId"];
		array_push($empIdArr, $loopEmpId);
		getNlevelRm($loopEmpId,$empIdArr);
	}
}
?>