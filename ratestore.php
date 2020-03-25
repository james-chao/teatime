<?

require_once "conn.php";
$sgrade=$_REQUEST['value'];
$srater=$_COOKIE['teatimeuserno'];
$sno=$_REQUEST['sno'];

function userrated($sno,$srater)
{
	global $link;
	$strSql = "SELECT srater FROM ttstoretb WHERE sno=$sno AND INSTR(srater,'$srater')";
	$data_rows=mysql_query($strSql,$link);
	if(list($srater)=mysql_fetch_row($data_rows))
		return 0; //rated
	else
		return 1; //to rate
}

if(!empty($sno) && userrated($sno,$srater))
{
	$strSql = "UPDATE ttstoretb SET sgrade=(sgrade+$sgrade),srater=CONCAT('$srater,',srater) WHERE sno=$sno";
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "1";
	else
		echo "0";
}
else
	echo "0"; //failure
?>