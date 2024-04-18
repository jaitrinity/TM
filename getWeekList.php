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

$currentDate = date('Y-m-d');
// $currentDate = "2024-12-29";
$date = new DateTime($currentDate);
$year = $date->format("Y");
$week = $date->format("W");

$weekSql = "SELECT DISTINCT `Week` FROM `MyTask` where `EmpId`='$empId' and `ProjectName`='$project' and date_format(`CreateDate`,'%Y')=$year";
// echo $weekSql;
$weekQuery = mysqli_query($conn,$weekSql);
$rowCount = mysqli_num_rows($weekQuery);
$submitWeekList = array();
if($rowCount != 0){
	while($weekRow = mysqli_fetch_assoc($weekQuery)){
		array_push($submitWeekList, $weekRow["Week"]);
	}
}

$weekList = array();
$weekJson = array(
	'week' => "Yesterday",
	'weekDate' => date('d-M-Y', strtotime('-1 day'))
);
array_push($weekList, $weekJson);
$weekJson = array(
	'week'=> "Today",
	'weekDate' => $date->format("d-M-Y")
);
array_push($weekList, $weekJson);

for($i=1;$i<=$week;$i++){
	$weekNo = $i;
	$week_array = getStartAndEndDate($weekNo,$year);
	$startWeekDate = $week_array["week_start"];
	$endWeekDate = $week_array["week_end"];

	$weekDates = $startWeekDate." to ".$endWeekDate;
	$weeks = "Week ".$weekNo;
	if(in_array($weeks,$submitWeekList)){

	}
	else{
		$weekJson = array(
			'week'=> $weeks,
			'weekDate' => $weekDates
		);
		array_push($weekList, $weekJson);
	}
}
$output = array(
	'weekList' => $weekList
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