<?
require_once "conn.php";

$sname=trim($_REQUEST["storename"]);

$strSql = "SELECT sname FROM ttstoretb WHERE sname='$sname'";

$data_rows=mysql_query($strSql,$link);

if(list($sname)=mysql_fetch_row($data_rows)) 
	echo "0";
else
	echo "1";
?>