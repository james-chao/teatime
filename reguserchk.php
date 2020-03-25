<?
header( 'Content-Type: text/html; charset=utf-8' );

require_once "conn.php";

function userexist($uuser,$upass)
{
	global $link; 
	$strSql = "SELECT uuser,upass,utype,unick FROM ttusertb WHERE uuser='$uuser' AND upass='$upass'";

	$data_rows=mysql_query($strSql,$link);
	
	if(list($uuser,$upass,$utype,$unick)=mysql_fetch_row($data_rows)) 
	{
		return 1;
	}
	else
	{
		$epxtime= 24 * 60 * 60;
		setcookie("teatimeuuser",$uuser,time()+$epxtime);
		setcookie("teatimeunick",$unick,time()+$epxtime);
		setcookie("teatimeutype",$utype,time()+$epxtime);
		return 0;
	}
}

function addnewuser($uuser,$upass)
{
	global $link; 
	$nickname=trim($_REQUEST["nickname"]);
	$birthday=trim($_REQUEST["birthday"]);
	$sex=trim($_REQUEST["sex"]);
	$telphone=trim($_REQUEST["telphone"]);
	$uip=$_SERVER['REMOTE_ADDR'];
	$utime=date("Y-m-d H:i:s");
	$ustore=43; //the tea time system
	
	$strSql = "INSERT INTO ttusertb (uuser,upass,usex,utel,ubirth,unick,utype,uname,ufax,uurl,uaddr,upic,uintro,ustore,uonoff,uactive,uip,ulastlogin) VALUES ('$uuser','$upass',$sex,'$telphone','$birthday','$nickname',1,'','','','','nophoto.png','','$ustore','y','n','$uip','$utime')";
	
	$ret=mysql_query($strSql,$link);
	
	if($ret==true) 
		return 1;
	else	
		return 0;
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

function sendverifymail($uuser)
{
$nickname=trim($_REQUEST["nickname"]);
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
如果您要成 為台灣下午茶 (iTeaTime.com.tw) 的會員，必須依照這封確認信來啟動您的帳號<br>
要啟動帳號請按以下連結，系統將會帶您前往確認頁，並自動完成確認並執行帳號啟動程序:<br>
<br>
<a href='http://lttap01.rsquanta.com/activeuser.php?key=$actkey'>http://lttap01.rsquanta.com/activeuser.php?key=$actkey</a><br>
<br>
確認啟動後，請用您註冊的帳號及密碼登入，謝謝。<br>
-----------------<br>
感謝您的註冊與參與<br>

下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
james.chao@quantatw.com<br>

============================================<br>
註冊人 IP: ".$_SERVER['REMOTE_ADDR']."</span>";

sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}

$email=trim($_REQUEST["email"]);
$password1=trim($_REQUEST["password1"]);
$password2=trim($_REQUEST["password2"]);

if($password1!=$password2) echo "<script>alert('兩次輸入的密碼不同,將返回重新輸入');history.back();</script>";
if(userexist($email,$password1))
	echo "<script>alert('註冊失敗，該用戶已經存在');history.back();</script>";
else
{
	if(addnewuser($email,md5($password1)))
	{
		sendverifymail($email);
		echo "<script>alert('註冊成功,您必須先到註冊信箱點選認證信函啟動此帳號');location.href='/';</script>";
	}
	else
		echo "<script>alert('註冊失敗,可能伺服器忙碌,請稍後再試');history.back();</script>";
}
?>