<?
require_once "conn.php";

$uuser=trim($_REQUEST["email"]);

$strSql = "SELECT uuser FROM ttusertb WHERE uuser='$uuser'";

$data_rows=mysql_query($strSql,$link);

if(list($uuser)=mysql_fetch_row($data_rows)) 
	echo "0";
else
	echo "1";

return;
?>