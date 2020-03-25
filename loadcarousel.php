<?
header( 'Content-Type: text/html; charset=utf-8' );
if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$ttuser=$_COOKIE['teatimeuuser'];
if($ttuser)
{
	$strSql="SELECT ustore FROM ttusertb WHERE uuser='$ttuser' AND ustore<>0";
	$data_rows=mysql_query($strSql,$link);

	if(list($ustore)=mysql_fetch_row($data_rows))
	{
		$strSql="SELECT sno,sname,sintro,stel,sfax,spic FROM ttstoretb WHERE sno in ($ustore) LIMIT 20";
		$data_rows=mysql_query($strSql,$link);
		
		while(list($sno,$sname,$sintro,$stel,$sfax,$spic)=mysql_fetch_row($data_rows))
		{
			if(!empty($fax)) $fax="FAX: $fax";
			echo "<a href='javascript:void(0);' rel='$sno' title='$sname' class='selsno'><img class='cloudcarousel' src='storepic/$spic' width='110' height='70' alt='$sintro' title='$sname (TEL:$stel $fax)' /></a>";
		}
		echo "<script type='text/javascript'>
			$(document).ready(function(){
				$('#da-vinci-carousel').CloudCarousel({ 
					reflHeight: 56,
					reflGap:1,
					titleBox: $('#da-vinci-title'),
					altBox: $('#da-vinci-alt'),
					buttonLeft: $('#but1'),
					buttonRight: $('#but2'),
					yRadius:30,
					xPos: 340,
					yPos: 80,
					speed:0.05,
					autoRotate: 'left',
					autoRotateDelay: 5000,
					mouseWheel:true	
				});
				$('.selsno').click(function(){
					var sno=$(this).attr('rel');
					$.post('storeprofile.php',{sno:sno}, function(sdata){
						$('#storeprofile').html(sdata);
					});
					$.post('storeproduct.php',{sno:sno}, function(pdata){
						$('#storeproduct').html(pdata);
					});
				});
			});
			</script>";
	}
}
?>