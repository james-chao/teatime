<?
header( 'Content-Type: text/html; charset=utf-8' );

set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/'); 
require_once('Classes/PHPExcel.php');
//require_once 'Classes/PHPExcel/Writer/Excel2007.php';
//require_once 'Classes/PHPExcel/Writer/Excel5.php';
include 'Classes/PHPExcel/IOFactory.php';
	
require "conn.php";
ini_set('memory_limit', '-1'); //unlimited memory usage of server
$sno=$_REQUEST['sno'];
if($sno!="" && intval($sno)>0)
{
	$strSql="SELECT sname FROM ttstoretb WHERE sno=$sno";
	$data_rows=mysql_query($strSql);
	if(list($sname) = mysql_fetch_row($data_rows)) 
	{
		downprod($sno,$sname);
		exec("del ".dirname(__FILE__)."\\*.xls /q"); //delete the .xls file.
	}

}
else
	echo "no data";


function downprod($sno,$sname){

	// Must be fresh start
    if( headers_sent() ) die('Headers Sent');

	// Required for some browsers
	if(ini_get('zlib.output_compression')) ini_set('zlib.output_compression', 'Off'); 
	
	$filename = outputfile($sno,$sname);
	$fullPath =dirname(__FILE__)."\\".$filename;
	
	if(file_exists($fullPath)){ 
		$fsize = filesize($fullPath); 
		//header('Content-Description: File Transfer');
		//header("Accept-Ranges:bytes"); 
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers
		//header("Cache-Control: no-cache",true); //In seconds
		//header('Content-type: application/octet-stream');
		//header("Content-Type: application/force-download");
		header('Content-Type: application/vnd.ms-excel;');                 // This should work for IE & Opera
   		header("Content-type: application/x-msexcel");                    // This should work for the rest 
		//header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
		header("Content-Disposition: attachment; filename=".basename($filename).";");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".$fsize);
		//ob_clean();
		//flush(); 
		@readfile($fullPath);
		
	}

}


function outputfile($sno,$sname)
{
	
	$objPHPExcel = new PHPExcel();
	
	if($sno!="" && intval($sno)>0)
	{
		$objPHPExcel=bydetail($objPHPExcel,$sno,$sname);
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$filename=Savetofile($objPHPExcel,$sno);
		return $filename;
	}
}

function Savetofile($objPHPExcel,$sno)
{
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$filename = "TeaTime_Store".$sno."_".date("Ymd").'.xls';
	$objWriter->save(dirname(__FILE__)."\\".$filename);
	
	return $filename;

}

function bydetail($objPHPExcel,$sno,$sname)
{
	global $link;
	$cols=array('A','B','C','D','E','F','G','H','I');
	
	$objPHPExcel->setActiveSheetIndex(0);
	$sheet=$objPHPExcel->getActiveSheet();
	$sheet->setTitle('商品列表');
	foreach($cols as $cl)
	{
		$sheet->getColumnDimension($cl)->setAutoSize(true); 
	}
	$sheet->getDefaultStyle()->getFont()->setSize(10); 
	//$sheet->getDefaultStyle()->getFont()->setName('Arial');
	
	$i=0;
	$k=2; //data from row 2
	if($i==0)
	{								

		$sheet->setCellValue('A1','店名');
		$sheet->setCellValue('B1','類別');
		$sheet->setCellValue('C1','品名');
		$sheet->setCellValue('D1','大小');
		$sheet->setCellValue('E1','單價');
		$sheet->setCellValue('F1','冷熱');
		$sheet->setCellValue('G1','甜淡');
		$sheet->setCellValue('H1','產品說明');
		$sheet->setCellValue('I1','特殊功效');
		//set font style
		$styleArray = array(
			'font' => array('bold' => true)
		);
		$sheet->getStyle('A1:I1')->applyFromArray($styleArray);
		$sheet->getStyle('A1:I1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE); 
		
	}
	
	$sql="SELECT cname,pname,psize,pprice,ptemp,ptaste,pintro,psuit FROM ttcatetb C,ttprodtb P WHERE P.sno=$sno AND C.cno=P.cno";

	$data_rows=mysql_query($sql);
	
	while(list($cname,$pname,$psize,$pprice,$ptemp,$ptaste,$pintro,$psuit)=mysql_fetch_row($data_rows))
	{

		$i++;
		
		$sheet->setCellValue('A'.$k,$sname);
		$sheet->setCellValue('B'.$k,$cname);
		$sheet->setCellValue('C'.$k,$pname);
		$sheet->setCellValue('D'.$k,$psize);
		$sheet->setCellValue('E'.$k,$pprice);
		$sheet->setCellValue('F'.$k,$ptemp);
		$sheet->setCellValue('G'.$k,$ptaste);
		$sheet->setCellValue('H'.$k,$pintro);
		$sheet->setCellValue('I'.$k,$psuit);
		
		$k++;
		
	}

	
	$databorder=array('borders' => array(
		'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN) )
	);
	$sheet->getStyle('A1:I'.($k-1))->applyFromArray($databorder);
	
	return $objPHPExcel;

}
?>
