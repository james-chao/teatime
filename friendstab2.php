<?
/*
 the version without user's authorization
*/
require_once "conn.php";
$uno=$_COOKIE['teatimeuserno'];
if($uno=="") return false;
?>
<div style="border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;">您可能也認識的朋友</div><? 

//$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.ufno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.uno=$uno AND F.fagree='y' AND N.ufno<>$uno UNION SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.uno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND F.fagree='y' AND N.ufno<>$uno LIMIT 25";
$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND U.uno<>$uno AND F.fagree='n'";
$data_rows=mysql_query($strSql);
while(list($ufno,$uname,$unick,$upic)=mysql_fetch_row($data_rows))
{
	echo "<div  class='userpic' style='width:34px;height:34px;float:left;margin:3px;margin-left:11px;'><img alt='加 $unick 為我的朋友' title='加 $unick 為我的朋友' src='userpic/$upic' width='34' height='34' ufno='$ufno' /><div class='useradd' /></div></div>";
}
?><div style="clear:both;border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;"><a href="tobefriend.php" >朋友搜尋</a></div>
<style>
.useradd{ width:15px;height:15px;background-image:url(css/icons/edit_add.png);background-repeat:no-repeat; position:relative; top:-32px;left:18px; z-index:1000; display:none;}
</style>
<script>
$(document).ready(function(){
	$('.userpic').mouseover(function(){
		$(this).children(':nth-child(2)').css('display','block');
	}).mouseout(function(){
		$(this).children(':nth-child(2)').css('display','none');
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