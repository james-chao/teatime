<?
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 交友請求</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script src="js/jquery.easyui.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.colorize-2.0.0.js"></script>
<script type="text/javascript" src="js/jquery.copy.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#tabsdiv').tabs();
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	$('.tips').tooltip();
	$(".profiletb").colorize();
	$('.stimg').click(function(){
		var st=$(this).attr('subid');
		if(st=='n')
			$(this).attr('src','css/images/tree_checkbox_1.gif').attr('subid','y').attr('alt','運作中,可以取消訂單');
		else
			$(this).attr('src','css/images/tree_checkbox_0.gif').attr('subid','n').attr('alt','已砍單,可以恢復訂單');
		var ono=$(this).attr('id');
		(st=='y')?st='n':st='y';
		$.post("changeod.php",{ono:ono,st:st}, function(ret){if(ret) alert('訂單狀態已變更');});
	});
	//copy to Clipboard
	$(".clip").click(function(){
      $.copy($(this).val());
	  $.messager.show({title:'訊息',msg:'已複製密碼到剪貼簿',width:150});
    });
	$('#fdsrchbtn').click(function(){
		var srch=$('#fdsrchtxt').val();
		$.post("srchfriends.php",{srch:srch},function(r){
			if(r==0)
				$('#fdsrchdiv').html('<div style="margin:10px;color:#ff3300">找不到相關資料</div>');
			else
				$('#fdsrchdiv').html(r);
		});
	});
	$('.addfdlink').click(function(){
		var ufno=$(this).attr('ufno');
		$.post("addfriend.php",{ufno:ufno},function(r){
			var msg="";
			if(r==0) msg="加入失敗";
			else if(r==1) msg="交友等候認可回覆";
			else if(r==2) msg="已經加入過了";
			else msg="加入朋友發生錯誤";
	  		$.messager.show({title:'訊息',msg:msg,width:150});
		});
	});
	$('.agreebtn,.disagreebtn').click(function(){
		var ufno=$(this).attr('ufno');
		var ycval=$(this).attr('val');
		
		$.post("updnewfriend.php",{ufno:ufno,ycval:ycval},function(r){
			if(r==1) 
			{
				$('#agreebtn'+ufno).remove();
			}
		});
	});
});
 
</script>
<style>

.clip{color:#FF0080;cursor:pointer;}
.newfddiv{background-color:#eeeeee;width:400px;height:70px;margin-bottom:4px;}
.unickclass{float:left;background:none;margin-left:40px;font-size:12px;width:100px;font-weight:bold;color:#36F;}
.userclass{width:200px;height:50px;margin:3px;margin-left:11px;font-weight:bold;font-family:"微軟正黑體"}
.disagreebtn{margin-left:20px;background-color:#963;color:#ffffff}
.agreebtn{margin-left:70px;background-color:#369;color:#ffffff}
</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/version_03.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/version_04.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="newsdiv"><img src="images/title_addfriend.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="left" valign="top" style="padding-left:10px;"><div style="width:680px;"><div style="border:solid 0px #cccccc;border-top:solid 1px #cccccc;font-weight:bold;padding:4px;padding-left:14px; background-color:#eeeeee;text-align:left;margin-bottom:4px;">交友請求</div>
      <div style="float:left;width:400px;border:solid 1px #cccccc;margin-right:10px;padding:10px;"><? 
	  $uno=$_COOKIE['teatimeuserno'];

$strSql="SELECT F.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U  ON F.uno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND fagree='n'";
$data_rows=mysql_query($strSql);

while(list($ufno,$uname,$unick,$upic,$ufno)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	
	echo "<div class='newfddiv' title='$unick 請求成為好友' id='agreebtn$ufno'><div style='float:left;margin:5px;'><img alt='$unick 請求成為好友' src='$imgsrc' style='height:60px;width:60px' /></div><div style='float:left;margin-top:25px'><span class='unickclass'>$unick</span><button class='agreebtn' ufno='$ufno' val='y'>同意</button><button class='disagreebtn' ufno='$ufno' val='c'>拒絕</button></div></div>";
	
}?><div style="width:388px;border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;"><img src="css/icons/search.png" /><span style="color:#C33">朋友搜尋</span><br />請輸入朋友暱稱、姓名或Email關鍵字:<input type="text" style='width:100px' id="fdsrchtxt" />&nbsp;&nbsp;<input type="button" style='width:30px' value="搜尋" id="fdsrchbtn" /></div><div id="fdsrchdiv" style="border:solid 1px #cccccc;"></div><div><?
$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND F.uno=$uno AND F.fagree='y' ORDER BY addtime DESC LIMIT 25";
$data_rows=mysql_query($strSql);
while(list($ufno,$uname,$unick,$upic)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	
	echo "<div  class='userclass' style='width:250px'><img align='left' title='$unick 已加您為朋友' src='$imgsrc' width='45' height='45' style='margin-right:10px;' />$unick 已加您為朋友</div>";
}

?></div></div><div style="float:left;width:240px;border:solid 1px #cccccc;"><div style="border:solid 2px #eeeeee;text-align:center;font-weight:bold;padding:4px;">您可能也認識的朋友</div><? 

$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.ufno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.uno=$uno AND F.fagree='y' AND N.ufno<>$uno UNION SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttfriendtb N INNER JOIN ttusertb U ON F.uno=N.uno AND N.ufno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND F.fagree='y' AND N.ufno<>$uno LIMIT 10";
//$strSql="SELECT U.uno,uname,unick,upic FROM ttfriendtb F INNER JOIN ttusertb U ON F.ufno=U.uno WHERE uonoff='y' AND U.uno<>$uno AND F.fagree='n'";
$data_rows=mysql_query($strSql);
while(list($ufno,$uname,$unick,$upic)=mysql_fetch_row($data_rows))
{
	$imgsrc=$util->picurl."quanta_".$upic."_MThumb.jpg";
	if(!$util->isPhotoExist($imgsrc)) $imgsrc="userpic/nophoto.png";
	
	echo "<div  class='userclass'><img align='left' title='加 $unick 為我的朋友' src='$imgsrc' width='45' height='45' style='margin-right:10px;' />$unick <div style='float:left;width:80px;'><a href='javascript:void(0)' class='addfdlink' ufno='$ufno'><img src=css/icons/edit_add.png width='12' height='12' />加為朋友</a></div></div>";
}
?></div></div></td>
      <td background="images/sub01_14.gif">&nbsp;</td>
    </tr>
    <tr>
      <td width="47" height="21" valign="bottom" background="images/sub01_19.gif"><img src="images/sub01_22.gif" width="47" height="21" alt="" /></td>
      <td valign="bottom" background="images/sub01_24.gif">&nbsp;</td>
      <td width="24" height="21" valign="bottom" background="images/sub01_14.gif"><img src="images/sub01_25.gif" width="24" height="21" alt="" /></td>
    </tr>
  </table>
</div>
</body>
</html>