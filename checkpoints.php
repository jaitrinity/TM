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

$proSql = "SELECT `Project`, `Subject` FROM `RoleMaster` where `RoleId`=$roleId";
$proQuery = mysqli_query($conn,$proSql);
$proRow = mysqli_fetch_assoc($proQuery);
$project = $proRow["Project"];
$projectList = explode(",", $project);
$subject = $proRow["Subject"];
$subjectList = explode(",", $subject);

// $currentDate = date('Y-m-d');
// $date = new DateTime($currentDate);
// $year = $date->format("Y");
// $week = $date->format("W");

// $weekSql = "SELECT DISTINCT `Week` FROM `MyTask` where `EmpId`='$empId' and date_format(`CreateDate`,'%Y')=$year";
// $weekQuery = mysqli_query($conn,$weekSql);
// $rowCount = mysqli_num_rows($weekQuery);
// $subWeekList = array();
// if($rowCount != 0){
// 	while($weekRow = mysqli_fetch_assoc($weekQuery)){
// 		array_push($subWeekList, $weekRow["Week"]);
// 	}
// }

// $weekList = array();
// $weekJson = array(
// 	'week'=> "Today",
// 	'weekDate' => $date->format("d-M-Y")
// );
// array_push($weekList, $weekJson);

// for($i=1;$i<=$week;$i++){
// 	$weekNo = $i;
// 	$week_array = getStartAndEndDate($weekNo,$year);
// 	$startWeekDate = $week_array["week_start"];
// 	$endWeekDate = $week_array["week_end"];

// 	$weekDates = $startWeekDate." to ".$endWeekDate;
// 	$weeks = "Week ".$weekNo;
// 	if(in_array($weeks,$subWeekList)){

// 	}
// 	else{
// 		$weekJson = array(
// 			'week'=> $weeks,
// 			'weekDate' => $weekDates
// 		);
// 		array_push($weekList, $weekJson);
// 	}
// }


$sql="SELECT * FROM `Checkpoints`";
$query = mysqli_query($conn,$sql);

$noOfTask;
while($row = mysqli_fetch_assoc($query)){
	$id = $row["Id"];
	$desc = $row["Description"];
	$value = $row["Value"];
	$valueList = explode(",", $value);
	if($id == 1){
		$noOfTask = $value;
	}
}
$output = array(
	'projectList' => $projectList,
	'kpiList' => $subjectList,
	// 'weekList' => $weekList,
	'noOfTask' => $noOfTask
);
echo json_encode($output);
?>

<?php
function getStartAndEndDate($week, $year) {
 	$dto = new DateTime();
	$dto->setISODate($year, $week);
	$ret['week_start'] = $dto->format('d-M-Y');
	$dto->modify('+6 days');
	$ret['week_end'] = $dto->format('d-M-Y');
	return $ret;
}
?>