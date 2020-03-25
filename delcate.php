<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$cno=$_POST['cno'];
$sno=$_POST['sno'];

if($cno && $sno)
{
	$strSql ="DELETE FROM ttcatetb WHERE cno=$cno;";
	$strSql .="DELETE FROM ttprodtb WHERE cno=$cno;";
	$ret=mysqli_multi_query($link2,$strSql);
	if($ret==true)
		echo "1";
	else
		echo "0";
}
?>