<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	exit;
}

require_once "conn.php";

$sno=$_REQUEST['sno'];
$err=0;
$nsno=0;
if($sno!="" && intval($sno)>0)
{
	$nsno=copystore($sno);
	if($nsno<=0) {echo "0"; exit;}
	$err=copycate($sno,$nsno);
	($err>0)?$err:"0";
	
}
else
	echo "0";
	
function copystore($sno)
{
	$newsno=array(-1);
	$sql="SELECT sintro,surl,spic,stype FROM ttstoretb WHERE sno=$sno";
	$data_rows=mysql_query($sql);
	
	if(list($sintro,$surl,$spic,$stype)=mysql_fetch_row($data_rows))
	{
		$sname=$_REQUEST['nsname'];
		$sowner=$_COOKIE['teatimeuuser'];
		$lastupdate=date("Y-m-d H:i:s");
		$strSQL="INSERT INTO ttstoretb (sname,sintro,surl,spic,stype,sowner,lastupdate) VALUES ('$sname','$sintro','$surl','$spic','$stype','$sowner','$lastupdate')";
		$ret=mysql_query($strSQL);
		if($ret==true)
		{
			$sql="SELECT sno FROM ttstoretb WHERE sname='$sname' AND lastupdate='$lastupdate'";
			$data_rows=mysql_query($sql);
			$newsno=mysql_fetch_array($data_rows);
		}
	}
	return $newsno[0];
}

function copycate($sno,$nsno)
{
	global $link2;
	$sql="SELECT cno,cname FROM ttcatetb WHERE sno=$sno";
	$data_rows=mysql_query($sql);
	$err=0;
	$strSQL="";
	$strProd="";
	while(list($cno,$cname)=mysql_fetch_row($data_rows))
	{
		$strSQL="INSERT INTO ttcatetb (sno,cname) VALUES ($nsno,'$cname')";
		$ret=mysql_query($strSQL);
		if($ret==true)
		{
			$sql="SELECT cno FROM ttcatetb WHERE sno=$nsno AND cname='$cname'";
			$rows=mysql_query($sql);
			$newcno=mysql_fetch_array($rows);
			if($newcno[0]>0) 
			{
				$strProd .=copyprod($sno,$cno,$nsno,$newcno[0]);
				
			}
		}
	}
	if($strProd!="") 
	{
		$ret=mysqli_multi_query($link2,$strProd) or die(mysqli_error($link2));
		if($ret!=true) $err++;
	}
	
	return $err;
}

function copyprod($sno,$cno,$nsno,$newcno)
{
	$sql="SELECT pname,pprice,ppic,pintro,psize,ptaste,ptemp,psuit,pgood,pnormal,pbad,prater FROM ttprodtb WHERE sno=$sno AND cno=$cno";
	$data_rows=mysql_query($sql);
	$strSQL="";
	$err=0;
	while(list($pname,$pprice,$ppic,$pintro,$psize,$ptaste,$ptemp,$psuit,$prater)=mysql_fetch_row($data_rows))
	{
		$strSQL .="INSERT INTO ttprodtb (sno,cno,pname,pprice,ppic,pintro,psize,ptaste,ptemp,psuit,prater) VALUES ($nsno,$newcno,'$pname','$pprice','$ppic','$pintro','$psize','$ptaste','$ptemp','$psuit','0');";
	}
	
	return $strSQL;
}	
?>
