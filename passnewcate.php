<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$cno=$_POST['element_id'];
$cname=trim($_POST['update_value']);

if($cno && $cname)
{
	$strSql="UPDATE ttcatetb SET cname='$cname' WHERE cno=$cno";
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "$cname";
	else
		echo "0";
}
?>