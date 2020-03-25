<?
require_once "conn.php";

$cname=trim($_REQUEST["cname"]);
$sno=trim($_REQUEST["sno"]);

if(empty($sno))
{
	echo "0";
}
else
{
	$strSql = "SELECT cname FROM ttcatetb WHERE cname='$cname' AND sno=$sno";
	
	$data_rows=mysql_query($strSql,$link);
	
	if(list($cname)=mysql_fetch_row($data_rows)) 
		echo "0";
	else
		echo "1";
}
?>