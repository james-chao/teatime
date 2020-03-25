<?
header( 'Content-Type: text/html; charset=utf-8' );

//$tu=trim($_GET['tu']);
$tu=$_COOKIE['teatimeuuser'];
$tusertype=$_COOKIE['teatimeutype'];
if(strlen($tu)!=0 && $tu!="undefined") 
{
	echo "<b>".$tu."</b> | <a href='logout.php'>登出</a>";
	if($tusertype==2) echo " | <a href='webadmin/adminmanage.php'>後台管理</a>";
}
else 
	echo "<b>歡迎光臨!!</b> | <a id='loginlink' href='javascript:void(0);' onclick='document.all.ttader.click();'>登入</a> | <a href='reguser.html'>註冊</a>";


?>