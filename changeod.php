<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
$ono=$_POST['ono'];
$st=$_POST['st'];
echo "<script language='javascript'>alert('$ono,$st');</script>";
if($st && $ono)
{
	$strSql="UPDATE ttordertb SET oonoff='$st' WHERE ono=$ono";
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "$st,$ono";
	else
		echo "0";
}
?>