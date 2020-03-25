<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

function storeexist($sname)
{
	global $link; 
	$strSql = "SELECT sname FROM ttstoretb WHERE sname='$sname'";

	$data_rows=mysql_query($strSql,$link);
	
	if(list($sname)=mysql_fetch_row($data_rows)) 
		return 1;
	else
		return 0;
}

function addnewstore($sname)
{
	global $link; 
	$sintro=$_REQUEST["storeintro"];
	
	if(!empty($_REQUEST["thezip"])) $szip=$_REQUEST["thezip"];
	if($_REQUEST["thecity"]!="縣市") $scity=$_REQUEST["thecity"];
	if($_REQUEST["thecounty"]!="鄉鎮市區") $scounty=$_REQUEST["thecounty"];
	if(!empty($_REQUEST["storeaddr"])) $saddr=$_REQUEST["storeaddr"];
	
	if($_REQUEST["storeurl"]=="http://") $surl =""; else $surl=$_REQUEST["storeurl"];
	$stel =$_REQUEST["storetel"];
	$sfax =$_REQUEST["storefax"];
	$sdispatch =$_REQUEST["sdispatch"];
	$stype =$_REQUEST["storetype"];
	$sowner =$_COOKIE['teatimeuuser'];
	$lastupdate=date("Y-m-d H:i:s");
	
	$spicname=$_FILES["storepic"]["name"];
	$spicsize=$_FILES["storepic"]["size"];
	$spictype=$_FILES["storepic"]["type"];
	if(!empty($spicname))
	{
		if(($spicsize/1024)<1500 && ($spictype=="image/jpeg" || $spictype=="image/pjpeg" || $spictype=="image/gif"|| $spictype=="image/x-png"))
		{
			$FilePath="storepic/".$spicname;
			move_uploaded_file($_FILES["storepic"]["tmp_name"],$FilePath);
		}
		else
		{
			$spicname="nostore.jpg";
			echo "<script>alert('圖檔上傳不成功，可能圖檔太大了，或是非jpg,gif,png圖檔格式!');</script>";
		}
	}
	else
		$spicname="nostore.jpg";
	
	$strSql = "INSERT INTO ttstoretb (sname,sgrade,srater,sintro,szip,scity,scounty,saddr,surl,spic,stel,sfax,sdispatch,stype,sowner,lastupdate) VALUES ('$sname',0,'','$sintro','$szip','$scity','$scounty','$saddr','$surl','$spicname','$stel','$sfax','$sdispatch','$stype','$sowner','$lastupdate')";

	$ret=mysql_query($strSql,$link);
	
	if($ret==true) 
		return 1;
	else	
		return 0;
}

$sname=trim($_REQUEST["storename"]);

if(storeexist($sname))
	echo "<script>alert('新增失敗，該店家已經存在');history.back();</script>";
else
{
	if(addnewstore($sname))
	{
		echo "<script>alert('新增店家成功');location.href='addstore.html';</script>";
	}
	else
		echo "<script>alert('新增失敗，可能伺服器忙碌或資料錯誤,請稍後再試');location.href='addstore.html';</script>";
}
?>