<?php
include("dbConfiguration.php");
$methodType = $_SERVER['REQUEST_METHOD'];
if($methodType != "POST"){
	$output = array('code' => 405, 'message' => 'Invalid method Type');
	echo json_encode($output);
	return;
}
$insertType = $_REQUEST["insertType"];

$json = file_get_contents('php://input');
$jsonData=json_decode($json);

if($insertType == "employee"){
	$name = $jsonData->name;
	$mobile = $jsonData->mobile;
	$emailId = $jsonData->emailId;
	$roleId = $jsonData->roleId;
	$rmId = $jsonData->rmId;

	$sql = "SELECT * from `Employees` where `Email` = ? and `IsActive` = 1";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("s", $emailId);
	$stmt->execute();
	$query = $stmt->get_result();
	$rowCount = mysqli_num_rows($query);
	if($rowCount != 0){
		$output = array(
			'code' => 204, 
			'message' => "Employee already exist on $emailId"
		);
		echo json_encode($output);
		return;
	}

	$configSql = "SELECT (`EmpCount`+1) as `EmpCount` FROM `Configuration`";
	$configQuery = mysqli_query($conn,$configSql);
	$configRow = mysqli_fetch_assoc($configQuery);
	$empCount = $configRow["EmpCount"];
	$employeeId = 'tr'.$empCount;
	$passTxt = rand();
	$password = base64_encode($passTxt);

	$sql = "INSERT INTO `Employees`(`EmpId`, `Name`, `Mobile`, `Email`, `PassTxt`, `Password`, `RoleId`, `RMId`) VALUES (?,?,?,?,?,?,?,?)";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("ssssssis", $employeeId, $name, $mobile, $emailId, $passTxt, $password, $roleId, $rmId);
	if($stmt->execute()){

		$output = array(
			'code' => 200, 
			'message' => "Employee successfully inserted"
		);
		echo json_encode($output);

		$updateConfig = "UPDATE `Configuration` set `EmpCount` = $empCount";
		mysqli_query($conn,$updateConfig);
	}
}
?>