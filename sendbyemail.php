<?php
header( 'Content-Type: text/html; charset=utf-8' );

set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/'); 
require_once('Classes/PHPExcel.php');
//require_once 'Classes/PHPExcel/Writer/Excel2007.php';
//require_once 'Classes/PHPExcel/Writer/Excel5.php';
include 'Classes/PHPExcel/IOFactory.php';

include_once(dirname(__FILE__) . '/Classes/utility.php');
//include_once(dirname(__FILE__) . '/Classes/quantaapp.php');
	
require "conn.php";

ini_set('memory_limit', '-1'); //unlimited memory usage of server

function hischeck($pdtyp,$odeadline)
{
	if($pdtyp=="none")
	{
		$ret=time()-strtotime($odeadline);
	}
	else //for cycles
	{
		$deadline_his=date("H:i:s",strtotime($odeadline));// take hour:min:sec parts from deadline
		$now4his=strtotime(date("Y-m-d")." ".$deadline_his); //reassemble the date for today
		$ret=time()-$now4his;
	}
	return $ret;
}
function dateDiff($startTime, $endTime) {
    $start = strtotime(date("Y-m-d",strtotime($startTime))); //just Y-m-d, no h:i:s
    $end = strtotime(date("Y-m-d",strtotime($endTime)));
    $timeDiff = $end - $start;
    return floor($timeDiff / (60 * 60 * 24));
}
function weekcycno($startTime, $endTime,$weekChk) {
	$start = date("Y-m-d",strtotime($startTime));
	$days = dateDiff($startTime, $endTime);
	$cyc=0; //the order created day
	
	for($x=1;$x<=$days;$x++) //from second day
	{
		$varday = strtotime("$start +$x day");
		$matched = in_array(date("w",$varday),explode("-",$weekChk));
		if($matched) $cyc++;
	}
	return $cyc;
}

$strSql="SELECT ono,ocreatetime,odeadline,operiod,omailflag FROM ttordertb WHERE oonoff='y' AND omailflag<>'y' AND ocreatetime<now()";
$data_rows=mysql_query($strSql);
//$i=0;

while(list($ono,$ocreatetime,$odeadline,$operiod,$omailflag) = mysql_fetch_row($data_rows)) 
{
	//$i++;

	//echo "$i=>$ono,$ocreatetime,$odeadline,$operiod,$omailflag"."<br>";
	$periodarr=array();
	$pdtyp="";
	$pdval="";
	//for period order
	$periodarr=explode("#",$operiod);
	$pdtyp=$periodarr[0];
	$pdval=$periodarr[1]; 
	$now=date("Y-m-d H:i:s");
	switch($pdtyp)
	{
		case "none":
			$ttcycno=0;
			$ctcycno=0;
			break;
		case "days": 
			$sndiff=dateDiff($ocreatetime,$now);
			$sediff=dateDiff($ocreatetime,$odeadline);
			$ttcycno=$sediff/$pdval; //all cycles: start to deadline cycles
			$ctcycno=$sndiff/$pdval; //current cycle
			break;
		case "weeks":
			$ttcycno=weekcycno($ocreatetime,$odeadline,$pdval); //all cycles
			$ctcycno=weekcycno($ocreatetime,$now,$pdval); //current cycle
			break;
	}
	
	//send mail on correct (days,weeks) cycle
	$hischeck=hischeck($pdtyp,$odeadline); //check deadline for hr:min:sec
	//echo "($ono,$odeadline,$now#$pdtyp#$hischeck#$omailflag#$ctcycno#$ttcycno)<br>";
	if($hischeck>=0 && ($pdtyp=="none" || (intval($omailflag)<$ctcycno && $ctcycno<=$ttcycno))) 
	{
		SendMail($ono,$ctcycno,$ttcycno);
	}
}
exec("del ".dirname(__FILE__)."\\*.xls /q"); //delete the .xls file.
/*
echo "<script>window.opener=null;window.close();</script>"; 
*/

function SendMail($ono,$ctcycno,$ttcycno){ // 傳送 mail

	$sql2="SELECT ono,unick,otitle,onote,uemail,odeadline,ocreatetime,sname,stel,scity,scounty,saddr,oorderkey FROM ttordertb AS O INNER JOIN ttstoretb AS S ON O.sno=S.sno WHERE O.ono=$ono AND oonoff='y' AND omailflag<>'y'";
	$data_rows2=mysql_query($sql2);
	if(list($ono,$unick,$title,$note,$email,$deadline,$createtime,$storename,$storetel,$scity,$scounty,$storeaddr,$oorderkey) = mysql_fetch_row($data_rows2)) 
	{
		//send app message
		$orderarr=array(
			'title'=>$title,
			'oorderkey'=>$oorderkey,
			'email'=>$email,
			'deadline'=>$deadline,
			'storename'=>$storename,
			'storetel'=>$storetel,
			'storeaddr'=>"(".$scity.$scounty.")".$storeaddr,
			'ono'=>$ono,
			'ok'=>$oorderkey
		);
		//sendAppMessage($unick,$orderarr);
		//echo "$ono,$unick,$title,$email,$deadline,$createtime,$storename,$storetel,$scity,$scounty,$storeaddr,$oorderkey"."<br>";
		$msg = "<div style='font-size:12px;'>============================================================ <br>
		邀約標題: $title <br>
		邀約密碼: <span style='color:blue;font-weight:bold;'>$oorderkey</span><br>
		發起人: $unick <br>
		E-mail: $email <br>
		終止時間: $deadline <br>
		邀約說明: $note <br>
		店家名稱: $storename <br>
		店家電話: $storetel <br>
		店家地址: ($scity$scounty) $storeaddr <br>
		訂購網址: [<a href='http://lttap01.rsquanta.com/ordermenu.php?ono=$ono&ok=$oorderkey'>請點我</a>] <br>
		============================================================ <br>
		下午茶:<a href='http://lttap01.rsquanta.com'>http://lttap01.rsquanta.com</a><div>";
		$subject = "TeaTime [$title] 邀約已結束...訂購清單"; 
		$subject = '=?utf-8?B?'.base64_encode("$subject").'?='; // 標題加密(防亂碼)
		$from = "james.chao@quantatw.com"; 
		$mailto="james.chao@quantatw.com";
		$cc=$email;
		$fromname="下午茶";

		$boundary = uniqid( ""); // 產生分隔字串
		// 設定MAIL HEADER
		$headers = '';
		$headers .= 'MIME-Version: 1.0'."\n";
		$headers .= 'Content-type: multipart/mixed; boundary="'.$boundary.'"; charset="UTF-8"'."\n"; //宣告分隔字串
		$headers .= 'From: '.$from."\n"; // 設定寄件者
		if($cc) $headers .="Cc: $cc\n"; //// 設定副件者
		//$headers .= 'X-Mailer: PHP/' . phpversion()."\n";
		// 信件內容開始
		$body = '--'.$boundary."\n";
		$body .= 'Content-type: text/html; charset="UTF-8"'."\n";// 信件本文header
		$body .= 'Content-Transfer-Encoding: 7bit'."\n\n";// 信件本文header
		$body .= $msg."\n"; // 本文內容
		//附加檔案處理
		
		$filename = outputfile($ono,$ctcycno); //撈出所有訂購資料，並依明細、商品及暱稱分頁
		
		$filename =dirname(__FILE__)."\\".$filename;
	
		if(file_exists($filename)){
			if(function_exists("finfo_open")){
				$finfo = finfo_open(FILEINFO_MIME);
				$mimeType=finfo_file($finfo,$filename);
				finfo_close($finfo);
			}
			elseif(function_exists("mime_content_type"))	
				$mimeType = mime_content_type($filename); // 判斷檔案類型
			
			if(!$mimeType)	$mimeType ="application/unknown"; // 若判斷不出則設為未知
						
			$fp = fopen($filename, "r"); // 開啟檔案

			$read = fread($fp, filesize($filename)); // 取得檔案內容 
			fclose($fp); // 關閉檔案
			$read = base64_encode($read);//使用base64編碼
			$read = chunk_split($read);  //把檔案所轉成的長字串切開成多個小字串
			$file = basename($filename); //傳回不包含路徑的檔案名稱(mail中會顯示的檔名)
	
			// 附檔處理開始
			$body .= '--'.$boundary ."\n";
			// 設定附加檔案HEADER
			$body .= 'Content-type: '.$mimeType.'; name='.$filename."\n";
			$body .= 'Content-transfer-encoding: base64'."\n";
			$body .= 'Content-disposition: attachment; filename='.$file."\n\n";
			// 加入附加檔案內容
			$body .= $read ."\n";
		}//處理附加檔案完畢
		$body .= "--$boundary--";//郵件結尾
	
		$ret=mail($mailto, $subject, $body, $headers); // 寄出信件
		if($ret==true){
			
			if($ctcycno==$ttcycno) $ctcycno='y';
			$sql="update ttordertb SET omailflag='$ctcycno' WHERE ono=$ono";
			mysql_query($sql);
		}
	}

}
function sendAppMessage($displayname,$ordarr)
{
global $util;

$workid=$util->myldap->getUserWorkid($displayname);
$mailbody="$displayname 您好,
以下是您本次的邀約明細
============================
邀約標題: $ordarr[title]
邀約密碼: $ordarr[oorderkey]
發起人: $displayname
E-mail: $ordarr[email]
終止時間: $ordarr[deadline]
店家名稱: $ordarr[storename]
店家電話: $ordarr[storetel]
店家地址: $ordarr[storeaddr]
訂購網址: http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[ono]&ok=$ordarr[oorderkey]
============================
請使用上述的網址登入查看訂購明細，謝謝。
-------------------------------------------
感謝您參與下午茶訂購系統 http://lttap01.rsquanta.com
系統聯絡人:james.chao@quantatw.com";
if($workid)
{
	$app=new quantaapp();
	$userlist=$app->registerList();
	$pos=strpos($userlist,$workid);
	if($pos!==false)
	{
		$msgdata=array(
			'ReceiveCompanyCode'=>'QCI', 	//收訊者公司別
			'ReceiveType'=>'3', 			//收訊者類別 3:person
			'ReceiveID'=>$workid,		//收訊者代號
			'Title'=>$ordarr['title'].'已結束',	//訊息標題	
			'Detail'=>$mailbody
		);
		$app->simpleMessage($msgdata);
	}
}
}

function outputfile($orderno,$ctcycno)
{
	
	$objPHPExcel = new PHPExcel();
	$objPHPExcel->createSheet(1);
	$objPHPExcel->createSheet(2);
	
	if($orderno!="" && intval($orderno)>0)
	{
		$objPHPExcel=bydetail($objPHPExcel,$orderno,$ctcycno);
		$objPHPExcel=byproduct($objPHPExcel,$orderno,$ctcycno);
		$objPHPExcel=byperson($objPHPExcel,$orderno,$ctcycno);
		
		$objPHPExcel->setActiveSheetIndex(0);
		
		$filename=Savetofile($objPHPExcel,$orderno,$ctcycno);
		return $filename;
	}
}

function Savetofile($objPHPExcel,$orderno,$ctcycno)
{
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$filename = "TeaTime_Ord".$orderno."cyc".$ctcycno."_".date("Ymd").'.xls';
	$objWriter->save(dirname(__FILE__)."\\".$filename);
	
	return $filename;

}

function bydetail($objPHPExcel,$orderno,$ctcycno)
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
	
	$strSql="SELECT pname,L.pprice,ppic,uoption,pqty,unick,content,ordertime,sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$ctcycno ORDER BY unick,ordertime desc";

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
			if($spic!=""  && file_exists("storepic/$ppic"))
			{
				$objDrawing = new PHPExcel_Worksheet_Drawing();    
				$objDrawing->setName("$sname");    
				$objDrawing->setDescription("$remark");    
				$objDrawing->setPath(dirname(__FILE__)."/storepic/$spic");    
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
		if($ppic!=""  && file_exists("prodpic/$ppic"))
		{
			$objDrawing = new PHPExcel_Worksheet_Drawing();    
			$objDrawing->setName("$pname");    
			$objDrawing->setDescription('$remark');    
			$objDrawing->setPath(dirname(__FILE__)."/prodpic/$ppic");    
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
function byproduct($objPHPExcel,$orderno,$ctcycno)
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
	
	$strSql="SELECT pname,L.pprice,ppic,sum(pqty),GROUP_CONCAT(CONCAT('#',unick,':'), CONCAT(uoption,'/ ',content,'/ 數量:',pqty)),sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$ctcycno GROUP BY pname,L.pprice,ppic ORDER BY L.pno";

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
				$objDrawing->setPath(dirname(__FILE__)."/storepic/$spic");    
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
function byperson($objPHPExcel,$orderno,$ctcycno)
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
	
	$strSql="SELECT unick,GROUP_CONCAT(pname,':NT$',L.pprice,'/',uoption,'/ 數量:',pqty),content,sum(pqty*L.pprice),sname,stel,sfax,spic,saddr,sintro FROM ttlisttb AS L INNER JOIN ttprodtb AS P INNER JOIN ttstoretb AS S ON S.sno=P.sno AND L.pno=P.pno WHERE ono=$orderno AND L.cycno=$ctcycno GROUP BY unick ORDER BY unick,ordertime desc";
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
				$objDrawing->setPath(dirname(__FILE__)."/storepic/$spic");    
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

?>
