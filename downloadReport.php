<?php 
include("dbConfiguration.php");
require '/var/www/trinityapplab.in/html/PHPExcel-1.8/Classes/PHPExcel.php';

$sql="SELECT e.Name, mt.ProjectName, mt.Week, mt.NoOfTask, e1.Name as RM_Name, mt.RM_Rating, mt.RM_Remark, mt.RM_RatingDate, e2.Name as SU_Name, mt.SU_Rating, mt.SU_Remark, mt.SU_RatingDate, mt.CreateDate FROM MyTask mt join Employees e on mt.EmpId = e.EmpId left join Employees e1 on mt.RM_EmpId = e1.EmpId left join Employees e2 on mt.SU_EmpId = e2.EmpId where 1=1 order by e.Name";
$query = mysqli_query($conn,$sql);
$taskList = array();
while($row = mysqli_fetch_assoc($query)){
	$taskObj = array(
		'name' => $row["Name"],
		'project' => $row["ProjectName"],
		'week' => $row["Week"],
		'noOfTask' => $row["NoOfTask"],
		'rmName' => $row["RM_Name"],
		'rmRating' => setBlank($row["RM_Rating"]),
		'rmRemark' => setBlank($row["RM_Remark"]),
		'rmRatingDate' => setBlank($row["RM_RatingDate"]),
		'suName' => setBlank($row["SU_Name"]),
		'suRating' => setBlank($row["SU_Rating"]),
		'suRemark' => setBlank($row["SU_Remark"]),
		'suRatingDate' => setBlank($row["SU_RatingDate"]),
		'submitDate' => $row["CreateDate"]
	);
	array_push($taskList, $taskObj);
}
// echo json_encode($taskList);

$objPHPExcel = new PHPExcel();
$border_style= array(
	'borders' => array(
		'allborders' => array(
			'style' => PHPExcel_Style_Border::BORDER_THIN,
			'color' => array(
				'argb' => '000000'
			)
		)
	),
	'alignment' => array(
		'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
	)
);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1',"Name")
    ->setCellValue('B1',"Project")
    ->setCellValue('C1',"Week")
    ->setCellValue('D1',"No Of Task")
    ->setCellValue('E1',"Submit Date")
    ->setCellValue('F1',"RM Name")
    ->setCellValue('G1',"RM Rating")
    ->setCellValue('H1',"RM Remark")
    ->setCellValue('I1',"RM Rating Date")
    ->setCellValue('J1',"SU Name")
    ->setCellValue('K1',"SU Rating")
    ->setCellValue('L1',"SU Remark")
    ->setCellValue('M1',"SU Rating Date");

$cellRow=1;
for($i=0;$i<count($taskList);$i++){
	$cellRow++;

	$taskobj = $taskList[$i];

	$objPHPExcel->setActiveSheetIndex(0)
	->setCellValue('A'.$cellRow,$taskobj["name"])
	->setCellValue('B'.$cellRow,$taskobj["project"])
	->setCellValue('C'.$cellRow,$taskobj["week"])
	->setCellValue('D'.$cellRow,$taskobj["noOfTask"])
	->setCellValue('E'.$cellRow,$taskobj["submitDate"])
	->setCellValue('F'.$cellRow,$taskobj["rmName"])
	->setCellValue('G'.$cellRow,$taskobj["rmRating"])
	->setCellValue('H'.$cellRow,$taskobj["rmRemark"])
	->setCellValue('I'.$cellRow,$taskobj["rmRatingDate"])
	->setCellValue('J'.$cellRow,$taskobj["suName"])
	->setCellValue('K'.$cellRow,$taskobj["suRating"])
	->setCellValue('L'.$cellRow,$taskobj["suRemark"])
	->setCellValue('M'.$cellRow,$taskobj["suRatingDate"]);
}
$dateTimeFormat = "yyyy-mm-dd HH:mm:ss";
$sheet = $objPHPExcel->getActiveSheet();
$sheet->getStyle("A1:M1")->getFont()->setBold(true);
for($ii=1;$ii<=$cellRow;$ii++){
	$sheet->getStyle("A".$ii.":M".$ii)->applyFromArray($border_style);
	if($ii != 1){
		$submitDate = $sheet->getCellByColumnAndRow(4, $ii)->getValue();
	    $sheet->setCellValueByColumnAndRow(4, $ii,  PHPExcel_Shared_Date::PHPToExcel($submitDate));
		$sheet->getStyleByColumnAndRow(4, $ii) ->getNumberFormat()->setFormatCode($dateTimeFormat);

   		$rmRatingDate = $sheet->getCellByColumnAndRow(8, $ii)->getValue();
   		if($rmRatingDate !=""){
   			$sheet->setCellValueByColumnAndRow(8, $ii,  PHPExcel_Shared_Date::PHPToExcel($rmRatingDate));
		    $sheet->getStyleByColumnAndRow(8, $ii) ->getNumberFormat()->setFormatCode($dateTimeFormat);
   		}
		   
	    $suRatingDate = $sheet->getCellByColumnAndRow(12, $ii)->getValue();
	    if($suRatingDate !=""){
	    	$sheet->setCellValueByColumnAndRow(12, $ii,  PHPExcel_Shared_Date::PHPToExcel($suRatingDate));
			$sheet->getStyleByColumnAndRow(12, $ii) ->getNumberFormat()->setFormatCode($dateTimeFormat);
	    }	
   }
}

$filename='TastReport';
$objPHPExcel->getActiveSheet()->setTitle($filename);
$objPHPExcel->setActiveSheetIndex(0);


header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: max-age=0');
// $fileExt = "xls";
$fileExt = "xlsx";
if($fileExt == "xls"){
	header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
}
else if($fileExt == "xlsx"){
	header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
	$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel2007');
}
$objWriter->save('php://output');
exit;
?>

<?php
function setBlank($oldValue){
	return $oldValue == null ? "" : $oldValue;
}
?>