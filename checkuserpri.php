<?
header( 'Content-Type: text/html; charset=utf-8' );

require_once "conn.php";

$ttordkey=trim($_REQUEST["ttordkey"]);
$uuser=trim($_REQUEST["ttaduser"]);
$upass=md5(trim($_REQUEST["ttadpass"]));
$meta=$_REQUEST["meta"];

//who login
switch($meta)
{
case 0:
	checkordkey($ttordkey);
	break;
case 1:
	checkuser($uuser,$upass);
	break;
}

function checkuser($uuser,$upass)
{
	global $link; 
	$strSql = "SELECT uno,uuser,upass,utype,unick FROM ttusertb WHERE uonoff='y' AND uactive='y' AND uuser='$uuser' AND upass='$upass'";

	$data_rows=mysql_query($strSql);

	if(list($uno,$uuser,$upass,$utype,$unick)=mysql_fetch_row($data_rows)) 
	{
		updatelogin($uuser);
		$epxtime= 365*24*60*60;
		$uflag=checkuflag(); //uf99999
		if($uflag!="" && strlen($uflag)>2) setcookie("teatimeuflag",$uflag,time() + $epxtime);
		setcookie("teatimeuuser",$uuser,time()+$epxtime);
		setcookie("teatimeunick",$unick,time()+$epxtime);
		setcookie("teatimeutype",$utype,time()+$epxtime);
		setcookie("teatimeuserno",$uno,time()+$epxtime);
		setcookie("teatimeoldono","0",time()+$epxtime); //user order checking for no-duplication
		
		if($utype==1)
			echo "<script>location.href='member.php';</script>";
		else if($utype==2)
			echo "<script>location.href='webadmin/adminmanage.php';</script>";
	}
	else
	{
		echo "<script>alert('登入失敗，請確認您的帳號密碼，並確認帳號已啟動');location.href='index.html';</script>";
	}
}

function checkordkey($ttordkey)
{
	global $link; 
	$strSql = "SELECT ono FROM ttordertb WHERE oorderkey='$ttordkey'";
	
	$data_rows=mysql_query($strSql);
	
	if(list($ono)=mysql_fetch_row($data_rows)) 
	{
		$epxtime= 365*24*60*60;
		$uflag=checkuflag(); //uf99999
		if($uflag!="" && strlen($uflag)>2) setcookie("teatimeuflag",$uflag,time() + $epxtime);
		setcookie("teatimeorderno",$ono,time() + $epxtime);
		setcookie("teatimeorderkey",$ttordkey,time() + $epxtime);
		
		echo "<script>location.href='ordermenu.php'</script>";
	}
	else
	{
		echo "<script>alert('找不到此訂單，請確認您的訂單密碼');location.href='/';</script>";
	}
}

function checkuflag()
{	//for user order history
	$uflag="";
	if(!isset($_COOKIE['teatimeuflag']) || $_COOKIE['teatimeuflag']=="") 
	{
		$strSql = "SELECT cfvalue FROM ttconfigtb WHERE cfname='uflag'";
		$rows=mysql_query($strSql);
		$value=mysql_fetch_array($rows);
		$uflag="uf".(intval($value[0])+1);
		$strSql = "UPDATE ttconfigtb SET cfvalue='".(intval($value[0])+1)."' WHERE cfname='uflag'";
		mysql_query($strSql);
		
		//暫時更新uflag用
		/*$unick=$_COOKIE['teatimeunick'];
		$strSql = "UPDATE ttlisttb SET uflag='$uflag' WHERE unick='$unick'";
		mysql_query($strSql);*/
	}

	return $uflag;
}
function updatelogin($uuser)
{
	global $link; 
	$strSql = "UPDATE ttusertb SET uip='".$_SERVER['REMOTE_ADDR']."', ulastlogin='".date("Y-m-d H:i:s")."' WHERE uuser='$uuser'";
	mysql_query($strSql);
}
?>