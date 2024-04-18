<?php
include("dbConfiguration.php");
require 'SendMailClass.php';

$sql = "SELECT DISTINCT mt.RM_EmpId, e.Name, e.Email FROM MyTask mt join Employees e on mt.RM_EmpId = e.EmpId where mt.RM_Rating is null and mt.RM_EmpId !=''";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$rmEmpId = $row["RM_EmpId"];
	$rmName = $row["Name"];
	// $rmEmail = $row["Email"];
	$rmEmail = "jai.prakash@trinityapplab.co.in";

	$table = "<table border=1 cellspacing=0 cellpadding=5>";
	$table .= "<thead>";
	$table .= "<tr>";
	$table .= "<th>TaskId</th> <th>Name</th> <th>Project</th> <th>Week</th> <th>No of task </th>";
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= "<tbody>";
	$myTaskSql = "SELECT mt.TaskId, e.Name, mt.ProjectName, mt.Week, mt.NoOfTask FROM MyTask mt join Employees e on mt.EmpId = e.EmpId where mt.RM_EmpId='$rmEmpId' and mt.RM_Rating is null order by mt.TaskId desc";
	$myTaskQuery = mysqli_query($conn,$myTaskSql);
	while($myTaskRow = mysqli_fetch_assoc($myTaskQuery)){
		$taskId = $myTaskRow["TaskId"];
		$name = $myTaskRow["Name"];
		$project = $myTaskRow["ProjectName"];
		$week = $myTaskRow["Week"];
		$noOfTask = $myTaskRow["NoOfTask"];

		$table .= "<tr>";
		$table .= "<td>$taskId</td> <td>$name</td> <td>$project</td> <td>$week</td> <td>$noOfTask</td>";
		$table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "</table>";

	$msg = "Dear $rmName,<br><br>";
	$msg .= "Below task is pending for your rating:<br><br>";
	$msg .= $table;
	$msg .= "<br><br>";
	$msg .= "Regards,<br>";
	$msg .= "Trinity automation team.";

	$subject = "Pending task for rating";
	$sendMailObj = new SendMailClass();
	$sendMailStatus = $sendMailObj->sendMail($rmEmail, $subject, $msg, null);

	header('Content-Type: text/html');
	echo $msg;
}


$sql = "SELECT DISTINCT mt.SU_EmpId, e.Name, e.Email FROM MyTask mt join Employees e on mt.SU_EmpId = e.EmpId where mt.RM_Rating is not null and mt.SU_Rating is null and mt.SU_EmpId !=''";
$query = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($query)){
	$suEmpId = $row["SU_EmpId"];
	$suName = $row["Name"];
	// $suEmail = $row["Email"];
	$suEmail = "jai.prakash@trinityapplab.co.in";

	$table = "<table border=1 cellspacing=0 cellpadding=5>";
	$table .= "<thead>";
	$table .= "<tr>";
	$table .= "<th>TaskId</th> <th>Name</th> <th>Project</th> <th>Week</th> <th>No of task </th>";
	$table .= "</tr>";
	$table .= "</thead>";
	$table .= "<tbody>";
	$myTaskSql = "SELECT mt.TaskId, e.Name, mt.ProjectName, mt.Week, mt.NoOfTask FROM MyTask mt join Employees e on mt.EmpId = e.EmpId where mt.SU_EmpId='$suEmpId' and mt.SU_Rating is null order by mt.TaskId desc";
	$myTaskQuery = mysqli_query($conn,$myTaskSql);
	while($myTaskRow = mysqli_fetch_assoc($myTaskQuery)){
		$taskId = $myTaskRow["TaskId"];
		$name = $myTaskRow["Name"];
		$project = $myTaskRow["ProjectName"];
		$week = $myTaskRow["Week"];
		$noOfTask = $myTaskRow["NoOfTask"];

		$table .= "<tr>";
		$table .= "<td>$taskId</td> <td>$name</td> <td>$project</td> <td>$week</td> <td>$noOfTask</td>";
		$table .= "</tr>";
	}
	$table .= "</tbody>";
	$table .= "</table>";

	$msg = "Dear $suName,<br><br>";
	$msg .= "Below task is pending for your rating:<br>";
	$msg .= $table;
	$msg .= "<br><br>";
	$msg .= "Regards,<br>";
	$msg .= "Trinity automation team.";

	$subject = "Pending task for rating";
	$sendMailObj = new SendMailClass();
	$sendMailStatus = $sendMailObj->sendMail($suEmail, $subject, $msg, null);

	header('Content-Type: text/html');
	echo $msg;

}
?>