<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript">
$(document).ready(function(){
	$('div.usermenutitle').click(function(){		
		$(this).next('div.usermenucon').slideToggle();
	}).siblings('div.usermenucon').hide();
});
</script>
</head>
<body>
<?
//header( 'Content-Type: text/html; charset=utf-8' );

$tusr=trim($_GET['tu']);
$tusertype=$_COOKIE['teatimeutype'];
$teatimeunick=$_COOKIE['teatimeunick'];

if($tusr!="" && strlen($tusr)!=0 && $tusr!="undefined" && $tusertype>=1)
{
	holdermenu($teatimeunick);
}
else
{
	guestmenu();
}

function holdermenu($teatimeunick)
{
	echo "<b>$teatimeunick 您好</b><div style='height:5px;'></div><br /><a href='myorder.php'>我的邀約</a><br /><a href='myhistory.php'>訂購紀錄</a><br /><!--a href='mystore.php'>最愛店家</a-->
<div class='usermenutitle'><a href='javascript:void(0);'>店家維護</a></div><div class='usermenucon'><ul><li><a href='addstore.html'>新增店家</a></li><li><a href='storemain.php'>修改店家</a></li></ul></div>
<div class='usermenutitle'><a href='mainprod.html'>商品維護</a></div>
<a href='logout.php'>登出</a><br />
<a href='/'>回首頁</a><br />";
}

function guestmenu()
{
	echo "<p><b>訪客 您好</b></p><br /><a href='myhistory.php'>訂購記錄</a><br /><a href='reguser.html'>申請帳號</a><br /><a href='resendact.php'>重寄認証函</a><br />
<a href='/'>登入</a><br />
<a href='/'>回首頁</a><br />";
}
?>
</body>
</html>
