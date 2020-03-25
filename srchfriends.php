<?
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

include "conn.php";
$srch=trim($_POST['srch']);
if($srch==""){echo 0;exit;}
?>
<div style="border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;color:#99F">看看是不是您認識的朋友</div>
<? 
$uno=$_COOKIE['teatimeuserno'];
$strSql="SELECT uno,uname,unick,upic,uuser FROM ttusertb WHERE uonoff='y' AND uno<>$uno AND CONCAT(uname,uuser,unick) LIKE '%$srch%' LIMIT 25";
$data_rows=mysql_query($strSql);
while(list($ufno,$uname,$unick,$upic)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	echo "<div  class='userpic' style='width:350px;margin:3px;margin-left:11px;'><img alt='加 $unick 為我的朋友' title='加 $unick 為我的朋友' src='$imgsrc' width='40' height='40' ufno='$ufno' /><div class='usertxt'>$unick (點擊圖示送出加入朋友請求)</div><div class='useradd'></div></div>";
	
}
?>
<style>
.usertxt{position:relative; top:-22px;left:52px;}
.useradd{ width:15px;height:15px;background-image:url(css/icons/edit_add.png);background-repeat:no-repeat; position:relative; top:-52px;left:22px; z-index:1000; display:none;}
</style>
<script>
$(document).ready(function(){
	$('.userpic').mouseover(function(){
		$(this).children(':nth-child(3)').css('display','block');
	}).mouseout(function(){
		$(this).children(':nth-child(3)').css('display','none');
	}).click(function(){
		var ufno=$(this).children().attr('ufno');
		$.post("addfriend.php",{ufno:ufno},function(r){
			var msg="";
			if(r==0) msg="加入失敗";
			else if(r==1) msg="交友等候認可回覆";
			else if(r==2) msg="已經加入過了";
			else msg="加入朋友發生錯誤";
	  		$.messager.show({title:'訊息',msg:msg,width:150});
		});
	});
});
</script>