<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers:content-type");
$conn=mysqli_connect("localhost","db","P@ssw0rd","ThreatModeler");
mysqli_set_charset($conn, 'utf8');
?>