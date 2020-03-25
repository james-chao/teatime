<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
$sno=$_REQUEST['sno'];
$uno=$_COOKIE['teatimeuserno'];
if($sno && $uno)
{
	$strSql="UPDATE ttusertb SET ustore=REPLACE(ustore,'$sno,','') WHERE uno=$uno";
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "1";
	else
		echo "0";
}
?>