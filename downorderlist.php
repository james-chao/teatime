<?
header( 'Content-Type: text/html; charset=utf-8' );

set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/'); 
require_once('Classes/PHPExcel.php');
//require_once 'Classes/PHPExcel/Writer/Excel2007.php';
//require_once 'Classes/PHPExcel/Writer/Excel5.php';
include 'Classes/PHPExcel/IOFactory.php';

require_once "conn.php";
ini_set('memory_limit', '-1'); //unlimited memory usage of server


$objPHPExcel = new PHPExcel();
$objPHPExcel->createSheet(1);
$objPHPExcel->createSheet(2);

function bydetail($objPHPExcel,$orderno,$cycno)
{

	$objPHPExcel->setActiveSheetIndex(0);
	$sheet=$objPHPExcel->getActiveSheet();
	$sheet->setTitle('明細');
	$sheet->mergeCells('A1:F2'); 
	$sheet->mergeCells('A3:F3');
	$sheet->mergeCells('A4:F4');
	$sheet->mergeCells('A5:F5');
	$sheet->mergeCells('A6:F6');
	$sheet->getColumnDimension('A')->setAutoSize(true); 
	//$sheet->getColumnDimension('B')->setAutoSize(true); 
	//$sheet->getColumnDimension('C')->setAutoSize(true); 
	//$sheet->getColumnDimension('D')->setAutoSize(true); 
	//$sheet->getColumnDimension('E')->setAutoSize(true); 
	$sheet->getColumnDimension('F')->setAutoSize(true); 
	$sheet->getDefaultStyle()->getFont()->setSize(10); 
	//$sheet->getDefaultStyle()->getFont()->setName('Arial');
	
	$strSql="SELECT pname,L.pprice,ppic,uoption,pqty,unick,content,ordertime,sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$cycno  ORDER BY unick,ordertime desc";

	$data_rows=mysql_query($strSql);
	$i=0;
	$k=9; //data from row 8
	
	while(list($pname,$pprice,$ppic,$uoption,$pqty,$unick,$content,$ordertime,$sname,$stel,$sfax,$spic,$saddr,$sintro)=mysql_fetch_row($data_rows))
	{
		
		if($i==0)
		{
			$sheet->setCellValue('A1',$sname);
			$sheet->setCellValue('A3','TEL:'.$stel);
			$sheet->setCellValue('A4','FAX:'.$sfax);
			$sheet->setCellValue('A5',$saddr);
			$sheet->setCellValue('A6',$sintro);
			$sheet->setCellValue('A8','編號');
			$sheet->setCellValue('B8','品名');
			$sheet->setCellValue('C8','訂購人');
			$sheet->setCellValue('D8','選項');
			$sheet->setCellValue('E8','說明');
			$sheet->setCellValue('F8','單價');
			$sheet->setCellValue('G8','數量');
			$sheet->setCellValue('H8','小計');
			$sheet->setCellValue('I8','訂購時間');
			$sheet->setCellValue('J8','圖片');
			//set font style
			$styleArray = array(
				'font' => array('bold' => true)
			);
			$sheet->getStyle('A8:J8')->applyFromArray($styleArray);
			$sheet->getStyle('A8:J8')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE); 
			
			//設定漸層背景顏色雙色(灰/白) 
			$snamestyle=array( 
					'font' => array('bold' => true), 
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					), 
					'borders' => array(
						'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
					), 
					'fill' => array( 
						'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR, 
						'rotation'=> 90, 
						'startcolor' => array('rgb' => 'FFDCDC'), 
						'endcolor'=> array('argb' => 'FF000000') 
					 ) 
				);
			$sheet->getStyle('A1:I2')->applyFromArray($snamestyle);
			
			//set store pic
			if($spic!="" && file_exists("storepic/$ppic"))
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();    
				$objDrawing->setName("$sname");    
				$objDrawing->setDescription("$remark");    
				$objDrawing->setPath("storepic/$spic");    
				$objDrawing->setHeight(35);    
				$objDrawing->setCoordinates('G1');    
				$objDrawing->setOffsetX(10);    
				$objDrawing->setRotation(15);    
				$objDrawing->getShadow()->setVisible(true);    
				$objDrawing->getShadow()->setDirection(36);    
				$objDrawing->setWorksheet($sheet);
			}
		}
		$i++;
		
		//$sum=$pqty*$pprice;
		//$totalcost +=$sum;
		$sheet->setCellValue('A'.$k,$i);
		$sheet->setCellValue('B'.$k,$pname);
		$sheet->setCellValue('C'.$k,$unick);
		$sheet->setCellValue('D'.$k,$uoption);
		$sheet->setCellValue('E'.$k,$content);
		$sheet->setCellValue('F'.$k,$pprice);
		$sheet->setCellValue('G'.$k,$pqty);
		//$sheet->setCellValue('H'.$k,$sum);
		$sheet->setCellValue('H'.$k,'=F'.$k.'*'.'G'.$k); //sub count
		$sheet->setCellValue('I'.$k,$ordertime);
		$sheet->setCellValue('J'.$k,' '); //for pic
		if($ppic!="" && file_exists("prodpic/$ppic"))
		{
			$objDrawing = new PHPExcel_Worksheet_Drawing();    
			$objDrawing->setName("$pname");    
			$objDrawing->setDescription('$remark');    
			$objDrawing->setPath("prodpic/$ppic");    
			$objDrawing->setHeight(12);    
			$objDrawing->setCoordinates('J'.$k);    
			$objDrawing->setOffsetX(10);    
			$objDrawing->setRotation(15);    
			$objDrawing->getShadow()->setVisible(true);    
			$objDrawing->getShadow()->setDirection(36);    
			$objDrawing->setWorksheet($sheet); 
		}
		$k++;
		
	}
	$sheet->setCellValue('F'.$k,'合計:');
	$objStyleCK = $sheet->getStyle('A'.$k.':'.'J'.$k);
	$objStyleCK->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);    
	$objFontCK = $objStyleCK->getFont();    
	$objFontCK->setName('Courier New');    
	$objFontCK->setSize(12);    
	$objFontCK->setBold(true);    
	$objFontCK->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);    
	$objFontCK->getColor()->setARGB('FFFF0000');    
	$objAlignCK = $objStyleCK->getAlignment();    
	$objAlignCK->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);    
	$objAlignCK->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);    
	$objFillCK = $objStyleCK->getFill();    
	$objFillCK->setFillType(PHPExcel_Style_Fill::FILL_SOLID);    
	$objFillCK->getStartColor()->setARGB('FFEEEEEE');
	
	$databorder=array('borders' => array(
		'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN) )
	);
	$sheet->getStyle('A8:J'.($k-1))->applyFromArray($databorder);
	// if there is no order data;
	if($i==0) 
	{
		$sheet->setCellValue('G'.$k,'0'); //sum qty
		$sheet->setCellValue('H'.$k,'0'); //sum price
	}
	else
	{
		$sheet->setCellValue('G'.$k,'=SUM(G9:G'.($k-1).')'); //sum qty
		$sheet->setCellValue('H'.$k,'=SUM(H9:H'.($k-1).')'); //sum price
	}
	return $objPHPExcel;

}
function byproduct($objPHPExcel,$orderno,$cycno)
{

	$objPHPExcel->setActiveSheetIndex(1);
	$sheet=$objPHPExcel->getActiveSheet();
	$sheet->setTitle('商品');
	$sheet->mergeCells('A1:F2'); 
	$sheet->mergeCells('A3:F3');
	$sheet->mergeCells('A4:F4');
	$sheet->mergeCells('A5:F5');
	$sheet->mergeCells('A6:F6');
	$sheet->getColumnDimension('A')->setAutoSize(true); 
	//$sheet->getColumnDimension('B')->setAutoSize(true); 
	$sheet->getColumnDimension('C')->setAutoSize(true); 
	//$sheet->getColumnDimension('D')->setAutoSize(true); 
	//$sheet->getColumnDimension('E')->setAutoSize(true); 
	//$sheet->getColumnDimension('F')->setAutoSize(true); 
	$sheet->getDefaultStyle()->getFont()->setSize(10); 
	//$sheet->getDefaultStyle()->getFont()->setName('Arial');
	
	$strSql="SELECT pname,L.pprice,ppic,sum(pqty),GROUP_CONCAT(CONCAT('#',unick,':'), CONCAT(uoption,'/ ',content)),sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$cycno GROUP BY pname,L.pprice,ppic ORDER BY L.pno";

	$data_rows=mysql_query($strSql);
	$i=0;
	$k=9; //data from row 8
	
	while(list($pname,$pprice,$ppic,$pqty,$remark,$sname,$stel,$sfax,$spic,$saddr,$sintro)=mysql_fetch_row($data_rows))
	{
		
		if($i==0)
		{
			$sheet->setCellValue('A1',$sname);
			$sheet->setCellValue('A3','TEL:'.$stel);
			$sheet->setCellValue('A4','FAX:'.$sfax);
			$sheet->setCellValue('A5',$saddr);
			$sheet->setCellValue('A6',$sintro);
			$sheet->setCellValue('A8','編號');
			$sheet->setCellValue('B8','品名');
			$sheet->setCellValue('C8','說明');
			$sheet->setCellValue('D8','單價');
			$sheet->setCellValue('E8','數量');
			$sheet->setCellValue('F8','小計');
			
			//set font style
			$styletitle = array(
				'font' => array('bold' => true)
			);
			$sheet->getStyle('A8:I8')->applyFromArray($styletitle);
			$sheet->getStyle('A8:I8')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE); 
			
			//設定漸層背景顏色雙色(灰/白) 
			$snamestyle=array( 
					'font' => array('bold' => true), 
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					), 
					'borders' => array(
						'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
					), 
					'fill' => array( 
						'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR, 
						'rotation'=> 90, 
						'startcolor' => array('rgb' => 'FFDCDC'), 
						'endcolor'=> array('argb' => 'FF000000') 
					 ) 
				);
			$sheet->getStyle('A1:F2')->applyFromArray($snamestyle);
			
			//set store pic
			if($spic!="")
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();    
				$objDrawing->setName("$sname");    
				$objDrawing->setDescription("$remark");    
				$objDrawing->setPath("storepic/$spic");    
				$objDrawing->setHeight(35);    
				$objDrawing->setCoordinates('G1');    
				$objDrawing->setOffsetX(10);    
				$objDrawing->setRotation(15);    
				$objDrawing->getShadow()->setVisible(true);    
				$objDrawing->getShadow()->setDirection(36);    
				$objDrawing->setWorksheet($sheet);
			}
		}
		$i++;
		
		//$sum=$pqty*$pprice;
		//$totalcost +=$sum;
		$sheet->setCellValue('A'.$k,$i);
		$sheet->setCellValue('B'.$k,$pname);
		$sheet->setCellValue('C'.$k,str_replace(",","\n",$remark));
		$sheet->setCellValue('D'.$k,$pprice);
		$sheet->setCellValue('E'.$k,$pqty);
		//$sheet->setCellValue('H'.$k,$sum);
		$sheet->setCellValue('F'.$k,'=D'.$k.'*'.'E'.$k); //sub count
		$k++;
		
	}
	$sheet->setCellValue('D'.$k,'合計:');
	$objStyleCK = $sheet->getStyle('A'.$k.':'.'F'.$k);
	$objStyleCK->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);    $objFontCK = $objStyleCK->getFont();    
	$objFontCK->setName('Courier New');    
	$objFontCK->setSize(12);    
	$objFontCK->setBold(true);    
	$objFontCK->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);    
	$objFontCK->getColor()->setARGB('FFFF0000');    
	$objAlignCK = $objStyleCK->getAlignment();    
	$objAlignCK->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);    
	$objAlignCK->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);    
	$objFillCK = $objStyleCK->getFill();    
	$objFillCK->setFillType(PHPExcel_Style_Fill::FILL_SOLID);    
	$objFillCK->getStartColor()->setARGB('FFEEEEEE');
	
	$databorder=array('borders' => array(
		'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN) )
	);
	$sheet->getStyle('A8:F'.($k-1))->applyFromArray($databorder);
	// if there is no order data;
	if($i==0) 
	{
		$sheet->setCellValue('E'.$k,'0'); //sum qty
		$sheet->setCellValue('F'.$k,'0'); //sum price
	}
	else
	{
		$sheet->setCellValue('E'.$k,'=SUM(E9:E'.($k-1).')'); //sum qty
		$sheet->setCellValue('F'.$k,'=SUM(F9:F'.($k-1).')'); //sum price
	}
	return $objPHPExcel;

}
function byperson($objPHPExcel,$orderno,$cycno)
{

	$objPHPExcel->setActiveSheetIndex(2);
	$sheet=$objPHPExcel->getActiveSheet();
	$sheet->setTitle('訂購人');
	$sheet->mergeCells('A1:D2'); 
	$sheet->mergeCells('A3:D3');
	$sheet->mergeCells('A4:D4');
	$sheet->mergeCells('A5:D5');
	$sheet->mergeCells('A6:D6');
	$sheet->getColumnDimension('A')->setAutoSize(true); 
	//$sheet->getColumnDimension('B')->setAutoSize(true); 
	$sheet->getColumnDimension('C')->setAutoSize(true); 
	//$sheet->getColumnDimension('D')->setAutoSize(true); 
	//$sheet->getColumnDimension('E')->setAutoSize(true); 
	//$sheet->getColumnDimension('F')->setAutoSize(true); 
	$sheet->getDefaultStyle()->getFont()->setSize(10); 
	//$sheet->getDefaultStyle()->getFont()->setName('Arial');
	//$sheet->getColumnDimension('A')->setWidth(30); 
	
	$strSql="SELECT unick,GROUP_CONCAT(pname,':NT$',L.pprice,'/',uoption,'/ 數量:',pqty),content,sum(pqty*L.pprice),sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$cycno GROUP BY unick ORDER BY unick,ordertime desc";
	$data_rows=mysql_query($strSql);
	$i=0;
	$k=9; //data from row 8
	
	while(list($unick,$remark,$content,$sum,$sname,$stel,$sfax,$spic,$saddr,$sintro)=mysql_fetch_row($data_rows))
	{
		if($i==0)
		{
			$sheet->setCellValue('A1',$sname);
			$sheet->setCellValue('A3','TEL:'.$stel);
			$sheet->setCellValue('A4','FAX:'.$sfax);
			$sheet->setCellValue('A5',$saddr);
			$sheet->setCellValue('A6',$sintro);
			$sheet->setCellValue('A8','編號');
			$sheet->setCellValue('B8','訂購人');
			$sheet->setCellValue('C8','內容');
			$sheet->setCellValue('D8','說明');
			$sheet->setCellValue('E8','小計');
			
			//set font style
			$styleArray = array(
				'font' => array('bold' => true)
			);
			$sheet->getStyle('A8:I8')->applyFromArray($styleArray);
			$sheet->getStyle('A8:I8')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_BLUE); 
			
			//設定漸層背景顏色雙色(灰/白) 
			$snamestyle=array( 
					'font' => array('bold' => true), 
					'alignment' => array(
						'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
						'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
					), 
					'borders' => array(
						'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN) 
					), 
					'fill' => array( 
						'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR, 
						'rotation'=> 90, 
						'startcolor' => array('rgb' => 'FFDCDC'), 
						'endcolor'=> array('argb' => 'FF000000') 
					 ) 
				);
			$sheet->getStyle('A1:D2')->applyFromArray($snamestyle);
			
			//set store pic
			if($spic!="")
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();    
				$objDrawing->setName("$sname");    
				$objDrawing->setDescription("$remark");    
				$objDrawing->setPath("storepic/$spic");    
				$objDrawing->setHeight(35);    
				$objDrawing->setCoordinates('E1');    
				$objDrawing->setOffsetX(10);    
				$objDrawing->setRotation(15);    
				$objDrawing->getShadow()->setVisible(true);    
				$objDrawing->getShadow()->setDirection(36);    
				$objDrawing->setWorksheet($sheet);
			}
		}
		$i++;
		
		//$sum=$pqty*$pprice;
		//$totalcost +=$sum;
		$sheet->setCellValue('A'.$k,$i);
		$sheet->setCellValue('B'.$k,$unick);
		$sheet->setCellValue('C'.$k,str_replace(",","\n",$remark));
		$sheet->setCellValue('D'.$k,$content);
		$sheet->setCellValue('E'.$k,$sum);

		$k++;
		
	}
	$sheet->setCellValue('D'.$k,'合計:');
	$objStyleCK = $sheet->getStyle('A'.$k.':'.'E'.$k);
	$objStyleCK->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER);    $objFontCK = $objStyleCK->getFont();    
	$objFontCK->setName('Courier New');    
	$objFontCK->setSize(12);    
	$objFontCK->setBold(true);    
	$objFontCK->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);    
	$objFontCK->getColor()->setARGB('FFFF0000');    
	$objAlignCK = $objStyleCK->getAlignment();    
	$objAlignCK->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);    
	$objAlignCK->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);    
	$objFillCK = $objStyleCK->getFill();    
	$objFillCK->setFillType(PHPExcel_Style_Fill::FILL_SOLID);    
	$objFillCK->getStartColor()->setARGB('FFEEEEEE');

	$databorder=array('borders' => array(
		'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN) )
	);
	$sheet->getStyle('A8:E'.($k-1))->applyFromArray($databorder);
	    
	if($i==0)
	{
		$sheet->setCellValue('E'.$k,'0'); //sum price
	}
	else
	{
		$sheet->setCellValue('E'.$k,'=SUM(E9:E'.($k-1).')'); //sum price
	}

	return $objPHPExcel;


}

function Savetofile($objPHPExcel,$orderno,$ctcycno)
{
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$filename = "TeaTime_Ord".$orderno."cyc".$ctcycno."_".date("Ymd").'.xls';
	$objWriter->save($filename);
}
function Exporttofile($objPHPExcel,$orderno,$cycno)
{
	$filename = "TeaTime_Ord".$orderno."cyc".$ctcycno."_".date("Ymd").'.xls';
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=$filename");
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}
$orderno=$_REQUEST['ono'];
$cycno=$_REQUEST['cycno'];
//$oderkey=$_COOKIE['teatimeorderkey'];
if($orderno!="" && intval($orderno)>0)
{
	$objPHPExcel=bydetail($objPHPExcel,$orderno,$cycno);
	$objPHPExcel=byproduct($objPHPExcel,$orderno,$cycno);
	$objPHPExcel=byperson($objPHPExcel,$orderno,$cycno);
	
	$objPHPExcel->setActiveSheetIndex(0);
	
	//Savetofile($objPHPExcel,$orderno,$cycno);
	Exporttofile($objPHPExcel,$orderno,$cycno);
}

?>