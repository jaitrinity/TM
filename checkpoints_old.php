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

$weekSql = "SELECT DISTINCT `Week` FROM `MyTask` where `EmpId`='$empId'";
// echo $weekSql;
$weekQuery = mysqli_query($conn,$weekSql);
$rowCount = mysqli_num_rows($weekQuery);
$subWeekList = array();
if($rowCount != 0){
	while($weekRow = mysqli_fetch_assoc($weekQuery)){
		array_push($subWeekList, $weekRow["Week"]);
	}
}


$sql="SELECT * FROM `Checkpoints`";
$query = mysqli_query($conn,$sql);
$projectList = array();
$weekList = array();
$noOfTask;
while($row = mysqli_fetch_assoc($query)){
	$id = $row["Id"];
	$desc = $row["Description"];
	$value = $row["Value"];
	$valueList = explode(",", $value);
	if($id == 1){
		$projectList = $valueList;
	}
	else if($id == 2){
		// if(count($subWeekList) == 0){
		// 	$weekList = $valueList;
		// }
		// else{
		// 	for($w=0;$w<count($valueList);$w++){
		// 		$loopWeek = $valueList[$w];
		// 		if(in_array($loopWeek,$subWeekList)){

		// 		}
		// 		else{
		// 			array_push($weekList, $loopWeek);
		// 		}
		// 	}
		// }
		$currentYear = date('Y');
		$firstDate = $currentYear.'-01-01';
		$currentDate = date('Y-m-d');
		// $currentDate = $currentYear.'-02-15';
		$dateList = getDatesFromRange($firstDate, $currentDate);
		$noOfDays = 0;
		$weekDatesList = array();
		for($i=0;$i<count($dateList);$i++){
			$noOfDays++;
			$divi = $noOfDays/7;
			$week = round($divi);
			$mod = $noOfDays%7;

			$thisDate = $dateList[$i];
			array_push($weekDatesList, $thisDate);
			if($mod == 0){
				// $weekDateImp = implode(",", $weekDatesList);
				$weekDateImp = $weekDatesList[0].' to '.$weekDatesList[count($weekDatesList)-1];
				$weekNo = 'Week '.$week;
				// if(in_array($weekNo,$subWeekList)){
				// 	$weekDatesList = array();
				// }
				// else{
					$weekJson = array(
						'week'=> $weekNo,
						'weekDate' => $weekDateImp
					);
					array_push($weekList, $weekJson);
					$weekDatesList = array();
				// }	
			}
			else{
				if($noOfDays > 7){
					$week++;
				}
				if($i == (count($dateList)-1)){
					$weekDateImp = $weekDatesList[0].' to '.$weekDatesList[count($weekDatesList)-1];
					$weekNo = 'Week '.$week;
					// if(in_array($weekNo,$subWeekList)){
					// 	$weekDatesList = array();
					// }
					// else{
						$weekJson = array(
							'week'=> $weekNo,
							'weekDate' => $weekDateImp
						);
						array_push($weekList, $weekJson);
						$weekDatesList = array();
					// }
				}
			}
		}

		// $dualSql = "SELECT DATEDIFF('$currentDate', '$firstDate')+1 AS NoOfDay";
		// $dualQuery = mysqli_query($conn,$dualSql);
		// $dualRow = mysqli_fetch_assoc($dualQuery);
		// $noOfDays = $dualRow["NoOfDay"];
		// $week = $noOfDays/7;
		// $mod = $noOfDays%7;
		// if($mod != 0){
		// 	$week++;
		// }

		// for($w=1;$w<=$week;$w++){
		// 	$loopWeek = "Week".$w;
		// 	if(in_array($loopWeek,$subWeekList)){

		// 	}
		// 	else{
		// 		array_push($weekList, $loopWeek);
		// 	}
		// }
		
	}
	else if($id == 3){
		$noOfTask = $value;
	}
}
$output = array(
	'projectList' => $projectList,
	'weekList' => $weekList,
	'noOfTask' => $noOfTask
);
echo json_encode($output);
?>

<?php
function getDatesFromRange($start, $end, $format = 'Y-m-d') {     
    // Declare an empty array
    $array = array();
      
    // Variable that store the date interval
    // of period 1 day
    $interval = new DateInterval('P1D');
  
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
  
    // Use loop to store date into array
    foreach($period as $date) {                 
        $array[] = $date->format($format); 
    }
  
    // Return the array elements
    return $array;
}
?>