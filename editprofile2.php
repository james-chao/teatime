<?
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
<script src="../js/twzipcode-1.3.1.js" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	$('#addrcontainer').twzipcode();
});
</script>	
<form action="userprofile.php" method="post" enctype="multipart/form-data" name="form1" id="form1">
  <table border="0px" style="font-size:12px" width="500" id="userprofiletb">
  <tr> 
      <td align="right" valign="top">驗證碼:</td>
      <td align="left"><table width="250" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td style=" padding-left:10px;border:solid 1px #ffffff"><input name="checknum" type="text" id="checknum" style="width:60px; height:30px; font-size:20px; font-weight:bold;" /></td>
          <td style=" border:solid 1px #ffffff"><img src="verify_image.php" alt="點此刷新驗證碼" name="verify_code" width="120" height="50" border="0" id="verify_code"
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
  
</form>
