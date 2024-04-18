<?php 
// include("dbConfiguration.php");

// $currentDate = date('Y-m-d');
$currentDate = "2025-12-28";
$date = new DateTime($currentDate);
$year = $date->format("Y");
$week = $date->format("W");

$weekList = array();
for($i=1;$i<=$week;$i++){
	$weekNo = $i;
	$week_array = getStartAndEndDate($weekNo,$year);
	$startWeekDate = $week_array["week_start"];
	$endWeekDate = $week_array["week_end"];

	$weekDates = $startWeekDate." to ".$endWeekDate;
	$weeks = "Week ".$weekNo;
	
	$weekJson = array(
		'week'=> $weeks,
		'weekDate' => $weekDates
	);
	array_push($weekList, $weekJson);
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