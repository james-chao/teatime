<?php
include_once(dirname(__FILE__) . '/Classes/utility.php');

if(!$util->error && $util->userdata){
	$uuser=$util->userdata['uuser'];
	$unick=$util->userdata['unick'];
	$utype=$util->userdata['utype'];
	$uno=$util->userdata['uno'];
}

if(empty($utype)) $utype=$_COOKIE['teatimeutype'];
if(empty($uno)) $uno=$_COOKIE['teatimeuserno'];
if(empty($uuser)) $uuser=$_COOKIE['teatimeuuser'];
if(empty($unick)) $unick=$_COOKIE['teatimeunick'];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script type="text/javascript" language="javascript">
$(document).ready(function(){
	// When a link is clicked
	$("#ttader").click(function () {
		$("#ttsecdiv").hide();
		$("#ttaddiv").show(function(){
			$('input:text').focus(function(){ 
				$(this).val("").css("color","#0000f0");
				if($(this).attr("id")=='ttadpasstxt'){
					$(this).hide();
					$('#ttadpass').show().focus();
				}
			});
		});
	});
	$("#nobody").click(function () {
		$("#ttsecdiv").show().fadeIn();
		$("#ttaddiv").hide();	  
	});
	$('#ttform').submit(function(){
		if($('#ttader').attr("checked")==true && $("#ttaddiv").css('display')!='none' && ($('#ttaduser').val()=="" || $('#ttadpass').val()==""))
		{
			alert('請輸入帳號及密碼'); 
			return false;
		}
		else if($('#nobody').attr("checked")==true && $('#ttseckey').val()=="")
		{
			alert('請輸入訂單密碼'); 
			return false;
		}

		$(this).attr('action','checkuserpri.php');
		return true; 
	});
	//for user login or not
	$.post("getfdinvitenum.php",function(data){
		if(parseInt(data)>0) $('#fdicnt').text(data).show();
	});
	
	$('#tipboxclose').click(function(){
		$('#tipbox').fadeOut();
	});
	
	
	 $(".add2fd").mouseover(function(){
		$("#hiboxcont").html('');
		var rel= $(this).attr("rel");
		var offset = $(this).offset();  
		var mt=offset.top;
		var ml=offset.left-20;
		if(rel==1)
		{
			$.post("getfdrequest.php",function(data){
				$("#hiboxcont").html(data);
			});
		}
		else if(rel==2)
		{
			$.post("getfdinvite.php",function(data){
				$("#hiboxcont").html(data);
			});
		}
		else if(rel==3)
		{
			$("#hiboxcont").html("<img src='css/images/datagrid_row_expand.gif' /> <a href='addstore.html'>新增店家</a> <img src='css/images/tree_file.gif' /> <a href='storemain.php'>修改店家</a>");
		} 
		$("#hibox").css({"top":mt,"left":ml}).show();
	}).blur(function(){$("#hibox").hide();});
	
});	

</script>

<style type="text/css">
<!--
.tbb {	BORDER-TOP: #c9d7f1 1px solid; FONT-SIZE: 1px;	WIDTH: 100%; POSITION: absolute; TOP: 24px; HEIGHT: 0px}
#tbar {	FONT-SIZE: 13px; PADDING-TOP: 1px! important;FLOAT: left; HEIGHT: 22px}
#tog {PADDING-RIGHT: 10px; PADDING-LEFT: 10px; PADDING-BOTTOM: 0px; PADDING-TOP: 3px}
#tuser {FONT-SIZE: 13px; PADDING-TOP: 1px! important;PADDING-BOTTOM: 7px! important; TEXT-ALIGN: right}
#footer A {DISPLAY: inline-block; MARGIN: 0px 12px}
.zxdiv{position: absolute;border: 2px solid #06F;background-color:#DFFAFF;line-height:1.8;font-size:13px;z-index:1000;filter:alpha(opacity=50); opacity:0.5; -moz-opacity:0.5; display:none;}

.add2fd2{display:inline-block;border:solid 1px #cccccc;color:#ffffff;font-weight:blod;width:14px;text-align:center; font-family:"Courier New", Courier, monospace}
#hibox{display:none;position:relative;z-index:100;zoom:1;}
#hibox .bd{width:229px;background:#fff;border:1px solid #b2cbd8;margin-left:11px;padding:8px;position:relative;color:#666;font-size:92%;line-height:1.5em;zoom:1;*position:absolute;}
#hibox .bd span{display:block;width:3px;height:3px;background:url(images/hibox.png) 0 0 no-repeat;font-size:0;position:absolute;}
#hibox .bd span.r1{top:-1px;left:-1px;}
#hibox .bd span.r2{background-position:0 -6px;top:-1px;right:-1px;_right:-2px;}
#hibox .bd span.r3{background-position:0 -13px;bottom:-1px;right:-1px;_bottom:-1px;_right:-2px;}
#hibox .bd span.r4{background-position:0 -19px;bottom:-1px;left:-1px;_bottom:-1px;}
#hibox .bd div.ar{width:11px;height:12px;background:url(images/hibox.png) 0px -40px no-repeat;position:absolute;top:-11px;left:18%;}

-->
</style>
</head>
<!-- for XMLHttp
<body onload="makeRequest();">
-->
<body>
<div id="topitem">
  <div id="tog">
    <div id="tbar">
<?php
if(!empty($uuser) && $utype>=1 && is_object($util))
{
	//new friends, fagree type==> y:agree, c:cancel, n:notyet
	$strSql="SELECT F.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U  ON F.uno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND fagree='n'";

	//$util->db->Query($strSql);
	//$addfdcnt=$util->db->RowCount();
	
	//myorder
	$where["uno"] = $util->db->SQLValue($uno);
	$util->db->SelectRows("ttordertb", $where);
	$myordcnt=$util->db->RowCount();

	//friend invite
	//$strSql="SELECT I.uno,ufname,unick,otitle FROM ttinvitetb I INNER JOIN ttordertb O ON I.uno=O.uno AND I.ono=O.ono WHERE I.ufname='$unick' AND omailflag<>'y'";
	//$util->db->Query($strSql);
	//$fdordcnt=$util->db->RowCount();
	//holdermenu($addfdcnt,$myordcnt,$fdordcnt,$unick);
	
	holdermenu($myordcnt,$unick);	
}
else
{
	guestmenu();
}		

//function holdermenu($addfdcnt,$myordcnt,$fdordcnt,$unick)
function holdermenu($myordcnt,$unick)
{
	echo "<b>$unick 您好</b> | <a href='pickstore.php'>發起邀約</a>";
	//if($addfdcnt>0) echo "<a href='tobefriend.php' rel='1' class='add2fd'>交友請求</a><span id='addfdcnt' class='add2fd2' style='background-color:#ff0000;'>$addfdcnt</span>";
	//else echo "<a href='tobefriend.php'>交友請求</a>";
	
	echo " | <a href='myorder.php'>我的邀約</a>";
	if($myordcnt>0) echo"<span class='add2fd2' style='background-color:#009000;'>$myordcnt</span>";

	//if($fdordcnt>0) echo " | <a href='friendinvite.php' class='add2fd' rel='2'>朋友邀約</a><span class='add2fd2' style='background-color:#0000ff;'>$fdordcnt</span>";
	//else echo " | <a href='friendinvite.php'>朋友邀約</a>";
	
	echo " | <a href='javascript:void(0)' rel='3' class='add2fd'>維護店家</a> | <a href='mainprod.html'>維護商品</a> | <a href='member.php'>會員專區</a> | <a href='/'>回首頁</a>
 <div id='hibox'>
    <div class='bd'><div id='hiboxcont'></div>
		<span class='r1'></span><span class='r2'></span><span class='r3'></span><span class='r4'></span><div class='ar'></div>
	</div>
</div>";
}

function guestmenu()
{
	global $unick;
	if($unick) $thenick=$unick; else $thenick="訪客";
	echo "<b>$thenick 您好</b> | <a href='orderlist.php'>訂單明細</a> | <a href='myhistory.php'>訂購記錄</a> | <a href='resendact.php'>重寄認証函</a> | <a href='/'>回首頁</a>";
}
?>    
    </div>
    <div id="tuser" width="100%">
<?php
if(!empty($uuser) && $uuser!="undefined") 
{
	echo "<b>".$uuser."</b> | <a href='logout.php'>登出</a>";
	if($utype==2) echo " | <a href='webadmin/adminmanage.php'>管理</a>";
}
else 
	echo "<b>歡迎光臨!!</b> | <a id='loginlink' href='javascript:void(0);' onclick='document.all.ttader.click();'>登入</a> | <a href='reguser.html'>註冊</a>";
?></div>
    <div class="tbb" style="LEFT: 0px"></div>
    <div class="tbb" style="RIGHT: 0px"></div>
  </div>
</div>
<center>
<div>
  <div style="PADDING-RIGHT: 0px; PADDING-LEFT: 0px; PADDING-BOTTOM: 3px; PADDING-TOP: 28px">
    <div title="TeaTime訂購系統" style="BACKGROUND: url(images/headbg.jpg) no-repeat; WIDTH: 616px; HEIGHT: 400px" align="left" >
      <div style="FONT-WEIGHT: bold; FONT-SIZE: 16px; LEFT: 214px; COLOR: #777; POSITION: relative; TOP: 70px" nowrap="noWrap">下午茶訂購系統 v2.4</div>
<?php
if($util->error || !$util->userdata){
?>	    
<FORM name="ttform" id="ttform" method="post" >
      <DIV id="ttsecdiv" style="margin-top: 230px; margin-left: 185px; height: 32px"><img src="images/keyfolder.png" /><input name="ttordkey" type="text" style="BORDER-RIGHT: #999 1px solid; PADDING-RIGHT: 8px; BORDER-TOP: #ccc 1px solid; PADDING-LEFT: 6px; BACKGROUND: #fff; PADDING-BOTTOM: 0px; MARGIN: 0px; FONT: 14px arial,sans-serif bold; VERTICAL-ALIGN: top; BORDER-LEFT: #ccc 1px solid; COLOR: #f00; PADDING-TOP: 5px; BORDER-BOTTOM: #999 1px solid" title="訂單密碼" autocomplete="off" size=25 id="ttseckey" /></DIV><DIV id="ttaddiv" style="display:none;margin-top: 230px; margin-left: 185px; height: 68px"><img src="images/email.png" /><input title="發起人/管理員帳號" style="BORDER-RIGHT: #999 1px solid; PADDING-RIGHT: 8px; BORDER-TOP: #ccc 1px solid; PADDING-LEFT: 6px; BACKGROUND: #fff; PADDING-BOTTOM: 0px; MARGIN: 0px; FONT: 14px arial,sans-serif bold; VERTICAL-ALIGN: top; BORDER-LEFT: #ccc 1px solid; COLOR: #ccc; PADDING-TOP: 5px; BORDER-BOTTOM: #999 1px solid" size=25 name="ttaduser" id="ttaduser" value="(登入帳號是註冊信箱)" /><div class="blankdiv"></div><img src="images/keylock.png" /><input title="發起人/管理員密碼" style="BORDER-RIGHT: #999 1px solid; PADDING-RIGHT: 8px; BORDER-TOP: #ccc 1px solid; PADDING-LEFT: 6px; BACKGROUND: #fff; PADDING-BOTTOM: 0px; MARGIN: 0px; FONT: 14px arial,sans-serif bold; VERTICAL-ALIGN: top; BORDER-LEFT: #ccc 1px solid; COLOR: #ccc; PADDING-TOP: 5px; BORDER-BOTTOM: #999 1px solid" size=25 value="(會員密碼)" id="ttadpasstxt" /><input title="發起人/管理員密碼" style="display:none;BORDER-RIGHT: #999 1px solid; PADDING-RIGHT: 8px; BORDER-TOP: #ccc 1px solid; PADDING-LEFT: 6px; BACKGROUND: #fff; PADDING-BOTTOM: 0px; MARGIN: 0px; FONT: 14px arial,sans-serif bold; VERTICAL-ALIGN: top; BORDER-LEFT: #ccc 1px solid; COLOR: #ccc; PADDING-TOP: 5px; BORDER-BOTTOM: #999 1px solid;" size=25 id="ttadpass" name="ttadpass" type="password" /></DIV><BR style="LINE-HEIGHT: 0;"><div style="margin-top: 5px; margin-left: 265px;"><INPUT type=submit value="進入系統" style="height:30px;"></div>
  <div style="margin-top: 5px; margin-left: 225px;"><FONT size=-1><SPAN style="TEXT-ALIGN: left"><INPUT id=nobody type=radio CHECKED value=0 name=meta><LABEL for=nobody> 訂單密碼 </LABEL><INPUT id=ttader type=radio value=1 name=meta>
      <font size=-1>登入系統</font></font>
      <LABEL for=master></LABEL></FONT></div></FORM>
<?php
}
else
{
	echo "<DIV id='ttsecdiv' style='margin-top: 230px; margin-left: 185px; height: 32px'>~歡迎您來到TeaTime下午茶訂購系統~</div>";
}
?>
    </div>
  </div>
  <br />
                
  </div>
  
 <!--左下漂浮开始-->
<div id="tipbox" class="zxdiv" style="width:300px;height:90px"><div style='margin-bottom:10px;text-align:right;padding:8px;cursor:pointer'><img alt='關閉' title='關閉' src='css/images/tabs_close.gif' id='tipboxclose' /></div><a href="friendinvite.php">您目前有<span id="fdicnt" style="color:#ff0000"></span>個新的朋友邀約</a></div>

<SCRIPT LANGUAGE="JavaScript">
function scall(){
	document.getElementById("tipbox").style.top=(document.documentElement.scrollTop+document.documentElement.clientHeight-document.getElementById("tipbox").offsetHeight)+"px";
	document.getElementById("tipbox").style.left=(document.documentElement.scrollLeft+document.documentElement.clientWidth-document.getElementById("tipbox").offsetWidth)+"px";
}

window.onscroll=scall;
window.onresize=scall;
window.onload=scall;
</SCRIPT>
<!--左下漂浮结束-->
</center>
</body>
</html>