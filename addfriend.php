<?
header( 'Content-Type: text/html; charset=utf-8' );

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}
include "conn.php";

$ufno=$_POST['ufno'];
$uno=$_COOKIE['teatimeuserno'];
$strSql="SELECT fno FROM ttfriendtb WHERE uno=$uno AND ufno=$ufno";
$data=mysql_query($strSql);

if(list($fno)=mysql_fetch_row($data))
	echo 2;
else
{
	$strSql="INSERT INTO ttfriendtb (uno,ufno,fagree,addtime) VALUES ($uno,$ufno,'n',now())"; 
	$ret=mysql_query($strSql);
	if($ret==true) sendfdreqmail($ufno);
	echo ($ret==true)?1:0;
}

function sendfdreqmail($ufno)
{
if($ufno<=0) return false;

$strSql="SELECT uuser,unick FROM ttusertb WHERE uno=$ufno"; 
$data_row=mysql_query($strSql);
if(!list($uemail,$unick)=mysql_fetch_row($data_row)) return false;
	
$subject="下午茶訂購系統-新朋友的交友請求函";
$from="james.chao@quantatw.com";
$fromname="TeaTime下午茶";
$to=$uemail;
$cc="";
$bcc="james.chao@quantatw.com";
$mailbody="<span style='font-size:12px'>
$unick 您好,<br>
============================================<br>
您有一個新的交友請求，請立即<a href='http://lttap01.rsquanta.com/tobefriend.php'>上網</a>查看。<br>
============================================<br>
感謝您的參與<br>

下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
james.chao@quantatw.com<br></span>";
sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}

function sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody)
{
	$subject="=?UTF-8?B?".base64_encode("$subject")."?=";
	$mailFrom= $from;
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

?>