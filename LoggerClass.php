<?php 
class LoggerClass{
	public function insertLog($empId, $event){
		global $conn;

		$sql = "SELECT `Name` FROM `Employees` where `EmpId`='$empId'";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$query = $stmt->get_result();
		$row = mysqli_fetch_assoc($query);
		$empName = $row["Name"];
		
		$sql = "INSERT INTO `Logger`(`EmpId`, `Name`, `Event`) VALUES (?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("sss", $empId, $empName, $event);
		$stmt->execute();
	}
}
?>