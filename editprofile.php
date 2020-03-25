<?
session_cache_limiter('private,max-age=10800');  //nocache,public
session_start();
header("Cache-control: private"); 
header( 'Content-Type: text/html; charset=utf-8' );


if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";

$teatimeuuser=$_COOKIE['teatimeuuser'];

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
<script src="js/twzipcode-1.3.1.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script src="js/jquery.tooltip.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#addrcontainer').twzipcode();
	$('#userprofiletb td:empty').html('&nbsp;');

	$('#checknum').click(function(){$(this).val('');});
	$('#savebutton').click(function(){
		if($('#checknum').val()!=$.cookie('teatimevcode')) {alert('驗證碼錯誤,請重新輸入'); return false;}
	});
	$('.tonus').tooltip({delay: 0, showURL: false, bodyHandler: function() {return $("<img/>").attr("src", this.src);}});
});
 
</script>
<style>

#userprofiletb td{ border:solid 1px #999999;}

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
      <td align="center" valign="top"><form action="userprofile.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table border="0px" style="font-size:12px" width="500" id="userprofiletb">
  <tr> 
      <td align="right" valign="top">驗證碼:</td>
      <td align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td><input name="checknum" type="text" id="checknum" style="width:60px; height:30px; font-size:20px; font-weight:bold;" /></td>
          <td><img src="verify_image.php" alt="點此刷新驗證碼" name="verify_code" width="120" height="50" border="0" id="verify_code"
onclick="this.src='verify_image.php?'+ Math.random();" style="cursor:pointer;" /></td>
          </tr>
      </table></td>
      </tr>
  <tr> 
      <td align="right">暱名:</td>
      <td align="left"><input type="text" id="nickname" name="nickname" style="width:120px" value="<?=$unick;?>" /></td>
      </tr>
    <tr> 
      <td align="right">真實姓名:</td>
      <td align="left"><input type="text" id="realname" name="realname" style="width:180px" value="<?=$uname;?>" /></td>
      </tr>
    <tr>
      <td align="right">用戶密碼:</td>
      <td align="left"><input type="text" id="userpass" name="userpass" style="width:120px" /></td>
    </tr>
    <tr>
      <td align="right">電話:</td>
      <td align="left"><input type="text" id="telphone" name="telphone" style="width:120px" value="<?=$utel;?>" /></td>
    </tr>
    <tr> 
      <td align="right">傳真:</td>
      <td align="left"><input type="text" id="userfax" name="userfax" style="width:120px" value="<?=$ufax;?>" /></td>
      </tr>
    <tr>
      <td align="right">E-mail:</td>
      <td align="left"><input name="useremail" type="text" id="useremail" style="width:250px" value="<?=$uuser;?>" readonly="readonly" /></td>
    </tr>
    <tr>
      <td align="right">個人網址:</td>
      <td align="left"><input type="text" id="userurl" name="userurl" style="width:250px" value="<?=$uurl;?>" /></td>
    </tr>
      <tr> 
      <td align="right">性別:</td>
      <td align="left"><input type="radio" id="sexm" name="usersex" value="1" <? if($usex==1) echo 'checked';?> />
男&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" id="sexg" name="usersex" value="0" <? if($usex==0) echo 'checked';?> />
女</td>
      </tr>
          <tr> 
      <td align="right">生日:</td>
      <td align="left"><input type="text" id="userbirth" name="userbirth" style="width:120px" value="<?=$ubirth;?>" /></td>
      </tr>
        <tr> 
      <td align="right" valign="top">聯絡地址:</td>
      <td align="left"><div id="addrcontainer"></div><input class="theaddr" type="text" id="useraddr" name="useraddr" style="width:250px" value="<?=$uaddr;?>" />
      </td>
      </tr>

    <tr> 
      <td align="right" valign="top">上傳圖片:</td>
      <td align="left" valign="top"><input type="file" id="userpic" name="userpic" style="width:250px" />目前圖片:<img class="tonus" width="30" height="30" src="userpic/<?=$upic;?>" /> <?=$upic;?></td>
      </tr>
        <tr> 
      <td align="right">個人簡介:</td>
      <td align="left"><textarea name="userintro" rows="4" id="userintro" style="width:250px" type="text"><?=$uintro;?></textarea></td>
      </tr>
    
  </table>
  <br />
  
    <input type="submit" name="savebutton" id="savebutton" value="確定送出" style="height:30pt; width:50pt;" />
  
</form></td>
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
