<?php 
include("dbConfiguration.php");
$sql = "SELECT * FROM `Employees` where (`Password` is null or `Password`='')";
$success = 0;
$fail = 0;
$query=mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$id = $row["Id"];
	// $empId = $row["EmpId"];
	$passTxt = $row["PassTxt"];
	$encode = base64_encode($passTxt);
	// $decode = base64_decode($encode);

	$passEnc = "UPDATE `Employees` set `Password` = '$encode' where `Id` = $id";
	if(mysqli_query($conn,$passEnc)){
		$success++;
	}
	else{
		$fail++;
	}
}
$output = array('success' => $success, 'fail' => $fail);
echo json_encode($output);

?>