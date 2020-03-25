<style>
.frdlistdiv{width:230px;height:30px;background-color:#ECF5FF;margin-bottom:1px;padding:2px;}
.agreebtn{margin-left:100px;background-color:#369;color:#ffffff}
.disagreebtn{margin-left:20px;background-color:#963;color:#ffffff}
</style>
<?
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
include "conn.php";

$uno=$_COOKIE['teatimeuserno'];

$strSql="SELECT F.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U  ON F.uno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND fagree='n'";
$data_rows=mysql_query($strSql);
echo "<div style='text-align:right;padding:4px;cursor:pointer'><img alt='關閉' title='關閉' src='css/images/tabs_close.gif' id='hiboxclose' /></div>";

while(list($ufno,$uname,$unick,$upic,$ufno)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	echo "<div class='frdlistdiv' title='$unick 請求成為好友' id='agreebtn$ufno'><span style='background:none'><img alt='$unick 請求成為好友' src='$imgsrc' width='30' height='30' /></span><span style='background:none;margin-left:50px;font-size:11px;width:50px;color:#36F;'>$unick</span><button class='agreebtn' ufno='$ufno' val='y'>同意</button><button class='disagreebtn' ufno='$ufno' val='c'>拒絕</button></div>";
	
}
?>
<script>
$(document).ready(function(){
	$('.agreebtn').click(function(){
		var ufno=$(this).attr('ufno');
		var ycval=$(this).attr('val');
		$.post("updnewfriend.php",{ufno:ufno,ycval:ycval},function(r){
			if(r==1) 
			{
				$('#agreebtn'+ufno).remove();
				$('#addfdcnt').text($('#addfdcnt').text()-1);
			}
		});
	});
	$('#hiboxclose').click(function(){
		$('#hibox').hide();
	});
});
</script>