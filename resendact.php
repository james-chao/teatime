<?
header( 'Content-Type: text/html; charset=utf-8' );

function sendverifymail($uuser)
{

$actkey=md5($uuser);
$subject="下午茶訂購系統-新用戶認證信";
$from="james.chao@quantatw.com";
$fromname="TeaTime下午茶";
$to=$uuser;
$cc="";
$bcc="";
$mailbody="<span style='font-size:12px;'>
您好,<br>
<br>
這個 Email: $uuser 已經被用來於<u>下午茶訂購系統</u>註冊了一個新帳號。<br>
============================================<br>
如果您要成為下午茶 (iTeaTime.com.tw) 的會員，必須依照這封確認信來啟動您的帳號<br>
要啟動帳號請按以下連結，系統將會帶您前往確認頁，並自動完成確認並執行帳號啟動程序:<br>
<br>
<a href='http://lttap01.rsquanta.com/activeuser.php?key=$actkey'>http://lttap01.rsquanta.com/activeuser.php?key=$actkey</a><br>
<br>
確認啟動後，請用您註冊的帳號及密碼登入，謝謝。<br>
----------------------------------<br>
感謝您的註冊與參與<br>

下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
james.chao@quantatw.com<br>
============================================<br>
註冊人 IP: ".$_SERVER['REMOTE_ADDR']."</span>";

return sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}

function chkservice($host, $port)
{  
    if (ini_get('display_errors')==1){ //判斷ini的display errors的設定
    $ch_ini_display=1;
    ini_set('display_errors', 0);//設定連線錯誤時不要display errors
    }
    $hostip = gethostbyname($host); //檢查輸入的host name dns正解
        if (!$x = fsockopen($hostip, $port, $errno, $errstr, 1)) //測試連線
            $ret=0;
        else
			$ret=1;
	if ($x) fclose($x); //關閉連線
    if ($ch_ini_display==1) ini_set('display_errors', 1); //將ini的display error設定改回來
	return $ret;
} 
function sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody)
{
	//$header .="Reply-To: ".$to."\r\n";
	//$header .="Return-Path: ".$to."\r\n";
	//$header .="Content-Type: multipart/alternative\r\n";
	$subject="=?UTF-8?B?".base64_encode("$subject")."?=";
	//$mailFrom = '=?utf-8?B?'.base64_encode("$fromname").'?='.' <$from>';
	$mailFrom = "$from";
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
	$headers .= "From: $mailFrom\r\n";
	$headers .= "To: $to\r\n";
	
	if($cc) $headers .="Cc: $cc\r\n";
	if($bcc) $headers .= "Bcc: $bcc\r\n";
	
	
	if($from&&$to&&$mailbody&&$subject)
	{
		
		if(chkservice('10.243.29.110',25))
		{
			$ret=mail($to, $subject, $mailbody, $headers);
		}
	}
	return $ret;
}
$email=trim($_REQUEST["email"]);
$sendbtn=trim($_REQUEST["sendbtn"]);
if($email!="" && $sendbtn=="確定送出" )
{
	$ret=sendverifymail($email);
	if($ret)
		echo "<script>alert('寄件成功,請到註冊信箱點選認證信函啟動此帳號');</script>";
	else
		echo "<script>alert('寄件失敗,可能伺服器忙碌,請稍後再試');</script>";
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TeaTime 茶飲訂購系統 重寄認証信</title>
<link href="css/common.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
<script src="js/common.js" type="text/javascript"></script>
<script src="js/jquery.cookie.js" type="text/javascript"></script>
<script>
$(document).ready(function(){
	
	$('#sendbtn').click(function(){
		var email=$('#email').val();
		if(email=="") 
		{
			alert('請填寫email後才可以寄送');
			return false;
		}
	});
});	
</script>
<style>
.mtitle{ text-align:left; font-size:14px; font-weight:bold;}
#userprofiletb td{ border:solid 1px #999999;}
</style>
</head>
<body>
<div id="topdiv"><div style="width:385px;"><img src="images/reverify_03.jpg" width="385" height="220" alt=""></div><div style="width:385px;  position:relative; left:385px; top:-220px;"><img src="images/pick2.jpg" width="385" height="220" alt="" /></div></div>
<div id="leftdiv"><div style="width:50px; height:149px; "><img src="images/sub01_01.jpg" width="250" height="149" id="getT" /></div><div id="menubtndiv" style="width:250px; height:490px; background-image:url(images/menubg.jpg); background-repeat:no-repeat"><div id="ordbtndiv" class="ordbtn"><img src="images/ordbtn_on.gif" /></div><div id="odlbtndiv" class="odlbtn"><img src="images/odlbtn_on.gif" /></div><div id="grlbtndiv" class="grlbtn"><img src="images/grlbtn_on.gif" /></div><div id="bplbtndiv" class="bplbtn"><img src="images/bplbtn_on.gif" /></div><div id="cpnbtndiv" class="cpnbtn"><img src="images/cpnbtn_on.gif" /></div><div id="membtndiv" class="membtn"><img src="images/membtn_on.gif" /></div><div id="gbbbtndiv" class="gbbbtn"><img src="images/gbbbtn_on.gif" /></div><div id="olinkdiv"></div></div></div>
<div id="newsdiv"><img src="images/title_reverify.gif" width="432" height="44" alt="" /></div>
<div id="rightdiv"><table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td width="47" valign="top" background="images/sub01_19.gif"><img src="images/sub01_07-08.gif" width="47" height="28" alt="" /></td>
      <td valign="top" style="background-image:url(images/sub01_09.gif); background-position:top;">&nbsp;</td>
      <td width="24" height="28" valign="top" background="images/sub01_14.gif"><img src="images/sub01_10.gif" width="24" height="28" alt="" /></td>
    </tr>
    <tr>
      <td valign="top" background="images/sub01_19.gif"><img src="images/sub01_09-13.gif" width="47" height="121" alt="" /></td>
      <td align="center" valign="top">
<div style="width:550px; border:#693 dashed 1px;padding:10px;">
<form action="resendact.php" method="post">
  <table cellspacing='0' cellpadding='0' align='center' border='0'>
    <tr>
      <td width='6' height='6'><img src='images/new_box02_top_left.gif'  width='6' height='6' /></td>
      <td background='images/new_box02_top_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
      <td width='6' height='6'><img src='images/new_box02_top_right.gif'  width='6' height='6' /></td>
    </tr>
    <tr>
      <td background='images/new_box02_left_bg.gif'></td>
      <td >
          <table width="100%" border="0" cellpadding="2" cellspacing="2" bgcolor="#ECF1FB">
            <tr>
              <td align="center">&nbsp;</td>
              <td>重寄認証信: <img src="images/email.gif" width="15" height="15" /></td>
            </tr>
            <tr>
              <td width="140" align="center" class="mtitle">請填您的註冊信箱</td>
              <td><input type="text" name="email" id="email" style="width:300px;" /></td>
            </tr>
            <tr>
              <td colspan="2" align="center"><input type="submit" value="確定送出"  name="sendbtn" id="sendbtn" /></td>
              </tr>
          </table>
       </td>
      <td background='images/new_box02_right_bg.gif'></td>
    </tr>
    <tr>
      <td><img src='images/new_box02_bottom_left.gif'  width='6' height='6' /></td>
      <td background='images/new_box02_bottom_bg.gif'><img src='images/blank.gif'  width='1' height='1' /></td>
      <td><img src='images/new_box02_bottom_right.gif'  width='6' height='6' /></td>
    </tr>
  </table>
</form>
</div></td>
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