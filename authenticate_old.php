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

$mobile = $jsonData->mobile;
$email = $jsonData->email;
$password = $jsonData->password;

$type="";
$empArr = array();
if($email == null){
	$sql = "SELECT e.EmpId, e.Name, e.RoleId, e.Theme, rm.Role, rm.Subject FROM Employees e join RoleMaster rm on e.RoleId = rm.RoleId WHERE e.Mobile = '$mobile' and e.Password = BINARY('$password') and e.IsActive=1";
	$type = "mobile";
}
else{
	$sql = "SELECT e.EmpId, e.Name, e.RoleId, e.Theme, rm.Role, rm.Subject FROM Employees e join RoleMaster rm on e.RoleId = rm.RoleId WHERE e.Email = BINARY('$email') and e.Password = BINARY('$password') and e.IsActive=1";
	$type = "email";
}
	
$query = mysqli_query($conn,$sql);

if(mysqli_num_rows($query) != 0){
	$row = mysqli_fetch_assoc($query);
	$empId = $row["EmpId"];
	$empName = $row["Name"];
	$roleId = $row["RoleId"];
	$role = $row["Role"];
	$theme = $row["Theme"];
	$themeExp = explode(":", $theme);
	$themeOption = $themeExp[0];
	$themeColor = $themeExp[1];
	$subject = $row["Subject"];
	$subList = explode(",", $subject);

	
	$empJson = array(
		'empId' => $empId,
		'name' => $empName,
		'roleId' => $roleId,
		'role' => $role,
		'themeOption' => $themeOption,
		'themeColor' => $themeColor,
		'subject' => $subList
	);
	$output = array(
		'code' => 200,
		'message' => 'SUCCESSFUL',
		'data' => $empJson
	);
	echo json_encode($output);
}
else{
	$output = array(
		'code' => 204,
		'message' => "Either $type or password is incorrect, please try again."
	);
	echo json_encode($output);
}
?>