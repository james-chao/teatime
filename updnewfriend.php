<?
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
$uno=$_COOKIE['teatimeuserno'];
$ufno=$_POST['ufno'];
$ycval=$_POST['ycval'];

$strSql="UPDATE ttfriendtb SET fagree='$ycval',addtime=now() WHERE ufno=$uno AND uno=$ufno";
$ret=mysql_query($strSql);

echo ($ret==true)?1:0;
?>