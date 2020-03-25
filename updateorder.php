<?
header( 'Content-Type: text/html; charset=utf-8' );
include_once(dirname(__FILE__) . '/Classes/utility.php');
include_once(dirname(__FILE__) . '/Classes/quantaapp.php');

if ($_COOKIE['teatimeuuser']=="" || $_COOKIE['teatimeutype']==""){
	echo "<script language='javascript'>alert('網絡超時或您還沒有登入！');location.href='/';</script>";
}

require_once "conn.php";
$ono=$_REQUEST['ono'];
$pnoarr=$_REQUEST['pno'];
if(is_array($pnoarr)) $pnos=implode(",",$pnoarr);

$otitle=trim($_REQUEST['ordertitle']);
$unick=trim($_REQUEST['nickname']);
$odeadline=trim($_REQUEST['deadline']);
$onote=trim($_REQUEST['ordernote']);
$uemail=trim($_REQUEST['useremail']);
//$ocreatetime=date("Y-m-d H:i:s");
$cycle=$_REQUEST['cycle'];
if($cycle=="byone")
{
	$operiod="none#0";
}
else if($cycle=="bydays")
{
	$operiod="days#".trim($_REQUEST['duration']);
}
else if($cycle=="byweeks")
{
	$operiod="weeks#".implode("-",$_REQUEST['week']);
}

$changetime=date("Y-m-d H:i:s");
$oorderkey=$_REQUEST['oorderkey'];
$alldata=array("$unick","$uemail","$otitle","$onote","$odeadline","$oorderkey","$changetime","$ono");

if($ono && $pnos)
{
	global $util;

	$strSql="UPDATE ttordertb SET unick='$unick',uemail='$uemail',pnos='$pnos',otitle='$otitle',onote='$onote',odeadline='$odeadline',operiod='$operiod' WHERE ono=$ono";

	$ret=mysql_query($strSql,$link);
	if($ret==true)
	{
		save4users($alldata);
		echo "<script>alert('修改成功');location.href='member.php';</script>";
	}
	else
		echo "<script>alert('修改失敗');location.href='member.php';</script>";
}

function send2friend($ufemail='',$ordarr=array())
{
	//'$unick','$uemail','$otitle','$onote','$odeadline','$oorderkey','$ocreatetime'

	$subject="下午茶訂購系統-$ordarr[0] 更改了邀約內容";
	$from=$ordarr[1];
	$fromname="TeaTime下午茶";
	if(!empty($ufemail)) $to=$ufemail;
	$cc="";
	$bcc="";
	
	$mailbody="<span style='font-size:12px'>
	您好,<br>
	<br>
	您的朋友 $ordarr[0] 於 $ordarr[6] 的時候,在<u>下午茶訂購系統</u>修改了一個邀約。<br>
	以下是本邀約修改後的詳細資料<BR>
	============================================<br>
	發起人: <a href='mailto:$ordarr[1]'>$ordarr[0]</a><br>
	邀約標題: $ordarr[2]<br>
	訂單密碼: <span style='color:#00f;font-weight:bold'><a href='http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]'>$ordarr[5]</a></span><br>
	邀約說明: $ordarr[3]<br>
	終止時間: $ordarr[4]<br>
	============================================<br>
	<br>
	<a href='http://lttap01.rsquanta.com/'>http://lttap01.rsquanta.com/</a><br>
	<br>
	請使用上述的<font color='#ff0000'>訂單密碼</font>登入確認，謝謝。<br>
	---------------------------------------------------<br>
	感謝您的參與<br>
	
	下午茶訂購系統 <a href='lttap01.rsquanta.com' target='_blank'>http://lttap01.rsquanta.com/</a> <br>
	系統聯絡人:james.chao@quantatw.com</span>";
	
	//echo "$from,$fromname,$to,$cc,$bcc,$subject,$mailbody2";
	sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody);
}
function sendAppMessage($displayname,$ordarr)
{
global $util;
$onote=strip_tags($ordarr[3]);
$workid=$util->myldap->getUserWorkid($displayname);
$mailbody="您好,

您的朋友 $ordarr[0] 於 $ordarr[6] 的時候,在TeaTime訂購系統修改了一個邀約。
以下是本邀約的詳細資料
============================
發起人: $ordarr[0]
邀約標題: $ordarr[2]
訂單密碼: http://lttap01.rsquanta.com/ordermenu.php?ono=$ordarr[7]&ok=$ordarr[5]
邀約說明: $onote
終止時間: $ordarr[4]
============================
請使用上述的'訂單密碼'登入確認，謝謝。
-------------------------------------------
感謝您參與下午茶訂購系統 http://lttap01.rsquanta.com
系統聯絡人:james.chao@quantatw.com";
if($workid)
{
	$app=new quantaapp();
	$userlist=$app->registerList();
	$pos=strpos($userlist,$workid);
	if($pos!==false)
	{
		$msgdata=array(
			'ReceiveCompanyCode'=>'QCI', 	//收訊者公司別
			'ReceiveType'=>'3', 			//收訊者類別 3:person
			'ReceiveID'=>$workid,		//收訊者代號
			'Title'=>'您的朋友修改了下午茶邀約',			//訊息標題	
			'Detail'=>$mailbody
		);
		$app->simpleMessage($msgdata);
	}
}
}
function sendmail($from,$fromname,$to,$cc,$bcc,$subject,$mailbody)
{
	//$header .="Reply-To: ".$to."\r";
	//$header .="Return-Path: ".$to."\r";
	//$header .="Content-Type: multipart/alternative\r";
	$subject="=?UTF-8?B?".base64_encode("$subject")."?=";
	//$mailFrom = '=?utf-8?B?'.base64_encode("$fromname").'?='.' <$from>';
	$mailFrom= $from;
	$headers = "MIME-Version: 1.0\r";
	$headers .= "Content-type: text/html; charset=UTF-8\r";
	$headers .= "From: $mailFrom\r";
	$headers .= "To: $to\r";
	if($cc) $headers .="Cc: $cc\r";
	if($bcc) $headers .= "Bcc: $bcc\r";
	
	
	if($from&&$to&&$mailbody&&$subject)
	{
		
		if(chkservice('10.243.29.110',25))
		{
			//echo "mail($to, $subject, $mailbody, $headers)";
			//mail("james.chao@quantatw.com", "test", "test", $headers);
			$ret=mail($to, $subject, $mailbody, $headers);
			
			//echo $mailbody;
			//echo $headers;
			//exit;
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

function save4users($alldata)
{
	global  $util;
	$ufnames=str_replace(";",",",$_POST["ufname"]); 
	$ufnamearr=explode(",",$ufnames);
	$uno=$_COOKIE['teatimeuserno'];
	$userArray=array();
	$mailArray=array();
	foreach($ufnamearr as $term)
	{
		$term=trim($term);
		$ret=$util->myldap->isPersonGroup($term);
		if($ret) //it is a group
		{
			$stripterm=trim($term,"*");
			$subUsers=$util->myldap->getAllUserInGroup($stripterm);
			if (is_array($subUsers)) 
			{
				$userArray = array_merge($userArray, $subUsers); 
				$userArray = array_unique($userArray);
			}
			//$term=trim($term,"*");
			//have to fully matched
			$getarr=$util->myldap->getKeyGroup($term,true); 
			$getarr=array_keys($getarr);
			$mailArray[]=$getarr[0];
			//deal with duplicated email which in group
			foreach($mailArray as $key=>$mail)
			{
				if(array_key_exists($mail,$userArray)) unset($mailArray[$key]);
			}
		}
		else //it is a person
		{
			if(!in_array($term,$userArray)) 
			{
				$userArray[]=$term;
				$getarr=$util->myldap->getKeyUser($term,true); //have to fully matched
				$getarr=array_keys($getarr);
				$mailArray[]=$getarr[0];
			}
		}
		
	}
	
	//delete all recipts
	$strSql="DELETE FROM ttinvitetb WHERE ono=".$alldata[7];
	$util->db->Query($strSql);
	
	
	foreach($userArray as $ufn)
	{
		if(!empty($ufn)) 
		{
			$values=array(
				'uno'=>$uno,
				'ufname'=>$util->db->SQLValue($ufn),
				'ono'=>$util->db->SQLValue($alldata[7],"int"),
				'addtime'=>$util->db->SQLValue(date("Y-m-d H:i:s"))
			);
			$ret=$util->db->InsertRow("ttinvitetb",$values);
			
			//send quanta app message
			//if($ret) sendAppMessage($ufn,$alldata);
		}
	}
	
	//send email to person or group
	foreach($mailArray as $ufemail)
	{
		send2friend($ufemail,$alldata);
	}
	
	return $mailArray;
}
?>