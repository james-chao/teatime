<?php   
header( 'Content-Type: text/html; charset=utf-8' );
set_include_path(get_include_path() . PATH_SEPARATOR . './Classes/');   
include 'PHPExcel/IOFactory.php';   
include 'conn.php';  
 
$fn=$_REQUEST['fn'];
$filename=$_FILES[$fn."file"]["name"];
$fileext=end(explode(".",$filename));
if($filename)
{
	$FilePath="upload/".$filename;
	move_uploaded_file($_FILES[$fn."file"]["tmp_name"],$FilePath);
}
else
{
	echo "<script>alert('沒有檔案或檔案上傳失敗');</script>";
	return false;
}
	
switch($fn)
{
	case 'sexcel5': 
		if($fileext!="xls") {echo "<script>alert('這不是 xls 檔案');</script>"; return false;}
		$reader = PHPExcel_IOFactory::createReader('Excel5'); // 讀取舊版 excel 檔案
		$PHPExcel = $reader->load($FilePath); // 檔案名稱   
		$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)   
		$highestRow = $sheet->getHighestRow(); // 取得總列數   
		$highestColumn = ord($sheet->getHighestColumn())-64; //Char to Ascii
		$readtype=true;
	break;
	case 'sexcel2007': 
		if($fileext!="xlsx") {echo "<script>alert('這不是 xlsx 檔案');</script>";return false;}
		$reader = PHPExcel_IOFactory::createReader('Excel2007'); 
		$PHPExcel = $reader->load($FilePath); // 檔案名稱   
		$sheet = $PHPExcel->getSheet(0); // 讀取第一個工作表(編號從 0 開始)   
		$highestRow = $sheet->getHighestRow(); // 取得總列數   
		$highestColumn = ord($sheet->getHighestColumn())-64; //Char to Ascii
		$readtype=true;
	break;
	case 'sexcelcsv': 
		if($fileext!="csv") {echo "<script>alert('這不是 csv 檔案');</script>";return false;}
		$highestColumn=10; //force to run csvupfile()
		$readtype=false;
	break;
}


if($highestColumn>10) //so far just 10 columns for uplaoding
{
	echo "<script>alert('欄位數目不符');</script>";
	return false;
}
else
{
	$highestColumn=10;
	$readtype?excelupfile($sheet,$highestRow,$highestColumn):csvupfile($FilePath);
}

function excelupfile($sheet,$highestRow,$highestColumn)
{
 
	$phead="<table border='0' cellspacing='1' cellpadding='1' width='99%' class='newpdtd'><thead id='newpdtb'><tr><th>店名</th><th>電話</th><th>傳真</th><th>網址</th><th>縣市</th><th>鄉鎮市區</th><th>完整地址</th><th>類型</th><th>送達地區</th><th>簡介與規則</th><th>成敗</th></tr></thead><tbody>";
	//$phead="<table border='0' cellspacing='1' cellpadding='1' width='99%' class='newpdtd'><tbody>";
	
	$pbody="";
	for ($row=0; $row <=$highestRow; $row++) { 

		$sno=0;
		$okflag=0;
		$valempty=0;
		$msg="";
		$pbody .="<tr class='imgALL".$row."'>";	
		for ($column=0; $column < $highestColumn; $column++) 
		{   
			$val = trim($sheet->getCellByColumnAndRow($column, $row)->getValue());  
			if($val=="" && ($column<2)) {$valempty++; $msg .="#店家、電話為必填項目\n"; break; } //no store,cate,prod,price
			if($val=="") $val="&nbsp;";
		
			if($row!=1){
				$pbody .="<td>$val</td>";
				if($column%$highestColumn==0)
				{
					$sno=getsno($val);
					if($sno) 
					{
						$okflag++; 
						$msg .="#店家已經存在\n";
					}
					$sqldata .="'$val',";
				}
				else
				{
					$sqldata .="'$val'";
					if($column%$highestColumn!=($highestColumn-1)) 
					{
						$sqldata .=",";
					}
					else
					{
						$udate=date("Y-m-d H:i:s");
						if(!$sno) $strSql .= "INSERT INTO ttstoretb (sname,stel,sfax,surl,scity,scounty,saddr,stype,sdispatch,sintro,lastupdate) VALUES ($sqldata,'$udate');";
						$sqldata=""; //reset after binding the SQL string
					}
				}
			}
			
		}
		if(!$valempty && $row!=1)
		{
			if(!$okflag) 
			{
				$pbody .="<td><img src='css/images/tree_dnd_yes.png' class='imgOK' alt='#上傳成功' /></td>"; 
				$pbody=str_replace('imgALL'.$row,'imgOK',$pbody);
			}
			else 
			{
				$pbody .="<td><img src='css/images/tree_dnd_no.png' alt='$msg' class='imgNO' /></td>";
				$pbody=str_replace('imgALL'.$row,'imgNO',$pbody);
			}
		}
	  	$pbody .="</tr>";
	}  
	$pbottom="</tbody></table>";
	
	//echo $strSql;
	if(!empty($strSql))
	{
		global $link2;
		$ret=mysqli_multi_query($link2,$strSql) or die(mysqli_error());
	}
	echo $phead.$pbody.$pbottom; //show the cells
}

function csvupfile($FilePath)
{	
	if(empty($FilePath)) return false;
	
	if($f=fopen($FilePath,"r"))
	{ 
		$phead="<table border='0' cellspacing='1' cellpadding='1' width='99%' class='newpdtd'><thead id='newpdtb'><tr><th>店名</th><th>電話</th><th>傳真</th><th>網址</th><th>縣市</th><th>鄉鎮市區</th><th>完整地址</th><th>類型</th><th>送達地區</th><th>簡介與規則</th><th>成敗</th></tr></thead><tbody>";
		$pbody="";
		$row=0;
		$isbig5=0;
		$totalcolumn=10;
		while(!feof($f))
		{
			$sno=0;
			$okflag=0;
			$msg="";
			
			
			$pbody .="<tr class='imgALL".$row."'>";	
			
			$line=trim(fgets($f)); //get row data
			if(empty($line)) continue; //may some empty line after the data.
			if($row==0)
			{
				$dataarray=explode(",",$line);//check colowns at first time
				 if(count($dataarray)!=$totalcolumn)
				 {
					echo "<script>alert('欄位數目不符');</script>";
					return false;
				 }
				 if(!is_utf8($line)) $isbig5=1; //for big5 char
				 
			}
			else
			{
				if($isbig5) $line=iconv("big5","UTF-8",$line); //transfer big5 to utf-8
				$dataarray=explode(",",$line);
				
				foreach($dataarray as $data) //deal with columns
				{
					$data=trim($data);
					if($data=="") $data="&nbsp;";
					$pbody .="<td>$data</td>";
					if($col%$totalcolumn==0)
					{
						$sno=getsno($data); 
						if($sno) 
						{
							$okflag++; 
							$msg .="#店家已經存在\n";
						}
						$sqldata .="'$data',";
					}
					else
					{
						$sqldata .="'$data'";
						if($col%$totalcolumn!=($totalcolumn-1)) 
						{
							$sqldata .=",";
						}
						else
						{
							$udate=date("Y-m-d H:i:s");
							if(!$sno)
								$strSql .= "INSERT INTO ttstoretb (sname,stel,sfax,surl,scity,scounty,saddr,stype,sdispatch,sintro,lastupdate) VALUES ($sqldata,'$udate');";
								$sqldata=""; //reset after binding the SQL string
						}
					}
					$col++;
				}
				
				if(!$okflag) 
				{
					$pbody .="<td><img src='css/images/tree_dnd_yes.png' class='imgOK' alt='#上傳成功' /></td>"; 
					$pbody=str_replace('imgALL'.$row,'imgOK',$pbody);
				}
				else 
				{
					$pbody .="<td><img src='css/images/tree_dnd_no.png' alt='$msg' class='imgNO' /></td>";
					$pbody=str_replace('imgALL'.$row,'imgNO',$pbody);
				}
				$pbody .="</tr>";
			}  
			$pbottom="</tbody></table>";
						
			$row++;
		}
	}
	fclose($f);
	//echo $strSql;
	if(!empty($strSql))
	{
		global $link2;
		$ret=mysqli_multi_query($link2,$strSql) or die(mysqli_error());
	}
	echo $phead.$pbody.$pbottom; //show the cells
}

//判斷字串是否為utf8
function  is_utf8($str)  {
    $i=0;
    $len  =  strlen($str);

    for($i=0;$i<$len;$i++)  {
        $sbit = ord(substr($str,$i,1));
        if($sbit  <  128)  {
            //本字節為英文字符，不與理會
        }elseif($sbit  >  191  &&  $sbit  <  224)  {
            //第一字節為落於192~223的utf8的中文字(表示該中文為由2個字節所組成utf8中文字)，找下一個中文字
            $i++;
        }elseif($sbit  >  223  &&  $sbit  <  240)  {
            //第一字節為落於223~239的utf8的中文字(表示該中文為由3個字節所組成的utf8中文字)，找下一個中文字
            $i+=2;
        }elseif($sbit  >  239  &&  $sbit  <  248)  {
            //第一字節為落於240~247的utf8的中文字(表示該中文為由4個字節所組成的utf8中文字)，找下一個中文字
            $i+=3;
        }else{
            //第一字節為非的utf8的中文字
            return  0;
        }
    }
    //檢查完整個字串都沒問體，代表這個字串是utf8中文字
    return  1;
}

function getsno($val)
{
	global $link;
	$strSql = "SELECT sno FROM ttstoretb WHERE sname='$val'";
	$data_rows=mysql_query($strSql,$link);
//echo $strSql;
	if(list($sno)=mysql_fetch_row($data_rows)) 
		return $sno;
	else
		return 0;
		
}

?>