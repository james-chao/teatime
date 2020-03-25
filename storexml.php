<? 
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

/*
$westLng=$_REQUEST['westLng'];
$eastLng=$_REQUEST['eastLng'];
$northLat=$_REQUEST['northLat'];
$southLat=$_REQUEST['southLat'];
$zoom=$_REQUEST['zoom'];
*/
		
//$strSql="SELECT sno,sname,stel,sfax,surl,SUBSTR(sintro,1,30),saddr,SUBSTRING_INDEX(sgps,',',1),SUBSTRING_INDEX(sgps,',',-1) FROM ttstoretb WHERE sgps<>'' AND $southLat<SUBSTRING_INDEX(sgps,',',1) AND SUBSTRING_INDEX(sgps,',',1)<$northLat AND $westLng<SUBSTRING_INDEX(sgps,',',-1) AND SUBSTRING_INDEX(sgps,',',-1)<$eastLng";
$scity=$_REQUEST['city'];
$scounty=$_REQUEST['county'];
$searchtxt=$_REQUEST['searchtxt'];
if($scity!="") $scity=" AND scity='$scity'";
if($scounty!="") $scounty=" AND scounty='$scounty'";
if($searchtxt!="") $searchtxt=" AND CONCAT_WS(sname,stel,sfax,scity,scounty,saddr,sintro,surl,sdispatch,spic) LIKE '%$searchtxt%'";
if($scity=="" && $scounty=="") $limit=" LIMIT 50";

$strSql="SELECT sno,sname,stel,sfax,surl,SUBSTR(sintro,1,30),saddr,SUBSTRING_INDEX(sgps,',',1),SUBSTRING_INDEX(sgps,',',-1) FROM ttstoretb WHERE sgps<>'' $scity $scounty $searchtxt ORDER BY lastupdate DESC $limit";

$data_rows=mysql_query($strSql,$link);
if($spic!="") $storepic="<div><img src='storepic/$spic' width='100' height='60' /></div>";
echo "<markers>";
while(list($sno,$sname,$stel,$sfax,$surl,$sintro,$saddr,$slat,$slng,$spic)=mysql_fetch_row($data_rows))
{
	if($surl) $surl="<a target='_blank' href='$surl'>本店網頁</a>";
	print "<marker shopId='$sno' shopName='$sname' lng='$slng' lat='$slat'>
			<infoWindow><![CDATA[
				<div >
					<div><b style='font-size:110%;'> $sname</b> $surl</div>
					<div>簡介： $sintro </div>
					$storepic
					<div>地址： $saddr </div>
				</div>
			]]></infoWindow>
		</marker>";
}
echo "</markers>";
?>
