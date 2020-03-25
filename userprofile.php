<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
require_once "conn.php";

$teatimeuuser=$_COOKIE['teatimeuuser'];
$savebtn=$_REQUEST['savebutton'];
if($savebtn=="確定送出")
{
	global $link; 
	$uname=trim($_REQUEST["realname"]);
	if(!empty($_REQUEST["userpass"]))
	{
		$upass=md5(trim($_REQUEST["userpass"]));
		$passstring="upass='$upass',";
	}
	$unick=trim($_REQUEST["nickname"]);
	$ubirth=trim($_REQUEST["userbirth"]);
	$usex=$_REQUEST["usersex"];
	$utel=trim($_REQUEST["telphone"]);
	$ufax=trim($_REQUEST["userfax"]);
	$uurl=trim($_REQUEST["userurl"]);
	$uintro=trim($_REQUEST["userintro"]);
	if(!empty($_REQUEST["thezip"])) $uaddr=$_REQUEST["thezip"];
	if($_REQUEST["thecity"]!="縣市") $uaddr.=$_REQUEST["thecity"];
	if($_REQUEST["thecounty"]!="鄉鎮市區") $uaddr.=$_REQUEST["thecounty"];
	if(!empty($_REQUEST["useraddr"])) $uaddr.=$_REQUEST["useraddr"];
	
	
	$uip=$_SERVER['REMOTE_ADDR'];
	$utime=date("Y-m-d H:i:s");
	
	$upicname=$_FILES["userpic"]["name"];
	$upicsize=$_FILES["userpic"]["size"];
	$upictype=$_FILES["userpic"]["type"];
	
	if(!empty($upicname))
	{
		if(($upicsize/1024)<1500 && ($upictype=="image/jpeg" || $upictype=="image/pjpeg" || $upictype=="image/gif"|| $upictype=="image/x-png"))
		{
			$FilePath="userpic/".$upicname;
			move_uploaded_file($_FILES["userpic"]["tmp_name"],$FilePath);
		}
		else
		{
			$upicname="nophoto.png";
			echo "<script>alert('上傳圖檔不成功，可能圖檔太大了，或是非jpg,gif,png圖檔格式!');</script>";
		}
	}
	else
		$upicname="nophoto.png";
		
	$strSql = "UPDATE ttusertb SET $passstring usex=$usex,utel='$utel',ubirth='$ubirth',unick='$unick',uname='$uname',ufax='$ufax',uurl='$uurl',uaddr='$uaddr',upic='$upicname',uintro='$uintro' WHERE uuser='$teatimeuuser'";
	
	$ret=mysql_query($strSql,$link);

}

if(!empty($teatimeuuser))
{
	$strSql = "SELECT * FROM ttusertb WHERE uuser='$teatimeuuser'";

	$data_rows=mysql_query($strSql,$link);

	list($uno,$uuser,$upass,$usex,$utel,$ubirth,$unick,$utype,$uname,$ufax,$uurl,$uaddr,$upic,$uintro,$ustore,$uonoff,$uactive,$uip,$ulastlogin)=mysql_fetch_row($data_rows);
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 個人資料</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="css/jquery.tooltip.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	$('#edtbutton').click(function(){   
		//window.open('editprofile.php','_self');
		location.href='editprofile.php';
	});
	$('#profiletb td:empty').html('&nbsp;');
	//show image tip
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
	
});	
</script>
<style>

#profiletb td{ border:solid 1px #999999;}

</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/version_03.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/version_04.jpg" width="385" height="220" alt=""></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div>
<div id="newsdiv"><img src="images/title_profile.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="center" valign="top">
  <table width="450" border="0" cellpadding="4" cellspacing="1" id="profiletb">
 <tr> 
      <td width="100" align="right">暱名:</td>
      <td align="left"><?=$unick;?></td>
      </tr>
    <tr> 
      <td align="right">真實姓名:</td>
      <td align="left"><?=$uname;?></td>
      </tr>
    <tr>
      <td align="right">聯絡電話:</td>
      <td align="left"><?=$utel;?></td>
    </tr>
    <tr> 
      <td align="right">傳真:</td>
      <td align="left"><?=$ufax;?></td>
      </tr>
      <tr> 
      <td align="right">Email:</td>
      <td align="left"><?=$uuser;?></td>
      </tr>
      <tr> 
      <td align="right">個人網址:</td>
      <td align="left"><?=$uurl;?></td>
      </tr>
        <tr> 
      <td align="right" valign="top">聯絡地址:</td>
      <td align="left"><?=$uaddr;?></td>
      </tr>
    <tr> 
      <td align="right">生日:</td>
      <td align="left"><?=$ubirth;?></td>
      </tr>
    <tr>
      <td align="right">性別:</td>
      <td align="left"><? if($usex==1) echo "男生"; else if($utype==2) echo "女生";?></td>
    </tr>

    <tr> 
      <td align="right">管理權限:</td>
      <td align="left"><? if($utype==1) echo "邀約發起人"; else if($utype==2) echo "網站管理員";?></td>
      </tr>

    <tr> 
      <td align="right" valign="top">個人圖示:</td>
      <td align="left" valign="top"><img class="tonus" width="30" height="30" src="<? if($upic=="nophoto.png") echo "userpic/nophoto.png"; else echo "userpic/$upic";?>" /> <?=$upic;?></td>
      </tr>
        <tr> 
      <td align="right">個人簡介:</td>
      <td align="left"><?=$uintro;?></td>
      </tr>
    <tr> 
      <td align="right" valign="top">帳號認証狀態:</td>
      <td align="left"><? if($uactive=="y") echo "已認証"; else echo "未認証";?></td>
      </tr>
    <tr>
      <td align="right" valign="top">帳號啟用狀態:</td>
      <td align="left"><? if($uonoff=="y") echo "啟用中"; else echo "未啟用";?></td>
      </tr>
    <tr>
      <td align="right" valign="top">上一次登入IP:</td>
      <td align="left"><?=$uip;?></td>
      </tr>
    <tr>
      <td align="right" valign="top">上一次登入時間:</td>
      <td align="left"><?=$ulastlogin;?></td>
      </tr>
  </table><div style=" position:relative;top:-402px; left:-260px;"> <input type="button" name="edtbutton" id="edtbutton" value="編輯個資" style="height:30pt; width:50pt;" /></div>
  
   </td>
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
