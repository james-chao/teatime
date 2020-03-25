<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/easyui.css">
<link rel="stylesheet" type="text/css" href="css/icon.css">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery.easyui.min.js"></script>
<style>
.add2fd2{display:inline-block;border:solid 1px #cccccc;color:#ffffff;font-weight:blod;width:14px;text-align:center; font-family:"Courier New", Courier, monospace}
#hibox{display:none;position:relative;z-index:100;zoom:1;}
#hibox .bd{width:229px;background:#fff;border:1px solid #b2cbd8;margin-left:11px;padding:8px;position:relative;color:#666;font-size:92%;line-height:1.5em;zoom:1;*position:absolute;}
#hibox .bd span{display:block;width:3px;height:3px;background:url(images/hibox.png) 0 0 no-repeat;font-size:0;position:absolute;}
#hibox .bd span.r1{top:-1px;left:-1px;}
#hibox .bd span.r2{background-position:0 -6px;top:-1px;right:-1px;_right:-2px;}
#hibox .bd span.r3{background-position:0 -13px;bottom:-1px;right:-1px;_bottom:-1px;_right:-2px;}
#hibox .bd span.r4{background-position:0 -19px;bottom:-1px;left:-1px;_bottom:-1px;}
#hibox .bd div.ar{width:11px;height:12px;background:url(images/hibox.png) 0px -40px no-repeat;position:absolute;top:-11px;left:18%;}

</style>
<script>
$(document).ready(function() {
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
<?
$tusr=trim($_REQUEST['tu']);
$tusertype=$_COOKIE['teatimeutype'];
$teatimeunick=$_COOKIE['teatimeunick'];
$uno=$_COOKIE['teatimeuserno'];

if($tusr!="" && strlen($tusr)!=0 && $tu!="undefined" && $tusertype>=1)
{
	include "conn.php";
	//new friends, fagree type==> y:agree, c:cancel, n:notyet
	$strSql="SELECT F.uno,uname,unick,upic,ufno FROM ttfriendtb F INNER JOIN ttusertb U  ON F.uno=U.uno WHERE uonoff='y' AND F.ufno=$uno AND fagree='n'";
	$data_rows=mysql_query($strSql);
	$addfdcnt=mysql_num_rows($data_rows);
	//myorder
	$strSql="SELECT ono FROM ttordertb WHERE uno=$uno";
	$data_rows=mysql_query($strSql);
	$myordcnt=mysql_num_rows($data_rows);
	//friend invite
	$strSql="SELECT I.uno,ufno,unick,otitle FROM ttinvitetb I INNER JOIN ttordertb O ON I.ufno=O.uno AND I.ono=O.ono WHERE I.uno=$uno AND omailflag<>'y'";
	$data_rows=mysql_query($strSql);
	$fdordcnt=mysql_num_rows($data_rows);
	holdermenu($addfdcnt,$myordcnt,$fdordcnt,$teatimeunick);
}
else
{
	guestmenu();
}		

function holdermenu($addfdcnt,$myordcnt,$fdordcnt,$teatimeunick)
{
	echo "<b>$teatimeunick 您好</b> | <a href='pickstore.php'>發起邀約</a>";
	//if($addfdcnt>0) echo "<a href='tobefriend.php' rel='1' class='add2fd'>交友請求</a><span id='addfdcnt' class='add2fd2' style='background-color:#ff0000;'>$addfdcnt</span>";
	//else echo "<a href='tobefriend.php'>交友請求</a>";
	
	echo " | <a href='myorder.php'>我的邀約</a>";
	if($myordcnt>0) echo"<span class='add2fd2' style='background-color:#009000;'>$myordcnt</span>";

	if($fdordcnt>0) echo " | <a href='friendinvite.php' class='add2fd' rel='2'>朋友邀約</a><span class='add2fd2' style='background-color:#0000ff;'>$fdordcnt</span>";
	else echo " | <a href='friendinvite.php'>朋友邀約</a>";
	
	echo " | <a href='javascript:void(0)' rel='3' class='add2fd'>維護店家</a> | <a href='mainprod.html'>維護商品</a> | <a href='member.php'>會員專區</a> | <a href='/'>回首頁</a>
 <div id='hibox'>
    <div class='bd'><div id='hiboxcont'></div>
		<span class='r1'></span><span class='r2'></span><span class='r3'></span><span class='r4'></span><div class='ar'></div>
	</div>
</div>";
}

function guestmenu()
{
	global $teatimeunick;
	if($teatimeunick) $thenick=$teatimeunick; else $thenick="訪客";
	echo "<b>$thenick 您好</b> | <a href='orderlist.php'>訂單明細</a> | <a href='myhistory.php'>訂購記錄</a> | <a href='resendact.php'>重寄認証函</a> | <a href='/'>回首頁</a>";
}
?>


