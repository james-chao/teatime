<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeorderno']=="" || $_COOKIE['teatimeorderkey']==""){
	echo "<script language='javascript'>alert('請確定您已取得正確的訂單碼！');location.href='/';</script>";
}

require_once "conn.php";
if($_POST['element_id']) $qclno=explode("-",$_POST['element_id']);
$data=$_POST['update_value'];


$strSql="";
switch($qclno[0])
{
case 'c': 
	$strSql="UPDATE ttlisttb SET content='$data' WHERE lno=$qclno[1]";
	break;
case 'q':
	$strSql="UPDATE ttlisttb SET pqty=$data WHERE lno=$qclno[1]";
	break;
case 'p':
	$strSql="UPDATE ttlisttb SET uoption='$data' WHERE lno=$qclno[1]";
	break;
}


if($qclno[1] && $data && $strSql!="")
{
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "$data";
	else
		echo "0";
}
?>