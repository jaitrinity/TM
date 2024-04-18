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

$email = $jsonData->email;
$password = $jsonData->password;

$empArr = array();
// $sql = "SELECT e.EmpId, e.Name, e.RoleId, e.Password, e.IsActive, e.Theme, rm.Role, rm.Subject FROM Employees e join RoleMaster rm on e.RoleId = rm.RoleId WHERE e.Email = BINARY('$email')";
$sql = "SELECT e.EmpId, e.Name, e.RoleId, e.Password, e.IsActive, e.Theme, rm.Role FROM Employees e join RoleMaster rm on e.RoleId = rm.RoleId WHERE e.Email = BINARY('$email')";
	
$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query) != 0){
	$row = mysqli_fetch_assoc($query);
	$myPassword = $row["Password"];
	$isActive = $row["IsActive"];
	$isEq = strcmp($myPassword,$password);
	if($isEq !=0){
		$output = array(
			'code' => 404,
			'message' => 'Password incorrect, please try again'
		);
		echo json_encode($output);
		return;
	}
	else if($isActive != 1){
		$output = array(
			'code' => 404,
			'message' => 'Given email is inactive, please try again'
		);
		echo json_encode($output);
		return;
	}

	$empId = $row["EmpId"];
	$empName = $row["Name"];
	$roleId = $row["RoleId"];
	$role = $row["Role"];
	$theme = $row["Theme"];
	$themeExp = explode(":", $theme);
	$themeOption = $themeExp[0];
	$themeColor = $themeExp[1];
	// $subject = $row["Subject"];
	// $subList = explode(",", $subject);

	$empJson = array(
		'empId' => $empId,
		'name' => $empName,
		'roleId' => $roleId,
		'role' => $role,
		'themeOption' => $themeOption,
		'themeColor' => $themeColor
		// 'subject' => $subList
	);
	$output = array(
		'code' => 200,
		'message' => 'SUCCESSFUL',
		'data' => $empJson
	);
	echo json_encode($output);

	require 'LoggerClass.php';
	$classObj = new LoggerClass();
	$classObj->insertLog($empId, "Login");
}
else{
	$output = array(
		'code' => 204,
		'message' => 'Invalid email, please try again.'
	);
	echo json_encode($output);
}
?>