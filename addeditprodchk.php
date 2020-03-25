<?
session_cache_limiter('private,max-age=10800');  //nocache,public
header("Cache-control: private"); 
header( 'Content-Type: text/html; charset=utf-8' );


if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$pcatestring="";
$cnamestring=$_REQUEST['cnamestring'];

function addprodcate($sno,$cname)
{
	global $pcatestring;
	$newcno=0;
	$strSql = "INSERT INTO ttcatetb (sno,cname) VALUES ($sno,'$cname')";
	$ret=mysql_query($strSql);
	if($ret) 
	{
		$cnamestring=str_replace($cnamestring,"-$cname",""); //remove the cate that has saved, EX: -AAA-CCC

		$strSql = "SELECT cno FROM ttcatetb WHERE sno=$sno AND cname='$cname'";
		$data_rows=mysql_query($strSql);
		list($newcno)=mysql_fetch_row($data_rows);
		$pcatestring .="|".$cname;
	}
	return $newcno;
}
function pickcate($sno,$cname)
{
	$strSql = "SELECT cno FROM ttcatetb WHERE sno=$sno AND cname='$cname'";
	//echo $strSql;
	$data_rows=mysql_query($strSql);
	list($cno)=mysql_fetch_row($data_rows);
	return $cno;
}

function prodexist($sno,$cno,$pn)
{ 	
	if(is_numeric($cno))
	{
		$strSql = "SELECT cno FROM ttprodtb WHERE sno=$sno AND cno=$cno AND pname='$pn'";
		$data_rows=mysql_query($strSql);
		//echo $strSql;
		if(list($cn)=mysql_fetch_row($data_rows)) 
			return 0;
		else
			return $cno;
	}
	return 1; //cno must be a cname
}

function getprodpic($m) 
{
	$ppicname=$_FILES["ppic"]["name"][$m];
	$ppicsize=$_FILES["ppic"]["size"][$m];
	$ppictype=$_FILES["ppic"]["type"][$m];
	
	if(!empty($ppicname))
	{
		if(($ppicsize/1024)<1500 && ($ppictype=="image/jpeg" || $ppictype=="image/pjpeg" || $ppictype=="image/gif"|| $ppictype=="image/x-png"))
		{
			$FilePath="prodpic/".$ppicname;
			move_uploaded_file($_FILES["ppic"]["tmp_name"][$m],$FilePath);
		}
		else
		{
			$ppicname="";
			echo "<script>alert('上傳 $ppicname 圖檔不成功，可能圖檔太大了，或是非jpg,gif,png圖檔格式!');</script>";
		}
	}
	else
		$ppicname="";
		
	return $ppicname;
}

function addeditprod()
{
	$snoarr=$_REQUEST["sno"];
	$cnoarr=$_REQUEST["cno"];
	$pnoarr=$_REQUEST['pno'];
	$pnamearr=$_REQUEST["pname"];
	$ppricearr=$_REQUEST["pprice"];
	$pintroarr=$_REQUEST["pintro"];
	$psizearr=$_REQUEST["psize"];
	$ptastearr=$_REQUEST["ptaste"];
	$ptemparr=$_REQUEST["ptemp"];
	$psuitarr=$_REQUEST["psuit"];
	$pgoodarr=$_REQUEST["pgood"];
	$pnormalarr=$_REQUEST["pnormal"];
	$pbadarr=$_REQUEST["pnormal"];
	$praterarr=$_REQUEST["prater"];
	
	foreach ($snoarr as $sno) 
    {
		if(!empty($sno)) break;
	}
	foreach ($cnoarr as $cnno) 
    {
		if(!empty($cnno)) break;
	}

	$k=0;
	$epxtime=30*24*60*60;
	setcookie("teatimenowsno",$sno,time()+$epxtime);
	
	global $pcatestring;
	global $cnamestring;
	
	foreach ($pnoarr as $pno) 
    {
		$ppic=getprodpic($k);
		//add new
		$getcno=prodexist($sno,$cnno,$pnamearr[$k]);
		if($pno==0 && $pnamearr[$k]!="" && $getcno>0 ) //must be new product while pno=0
		{	
			if($getcno==1) $getcno=pickcate($sno,$cnno);
			
			$strSql .= "INSERT INTO ttprodtb (sno,cno,pname,pprice,ppic,pintro,psize,ptaste,ptemp,psuit,pgood,pnormal,pbad,prater) VALUES ($sno,'$getcno','$pnamearr[$k]','$ppricearr[$k]','$ppic','$pintroarr[$k]','$psizearr[$k]','$ptastearr[$k]','$ptemparr[$k]','$psuitarr[$k]',0,0,0,'0');";
		}
		else if(!empty($pno) && $pno>0) //edit old 
		{	
			if($ppic!="") $ppic=",ppic='$ppic'"; //change if new pic selected otherwise keep it
			$strSql .="UPDATE ttprodtb SET pname='$pnamearr[$k]',pprice='$ppricearr[$k]',psize='$psizearr[$k]',ptemp='$ptemparr[$k]',ptaste='$ptastearr[$k]' $ppic,pintro='$pintroarr[$k]',psuit='$psuitarr[$k]' WHERE pno=$pno;";
		}
		$k++;
	}
	
	
	global $link2;
	$ret=true;
	/*echo $strSql;
	exit;*/
	if($strSql!="") $ret=mysqli_multi_query($link2,$strSql) or die(mysqli_error());

	if($ret==true) 
		return 0; //successful
	else	
		return 1; //failure
}

$pname=trim($_REQUEST["pname"]);
$savebtn=$_REQUEST['savebutton'];

if ($savebtn=="送出儲存" && isset($_POST['pname']) && is_array($_POST['pname']) && count($_POST['pname']) > 0) 
{
	$err=0;
	
	//for new cate
	$snoarr=$_REQUEST["sno"];
	foreach($snoarr as $sno){
		if(!empty($sno)) break;
	}
	$cnarr=explode("-",$cnamestring);
	foreach($cnarr as $cn)
	{
		//same cate no need to save twice
		if($cn && strpos($pcatestring,$cn)===false && !is_numeric($cn)) $ret2=addprodcate($sno,$cn); //add other cates
		if($ret2==0) $err++;
	}
	
	//for products
	$err +=addeditprod();
	
	if($err>0) 
		echo "<script>alert('維護成功');location.href='mainprod.html';</script>";
	else
		echo "<script>alert('維護失敗,請稍後再試');location.href='mainprod.html';</script>";
}

?>