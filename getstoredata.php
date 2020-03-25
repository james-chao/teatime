<?
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
//if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']=="") exit;

require_once "conn.php";
$sno=$_POST['sno'];

if(!empty($sno))
{
	global $link;
	$strSql="SELECT sname,stel,sfax,surl,scity,scounty,saddr,stype,sdispatch,spic,sintro FROM ttstoretb WHERE sno=$sno";
	$data_rows=mysql_query($strSql,$link);
	if(list($sname,$stel,$sfax,$surl,$scity,$scounty,$saddr,$stype,$sdispatch,$spic,$sintro)=mysql_fetch_row($data_rows))
	{
		echo "$sname|$stel|$sfax|$surl|$scity|$scounty|$saddr|$stype|$sdispatch|$spic|$sintro";
	}
}
else
	echo ""; //failure
?>