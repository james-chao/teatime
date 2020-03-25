<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
include "conn.php";

$ufnos=explode(",",str_replace(";",",",trim($_POST['ufname'])));
foreach($ufnos as $uf)
{
	if($uf!='') $ufno4sql .="'$uf',";
}
if(substr($ufno4sql,-1,1)==",") $ufno4sql=substr($ufno4sql,0,strlen($ufno4sql)-1);

if($ufno4sql!="")
{
	$uno=$_COOKIE['teatimeuserno'];
	$strSql="SELECT ufno FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND fagree='y' AND F.uno=$uno AND unick in ($ufno4sql)";

	$data_rows=mysql_query($strSql);
	
	while(list($ufno)=mysql_fetch_row($data_rows))
	{
		$ufnostr .= "$ufno;";
	}
}

echo $ufnostr;
?>