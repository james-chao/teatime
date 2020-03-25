<?

require_once "conn.php";
$pratesel=$_REQUEST['pratesel'];
$pno=$_REQUEST['pno'];
$uno=$_COOKIE['teatimeuserno'];

function userrated($pno,$uno)
{
	global $link;
	$uno=$_COOKIE['teatimeuserno'];
	$strSql = "SELECT prater FROM ttprodtb WHERE pno=$pno AND INSTR(prater,'$uno')";
	$data_rows=mysql_query($strSql,$link);
	if(list($prater)=mysql_fetch_row($data_rows))
		return 0; //rated
	else
		return 1; //to rate
}

if(!empty($pno) && userrated($pno,$uno))
{
	if($pratesel=="pgood")
		$pratesel="pgood";
	else if($pratesel=="pnormal")
		$pratesel="pnormal";
	else
		$pratesel="pgood";
	
	$uno=$_COOKIE['teatimeuserno'];	
	$strSql = "UPDATE ttprodtb SET $pratesel=($pratesel+1),prater=CONCAT('$uno,',prater) WHERE pno=$pno";
	
	$ret=mysql_query($strSql,$link);
	if($ret==true)
		echo "1";
	else
		echo "0";
}
else
	echo "0"; //failure
?>